<?php

namespace mwGearman\Worker;

use mwGearman\Worker;
use mwGearman\Exception;

class Pecl implements Worker
{

    /**
     * @var bool
     */
    protected $isConnected = false;

    /**
     * @var GearmanWorker
     */
    protected $worker;

    /**
     * @var array
     */
    protected $servers = array();

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @var array
     */
    protected $functions = array();

    /**
     * Constructor
     *
     * @return Pecl
     * @throws Exception\RuntimeException
     */
    public function __construct()
    {
        if (!extension_loaded('gearman')) {
            throw new Exception\RuntimeException('PECL gearman extension is not loaded');
        }
    }

    /**
     * Get GearmanWorker
     *
     * @return \GearmanWorker
     */
    public function getGearmanWorker()
    {
        if (!$this->worker) {
            $this->worker = new \GearmanWorker();
        }

        if ($this->timeout !== null) {
            $this->worker->setTimeout($this->timeout);
        }

        if ($this->functions !== null) {
            foreach ($this->functions as $f) {
                $this->worker->register($f);
            }
        }
    }

    /**
     * Set GearmanWorker
     *
     * @param \GearmanWorker $worker
     * @return Pecl
     */
    public function setGearmanWorker(\GearmanWorker $worker)
    {
        $this->worker = $worker;
        return $this;
    }

    /**
     * Add a Server
     *
     * @param string $host
     * @param int $port
     * @return Pecl
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

        if ($this->isConnected) {
            $this->getGearmanWorker()->addServer($host, $port);
        }

        return $this;
    }

    /**
     * Clear Servers
     * Note that clear servers will only apply if you have not started to
     * send tasks yet; since the PECL extension will lazily load the connection
     *
     * @return Pecl
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
     * @return Pecl
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
     * The GearmanWorker does a lazy connection so all
     * we are doing here is simply adding the servers.
     *
     * @return Pecl
     * @throws Exception\RuntimeException
     */
    public function connect()
    {
        if ($this->isConnected) {
            return $this;
        }
        if (count($this->servers) == 0) {
            throw new Exception\RuntimeException('You must add servers prior to connecting');
        }

        $client = $this->getGearmanWorker();
        $client->addServers(implode(',', array_keys($this->servers)));
        foreach ($this->functions as $f) {
            $client->register($f);
        }
        $this->isConnected = true;
        return $this;
    }

    /**
     * Close
     * The GearmanWorker does a lazy connection and
     * does not have a close method; we actually have
     * to deconstruct the object for this to work.
     *
     * @return Pecl
     */
    public function close()
    {
        if ($this->worker instanceof \GearmanWorker) {
            $this->worker = null;
        }
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
     * @return Pecl
     * @throws Exception\InvalidArgumentException
     */
    public function setTimeout($timeout)
    {
        if (!is_numeric($timeout)) {
            throw new Exception\InvalidArgumentException('Timeout must be an integer');
        }
        $this->timeout = $timeout;
        if ($this->isConnected) {
            $this->getGearmanWorker()->timeout($timeout);
        }
        return $this;
    }

    /**
     * Register a function
     *
     * @param string $func
     * @return Pecl
     * @throws Exception\InvalidArgumentException
     */
    public function register($func)
    {
        if (!is_string($func)) {
            throw new Exception\InvalidArgumentException('Function to register must be a string');
        }
        if (!in_array($func, $this->functions)) {
            $this->functions[] = $func;
            if ($this->isConnected) {
                $this->getGearmanWorker()->register($func);
            }
        }
        return $this;
    }

    /**
     * Unregister a function
     *
     * @param string $func
     * @return mwGearman\Worker\Pecl
     */
    public function unregister($func)
    {
        $key = array_search($func, $this->functions);
        if ($key !== false) {
            unset($this->functions[$key]);
            if ($this->isConnected) {
                $this->getGearmanWorker()->unregister($func);
            }
        }
        return $this;
    }

    /**
     * Test job server response
     *
     * @return bool
     */
    public function write($workload)
    {
        if (!$this->isConnected) {
            $this->connect();
        }
        return $this->getGearmanWorker()->echo();
    }

    /**
     * Wait for activity from one of the job servers
     *
     * @return bool
     */
    public function wait()
    {
        if (!$this->isConnected) {
            $this->connect();
        }
        return @$this->getGearmanWorker()->wait();
    }

    /**
     * Wait for work and perform jobs
     *
     * @return bool
     */
    public function work()
    {
        if (!$this->isConnected) {
            $this->connect();
        }
        return @$this->getGearmanWorker()->work();
    }


    /**
     * Get Error
     *
     * @return array|false 0 => number, 1 => message
     */
    public function getError()
    {
        if (!$this->isConnected) {
            return false;
        }

        $errno = $this->getGearmanWorker()->getErrno();
        if ($errno) {
            return array(
                $errno,
                $this->getGearmanWorker()->error(),
            );
        }

        return false;
    }
}
