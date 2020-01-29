<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem\Worker;

use ChessZebra\JobSystem\Context\ContextInterface;

interface WorkerInterface // phpcs:ignore
{
    /**
     * Runs the worker.
     *
     * @param ContextInterface $context The context used to pass data to the worker.
     */
    public function run(ContextInterface $context): void;
}
