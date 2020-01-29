<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem\Storage;

use ChessZebra\JobSystem\Job\JobInterface;
use ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob;
use Pheanstalk\Job;
use Pheanstalk\PheanstalkInterface;
use function assert;
use function json_encode;
use function microtime;

/**
 * A beanstalkd storage using Pheanstalk.
 */
final class Pheanstalk implements StorageInterface
{
    /** @var PheanstalkInterface */
    private $connection;

    /**
     * The timeout (in seconds) that is waited to retrieve a job from the queue.
     *
     * @var int
     */
    private $reserveTimeout;

    /**
     * Initializes a new instance of this class.
     */
    public function __construct(PheanstalkInterface $connection, int $timeout = 1)
    {
        $this->connection = $connection;
        $this->reserveTimeout = $timeout;
    }

    /**
     * Gets the reserved timeout (in seconds) used to retrieve a job.
     */
    public function getReserveTimeout(): int
    {
        return $this->reserveTimeout;
    }

    /**
     * Sets the timeout (in seconds) used to retrieve a job.
     *
     * @param int $reserveTimeout The timeout to set.
     */
    public function setReserveTimeout(int $reserveTimeout): void
    {
        $this->reserveTimeout = $reserveTimeout;
    }

    /**
     * Adds a job.
     *
     * @param JobInterface $job The job to add.
     */
    public function addJob(JobInterface $job): void
    {
        $tube = $job->getQueueName() ?: PheanstalkInterface::DEFAULT_TUBE;

        $data = json_encode([
            'type' => $job->getWorkerName(),
            'time' => microtime(true),
            'data' => $job->getWorkerParams(),
        ]);

        $priority = PheanstalkInterface::DEFAULT_PRIORITY + $job->getPriority();
        $delay = $job->getDelay() ?: PheanstalkInterface::DEFAULT_DELAY;
        $ttr = $job->getTimeToRun() ?: PheanstalkInterface::DEFAULT_TTR;

        $this->connection->putInTube($tube, $data, $priority, $delay, $ttr);
    }

    /**
     * Deletes the given job from the storage.
     *
     * @param StoredJobInterface $storedJob The job to delete.
     */
    public function deleteJob(StoredJobInterface $storedJob): void
    {
        $this->connection->delete($storedJob->getJob());
    }

    /**
     * Marks the job as failed.
     *
     * @param StoredJobInterface $storedJob The job to mark.
     */
    public function failJob(StoredJobInterface $storedJob): void
    {
        $this->connection->bury($storedJob->getJob());
    }

    /**
     * Reschedules the job so that it can be ran again.
     *
     * @param StoredJobInterface $storedJob The job to reschedule.
     * @param int|null $delay     The delay to set.
     * @param int|null $priority  The priority to set.
     */
    public function rescheduleJob(StoredJobInterface $storedJob, ?int $delay, ?int $priority): void
    {
        $job = $storedJob->createJobRepresentation();
        assert($job instanceof JobInterface);

        if ($priority === null) {
            $priority = $job->getPriority();
        }

        if ($delay === null) {
            $delay = $job->getDelay() + 60;
        }

        $this->connection->release($storedJob->getJob(), $priority - PheanstalkInterface::DEFAULT_PRIORITY, $delay);
    }

    /**
     * Retrieves the next available job or null when no job is available.
     */
    public function retrieveJob(): ?StoredJobInterface
    {
        $job = $this->connection->reserve($this->getReserveTimeout());
        assert($job instanceof Job || $job === null);

        if (!$job) {
            return null;
        }

        $stats = $this->connection->statsJob($job)->getArrayCopy();

        return new StoredJob($job, $stats);
    }

    /**
     * Keeps the job alive by resetting the time to run counter.
     *
     * @param StoredJobInterface $storedJob The job to ping.
     */
    public function pingJob(StoredJobInterface $storedJob): void
    {
        $this->connection->touch($storedJob->getJob());
    }
}
