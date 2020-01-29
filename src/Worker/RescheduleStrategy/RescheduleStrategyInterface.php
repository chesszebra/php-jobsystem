<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem\Worker\RescheduleStrategy;

use ChessZebra\JobSystem\Storage\StoredJobInterface;

/**
 * The interface used to determine the delay and priority used for rescheduling.
 */
interface RescheduleStrategyInterface // phpcs:ignore
{
    /**
     * Determines the delay in seconds.
     *
     * @param StoredJobInterface $job The job to determine the delay for.
     */
    public function determineDelay(StoredJobInterface $job): int;

    /**
     * Determines the priority.
     *
     * @param StoredJobInterface $job The job to determine the priority for.
     */
    public function determinePriority(StoredJobInterface $job): int;
}
