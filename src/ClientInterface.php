<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem;

use ChessZebra\JobSystem\Storage\StorageInterface;
use ChessZebra\JobSystem\Worker\RescheduleStrategy\RescheduleStrategyInterface;

interface ClientInterface
{
    /**
     * Gets the storage used by this client.
     *
     * @return StorageInterface
     */
    public function getStorage(): StorageInterface;

    /**
     * Gets the lifetime of the client.
     *
     * @return int Returns the lifetime in seconds.
     */
    public function getLifetime(): int;

    /**
     * Sets the lifetime (in seconds) for the client.
     *
     * @param int $lifetime The lifetime to set.
     * @return void
     */
    public function setLifetime(int $lifetime): void;

    /**
     * Gets the maximum amount of memory that can be used by the client.
     *
     * @return int The amount of memory in bytes.
     */
    public function getMaximumMemoryUsage(): int;

    /**
     * Sets the maximum amount of memory that can be used by the client.
     *
     * @param int $maximumMemoryUsage The memory in bytes to set.
     * @return void
     */
    public function setMaximumMemoryUsage(int $maximumMemoryUsage): void;

    /**
     * Gets the interval in between jobs.
     *
     * @return int Returns the interval in seconds.
     */
    public function getInterval(): int;

    /**
     * Sets the interval in between jobs.
     *
     * @param int $interval The interval in seconds to set.
     * @return void
     */
    public function setInterval(int $interval): void;

    /**
     * Gets the rescheduling strategy.
     *
     * @return RescheduleStrategyInterface
     */
    public function getRescheduleStrategy(): ?RescheduleStrategyInterface;

    /**
     * Sets the reschedule strategy.
     *
     * @param null|RescheduleStrategyInterface $rescheduleStrategy The strategy to set.
     * @return void
     */
    public function setRescheduleStrategy(?RescheduleStrategyInterface $rescheduleStrategy): void;

    /**
     * Runs the client.
     *
     * @return int The exit code.
     */
    public function run(): int;
}
