<?php
/**
 * mwGearman
 *
 * @category   mwGearman
 * @package    mwGearman
 * @subpackage mwGearman\Worker
 */

namespace mwGearman\Worker;

use mwGearman\Connection\AbstractPecl;
use mwGearman\Job\Pecl as PeclJob;
use mwGearman\WorkerInterface;
use mwGearman\Exception;
use GearmanJob;

/**
 * PECL Gearman Worker
 * Class implements the definition of a worker through the usage
 * of the PECL GearmanWorker.
 *
 * @category   mwGearman
 * @package    mwGearman
 * @subpackage mwGearman\Worker
 */
class Pecl extends AbstractPecl implements WorkerInterface
{
    /**
     * @var \GearmanWorker
     */
    protected $worker;

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
                $this->register($f, array($this, 'proxify'));
            }
        }

        return $this->worker;
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
        parent::addServer($host, $port);
        if ($this->isConnected) {
            $this->getGearmanWorker()->addServer($host, $port);
        }
        return $this;
    }

    /**
     * Open connection
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
        parent::connect();

        $client = $this->getGearmanWorker();
        $client->addServers(implode(',', array_keys($this->servers)));
        foreach ($this->functions as $f) {
            $client->register($f,0);
        }
        $this->isConnected = true;
        return $this;
    }

    /**
     * Close connection
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
        return parent::close();
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
        parent::setTimeout($timeout);
        if ($this->isConnected) {
            $this->getGearmanWorker()->timeout($timeout);
        }
        return $this;
    }

    /**
     * Register a function
     *
     * @param string $name
     * @param string|array $func
     * @return Pecl
     * @throws Exception\InvalidArgumentException
     */
    public function register($name, $func)
    {
        if (!is_string($func) && !is_array($func)) {
            throw new Exception\InvalidArgumentException('Function to register must be a string');
        } else if (!is_callable($func)) {
            throw new Exception\InvalidArgumentException('Function `%s` is not callable');
        }
        if (!isset($this->functions[$name])) {
            $this->functions[$name] = $func;
            if ($this->isConnected) {
                $this->getGearmanWorker()->register($name,0);
                $this->getGearmanWorker()->addFunction($name, array($this, 'proxify'));
            }
        }
        return $this;
    }

    /**
     * Unregister a function
     *
     * @param string $name
     * @return Pecl
     */
    public function unregister($name)
    {
        if (isset($this->functions[$name])) {
            $func = $this->functions[$name];
            unset($this->functions[$name]);
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
     * Proxify
     * This method is the glue for work to be passed
     * to the function requested
     *
     * @param \GearmanJob $job
     * @return mixed
     */
    public function proxify(GearmanJob $job)
    {
        $job = new PeclJob($job);
        if ($callback = $this->functions[$job->name()]) {
            return call_user_func($callback, $job);
        }
        return false;
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
