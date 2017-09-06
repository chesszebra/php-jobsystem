<?php declare(strict_types=1);
/**
 * PHP Job System (https://chesszebra.com)
 *
 * @link https://github.com/chesszebra/php-jobsystem for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/php-jobsystem/blob/master/LICENSE.md MIT
 */

namespace ChessZebra\JobSystem\Storage\Pheanstalk;

use ChessZebra\JobSystem\Job\Job;
use ChessZebra\JobSystem\Job\JobInterface;
use ChessZebra\JobSystem\Storage\StoredJobInterface;
use Pheanstalk\Job as PheanstalkJob;
use Pheanstalk\PheanstalkInterface;

final class StoredJob implements StoredJobInterface
{
    /**
     * @var PheanstalkJob
     */
    private $job;

    /**
     * @var array
     */
    private $stats;

    /**
     * Initializes a new instance of this class.
     *
     * @param PheanstalkJob $job The Pheanstalk job.
     * @param array $stats The statistics of the job.
     */
    public function __construct(PheanstalkJob $job, array $stats)
    {
        $this->job = $job;
        $this->stats = $stats;
    }

    /**
     * Gets the Pheanstalk job.
     *
     * @return PheanstalkJob
     */
    public function getJob(): PheanstalkJob
    {
        return $this->job;
    }

    /**
     * Gets the id of the job in the storage system.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->job->getId();
    }

    /**
     * Creates the original job representation.
     *
     * @return JobInterface
     */
    public function createJobRepresentation(): JobInterface
    {
        $json = json_decode($this->job->getData(), true);

        $job = new Job($json['type'], $json['data'], $this->stats['tube']);
        $job->setDelay((int)$this->stats['delay']);
        $job->setTimeToRun((int)$this->stats['ttr']);
        $job->setPriority((int)$this->stats['pri'] - PheanstalkInterface::DEFAULT_PRIORITY);

        return $job;
    }
}
