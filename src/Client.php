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
     * The underlying job system used to as a job storage.
     *
     * @var StorageInterface
     */
    private $storage;

    /**
     * The logger used to write information to.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * A container with all available workers.
     *
     * @var ContainerInterface
     */
    private $workers;

    /**
     * The interval in microseconds between each job.
     *
     * @var int
     */
    private $interval;

    /**
     * The maximum amount of seconds the client can run.
     *
     * @var int
     */
    private $lifetime;

    /**
     * The maximum memory that can be used (in bytes) before the client stops running.
     *
     * @var int
     */
    private $maximumMemoryUsage;

    /**
     * The strategy used to reschedule the job.
     *
     * @var null|RescheduleStrategyInterface
     */
    private $rescheduleStrategy;

    /**
     * Initializes a new instance of this class.
     *
     * @param StorageInterface $storage
     * @param LoggerInterface $logger
     * @param ContainerInterface $workers
     */
    public function __construct(StorageInterface $storage, LoggerInterface $logger, ContainerInterface $workers)
    {
        $this->storage = $storage;
        $this->logger = $logger;
        $this->workers = $workers;
        $this->interval = 500;
        $this->lifetime = 3600;
        $this->maximumMemoryUsage = PHP_INT_MAX;
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
     * Gets the lifetime of the client.
     *
     * @return int Returns the lifetime in seconds.
     */
    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    /**
     * Sets the lifetime (in seconds) for the client.
     *
     * @param int $lifetime The lifetime to set.
     * @return void
     */
    public function setLifetime(int $lifetime): void
    {
        $this->lifetime = $lifetime;
    }

    /**
     * Gets the maximum amount of memory that can be used by the client.
     *
     * @return int The amount of memory in bytes.
     */
    public function getMaximumMemoryUsage(): int
    {
        return $this->maximumMemoryUsage;
    }

    /**
     * Sets the maximum amount of memory that can be used by the client.
     *
     * @param int $maximumMemoryUsage The memory in bytes to set.
     * @return void
     */
    public function setMaximumMemoryUsage(int $maximumMemoryUsage): void
    {
        $this->maximumMemoryUsage = $maximumMemoryUsage;
    }

    /**
     * Gets the interval in between jobs.
     *
     * @return int Returns the interval in seconds.
     */
    public function getInterval(): int
    {
        return $this->interval;
    }

    /**
     * Sets the interval in between jobs.
     *
     * @param int $interval The interval in seconds to set.
     * @return void
     */
    public function setInterval(int $interval): void
    {
        $this->interval = $interval;
    }

    /**
     * Gets the rescheduling strategy.
     *
     * @return RescheduleStrategyInterface
     */
    public function getRescheduleStrategy(): ?RescheduleStrategyInterface
    {
        return $this->rescheduleStrategy;
    }

    /**
     * Sets the reschedule strategy.
     *
     * @param null|RescheduleStrategyInterface $rescheduleStrategy The strategy to set.
     * @return void
     */
    public function setRescheduleStrategy(?RescheduleStrategyInterface $rescheduleStrategy): void
    {
        $this->rescheduleStrategy = $rescheduleStrategy;
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
                $this->logger->emergency($e->getMessage(), [
                    'throwable' => $e,
                ]);

                $exitCode = 1;
                break;
            }

            // Allow the server to breath...
            usleep($this->interval);
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
        $runningTimeExpired = (time() - $startTime) >= $this->getLifetime();

        $memoryLimitReached = $memoryUsage >= $this->maximumMemoryUsage;

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
                $strategy = $this->getRescheduleStrategy();
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
}
