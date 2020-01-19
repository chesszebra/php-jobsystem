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
 * A fixed value meaning the delay and priority are determined based on a static value.
 */
final class Fixed implements RescheduleStrategyInterface
{
    /** @var int */
    private $delay;

    /** @var int */
    private $priority;

    /**
     * Initializes a new instance of this class.
     *
     * @param int $delay
     * @param int $priority
     */
    public function __construct(int $delay, int $priority)
    {
        $this->delay = $delay;
        $this->priority = $priority;
    }

    /**
     * Determines the delay in seconds.
     *
     * @param StoredJobInterface $job The job to determine the delay for.
     * @return int
     */
    public function determineDelay(StoredJobInterface $job): int
    {
        return $this->delay;
    }

    /**
     * Determines the priority.
     *
     * @param StoredJobInterface $job The job to determine the priority for.
     * @return int
     */
    public function determinePriority(StoredJobInterface $job): int
    {
        return $this->priority;
    }
}
