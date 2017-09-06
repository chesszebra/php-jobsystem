<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem\Context;

use ChessZebra\JobSystem\Job\JobInterface;
use ChessZebra\JobSystem\Storage\StorageInterface;
use ChessZebra\JobSystem\Storage\StoredJobInterface;
use Psr\Log\LoggerInterface;

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
     * @var StoredJobInterface
     */
    private $storedJob;

    /**
     * Parameters passed to the job.
     *
     * @var array
     */
    private $params;

    /**
     * Initializes a new instance of this class.
     *
     * @param StorageInterface $storage The job storage.
     * @param LoggerInterface $logger
     * @param StoredJobInterface $storedJob The job retreived from the storage.
     */
    public function __construct(StorageInterface $storage, LoggerInterface $logger, StoredJobInterface $storedJob)
    {
        $this->storage = $storage;
        $this->logger = $logger;
        $this->storedJob = $storedJob;
        $this->params = $storedJob->createJobRepresentation()->getWorkerParams();
    }

    /**
     * Gets the logger which can be used to write to the output stream.
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Adds a job to the storage to be executed.
     *
     * @param JobInterface $job The job to add.
     * @return void
     */
    public function addJob(JobInterface $job): void
    {
        $this->storage->addJob($job);
    }

    /**
     * Keeps the worker alive by resetting the time to run counter.
     *
     * @return void
     */
    public function pingJob(): void
    {
        $this->storage->pingJob($this->storedJob);
    }

    /**
     * Gets the value of a parameter or returns the default value when the parameter does not exists.
     *
     * @param string $name The name of the parameter to get.
     * @param mixed $defaultValue The default value to return.
     * @return mixed
     */
    public function getParam(string $name, $defaultValue = null)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }

        return $defaultValue;
    }
}
