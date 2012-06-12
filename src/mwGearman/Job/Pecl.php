<?php
/**
 * mwGearman
 *
 * @category mwGearman
 * @package mwGearman
 */

namespace mwGearman\Job;

use mwGearman\JobInterface;
use \GearmanJob;

/**
 * Job Interface
 *
 * @category mwGearman
 * @package mwGearman
 */
class Pecl extends AbstractJob
{
    /**
     * @var \GearmanJob
     */
    protected $job;

    /**
     * Constructor
     *
     * @param \GearmanJob $job
     * @return Pecl
     */
    public function __construct(GearmanJob $job)
    {
        $this->setGearmanJob($job);
    }

    /**
     * Get gearman job
     *
     * @return \GearmanJob
     */
    public function getGearmanJob()
    {
        return $this->job;
    }

    /**
     * Set gearman job
     *
     * @param \GearmanJob $job
     * @return Pecl
     */
    public function setGearmanJob(GearmanJob $job)
    {
         $this->job = $job;
         $this->setName($job->functionName())
             ->setUnique($job->unique())
             ->setHandle($job->handle())
             ->setWorkload($job->workload())
             ->setSize($job->workloadSize());
        return $this;      
    }

    /**
     * Notify the Client
     *
     * @param int $type one of NOTIFY_*
     * @param string $data
     * @return bool
     */
    public function notify($type, $data = null)
    {
        if ($type == JobInterface::NOTIFY_COMPLETE) {
            return $this->job->sendComplete($data);
        }
        if ($type == JobInterface::NOTIFY_DATA) {
            return $this->job->sendData($data);
        }
        if ($type == JobInterface::NOTIFY_EXCEPTION) {
            return $this->job->sendException($data);
        }
        if ($type == JobInterface::NOTIFY_FAIL) {
            return $this->job->sendFail();
        }
        if ($type == JobInterface::NOTIFY_WARNING) {
            return $this->job->notifyWarning($data);
        }
        return false;
    }

    /**
     * Update Status
     *
     * @param int $numerator
     * @param int $denominator
     * @return bool
     */
    public function updateStatus($numerator, $denominator)
    {
        return $this->job->sendStatus($numerator, $denominator);
    }
}
