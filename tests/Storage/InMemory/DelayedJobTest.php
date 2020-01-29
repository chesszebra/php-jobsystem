<?php

declare(strict_types=1);

namespace ChessZebra\JobSystem\Storage\InMemory;

use ChessZebra\JobSystem\Job\Job;
use PHPUnit\Framework\TestCase;

final class DelayedJobTest extends TestCase
{
    /**
     * Tests if the isReady method returns true when the job is actually ready.
     *
     * @covers \ChessZebra\JobSystem\Storage\InMemory\DelayedJob::__construct
     * @covers \ChessZebra\JobSystem\Storage\InMemory\DelayedJob::isReady
     */
    public function testReadyToRumble(): void
    {
        // Arrange
        $job = new Job('', []);
        $storedJob = new StoredJob($job, 1);
        $delayedJob = new DelayedJob($storedJob, null, null);

        // Act
        $result = $delayedJob->isReady();

        // Assert
        static::assertTrue($result);
    }

    /**
     * Tests if the isReady method returns falsse when the job is no ready yet.
     *
     * @covers \ChessZebra\JobSystem\Storage\InMemory\DelayedJob::__construct
     * @covers \ChessZebra\JobSystem\Storage\InMemory\DelayedJob::isReady
     */
    public function testWaitForReady(): void
    {
        // Arrange
        $job = new Job('', []);
        $storedJob = new StoredJob($job, 1);
        $delayedJob = new DelayedJob($storedJob, 1, null);

        // Act
        $result = $delayedJob->isReady();

        // Assert
        static::assertFalse($result);
    }
}
