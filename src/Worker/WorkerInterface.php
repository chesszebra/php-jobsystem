<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem\Worker;

use ChessZebra\JobSystem\Context\ContextInterface;

interface WorkerInterface
{
    /**
     * Runs the worker.
     *
     * @param ContextInterface $context The context used to pass data to the worker.
     * @return void
     */
    public function run(ContextInterface $context): void;
}
