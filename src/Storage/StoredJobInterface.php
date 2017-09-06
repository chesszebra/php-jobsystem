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
 * The representation of a job that was stored in the job system.
 */
interface StoredJobInterface
{
    /**
     * Gets the id of the job in the storage system.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Creates the original job representation.
     *
     * @return JobInterface
     */
    public function createJobRepresentation(): JobInterface;
}
