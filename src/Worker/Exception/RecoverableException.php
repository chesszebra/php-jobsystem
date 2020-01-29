<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem\Worker\Exception;

use ChessZebra\JobSystem\Worker\RescheduleStrategy\RescheduleStrategyInterface;
use RuntimeException;
use Throwable;

/**
 * This exception is thrown when an error did occur in a worker but the job can be rescheduled.
 */
final class RecoverableException extends RuntimeException // phpcs:ignore
{
    /** @var RescheduleStrategyInterface|null */
    private $rescheduleStrategy;

    public function getRescheduleStrategy(): ?RescheduleStrategyInterface
    {
        return $this->rescheduleStrategy;
    }

    public static function createWithRescheduleStrategy(
        RescheduleStrategyInterface $rescheduleStrategy,
        ?Throwable $previous = null
    ): RecoverableException {
        $msg = 'A job failed and needs to be rescheduled.';

        $result = new static($msg, 0, $previous);

        $result->rescheduleStrategy = $rescheduleStrategy;

        return $result;
    }
}
