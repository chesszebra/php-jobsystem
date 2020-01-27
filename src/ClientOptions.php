<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem;

use ChessZebra\JobSystem\Worker\RescheduleStrategy\RescheduleStrategyInterface;

final class ClientOptions
{
    /**
     * The interval in microseconds between jobs.
     *
     * @var int
     */
    private $interval;

    /**
     * The maximum amount of seconds the client can run.
     *
     * @var int
     */
    private $lifetime;

    /**
     * The maximum memory that can be used (in bytes) before the client stops running.
     *
     * @var int
     */
    private $maximumMemoryUsage;

    /**
     * The strategy used to reschedule a job when it fails.
     *
     * @var RescheduleStrategyInterface|null
     */
    private $rescheduleStrategy;

    public function __construct()
    {
        $this->interval = 500;
        $this->lifetime = 3600;
        $this->maximumMemoryUsage = PHP_INT_MAX;
    }

    /**
     * Gets the lifetime of the client.
     *
     * @return int Returns the lifetime in seconds.
     */
    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    /**
     * Sets the lifetime (in seconds) for the client.
     *
     * @param int $lifetime The lifetime to set.
     * @return void
     */
    public function setLifetime(int $lifetime): void
    {
        $this->lifetime = $lifetime;
    }

    /**
     * Gets the maximum amount of memory that can be used by the client.
     *
     * @return int The amount of memory in bytes.
     */
    public function getMaximumMemoryUsage(): int
    {
        return $this->maximumMemoryUsage;
    }

    /**
     * Sets the maximum amount of memory that can be used by the client.
     *
     * @param int $maximumMemoryUsage The memory in bytes to set.
     * @return void
     */
    public function setMaximumMemoryUsage(int $maximumMemoryUsage): void
    {
        $this->maximumMemoryUsage = $maximumMemoryUsage;
    }

    /**
     * Gets the interval in between jobs.
     *
     * @return int Returns the interval in seconds.
     */
    public function getInterval(): int
    {
        return $this->interval;
    }

    /**
     * Sets the interval in between jobs.
     *
     * @param int $interval The interval in seconds to set.
     * @return void
     */
    public function setInterval(int $interval): void
    {
        $this->interval = $interval;
    }

    /**
     * Gets the rescheduling strategy.
     *
     * @return RescheduleStrategyInterface|null
     */
    public function getRescheduleStrategy(): ?RescheduleStrategyInterface
    {
        return $this->rescheduleStrategy;
    }

    /**
     * Sets the reschedule strategy.
     *
     * @param RescheduleStrategyInterface|null $rescheduleStrategy The strategy to set.
     * @return void
     */
    public function setRescheduleStrategy(?RescheduleStrategyInterface $rescheduleStrategy): void
    {
        $this->rescheduleStrategy = $rescheduleStrategy;
    }
}
