<?php
/**
 * mwGearman
 *
 * @category mwGearman
 * @package mwGearman
 */

namespace mwGearman;

/**
 * Worker Interface
 *
 * @category mwGearman
 * @package mwGearman
 */
interface WorkerInterface
{
    /**
     * Register a function
     *
     * @param string $func
     * @return WorkerInterface
     */
    public function register($func);

    /**
     * Unregister a function
     *
     * @param string $func
     * @return WorkerInterface
     */
    public function unregister($func);

    /**
     * Test job server response
     *
     * @return bool
     */
    public function write($workload);

    /**
     * Wait for activity from one of the job servers
     *
     * @return bool
     */
    public function wait();

    /**
     * Wait for work and perform jobs
     *
     * @return bool
     */
    public function work();

    /**
     * Get Error
     *
     * @return array|false 0 => number, 1 => message
     */
    public function getError();
}
