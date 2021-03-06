<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem\Storage;

use ChessZebra\JobSystem\Job\Job;
use ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob;
use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\Contract\ResponseInterface;
use Pheanstalk\Job as PheanstalkJob;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function json_encode;

final class PheanstalkTest extends TestCase
{
    /**
     * The connection used to create tests.
     *
     * @var MockObject|PheanstalkInterface
     */
    private $connection;

    /**
     * Called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = $this->getMockForAbstractClass(PheanstalkInterface::class);
    }

    /**
     * Tests if the reserve timeout can be set and retrieved correctly.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk::__construct
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk::getReserveTimeout
     */
    public function testIfReserveTimeoutIsConstructed(): void
    {
        // Arrange
        // ...

        // Act
        $storage = new Pheanstalk($this->connection, 12345);

        // Assert
        static::assertEquals(12345, $storage->getReserveTimeout());
    }

    /**
     * Tests if the reserve timeout can be set and retrieved correctly.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk::getReserveTimeout
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk::setReserveTimeout
     */
    public function testSetGetReserveTimeout(): void
    {
        // Arrange
        $storage = new Pheanstalk($this->connection);

        // Act
        $storage->setReserveTimeout(12345);

        // Assert
        static::assertEquals(12345, $storage->getReserveTimeout());
    }

    /**
     * Tests if a job can be added without setting the queue name on it.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk::addJob
     */
    public function testAddJobWithNullQueue(): void
    {
        // Arrange
        $this->connection->expects(static::exactly(2))->method('useTube')->withConsecutive(
            [static::equalTo('default')],
            [static::equalTo('')]
        );

        $storage = new Pheanstalk($this->connection);

        $job = new Job('name', []);

        // Act
        $storage->addJob($job);

        // Assert
        // ...
    }

    /**
     * Tests if a job can be added without setting the queue name on it.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk::addJob
     */
    public function testAddJobWithQueueName(): void
    {
        // Arrange
        $this->connection->expects(static::exactly(2))->method('useTube')->withConsecutive(
            [static::equalTo('awesome')],
            [static::equalTo('')]
        );

        $storage = new Pheanstalk($this->connection);

        $job = new Job('name', [], 'awesome');

        // Act
        $storage->addJob($job);

        // Assert
        // ...
    }

    /**
     * Tests if a job can be added without setting the queue name on it.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk::deleteJob
     */
    public function testDeleteJob(): void
    {
        // Arrange
        $job = new PheanstalkJob(123, '');

        $this->connection->expects(static::once())->method('delete')->with(
            static::equalTo($job)
        );

        $storage = new Pheanstalk($this->connection);

        $storedJob = new StoredJob($job, []);

        // Act
        $storage->deleteJob($storedJob);

        // Assert
        // ...
    }

    /**
     * Tests if a job can be marked as fail.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk::failJob
     */
    public function testFailJob(): void
    {
        // Arrange
        $job = new PheanstalkJob(123, '');

        $this->connection->expects(static::once())->method('bury')->with(
            static::equalTo($job)
        );

        $storage = new Pheanstalk($this->connection);

        $storedJob = new StoredJob($job, []);

        // Act
        $storage->failJob($storedJob);

        // Assert
        // ...
    }

    /**
     * Tests if a job can be marked as fail.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk::rescheduleJob
     */
    public function testRescheduleJob(): void
    {
        // Arrange
        $job = new PheanstalkJob(123, json_encode([
            'type' => 'awesome',
            'data' => [],
        ]));

        $this->connection->expects(static::once())->method('release')->with(
            static::equalTo($job)
        );

        $storage = new Pheanstalk($this->connection);

        $storedJob = new StoredJob($job, [
            'tube' => 'default',
            'delay' => 0,
            'ttr' => 0,
            'pri' => PheanstalkInterface::DEFAULT_PRIORITY,
        ]);

        // Act
        $storage->rescheduleJob($storedJob, null, null);

        // Assert
        // ...
    }

    /**
     * Tests if a job can be marked as fail.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk::retrieveJob
     */
    public function testRetrieveJobWithoutJobPresent(): void
    {
        // Arrange
        $this->connection->expects(static::once())->method('reserve');
        $this->connection->expects(static::never())->method('statsJob');

        $storage = new Pheanstalk($this->connection);

        // Act
        $job = $storage->retrieveJob();

        // Assert
        static::assertNull($job);
    }

    /**
     * Tests if a job can be marked as fail.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk::retrieveJob
     */
    public function testRetrieveJobWithJobPresent(): void
    {
        // Arrange
        $job = new PheanstalkJob(123, '');

        $this->connection->expects(static::once())->method('reserve')->willReturn($job);

        $statsResponse = $this->getMockForAbstractClass(ResponseInterface::class);

        $this->connection->expects(static::once())->method('statsJob')->with(
            static::equalTo($job)
        )->willReturn($statsResponse);

        $storage = new Pheanstalk($this->connection);

        // Act
        $job = $storage->retrieveJob();

        // Assert
        static::assertInstanceOf(StoredJobInterface::class, $job);
    }

    /**
     * Tests if a job can be marked as fail.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk::pingJob
     */
    public function testPingJob(): void
    {
        // Arrange
        $job = new PheanstalkJob(123, '');
        $storedJob = new StoredJob($job, []);

        $this->connection->expects(static::once())->method('touch')->with(
            static::equalTo($job)
        );

        $storage = new Pheanstalk($this->connection);

        // Act
        $storage->pingJob($storedJob);

        // Assert
        // ...
    }
}
