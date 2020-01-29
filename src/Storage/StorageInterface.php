<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem\Storage;

use ChessZebra\JobSystem\Job\JobInterface;

interface StorageInterface // phpcs:ignore
{
    /**
     * Adds a job.
     *
     * @param JobInterface $job The job to add.
     */
    public function addJob(JobInterface $job): void;

    /**
     * Deletes the given job from the storage.
     *
     * @param StoredJobInterface $storedJob The job to delete.
     */
    public function deleteJob(StoredJobInterface $storedJob): void;

    /**
     * Marks the job as failed.
     *
     * @param StoredJobInterface $storedJob The job to mark.
     */
    public function failJob(StoredJobInterface $storedJob): void;

    /**
     * Reschedules the job so that it can be ran again.
     *
     * @param StoredJobInterface $storedJob The job to reschedule.
     * @param int|null $delay     The delay to set.
     * @param int|null $priority  The priority to set.
     */
    public function rescheduleJob(StoredJobInterface $storedJob, ?int $delay, ?int $priority): void;

    /**
     * Retrieves the next available job or null when no job is available.
     */
    public function retrieveJob(): ?StoredJobInterface;

    /**
     * Keeps the job alive by resetting the time to run counter.
     *
     * @param StoredJobInterface $storedJob The job to ping.
     */
    public function pingJob(StoredJobInterface $storedJob): void;
}
