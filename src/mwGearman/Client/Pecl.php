<?php
/**
 * Gearman ZF2 Module
 *
 * @category   mwGearman
 * @package    mwGearman
 * @subpackage mwGearman\Client
 */

namespace mwGearman\Client;

use \mwGearman\Client as Client,
    \mwGearman\Task as Task;

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
     * @return \mwGearman\Client\Pecl
     * @throws \RuntimeException
     */
    public function __construct()
    {
        if (!extension_loaded('gearman')) {
            throw new \RuntimeException('PECL gearman extension is not loaded');
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
     * @return \mwGearman\Client\Pecl
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
     * @return \mwGearman\Client\Pecl
     * @throws \IllegalArgumentException
     */
    public function addServer($host, $port = 4730)
    {
        if (!is_string($host)) {
            throw new \IllegalArgumentException('The server hostname must be a string');
        } else if (!is_numeric($port)) {
            throw new \IllegalArgumentException('The server port must be numberic');
        }

        $this->servers[$host . ':' . $port] = array($host, $port);
        return $this;
    }

    /**
     * Clear Servers
     * Note that clear servers will only apply if you have not started to
     * send tasks yet; since the PECL extension will lazily load the connection
     *
     * @return \mwGearman\Client\Pecl
     */
    public function clearServers()
    {
        $this->servers = array();
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
     * @return \mwGearman\Client\Pecl
     * @throws \IllegalArgumentException
     */
    public function setServers(array $servers)
    {
        foreach ($servers as $server) {

            if (!isset($server[0])) {
                throw new \IllegalArgumentException('The servers array must contain a host value.');
            }

            if (!isset($server[1])) {
                $server[1] = null;
            }
            $this->addServer($server[0], $server[1]);
        }
        return $this;
    }

    /**
     * Connect
     * The GearmanClient does a lazy connection so all
     * we are doing here is simply adding the servers.
     *
     * @return \mwGearman\Client\Pecl
     */
    public function connect()
    {
        if (count($this->servers) == 0) {
            throw new \RuntimeException('You must add servers prior to connecting');
        }

        $client = $this->getGearmanClient();
        $client->addServers(implode(',', array_keys($this->_servers)));
    }

    /**
     * Close
     * The GearmanClient does a lazy connection and
     * does not have a close method; we actually have
     * to deconstruct the object for this to work.
     *
     * @return \mwGearman\Client\Pecl
     */
    public function close()
    {
        if ($this->client instanceof GearmanClient) {
            $this->client = null;
        }
        return $this;
    }

    /**
     * Add Task
     * Add a task to be executed later with runTasks.
     *
     * @param \mwGearman\Task
     * @return \mwGearman\Client\Pecl
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

            $task = $this->client->$method(
                $task->getFunction(),
                $task->getWorkload(),
                $context,
                $task->getUnique()
            );
            if ($task instanceof GearmanTask) {
                $handles[] = $task->jobHandle();
            }
        }
        $this->client->runTasks();
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
     * @return \mwGearman\Client\Pecl
     */
    public function setContext($context)
    {
        if (!is_string($context)) {
            throw new \IllegalArgumentException('Context must be a string');
        }
        $this->context = $context;
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
     * @return \mwGearman\Client\Pecl
     * @throws \IllegalArgumentException
     */
    public function setTimeout($timeout)
    {
        if (!is_numeric($timeout)) {
            throw new \IllegalArgumentException('Timeout must be an integer');
        }
        $this->timeout = $timeout;
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
        if (method_exists($client, 'ping')) {
            return $client->ping($workload);
        }
        return $client->echo($workload);
    }
}
