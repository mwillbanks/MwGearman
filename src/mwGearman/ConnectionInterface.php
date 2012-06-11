<?php

namespace mwGearman;

interface ConnectionInterface {
    /**
     * Add a server
     *
     * @param string $host
     * @param int $port
     * @return ConnectionInterface
     */
    public function addServer($host, $port);

    /**
     * Clear servers
     *
     * @return ConnectionInterface
     */
    public function clearServers();

    /**
     * Get servers
     *
     * @return array
     */
    public function getServers();

    /**
     * Set servers
     *
     * @param array $servers list of servers in [] = array($host, $port)
     * @return ConnectionInterface
     */
    public function setServers(array $servers);

    /**
     * Open connection
     *
     * @return ConnectionInterface
     */
    public function connect();

    /**
     * Close connection
     *
     * @return ConnectionInterface
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
     * @return ConnectionInterface
     */
    public function setTimeout($timeout);
}
