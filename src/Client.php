<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem;

use ChessZebra\JobSystem\Context\Context;
use ChessZebra\JobSystem\Job\JobInterface;
use ChessZebra\JobSystem\Storage\StorageInterface;
use ChessZebra\JobSystem\Storage\StoredJobInterface;
use ChessZebra\JobSystem\Worker\Exception\RecoverableException;
use ChessZebra\JobSystem\Worker\Exception\UnknownWorkerException;
use ChessZebra\JobSystem\Worker\RescheduleStrategy\RescheduleStrategyInterface;
use ChessZebra\JobSystem\Worker\WorkerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class Client implements ClientInterface
{
    /**
     * The options used configure the client.
     *
     * @var ClientOptions
     */
    private $options;

    /**
     * The underlying job system used to as a job storage.
     *
     * @var StorageInterface
     */
    private $storage;

    /**
     * A container with all available workers.
     *
     * @var ContainerInterface
     */
    private $workers;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        ClientOptions $options,
        StorageInterface $storage,
        ContainerInterface $workers,
        LoggerInterface $logger
    ) {
        $this->options = $options;
        $this->storage = $storage;
        $this->workers = $workers;
        $this->logger = $logger;
    }

    /**
     * Gets the storage used by this client.
     *
     * @return StorageInterface
     */
    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    /**
     * Gets the options used to run this client.
     */
    public function getOptions(): ClientOptions
    {
        return $this->options;
    }

    /**
     * Runs the client.
     *
     * @return int The exit code.
     */
    public function run(): int
    {
        $exitCode = 0;
        $startTime = time();

        do {
            try {
                $this->processNextJob();
            } catch (Throwable $e) {
                $exitCode = $this->handleException($e);
                break;
            }

            // Allow the server to breath...
            usleep($this->getOptions()->getInterval());
        } while ($this->shouldKeepRunning($startTime, memory_get_usage(true)));

        return $exitCode;
    }

    /**
     * Checks if the client should keep running.
     *
     * @param int $startTime The unixtime of when the client started running.
     * @param int $memoryUsage The current amount of memory usage.
     * @return bool
     */
    private function shouldKeepRunning(int $startTime, int $memoryUsage): bool
    {
        $runningTimeExpired = (time() - $startTime) >= $this->getOptions()->getLifetime();

        $memoryLimitReached = $memoryUsage >= $this->getOptions()->getMaximumMemoryUsage();

        return !$runningTimeExpired && !$memoryLimitReached;
    }

    /**
     * @return void
     */
    private function processNextJob(): void
    {
        /** @var StoredJobInterface|null $job */
        $job = $this->storage->retrieveJob();

        if (!$job) {
            return;
        }

        // @todo Replace this with PSR-14 so we can move the logging into an event listener.
        $this->processJob($job);
    }

    /**
     * @param StoredJobInterface $storedJob
     */
    private function processJob(StoredJobInterface $storedJob): void
    {
        try {
            $this->executeJob($storedJob);
        } catch (RecoverableException $throwable) {
            $strategy = $throwable->getRescheduleStrategy();

            if ($strategy === null) {
                $strategy = $this->getOptions()->getRescheduleStrategy();
            }

            $this->rescheduleJob($storedJob, $strategy, $throwable);
        } catch (Throwable $throwable) {
            $this->failJob($storedJob, $throwable);
        }
    }

    /**
     * @throws UnknownWorkerException Thrown when the worker type is unknown.
     * @throws ContainerExceptionInterface Thrown when the worker cannot be retrieved from the service container.
     */
    private function executeJob(StoredJobInterface $storedJob): void
    {
        /** @var JobInterface $job */
        $job = $storedJob->createJobRepresentation();

        $this->logger->info(sprintf(
            '[#%d] Job "%s" started: %s',
            $storedJob->getId(),
            $job->getWorkerName(),
            json_encode($job->getWorkerParams(), JSON_UNESCAPED_SLASHES)
        ));

        if (!$this->workers->has($job->getWorkerName())) {
            throw new UnknownWorkerException(sprintf('The worker "%s" is not a valid worker.', $job->getWorkerName()));
        }

        /** @var WorkerInterface $worker */
        $worker = $this->workers->get($job->getWorkerName());
        $worker->run(new Context($this->storage, $this->logger, $storedJob));

        $this->storage->deleteJob($storedJob);

        $this->logger->info(sprintf(
            '[#%d] Finished and successfully deleted job',
            $storedJob->getId()
        ));
    }

    private function rescheduleJob(
        StoredJobInterface $storedJob,
        ?RescheduleStrategyInterface $strategy,
        Throwable $throwable
    ): void {
        $this->logger->emergency(sprintf(
            '[#%d] Rescheduling job: %s',
            $storedJob->getId(),
            $throwable->getMessage()
        ));

        $delay = $strategy ? $strategy->determineDelay($storedJob) : null;
        $priority = $strategy ? $strategy->determinePriority($storedJob) : null;

        $this->storage->rescheduleJob($storedJob, $delay, $priority);
    }

    private function failJob(StoredJobInterface $storedJob, Throwable $throwable): void
    {
        $this->logger->emergency(sprintf(
            '[#%d] Job failed: %s',
            $storedJob->getId(),
            $throwable->getMessage()
        ));

        $this->storage->failJob($storedJob);
    }

    private function handleException(Throwable $exception): int
    {
        $this->logger->emergency($exception->getMessage(), [
            'throwable' => $exception,
        ]);

        return 1;
    }
}
