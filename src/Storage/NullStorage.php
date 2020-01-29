<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem\Storage;

use ChessZebra\JobSystem\Job\JobInterface;

/**
 * A null storage which doesn't do anything.
 */
final class NullStorage implements StorageInterface
{
    /**
     * @inheritDoc
     */
    public function addJob(JobInterface $job): void
    {
    }

    /**
     * @inheritDoc
     */
    public function deleteJob(StoredJobInterface $storedJob): void
    {
    }

    /**
     * @inheritDoc
     */
    public function failJob(StoredJobInterface $storedJob): void
    {
    }

    /**
     * @inheritDoc
     */
    public function rescheduleJob(StoredJobInterface $storedJob, ?int $delay, ?int $priority): void
    {
    }

    /**
     * @inheritDoc
     */
    public function retrieveJob(): ?StoredJobInterface
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function pingJob(StoredJobInterface $storedJob): void
    {
    }
}
