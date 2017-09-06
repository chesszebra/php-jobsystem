<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem\Job;

/**
 * A default implementation of a job.
 */
final class Job implements JobInterface
{
    /**
     * @var string
     */
    private $workerName;

    /**
     * @var array
     */
    private $workerParams;

    /**
     * @var string
     */
    private $queueName;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var int|null
     */
    private $delay;

    /**
     * @var int|null
     */
    private $timeToRun;

    /**
     * Initializes a new instance of this class.
     *
     * @param string $workerName The name of the worker to run.
     * @param array $workerParams The parameters to pass to the worker.
     * @param null|string $queueName The name of the queue to store the job in.
     */
    public function __construct(string $workerName, array $workerParams, ?string $queueName = null)
    {
        $this->workerName = $workerName;
        $this->workerParams = $workerParams;
        $this->queueName = $queueName;
        $this->priority = 0;
    }

    /**
     * Gets the name of the worker to run.
     *
     * @return string
     */
    public function getWorkerName(): string
    {
        return $this->workerName;
    }

    /**
     * Gets an array with parameters that should be passed to the worker.
     *
     * @return array
     */
    public function getWorkerParams(): array
    {
        return $this->workerParams;
    }

    /**
     * Gets the name of the queue to store the job in.
     *
     * @return null|string
     */
    public function getQueueName(): ?string
    {
        return $this->queueName;
    }

    /**
     * Gets the priority of the job.
     * The higher this number is, the earlier the job is executed.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Sets the priority of the job.
     *
     * @param int $priority The priority to set.
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * Gets the delay in seconds which indicates when the job is started.
     *
     * @return int|null
     */
    public function getDelay(): ?int
    {
        return $this->delay;
    }

    /**
     * Sets the delay of the job.
     *
     * @param int|null $delay The delay to set.
     */
    public function setDelay(?int $delay): void
    {
        $this->delay = $delay;
    }

    /**
     * Gets the time to run in seconds which defines how long the job can run.
     *
     * @return int|null
     */
    public function getTimeToRun(): ?int
    {
        return $this->timeToRun;
    }

    /**
     * Sets the time to run in seconds.
     *
     * @param int|null $timeToRun The time in seconds to set.
     */
    public function setTimeToRun(?int $timeToRun): void
    {
        $this->timeToRun = $timeToRun;
    }
}
