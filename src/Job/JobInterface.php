<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem\Job;

/**
 * The interface that represents a job.
 */
interface JobInterface
{
    /**
     * Gets the name of the worker to run.
     *
     * @return string
     */
    public function getWorkerName(): string;

    /**
     * Gets an array with parameters that should be passed to the worker.
     *
     * @return array
     */
    public function getWorkerParams(): array;

    /**
     * Gets the queue to store the job in.
     *
     * @return null|string
     */
    public function getQueueName(): ?string;

    /**
     * Gets the priority of the job.
     * The higher this number is, the earlier the job is executed.
     *
     * @return int
     */
    public function getPriority(): int;

    /**
     * Gets the delay in seconds which indicates when the job is started.
     *
     * @return int|null
     */
    public function getDelay(): ?int;

    /**
     * Gets the time to run in seconds which defines how long the job can run.
     *
     * @return int|null
     */
    public function getTimeToRun(): ?int;
}
