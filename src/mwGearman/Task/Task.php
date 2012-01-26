<?php
/**
 * MW Gearman
 *
 * @category   mwGearman
 * @package    mwGearman
 * @subpackage mwGearman\Task
 */

namespace \mwGearman\Task;

/**
 * Gearman Task
 * Class implements the definition of a task.
 *
 * @category   mwGearman
 * @package    mwGearman
 * @subpacakge Task
 */
class Task
{
    /**
     * @var bool
     */
    protected $background;

    /**
     * @var string
     */
    protected $context;

    /**
     * @var string
     */
    protected $function;

    /**
     * @var string
     */
    protected $priority = 'normal';

    /**
     * Is Background Task
     *
     * @return bool
     */
    public function isBackground()
    {
        return $this->background;
    }

    /**
     * Set Background Task
     *
     * @param bool $isBg
     * @return \mwGearman\Task
     */
    public function setBackground($isBg)
    {
        $this->background = (bool) $isBg;
        return $this;
    }

    /**
     * Get Context
     * The context only applies to tasks that
     * run in a batch.
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Has Context
     *
     * @return bool
     */
    public function hasContext()
    {
        return !empty($this->context);
    }

    /**
     * Set Context
     *
     * @param string $context;
     * @return \mwGearman\Task
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Get Function
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Set Function
     *
     * @param string $func
     * @return \mwGearman\Task
     * @throws \IllegalArgumentException
     */
    public function setFunction($func)
    {
        if (empty($func) || !is_string($func)) {
            throw new \IllegalArgumentException('Function must be a valid string');
        }
        $this->function = $func;
        return $this;
    }

    /**
     * Get Priority
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set Priority
     *
     * @string $priority
     * @return \mwGearman\Task
     * @throws \IllegalArgumentException
     */
    public function setPriority($priority)
    {
        $allowed = array('high', 'normal', 'low');
        if (!in_array($priority, $allowed)) {
            throw new \IllegalArgumentException('Priority must be one of high, normal or low');
        }
        $this->priority = $priority;
        return $this;
    }

    /**
     * Get Unique
     *
     * @return string
     */
    public function getUnique()
    {
        return $this->unique;
    }

    /**
     * Set Unique
     *
     * @param string $uniq
     * @return \mwGearman\Task
     * @throws \IllegalArgumentException
     */
    public function setUnique($uniq)
    {
        if (!is_string($uniq)) {
            throw new \IllegalArgumentException('Unique must be a string');
        }
        $this->unique = $uniq;
        return $this;
    }

    /**
     * Get Workload
     *
     * @return string
     */
    public function getWorkload()
    {
        return $this->workload;
    }

    /**
     * Set Workload
     *
     * @param string $workload
     * @return \mwGearman\Task
     * @throws \IllegalArgumentException
     */
    public function setWorkload($workload)
    {
        if (empty($workload) || !is_string($worload)) {
            throw new \IllegalArgumentException('Workload must be non-null or a string');
        }
        return $this;
    }
}
