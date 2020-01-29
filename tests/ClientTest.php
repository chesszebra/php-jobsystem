<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem;

use ChessZebra\JobSystem\Job\JobInterface;
use ChessZebra\JobSystem\Storage\StorageInterface;
use ChessZebra\JobSystem\Storage\StoredJobInterface;
use ChessZebra\JobSystem\Worker\Exception\RecoverableException;
use ChessZebra\JobSystem\Worker\RescheduleStrategy\RescheduleStrategyInterface;
use ChessZebra\JobSystem\Worker\WorkerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use const PHP_INT_MAX;

final class ClientTest extends TestCase
{
    /** @var MockObject */
    private $storage;

    /** @var MockObject */
    private $logger;

    /** @var MockObject */
    private $workers;

    /** @var MockObject */
    private $storedJob;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = $this->getMockForAbstractClass(StorageInterface::class);
        $this->logger = $this->getMockForAbstractClass(LoggerInterface::class);
        $this->workers = $this->getMockForAbstractClass(ContainerInterface::class);
        $this->storedJob = $this->getMockForAbstractClass(StoredJobInterface::class);
    }

    /**
     * Tests if the storage is constructed.
     *
     * @covers \ChessZebra\JobSystem\Client::__construct
     * @covers \ChessZebra\JobSystem\Client::getStorage
     */
    public function testIfStorageIsConstructed(): void
    {
        // Arrange
        $client = new Client($this->storage, $this->logger, $this->workers);

        // Act
        $result = $client->getStorage();

        // Assert
        static::assertInstanceOf(StorageInterface::class, $result);
    }

    /**
     * Tests if the lifetime is constructed.
     *
     * @covers \ChessZebra\JobSystem\Client::__construct
     * @covers \ChessZebra\JobSystem\Client::getLifetime
     */
    public function testIfLifetimeIsConstructed(): void
    {
        // Arrange
        $client = new Client($this->storage, $this->logger, $this->workers);

        // Act
        $result = $client->getLifetime();

        // Assert
        static::assertEquals(3600, $result);
    }

    /**
     * Tests if setting the lifetime works.
     *
     * @covers \ChessZebra\JobSystem\Client::getLifetime
     * @covers \ChessZebra\JobSystem\Client::setLifetime
     */
    public function testSetGetLifetime(): void
    {
        // Arrange
        $client = new Client($this->storage, $this->logger, $this->workers);

        // Act
        $client->setLifetime(42);

        $result = $client->getLifetime();

        // Assert
        static::assertEquals(42, $result);
    }

    /**
     * Tests if the maximum memory usage is constructed.
     *
     * @covers \ChessZebra\JobSystem\Client::__construct
     * @covers \ChessZebra\JobSystem\Client::getMaximumMemoryUsage
     */
    public function testIfMaximumMemoryUsageIsConstructed(): void
    {
        // Arrange
        $client = new Client($this->storage, $this->logger, $this->workers);

        // Act
        $result = $client->getMaximumMemoryUsage();

        // Assert
        static::assertEquals(PHP_INT_MAX, $result);
    }

    /**
     * Tests if setting the maximum memory usage works.
     *
     * @covers \ChessZebra\JobSystem\Client::getMaximumMemoryUsage
     * @covers \ChessZebra\JobSystem\Client::setMaximumMemoryUsage
     */
    public function testSetGetMaximumMemoryUsage(): void
    {
        // Arrange
        $client = new Client($this->storage, $this->logger, $this->workers);

        // Act
        $client->setMaximumMemoryUsage(42);

        $result = $client->getMaximumMemoryUsage();

        // Assert
        static::assertEquals(42, $result);
    }

    /**
     * Tests if the interval is constructed.
     *
     * @covers \ChessZebra\JobSystem\Client::__construct
     * @covers \ChessZebra\JobSystem\Client::getInterval
     */
    public function testIfIntervalIsConstructed(): void
    {
        // Arrange
        $client = new Client($this->storage, $this->logger, $this->workers);

        // Act
        $result = $client->getInterval();

        // Assert
        static::assertEquals(500, $result);
    }

    /**
     * Tests if setting the interval works.
     *
     * @covers \ChessZebra\JobSystem\Client::getInterval
     * @covers \ChessZebra\JobSystem\Client::setInterval
     */
    public function testSetGetInterval(): void
    {
        // Arrange
        $client = new Client($this->storage, $this->logger, $this->workers);

        // Act
        $client->setInterval(42);

        $result = $client->getInterval();

        // Assert
        static::assertEquals(42, $result);
    }

    /**
     * Tests if the rescheduling strategy is constructed.
     *
     * @covers \ChessZebra\JobSystem\Client::__construct
     * @covers \ChessZebra\JobSystem\Client::getRescheduleStrategy
     */
    public function testIfRescheduleStrategyIsConstructed(): void
    {
        // Arrange
        $client = new Client($this->storage, $this->logger, $this->workers);

        // Act
        $result = $client->getRescheduleStrategy();

        // Assert
        static::assertNull($result);
    }

    /**
     * Tests if setting the rescheduling strategy works.
     *
     * @covers \ChessZebra\JobSystem\Client::getRescheduleStrategy
     * @covers \ChessZebra\JobSystem\Client::setRescheduleStrategy
     */
    public function testSetGetRescheduleStrategy(): void
    {
        // Arrange
        $client = new Client($this->storage, $this->logger, $this->workers);

        $strategy = $this->getMockForAbstractClass(RescheduleStrategyInterface::class);

        // Act
        $client->setRescheduleStrategy($strategy);

        $result = $client->getRescheduleStrategy();

        // Assert
        static::assertEquals($strategy, $result);
    }

    /**
     * Tests running the client.
     */
    public function testRunWithoutJobs(): void
    {
        // Arrange
        $client = new Client($this->storage, $this->logger, $this->workers);
        $client->setLifetime(0);

        // Act
        $result = $client->run();

        // Assert
        static::assertEquals(0, $result);
    }

    /**
     * Tests running the client.
     */
    public function testRunWithInvalidStorage(): void
    {
        // Arrange
        $this->logger->expects($this->once())->method('emergency');
        $this->storage->expects($this->once())->method('retrieveJob')->willThrowException(new RuntimeException());

        $client = new Client($this->storage, $this->logger, $this->workers);
        $client->setLifetime(0);

        // Act
        $result = $client->run();

        // Assert
        static::assertEquals(1, $result);
    }

    /**
     * Tests running the client.
     */
    public function testRunWithInvalidWorker(): void
    {
        // Arrange
        $this->logger->expects($this->once())->method('emergency');
        $this->storage->expects($this->once())->method('retrieveJob')->willReturn($this->storedJob);
        $this->storage->expects($this->once())->method('failJob');

        $client = new Client($this->storage, $this->logger, $this->workers);
        $client->setLifetime(0);

        // Act
        $result = $client->run();

        // Assert
        static::assertEquals(0, $result);
    }

    /**
     * Tests running the client.
     */
    public function testRunWithValidWorker(): void
    {
        // Arrange
        $worker = $this->getMockForAbstractClass(WorkerInterface::class);

        $this->workers->expects($this->once())->method('has')->with($this->equalTo('awesome'))->willReturn(true);
        $this->workers->expects($this->once())->method('get')->with($this->equalTo('awesome'))->willReturn($worker);

        $job = $this->getMockForAbstractClass(JobInterface::class);
        $job->expects($this->any())->method('getWorkerName')->willReturn('awesome');

        $this->storedJob->expects($this->any())->method('getId')->willReturn(123);
        $this->storedJob->expects($this->any())->method('createJobRepresentation')->willReturn($job);

        $this->storage->expects($this->once())->method('retrieveJob')->willReturn($this->storedJob);

        $client = new Client($this->storage, $this->logger, $this->workers);
        $client->setLifetime(0);

        // Act
        $result = $client->run();

        // Assert
        static::assertEquals(0, $result);
    }

    /**
     * Tests running the client.
     */
    public function testRunWithReschedule(): void
    {
        // Arrange
        $worker = $this->getMockForAbstractClass(WorkerInterface::class);
        $worker->expects($this->once())->method('run')->willThrowException(new RecoverableException());

        $this->workers->expects($this->once())->method('has')->with($this->equalTo('awesome'))->willReturn(true);
        $this->workers->expects($this->once())->method('get')->with($this->equalTo('awesome'))->willReturn($worker);

        $job = $this->getMockForAbstractClass(JobInterface::class);
        $job->expects($this->any())->method('getWorkerName')->willReturn('awesome');

        $this->storedJob->expects($this->any())->method('getId')->willReturn(123);
        $this->storedJob->expects($this->any())->method('createJobRepresentation')->willReturn($job);

        $this->storage->expects($this->once())->method('retrieveJob')->willReturn($this->storedJob);

        $client = new Client($this->storage, $this->logger, $this->workers);
        $client->setLifetime(0);

        // Act
        $result = $client->run();

        // Assert
        static::assertEquals(0, $result);
    }
}
