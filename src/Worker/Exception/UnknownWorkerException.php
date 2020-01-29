<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem\Worker\Exception;

use RuntimeException;

/**
 * This exception is thrown when a job is started with an unknown worker name.
 */
final class UnknownWorkerException extends RuntimeException // phpcs:ignore
{
}
