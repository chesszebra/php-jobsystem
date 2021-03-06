<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem\Storage\Pheanstalk;

use ChessZebra\JobSystem\Job\JobInterface;
use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\Job;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function json_encode;

final class StoredJobTest extends TestCase
{
    /**
     * Tests if the job can be retrieved.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob::__construct
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob::getJob
     */
    public function testGetJob(): void
    {
        // Arrange
        $pheanstalkJob = new Job(1337, '');
        $job = new StoredJob($pheanstalkJob, []);

        // Act
        $result = $job->getJob();

        // Assert
        static::assertEquals($pheanstalkJob, $result);
    }

    /**
     * Tests if the job id can be retrieved.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob::getId
     */
    public function testGetId(): void
    {
        // Arrange
        $pheanstalkJob = new Job(1337, '');
        $job = new StoredJob($pheanstalkJob, []);

        // Act
        $result = $job->getId();

        // Assert
        static::assertEquals(1337, $result);
    }

    /**
     * Tests if the job id can be retrieved.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob::createJobRepresentation
     */
    public function testCreateJobRepresentation(): void
    {
        // Arrange
        $pheanstalkJob = new Job(1337, json_encode([
            'type' => 'awesome',
            'data' => [],
        ]));
        $job = new StoredJob($pheanstalkJob, [
            'tube' => 'default',
            'delay' => PheanstalkInterface::DEFAULT_DELAY,
            'ttr' => PheanstalkInterface::DEFAULT_TTR,
            'pri' => PheanstalkInterface::DEFAULT_PRIORITY,
        ]);

        // Act
        $result = $job->createJobRepresentation();

        // Assert
        static::assertInstanceOf(JobInterface::class, $result);
    }

    /**
     * Tests if the job contains valid json.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob::createJobRepresentation
     */
    public function testCreateJobRepresentationWithoutData(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid JSON, got ""');

        // Arrange
        $pheanstalkJob = new Job(1337, '');
        $job = new StoredJob($pheanstalkJob, [
            'tube' => 'default',
            'delay' => PheanstalkInterface::DEFAULT_DELAY,
            'ttr' => PheanstalkInterface::DEFAULT_TTR,
            'pri' => PheanstalkInterface::DEFAULT_PRIORITY,
        ]);

        // Act
        $job->createJobRepresentation();
    }

    /**
     * Tests if the type member is present in the json data.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob::createJobRepresentation
     */
    public function testCreateJobRepresentationWithoutTypeMember(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing "type" field in "{"test": "test"}"');

        // Arrange
        $pheanstalkJob = new Job(1337, '{"test": "test"}');
        $job = new StoredJob($pheanstalkJob, [
            'tube' => 'default',
            'delay' => PheanstalkInterface::DEFAULT_DELAY,
            'ttr' => PheanstalkInterface::DEFAULT_TTR,
            'pri' => PheanstalkInterface::DEFAULT_PRIORITY,
        ]);

        // Act
        $job->createJobRepresentation();
    }

    /**
     * Tests if the data member is present in the json data.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob::createJobRepresentation
     */
    public function testCreateJobRepresentationWithoutDataMember(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing "data" field in "{"type": "test"}"');

        // Arrange
        $pheanstalkJob = new Job(1337, '{"type": "test"}');
        $job = new StoredJob($pheanstalkJob, [
            'tube' => 'default',
            'delay' => PheanstalkInterface::DEFAULT_DELAY,
            'ttr' => PheanstalkInterface::DEFAULT_TTR,
            'pri' => PheanstalkInterface::DEFAULT_PRIORITY,
        ]);

        // Act
        $job->createJobRepresentation();
    }

    /**
     * Tests if the job stats can be retrieved.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob::getStats
     */
    public function testGetStats(): void
    {
        // Arrange
        $pheanstalkJob = new Job(1337, json_encode([
            'type' => 'awesome',
            'data' => [],
        ]));

        $job = new StoredJob($pheanstalkJob, [
            'tube' => 'default',
            'delay' => PheanstalkInterface::DEFAULT_DELAY,
            'ttr' => PheanstalkInterface::DEFAULT_TTR,
            'pri' => PheanstalkInterface::DEFAULT_PRIORITY,
        ]);

        // Act
        $result = $job->getStats();

        // Assert
        static::assertEquals([
            'tube' => 'default',
            'delay' => 0,
            'ttr' => 60,
            'pri' => 1024,
        ], $result);
    }
}
