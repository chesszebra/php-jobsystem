<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem\Context;

use ChessZebra\JobSystem\Job\JobInterface;
use ChessZebra\JobSystem\Storage\StorageInterface;
use ChessZebra\JobSystem\Storage\StoredJobInterface;
use Psr\Log\LoggerInterface;

use function array_key_exists;

/**
 * A default context implementation.
 */
final class Context implements ContextInterface
{
    /**
     * The storage that held the job that is being executed.
     *
     * @var StorageInterface
     */
    private $storage;

    /**
     * The logger used to write to the output stream.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The job that was retrieved from storage.
     *
     * @var StoredJobInterface
     */
    private $storedJob;

    /**
     * Parameters passed to the job.
     *
     * @var mixed[]
     */
    private $params;

    /**
     * Statistics of the executed job.
     *
     * @var mixed[]
     */
    private $stats;

    /** @var int */
    private $nextInterval;

    /**
     * Initializes a new instance of this class.
     */
    public function __construct(StorageInterface $storage, LoggerInterface $logger, StoredJobInterface $storedJob)
    {
        $this->storage = $storage;
        $this->logger = $logger;
        $this->storedJob = $storedJob;
        $this->params = $storedJob->createJobRepresentation()->getWorkerParams();
        $this->stats = $storedJob->getStats();
        $this->nextInterval = 1;
    }

    /**
     * Gets the time in microseconds to wait before the next job can be executed.
     */
    public function getNextInterval(): int
    {
        return $this->nextInterval;
    }

    /**
     * Sets the time in microseconds to wait before the next job can be executed.
     */
    public function setNextInterval(int $nextInterval): void
    {
        $this->nextInterval = $nextInterval;
    }

    /**
     * Gets the logger which can be used to write to the output stream.
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Adds a job to the storage to be executed.
     *
     * @param JobInterface $job The job to add.
     */
    public function addJob(JobInterface $job): void
    {
        $this->storage->addJob($job);
    }

    /**
     * Keeps the worker alive by resetting the time to run counter.
     */
    public function pingJob(): void
    {
        $this->storage->pingJob($this->storedJob);
    }

    /**
     * Gets the value of a parameter or returns the default value when the parameter does not exists.
     *
     * @param string $name         The name of the parameter to get.
     * @param mixed $defaultValue The default value to return.
     *
     * @return mixed
     */
    public function getParam(string $name, $defaultValue = null)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }

        return $defaultValue;
    }

    /**
     * Gets all the parameters.
     *
     * @return mixed[]
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Gets the statistics of the current executed job.
     *
     * @return mixed[]
     */
    public function getStats(): array
    {
        return $this->stats;
    }
}
