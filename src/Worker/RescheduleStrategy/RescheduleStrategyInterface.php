<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem\Worker\RescheduleStrategy;

use ChessZebra\JobSystem\Storage\StoredJobInterface;

/**
 * The interface used to determine the delay and priority used for rescheduling.
 */
interface RescheduleStrategyInterface
{
    /**
     * Determines the delay in seconds.
     *
     * @param StoredJobInterface $job The job to determine the delay for.
     * @return int
     */
    public function determineDelay(StoredJobInterface $job): int;

    /**
     * Determines the priority.
     *
     * @param StoredJobInterface $job The job to determine the priority for.
     * @return int
     */
    public function determinePriority(StoredJobInterface $job): int;
}
