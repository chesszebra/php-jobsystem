<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem\Worker\Exception;

use ChessZebra\JobSystem\Worker\RescheduleStrategy\Fixed;
use PHPUnit\Framework\TestCase;

final class RecoverableExceptionTest extends TestCase
{
    /**
     * @covers \ChessZebra\JobSystem\Worker\Exception\RecoverableException::getRescheduleStrategy
     */
    public function testGetRescheduleStrategyDefaultsToNull(): void
    {
        // Arrange
        $exception = new RecoverableException();

        // Act
        $strategy = $exception->getRescheduleStrategy();

        // Assert
        static::assertNull($strategy);
    }

    /**
     * @covers \ChessZebra\JobSystem\Worker\Exception\RecoverableException::getRescheduleStrategy
     * @covers \ChessZebra\JobSystem\Worker\Exception\RecoverableException::createWithRescheduleStrategy
     */
    public function testCreateWithRescheduleStrategy(): void
    {
        // Arrange
        $strategy = new Fixed(1, 1);
        $exception = RecoverableException::createWithRescheduleStrategy($strategy);

        // Act
        $strategy = $exception->getRescheduleStrategy();

        // Assert
        static::assertNotNull($strategy);
        static::assertSame('A job failed and needs to be rescheduled.', $exception->getMessage());
    }
}
