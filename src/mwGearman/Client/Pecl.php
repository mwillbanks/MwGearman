<?php
/**
 * Gearman ZF2 Module
 *
 * @category   mwGearman
 * @package    mwGearman
 * @subpackage mwGearman\Client
 */

namespace mwGearman\Client;

use mwGearman\Client;
use mwGearman\Task;
use mwGearman\Exception;

/**
 * Gearman PECL Client
 *
 * @category   mwGearman
 * @package    mwGearman
 * @subpackage mwGearman\Client
 */
class Pecl implements Client
{
    /**
     * @var bool
     */
    protected $isConnected = false;

    /**
     * @var GearmanClient
     */
    protected $client;

    /**
     * @var array
     */
    protected $servers = array();

    /**
     * @var array
     */
    protected $tasks = array();

    /**
     * @var string
     */
    protected $context;

    /**
     * @var int
     */
    protected $timeout;

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
     * Get GearmanClient
     *
     * @return \GearmanClient
     */
    public function getGearmanClient()
    {
        if (!$this->client) {
            $this->client = new \GearmanClient();
        }

        if ($this->timeout !== null) {
            $this->client->setTimeout($this->timeout);
        }
        if ($this->context !== null) {
            $this->client->setContext($this->context);
        }
        return $this->client;
    }

    /**
     * Set GearmanClient
     *
     * @param \GearmanClient $client
     * @return Pecl
     */
    public function setGearmanClient(\GearmanClient $client)
    {
        $this->client = $client;
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
            $this->getGearmanClient()->addServer($host, $port);
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
     * The GearmanClient does a lazy connection so all
     * we are doing here is simply adding the servers.
     *
     * @return Pecl
     */
    public function connect()
    {
        if (count($this->servers) == 0) {
            throw new Exception\RuntimeException('You must add servers prior to connecting');
        }

        $client = $this->getGearmanClient();
        $client->addServers(implode(',', array_keys($this->servers)));
        $this->isConnected = true;
    }

    /**
     * Close
     * The GearmanClient does a lazy connection and
     * does not have a close method; we actually have
     * to deconstruct the object for this to work.
     *
     * @return Pecl
     */
    public function close()
    {
        if ($this->client instanceof \GearmanClient) {
            $this->client = null;
        }
        $this->isConnected = false;
        return $this;
    }

    /**
     * Add Task
     * Add a task to be executed later with runTasks.
     *
     * @param \mwGearman\Task
     * @return Pecl
     */
    public function addTask(Task $task)
    {
        $this->tasks[] = $task;
    }

    /**
     * Execute a Task Now
     *
     * @return string job handle
     */
    public function doTask(Task $task)
    {
        $client = $this->getGearmanClient();
        if (!$this->isConnected) {
            $this->connect();
        }

        $method = 'do';
        $method .= ucwords($task->getPriority());
        if ($task->isBackground()) {
            $method .= 'Background';
        }
        if ($method == 'doNormalBackground') {
            $method = 'doBackground';
        }

        if ($task->hasContext()) {
            $oldContext = $client->context();
            $client->setContext($task->getContext());
        }

        $handle = $client->$method(
            $task->getFunction(),
            $task->getWorkload(),
            $task->getUnique()
        );

        if ($task->hasContext()) {
            $client->setContext($oldContext);
        }

        return $handle;
    }

    /**
     * Run Tasks
     * Execute the current tasks in the list
     *
     * @return array job handles
     */
    public function runTasks()
    {
        if (!$this->isConnected) {
            $this->connect();
        }
        $handles = array();
        foreach ($this->tasks as $task)
        {
            $method = 'addTask';
            $method .= ucwords($task->getPriority());
            if ($task->isBackground()) {
                $method .= 'Background';
            }
            if ($method == 'doNormalBackground') {
                $method = 'doBackground';
            }

            $context = ($task->hasContext()) ? $task->getContext() : $this->getContext();

            $task = $this->getGearmanClient()->$method(
                $task->getFunction(),
                $task->getWorkload(),
                $context,
                $task->getUnique()
            );
            if ($task instanceof \GearmanTask) {
                $handles[] = $task->jobHandle();
            }
        }
        $this->getGearmanClient()->runTasks();
        return $handles;
    }

    /**
     * Get Context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set Context
     *
     * @param string $context
     * @return Pecl
     */
    public function setContext($context)
    {
        if (!is_string($context)) {
            throw new Exception\InvalidArgumentException('Context must be a string');
        }
        $this->context = $context;
        if ($this->isConnected) {
            $this->getGearmanClient()->setContext($context);
        }
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
            $this->getGearmanClient()->setTimeout($timeout);
        }
        return $this;
    }

    /**
     * Ping
     *
     * @param string $workload
     * @return bool
     */
    public function ping($workload)
    {
        $client = $this->getGearmanClient();
        if (!$this->isConnected) {
            $this->connect();
        }
        if (method_exists($client, 'ping')) {
            return $client->ping($workload);
        }
        return $client->echo($workload);
    }
}
