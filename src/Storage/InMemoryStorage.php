<?php

declare(strict_types=1);

namespace ChessZebra\JobSystem\Storage;

use ChessZebra\JobSystem\Job\JobInterface;
use ChessZebra\JobSystem\Storage\InMemory\DelayedJob;
use ChessZebra\JobSystem\Storage\InMemory\StoredJob;
use SplQueue;

/**
 * A storage adapter which keeps all jobs in memory.
 */
final class InMemoryStorage implements StorageInterface
{
    /** @var int */
    private $nextId;

    /** @var SplQueue */
    private $readyJobs;

    /** @var SplQueue */
    private $failedJobs;

    /** @var SplQueue */
    private $delayedJobs;

    public function __construct()
    {
        $this->nextId = 1;
        $this->readyJobs = new SplQueue();
        $this->failedJobs = new SplQueue();
        $this->delayedJobs = new SplQueue();
    }

    public function addJob(JobInterface $job): void
    {
        $this->readyJobs->enqueue(new StoredJob($job, $this->nextId++));
    }

    public function deleteJob(StoredJobInterface $storedJob): void
    {
        foreach ($this->readyJobs as $index => $job) {
            if ($job === $storedJob) {
                unset($this->readyJobs[$index]);
                return;
            }
        }

        foreach ($this->failedJobs as $index => $job) {
            if ($job === $storedJob) {
                unset($this->failedJobs[$index]);
                return;
            }
        }

        foreach ($this->delayedJobs as $index => $job) {
            if ($job === $storedJob) {
                unset($this->delayedJobs[$index]);
                return;
            }
        }
    }

    public function failJob(StoredJobInterface $storedJob): void
    {
        $this->failedJobs->enqueue($storedJob);
    }

    public function rescheduleJob(StoredJobInterface $storedJob, ?int $delay, ?int $priority): void
    {
        $this->delayedJobs->enqueue(new DelayedJob($storedJob, $delay, $priority));
    }

    public function retrieveJob(): ?StoredJobInterface
    {
        if (!$this->delayedJobs->isEmpty() && $this->delayedJobs[0]->isReady()) {
            /** @var DelayedJob $job */
            $job = $this->delayedJobs->dequeue();

            return $job->getStoredJob();
        }

        if ($this->readyJobs->isEmpty()) {
            return null;
        }

        return $this->readyJobs->dequeue();
    }

    public function pingJob(StoredJobInterface $storedJob): void
    {
    }
}
