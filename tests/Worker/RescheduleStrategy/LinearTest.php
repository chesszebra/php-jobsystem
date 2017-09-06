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
use PHPUnit\Framework\TestCase;

final class LinearTest extends TestCase
{
    /**
     * Tests if the delay can be determined.
     *
     * @covers \ChessZebra\JobSystem\Worker\RescheduleStrategy\Linear::__construct
     * @covers \ChessZebra\JobSystem\Worker\RescheduleStrategy\Linear::determineDelay
     */
    public function testDetermineDelay()
    {
        // Arrange
        $job = $this->getMockForAbstractClass(StoredJobInterface::class);

        $strategy = new Linear(1337, 42);

        // Act
        $result = $strategy->determineDelay($job);

        // Assert
        static::assertEquals(1337, $result);
    }

    /**
     * Tests if the priority can be determined.
     *
     * @covers \ChessZebra\JobSystem\Worker\RescheduleStrategy\Linear::__construct
     * @covers \ChessZebra\JobSystem\Worker\RescheduleStrategy\Linear::determinePriority
     */
    public function testDeterminePriority()
    {
        // Arrange
        $job = $this->getMockForAbstractClass(StoredJobInterface::class);

        $strategy = new Linear(1337, 42);

        // Act
        $result = $strategy->determinePriority($job);

        // Assert
        static::assertEquals(42, $result);
    }
}
