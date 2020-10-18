<?php

declare(strict_types=1);

namespace ChessZebra\JobSystem\Storage\InMemory;

use ChessZebra\JobSystem\Storage\StoredJobInterface;

use function time;

final class DelayedJob
{
    /** @var int */
    private $createdAt;

    /** @var StoredJobInterface */
    private $storedJob;

    /** @var int|null */
    private $delay;

    /** @var int|null */
    private $priority;

    public function __construct(StoredJobInterface $storedJob, ?int $delay, ?int $priority)
    {
        $this->createdAt = time();
        $this->storedJob = $storedJob;
        $this->delay = $delay;
        $this->priority = $priority;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getStoredJob(): StoredJobInterface
    {
        return $this->storedJob;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDelay(): ?int
    {
        return $this->delay;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function isReady(): bool
    {
        $nextTime = $this->createdAt;

        if ($this->delay !== null) {
            $nextTime += $this->delay;
        }

        return $nextTime <= time();
    }
}
