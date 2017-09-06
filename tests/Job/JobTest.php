<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem\Job;

use PHPUnit\Framework\TestCase;

final class JobTest extends TestCase
{
    /**
     * Tests if the job is created with the correct name.
     *
     * @covers \ChessZebra\JobSystem\Job\Job::__construct
     * @covers \ChessZebra\JobSystem\Job\Job::getWorkerName
     */
    public function testIfNameIsConstructed()
    {
        // Arrange
        $params = [];

        // Act
        $job = new Job('name', $params, null);

        // Assert
        static::assertEquals('name', $job->getWorkerName());
    }

    /**
     * Tests if the job is created with the correct parameters.
     *
     * @covers \ChessZebra\JobSystem\Job\Job::__construct
     * @covers \ChessZebra\JobSystem\Job\Job::getWorkerParams
     */
    public function testIfParametersIsConstructedWithoutParameters()
    {
        // Arrange
        $params = [];

        // Act
        $job = new Job('name', $params, null);

        // Assert
        static::assertEquals([], $job->getWorkerParams());
    }

    /**
     * Tests if the job is created with the correct parameters.
     *
     * @covers \ChessZebra\JobSystem\Job\Job::__construct
     * @covers \ChessZebra\JobSystem\Job\Job::getWorkerParams
     */
    public function testIfParametersIsConstructedWithParameters()
    {
        // Arrange
        $params = [
            'key' => 'value',
        ];

        // Act
        $job = new Job('name', $params, null);

        // Assert
        static::assertEquals($params, $job->getWorkerParams());
    }

    /**
     * Tests if the job is created with the correct queue.
     *
     * @covers \ChessZebra\JobSystem\Job\Job::__construct
     * @covers \ChessZebra\JobSystem\Job\Job::getQueueName
     */
    public function testIfQueueIsConstructedWithoutName()
    {
        // Arrange
        $params = [];

        // Act
        $job = new Job('name', $params, null);

        // Assert
        static::assertNull($job->getQueueName());
    }

    /**
     * Tests if the job is created with the correct queue.
     *
     * @covers \ChessZebra\JobSystem\Job\Job::__construct
     * @covers \ChessZebra\JobSystem\Job\Job::getQueueName
     */
    public function testIfQueueIsConstructedWithName()
    {
        // Arrange
        $params = [];

        // Act
        $job = new Job('name', $params, 'test');

        // Assert
        static::assertEquals('test', $job->getQueueName());
    }

    /**
     * Tests if the priority can be set and retrieved correctly.
     *
     * @covers \ChessZebra\JobSystem\Job\Job::getPriority
     * @covers \ChessZebra\JobSystem\Job\Job::setPriority
     */
    public function testSetGetPriority()
    {
        // Arrange
        $job = new Job('name', [], null);

        // Act
        $job->setPriority(12345);

        // Assert
        static::assertEquals(12345, $job->getPriority());
    }

    /**
     * Tests if the delay can be set and retrieved correctly.
     *
     * @covers \ChessZebra\JobSystem\Job\Job::getDelay
     * @covers \ChessZebra\JobSystem\Job\Job::setDelay
     */
    public function testSetGetDelay()
    {
        // Arrange
        $job = new Job('name', [], null);

        // Act
        $job->setDelay(12345);

        // Assert
        static::assertEquals(12345, $job->getDelay());
    }

    /**
     * Tests if the delay can be set and retrieved correctly.
     *
     * @covers \ChessZebra\JobSystem\Job\Job::getTimeToRun
     * @covers \ChessZebra\JobSystem\Job\Job::setTimeToRun
     */
    public function testSetGetTimeToRun()
    {
        // Arrange
        $job = new Job('name', [], null);

        // Act
        $job->setTimeToRun(12345);

        // Assert
        static::assertEquals(12345, $job->getTimeToRun());
    }
}
