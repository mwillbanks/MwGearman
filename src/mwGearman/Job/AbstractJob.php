<?php
/**
 * mwGearman
 *
 * @category mwGearman
 * @package mwGearman
 */

namespace mwGearman\Job;

use mwGearman\JobInterface;

/**
 * Job Interface
 *
 * @category mwGearman
 * @package mwGearman
 */
abstract class AbstractJob implements JobInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $handle;

    /**
     * @var string
     */
    protected $unique;

    /**
     * @var string
     */
    protected $workload;

    /**
     * @var int
     */
    protected $size;

    /**
     * Get the function name
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Set the function name
     *
     * @param string $name
     * @return JobInterface
     */
    protected function setName($name) {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * Get the job handle
     *
     * @return string
     */
    public function handle()
    {
        return $this->handle;
    }

    /**
     * Set the job handle
     *
     * @params scalar $handle
     * @return JobInterface
     */
    protected function setHandle($handle)
    {
        $this->handle = (string) $handle;
        return $this;
    }

    /**
     * Get the job unique id
     *
     * @return string
     */
    public function unique()
    {
        return $this->unique;
    }

    /**
     * Set the unique id
     *
     * @param scalar $id
     * @return JobInterface
     */
    protected function setUnique($id)
    {
        $this->unique = $id;
        return $this;
    }

    /**
     * Get the job workload
     *
     * @return string
     */
    public function workload()
    {
        return $this->workload;
    }

    /**
     * Set the job workload
     *
     * @param string $workload
     * @return JobInterface
     */
    protected function setWorkload($workload)
    {
        $this->workload = $workload;
        return $this;
    }

    /**
     * Get the job size
     *
     * @return int
     */
    public function size()
    {
        return $this->size;
    }

    /**
     * Set the job size
     *
     * @param int $size
     * @return JobInterface
     */
    protected function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Notify the Client
     *
     * @param int $type one of NOTIFY_*
     * @param string $data
     * @return bool
     */
    abstract public function notify($type, $data);

    /**
     * Update Status
     *
     * @param int $numerator
     * @param int $denominator
     * @return bool
     */
    abstract public function updateStatus($numerator, $denominator);
}
