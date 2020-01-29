<?php

declare(strict_types=1);

/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 */

namespace ChessZebra\JobSystem\Storage\Pheanstalk;

use ChessZebra\JobSystem\Job\Job;
use ChessZebra\JobSystem\Job\JobInterface;
use ChessZebra\JobSystem\Storage\StoredJobInterface;
use Pheanstalk\Job as PheanstalkJob;
use Pheanstalk\PheanstalkInterface;
use RuntimeException;
use function array_key_exists;
use function json_decode;
use function sprintf;

final class StoredJob implements StoredJobInterface
{
    /** @var PheanstalkJob */
    private $job;

    /** @var mixed[] */
    private $stats;

    /**
     * Initializes a new instance of this class.
     *
     * @param PheanstalkJob $job   The Pheanstalk job.
     * @param mixed[] $stats The statistics of the job.
     */
    public function __construct(PheanstalkJob $job, array $stats)
    {
        $this->job = $job;
        $this->stats = $stats;
    }

    /**
     * Gets the Pheanstalk job.
     */
    public function getJob(): PheanstalkJob
    {
        return $this->job;
    }

    /**
     * Gets the id of the job in the storage system.
     */
    public function getId(): int
    {
        return $this->job->getId();
    }

    /**
     * Creates the original job representation.
     *
     * @throws RuntimeException Thrown when invalid job data was provided.
     */
    public function createJobRepresentation(): JobInterface
    {
        $json = json_decode($this->job->getData(), true);

        if (!$json) {
            throw new RuntimeException(sprintf(
                'Invalid JSON, got "%s"',
                $this->job->getData()
            ));
        }

        if (!array_key_exists('type', $json)) {
            throw new RuntimeException(sprintf(
                'Missing "type" field in "%s"',
                $this->job->getData()
            ));
        }

        if (!array_key_exists('data', $json)) {
            throw new RuntimeException(sprintf(
                'Missing "data" field in "%s"',
                $this->job->getData()
            ));
        }

        $job = new Job($json['type'], $json['data'], $this->stats['tube']);
        $job->setDelay((int)$this->stats['delay']);
        $job->setTimeToRun((int)$this->stats['ttr']);
        $job->setPriority((int)$this->stats['pri'] - PheanstalkInterface::DEFAULT_PRIORITY);

        return $job;
    }

    /**
     * Gets a key value list with statistics about the job.
     *
     * @return mixed[]
     */
    public function getStats(): array
    {
        return $this->stats;
    }
}
