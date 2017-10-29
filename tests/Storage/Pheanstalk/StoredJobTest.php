<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem\Storage\Pheanstalk;

use ChessZebra\JobSystem\Job\JobInterface;
use Pheanstalk\PheanstalkInterface;
use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHPUnit\Framework\TestCase;

final class StoredJobTest extends TestCase
{
    /**
     * Tests if the job can be retrieved.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob::__construct
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob::getJob
     */
    public function testGetJob()
    {
        // Arrange
        $pheanstalkJob = new \Pheanstalk\Job(1337, '');
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
    public function testGetId()
    {
        // Arrange
        $pheanstalkJob = new \Pheanstalk\Job(1337, '');
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
    public function testCreateJobRepresentation()
    {
        // Arrange
        $pheanstalkJob = new \Pheanstalk\Job(1337, json_encode([
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
     * @expectedException RuntimeException
     * @expectedExceptionMessage Invalid JSON, got ""
     */
    public function testCreateJobRepresentationWithoutData()
    {
        // Arrange
        $pheanstalkJob = new \Pheanstalk\Job(1337, '');
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
     * Tests if the type member is present in the json data.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob::createJobRepresentation
     * @expectedException RuntimeException
     * @expectedExceptionMessage Missing "type" field in "{"test": "test"}"
     */
    public function testCreateJobRepresentationWithoutTypeMember()
    {
        // Arrange
        $pheanstalkJob = new \Pheanstalk\Job(1337, '{"test": "test"}');
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
     * Tests if the data member is present in the json data.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob::createJobRepresentation
     * @expectedException RuntimeException
     * @expectedExceptionMessage Missing "data" field in "{"type": "test"}"
     */
    public function testCreateJobRepresentationWithoutDataMember()
    {
        // Arrange
        $pheanstalkJob = new \Pheanstalk\Job(1337, '{"type": "test"}');
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
     * Tests if the job stats can be retrieved.
     *
     * @covers \ChessZebra\JobSystem\Storage\Pheanstalk\StoredJob::getStats
     */
    public function testGetStats()
    {
        // Arrange
        $pheanstalkJob = new \Pheanstalk\Job(1337, json_encode([
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
