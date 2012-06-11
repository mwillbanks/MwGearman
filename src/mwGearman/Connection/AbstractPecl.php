<?php
/**
 * Gearman ZF2 Module
 *
 * @category   mwGearman
 * @package    mwGearman
 * @subpackage mwGearman\Connection
 */

namespace mwGearman\Connection;

use mwGearman\ConnectionInterface;
use mwGearman\Exception;

/**
 * Gearman PECL Connection
 *
 * @category   mwGearman
 * @package    mwGearman
 * @subpackage mwGearman\Connection
 */
abstract class AbstractPecl implements ConnectionInterface
{
    /**
     * @var bool
     */
    protected $isConnected = false;

    /**
     * @var array
     */
    protected $servers = array();

    /**
     * @var int
     */
    protected $timeout;

    /**
     * Add a Server
     *
     * @param string $host
     * @param int $port
     * @return AbstractPecl
     * @throws Exception\InvalidArgumentException
     */
    public function addServer($host, $port = 4730)
    {
        if (!is_string($host)) {
            throw new Exception\InvalidArgumentException('The server hostname must be a string');
        } else if (!is_numeric($port)) {
            throw new Exception\InvalidArgumentException('The server port must be numberic');
        }

        $this->servers[$host . ':' . $port] = array($host, $port);
        return $this;
    }

    /**
     * Clear Servers
     * Note that clear servers will only apply if you have not started to
     * send tasks yet; since the PECL extension will lazily load the connection
     *
     * @return AbstractPecl
     */
    public function clearServers()
    {
        $this->servers = array();
        if ($this->isConnected) {
            $this->close();
        }
        return $this;
    }

    /**
     * Get Servers
     *
     * @return array
     */
    public function getServers()
    {
        return $this->servers;
    }

    /**
     * Set Servers
     *
     * @param array $servers list of servers in [] = array($host, $port)
     * @return AbstractPecl
     * @throws Exception\InvalidArgumentException
     */
    public function setServers(array $servers)
    {
        foreach ($servers as $server) {

            if (!isset($server[0])) {
                throw new Exception\InvalidArgumentException('The servers array must contain a host value.');
            }

            if (!isset($server[1])) {
                $this->addServer($server[0]);
            } else {
                $this->addServer($server[0], $server[1]);
            }
        }
        return $this;
    }

    /**
     * Connect
     * The GearmanClient does a lazy connection so all
     * we are doing here is simply adding the servers.
     *
     * @return AbstractPecl
     */
    public function connect()
    {
        if (count($this->servers) == 0) {
            throw new Exception\RuntimeException('You must add servers prior to connecting');
        }
        return $this;
    }

    /**
     * Close
     * The GearmanClient does a lazy connection and
     * does not have a close method; we actually have
     * to deconstruct the object for this to work.
     *
     * @return AbstractPecl
     */
    public function close()
    {
        $this->isConnected = false;
        return $this;
    }

    /**
     * Get Timeout
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set Timeout
     *
     * @param int $timeout
     * @return AbstractPecl
     * @throws Exception\InvalidArgumentException
     */
    public function setTimeout($timeout)
    {
        if (!is_numeric($timeout)) {
            throw new Exception\InvalidArgumentException('Timeout must be an integer');
        }
        $this->timeout = $timeout;
        return $this;
    }
}
