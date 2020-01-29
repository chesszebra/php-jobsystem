<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem;

use ChessZebra\JobSystem\Job\Job;
use ChessZebra\JobSystem\Job\JobInterface;
use ChessZebra\JobSystem\Storage\InMemoryStorage;
use ChessZebra\JobSystem\Storage\StorageInterface;
use ChessZebra\JobSystem\Storage\StoredJobInterface;
use ChessZebra\JobSystem\Worker\Exception\RecoverableException;
use ChessZebra\JobSystem\Worker\WorkerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class ClientTest extends TestCase
{
    /** @var ClientOptions */
    private $options;

    /** @var MockObject|StorageInterface */
    private $storage;

    /** @var MockObject|LoggerInterface */
    private $logger;

    /** @var MockObject|ContainerInterface */
    private $workers;

    /** @var MockObject|StoredJobInterface */
    private $storedJob;

    protected function setUp(): void
    {
        parent::setUp();

        $this->options = new ClientOptions();
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
        $client = new Client($this->options, $this->storage, $this->workers, $this->logger);

        // Act
        $result = $client->getStorage();

        // Assert
        static::assertInstanceOf(StorageInterface::class, $result);
    }

    /**
     * Tests running the client.
     */
    public function testRunWithoutJobs(): void
    {
        // Arrange
        $this->options->setLifetime(0);

        $client = new Client($this->options, $this->storage, $this->workers, $this->logger);

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

        $this->options->setLifetime(0);

        $client = new Client($this->options, $this->storage, $this->workers, $this->logger);

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
        $this->logger->expects($this->any())->method('emergency');
        $this->storage->expects($this->once())->method('retrieveJob')->willReturn($this->storedJob);
        $this->storage->expects($this->once())->method('failJob');

        $this->options->setLifetime(0);

        $client = new Client($this->options, $this->storage, $this->workers, $this->logger);

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

        $this->options->setLifetime(0);

        $client = new Client($this->options, $this->storage, $this->workers, $this->logger);

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

        $this->options->setLifetime(0);

        $client = new Client($this->options, $this->storage, $this->workers, $this->logger);

        // Act
        $result = $client->run();

        // Assert
        static::assertEquals(0, $result);
    }

    /**
     * Tests adding exception listeners
     */
    public function testExceptionListenerIsCalled(): void
    {
        // Arrange
        $called = false;

        $callback = function() use (&$called) {
            $called = true;
        };

        $this->options->setLifetime(0);

        $storage = new InMemoryStorage();
        $storage->addJob(new Job('worker', []));

        $client = new Client($this->options, $storage, $this->workers, $this->logger);
        $client->addExceptionListener($callback);

        // Act
        $client->run();

        // Assert
        static::assertTrue($called);
    }
}
