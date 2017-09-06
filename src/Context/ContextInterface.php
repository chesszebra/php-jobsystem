<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem\Context;

use ChessZebra\JobSystem\Job\JobInterface;
use Psr\Log\LoggerInterface;

/**
 * The context is passed to each worker when they should run.
 */
interface ContextInterface
{
    /**
     * Gets the logger which can be used to write to the output stream.
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;

    /**
     * Adds a job to the storage to be executed.
     *
     * @param JobInterface $job The job to add.
     * @return void
     */
    public function addJob(JobInterface $job): void;

    /**
     * Keeps the worker alive by resetting the time to run counter.
     *
     * @return void
     */
    public function pingJob(): void;

    /**
     * Gets the value of a parameter or returns the default value when the parameter does not exists.
     *
     * @param string $name The name of the parameter to get.
     * @param mixed $defaultValue The default value to return.
     * @return mixed
     */
    public function getParam(string $name, $defaultValue = null);
}
