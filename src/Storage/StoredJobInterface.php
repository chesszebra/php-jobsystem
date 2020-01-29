<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem\Storage;

use ChessZebra\JobSystem\Job\JobInterface;

/**
 * The representation of a job that was stored in the job system.
 */
interface StoredJobInterface // phpcs:ignore
{
    /**
     * Gets the id of the job in the storage system.
     */
    public function getId(): int;

    /**
     * Creates the original job representation.
     */
    public function createJobRepresentation(): JobInterface;

    /**
     * Gets a key value list with statistics about the job.
     *
     * @return mixed[]
     */
    public function getStats(): array;
}
