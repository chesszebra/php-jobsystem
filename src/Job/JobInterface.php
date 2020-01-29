<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem\Job;

/**
 * The interface that represents a job.
 */
interface JobInterface // phpcs:ignore
{
    /**
     * Gets the name of the worker to run.
     */
    public function getWorkerName(): string;

    /**
     * Gets an array with parameters that should be passed to the worker.
     *
     * @return mixed[]
     */
    public function getWorkerParams(): array;

    /**
     * Gets the queue to store the job in.
     */
    public function getQueueName(): ?string;

    /**
     * Gets the priority of the job.
     * The higher this number is, the earlier the job is executed.
     */
    public function getPriority(): int;

    /**
     * Gets the delay in seconds which indicates when the job is started.
     */
    public function getDelay(): ?int;

    /**
     * Gets the time to run in seconds which defines how long the job can run.
     */
    public function getTimeToRun(): ?int;
}
