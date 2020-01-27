<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem;

use ChessZebra\JobSystem\Storage\StorageInterface;

interface ClientInterface
{
    /**
     * Gets the storage used by this client.
     *
     * @return StorageInterface
     */
    public function getStorage(): StorageInterface;

    /**
     * Runs the client.
     *
     * @return int The exit code.
     */
    public function run(): int;
}
