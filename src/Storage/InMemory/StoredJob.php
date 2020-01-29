<?php

declare(strict_types=1);

namespace ChessZebra\JobSystem\Storage\InMemory;

use ChessZebra\JobSystem\Job\JobInterface;
use ChessZebra\JobSystem\Storage\StoredJobInterface;

final class StoredJob implements StoredJobInterface
{
    /** @var JobInterface */
    private $job;

    /** @var int */
    private $id;

    public function __construct(JobInterface $job, int $id)
    {
        $this->job = $job;
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function createJobRepresentation(): JobInterface
    {
        return $this->job;
    }

    /**
     * @return mixed[]
     */
    public function getStats(): array
    {
        return [];
    }
}
