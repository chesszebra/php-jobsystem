<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem\Worker\Exception;

use RuntimeException;

/**
 * This exception is thrown when an error did occur in a worker but the job can be rescheduled.
 */
final class RecoverableException extends RuntimeException
{
}
