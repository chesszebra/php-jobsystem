<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem;

use ChessZebra\JobSystem\Storage\StorageInterface;

interface ClientInterface // phpcs:ignore
{
    /**
     * Gets the storage used by this client.
     */
    public function getStorage(): StorageInterface;

    /**
     * Runs the client.
     *
     * @return int The exit code.
     */
    public function run(): int;
}
