<?php

declare(strict_types=1);

namespace ChessZebra\JobSystem\Storage;

use ChessZebra\JobSystem\Job\JobInterface;

/**
 * A null storage which doesn't do anything.
 */
final class NullStorage implements StorageInterface
{
    public function addJob(JobInterface $job): void
    {
    }

    public function deleteJob(StoredJobInterface $storedJob): void
    {
    }

    public function failJob(StoredJobInterface $storedJob): void
    {
    }

    public function rescheduleJob(StoredJobInterface $storedJob, ?int $delay, ?int $priority): void
    {
    }

    public function retrieveJob(): ?StoredJobInterface
    {
        return null;
    }

    public function pingJob(StoredJobInterface $storedJob): void
    {
    }
}
