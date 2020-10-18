<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem;

use ChessZebra\JobSystem\Worker\RescheduleStrategy\RescheduleStrategyInterface;
use PHPUnit\Framework\TestCase;

use function assert;

use const PHP_INT_MAX;

final class ClientOptionsTest extends TestCase
{
    /**
     * Tests if the lifetime is constructed.
     *
     * @covers \ChessZebra\JobSystem\ClientOptions::__construct
     * @covers \ChessZebra\JobSystem\ClientOptions::getLifetime
     */
    public function testIfLifetimeIsConstructed(): void
    {
        // Arrange
        $options = new ClientOptions();

        // Act
        $result = $options->getLifetime();

        // Assert
        static::assertEquals(3600, $result);
    }

    /**
     * Tests if setting the lifetime works.
     *
     * @covers \ChessZebra\JobSystem\ClientOptions::getLifetime
     * @covers \ChessZebra\JobSystem\ClientOptions::setLifetime
     */
    public function testSetGetLifetime(): void
    {
        // Arrange
        $options = new ClientOptions();

        // Act
        $options->setLifetime(42);

        $result = $options->getLifetime();

        // Assert
        static::assertEquals(42, $result);
    }

    /**
     * Tests if the maximum memory usage is constructed.
     *
     * @covers \ChessZebra\JobSystem\ClientOptions::__construct
     * @covers \ChessZebra\JobSystem\ClientOptions::getMaximumMemoryUsage
     */
    public function testIfMaximumMemoryUsageIsConstructed(): void
    {
        // Arrange
        $options = new ClientOptions();

        // Act
        $result = $options->getMaximumMemoryUsage();

        // Assert
        static::assertEquals(PHP_INT_MAX, $result);
    }

    /**
     * Tests if setting the maximum memory usage works.
     *
     * @covers \ChessZebra\JobSystem\ClientOptions::getMaximumMemoryUsage
     * @covers \ChessZebra\JobSystem\ClientOptions::setMaximumMemoryUsage
     */
    public function testSetGetMaximumMemoryUsage(): void
    {
        // Arrange
        $options = new ClientOptions();

        // Act
        $options->setMaximumMemoryUsage(42);

        $result = $options->getMaximumMemoryUsage();

        // Assert
        static::assertEquals(42, $result);
    }

    /**
     * Tests if the interval is constructed.
     *
     * @covers \ChessZebra\JobSystem\ClientOptions::__construct
     * @covers \ChessZebra\JobSystem\ClientOptions::getInterval
     */
    public function testIfIntervalIsConstructed(): void
    {
        // Arrange
        $options = new ClientOptions();

        // Act
        $result = $options->getInterval();

        // Assert
        static::assertEquals(500, $result);
    }

    /**
     * Tests if setting the interval works.
     *
     * @covers \ChessZebra\JobSystem\ClientOptions::getInterval
     * @covers \ChessZebra\JobSystem\ClientOptions::setInterval
     */
    public function testSetGetInterval(): void
    {
        // Arrange
        $options = new ClientOptions();

        // Act
        $options->setInterval(42);

        $result = $options->getInterval();

        // Assert
        static::assertEquals(42, $result);
    }

    /**
     * Tests if the rescheduling strategy is constructed.
     *
     * @covers \ChessZebra\JobSystem\ClientOptions::__construct
     * @covers \ChessZebra\JobSystem\ClientOptions::getRescheduleStrategy
     */
    public function testIfRescheduleStrategyIsConstructed(): void
    {
        // Arrange
        $options = new ClientOptions();

        // Act
        $result = $options->getRescheduleStrategy();

        // Assert
        static::assertNull($result);
    }

    /**
     * Tests if setting the rescheduling strategy works.
     *
     * @covers \ChessZebra\JobSystem\ClientOptions::getRescheduleStrategy
     * @covers \ChessZebra\JobSystem\ClientOptions::setRescheduleStrategy
     */
    public function testSetGetRescheduleStrategy(): void
    {
        // Arrange
        $options = new ClientOptions();

        $strategy = $this->getMockForAbstractClass(RescheduleStrategyInterface::class);
        assert($strategy instanceof RescheduleStrategyInterface);

        // Act
        $options->setRescheduleStrategy($strategy);

        $result = $options->getRescheduleStrategy();

        // Assert
        static::assertEquals($strategy, $result);
    }
}
