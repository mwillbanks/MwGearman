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
     * Add a Server
     *
     * @param string $host
     * @param int $port
     * @return WorkerInterface
     */
    public function addServer($host, $port);

    /**
     * Clear Servers
     *
     * @return WorkerInterface
     */
    public function clearServers();

    /**
     * Get Servers
     *
     * @return array
     */
    public function getServers();

    /**
     * Set servers
     *
     * @param array $servers list of servers in [] = array($host, $port)
     * @return WorkerInterface
     */
    public function setServers(array $servers);

    /**
     * Open connection
     *
     * @return WorkerInterface
     */
    public function connect();

    /**
     * Close connection
     *
     * @return WorkerInterface
     */
    public function close();

    /**
     * Get timeout
     *
     * @return int
     */
    public function getTimeout();

    /**
     * Set timeout
     *
     * @param int $timeout
     * @return WorkerInterface
     */
    public function setTimeout($timeout);

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
