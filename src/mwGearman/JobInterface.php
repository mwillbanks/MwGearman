<?php
/**
 * mwGearman
 *
 * @category mwGearman
 * @package mwGearman
 */

namespace mwGearman;

/**
 * Job Interface
 *
 * @category mwGearman
 * @package mwGearman
 */
interface JobInterface
{
    /**@+
     * Notify Constants
     */
    const NOTIFY_COMPLETE  = 1;
    const NOTIFY_DATA      = 2;
    const NOTIFY_EXCEPTION = 4;
    const NOTIFY_FAIL      = 8;
    const NOTIFY_WARNING   = 16;
    /**@-*/

    /**
     * Get the function name
     *
     * @return string
     */
    public function name();

    /**
     * Get the job handle
     *
     * @return string
     */
    public function handle();

    /**
     * Get the job unique id
     *
     * @return string
     */
    public function unique();

    /**
     * Get the job workload
     *
     * @return string
     */
    public function workload();

    /**
     * Get the job size
     *
     * @return int
     */
    public function size();

    /**
     * Notify the Client
     *
     * @param int $type one of NOTIFY_*
     * @param string $data
     * @return bool
     */
    public function notify($type, $data);

    /**
     * Update Status
     *
     * @param int $numerator
     * @param int $denominator
     * @return bool
     */
    public function updateStatus($numerator, $denominator);
}
