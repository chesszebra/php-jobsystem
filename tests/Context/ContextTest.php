<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem\Context;

use ChessZebra\JobSystem\Job\Job;
use ChessZebra\JobSystem\Job\JobInterface;
use ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob;
use ChessZebra\JobSystem\Storage\StorageInterface;
use ChessZebra\JobSystem\Storage\StoredJobInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ContextTest extends TestCase
{
    private $storage;
    private $logger;
    private $storedJob;

    protected function setUp()
    {
        parent::setUp();

        $this->storage = $this->getMockForAbstractClass(StorageInterface::class);
        $this->logger = $this->getMockForAbstractClass(LoggerInterface::class);
        $this->storedJob = $this->getMockForAbstractClass(StoredJobInterface::class);
    }

    /**
     * Tests if the logger can be retrieved.
     *
     * @covers \ChessZebra\JobSystem\Context\Context::__construct
     * @covers \ChessZebra\JobSystem\Context\Context::getLogger
     */
    public function testGetLogger()
    {
        // Arrange
        $context = new Context($this->storage, $this->logger, $this->storedJob);

        // Act
        $result = $context->getLogger();

        // Assert
        static::assertEquals($this->logger, $result);
    }

    /**
     * Tests if a job can be added via the Context
     *
     * @covers \ChessZebra\JobSystem\Context\Context::__construct
     * @covers \ChessZebra\JobSystem\Context\Context::addJob
     */
    public function testAddJob()
    {
        // Arrange
        $context = new Context($this->storage, $this->logger, $this->storedJob);

        $job = $this->getMockForAbstractClass(JobInterface::class);

        $this->storage->expects($this->once())->method('addJob')->with(
            $this->equalTo($job)
        );

        // Act
        $context->addJob($job);

        // Assert
        // ...
    }

    /**
     * Tests if a job can be pinged via the Context
     *
     * @covers \ChessZebra\JobSystem\Context\Context::__construct
     * @covers \ChessZebra\JobSystem\Context\Context::pingJob
     */
    public function testPingJob()
    {
        // Arrange
        $context = new Context($this->storage, $this->logger, $this->storedJob);

        $this->storage->expects($this->once())->method('pingJob')->with(
            $this->equalTo($this->storedJob)
        );

        // Act
        $context->pingJob();

        // Assert
        // ...
    }

    /**
     * Tests if a parameter can be retrieved.
     *
     * @covers \ChessZebra\JobSystem\Context\Context::__construct
     * @covers \ChessZebra\JobSystem\Context\Context::getParam
     */
    public function testGetParam()
    {
        // Arrange
        $job = new Job('name', [
            'existing' => 'existing-value',
        ]);

        $this->storedJob->expects($this->once())->method('createJobRepresentation')->willReturn($job);

        $context = new Context($this->storage, $this->logger, $this->storedJob);

        // Act
        $result = $context->getParam('existing', 'default');

        // Assert
        static::assertEquals('existing-value', $result);
    }

    /**
     * Tests if the default value for getParam works.
     *
     * @covers \ChessZebra\JobSystem\Context\Context::__construct
     * @covers \ChessZebra\JobSystem\Context\Context::getParam
     */
    public function testGetParamDefaultValue()
    {
        // Arrange
        $context = new Context($this->storage, $this->logger, $this->storedJob);

        // Act
        $result = $context->getParam('non-existing', 'default');

        // Assert
        static::assertEquals('default', $result);
    }

    /**
     * Tests if getParams returns a valid value.
     *
     * @covers \ChessZebra\JobSystem\Context\Context::__construct
     * @covers \ChessZebra\JobSystem\Context\Context::getParams
     */
    public function testGetParams()
    {
        // Arrange
        $context = new Context($this->storage, $this->logger, $this->storedJob);

        // Act
        $result = $context->getParams();

        // Assert
        static::assertEquals([], $result);
    }

    /**
     * Tests if getStats returns a valid value.
     *
     * @covers \ChessZebra\JobSystem\Context\Context::__construct
     * @covers \ChessZebra\JobSystem\Context\Context::getStats
     */
    public function testGetStats()
    {
        // Arrange
        $context = new Context($this->storage, $this->logger, $this->storedJob);

        // Act
        $result = $context->getStats();

        // Assert
        static::assertEquals([], $result);
    }
}
