<?php
/**
 * mwGearman
 *
 * @category mwGearman
 * @package mwGearman
 */

namespace mwGearman;

/**
 * Task Interface
 *
 * @category mwGearman
 * @package mwGearman
 */
interface TaskInterface
{
    /**
     * Get Priority
     *
     * @return string
     */
    public function getPriority();

    /**
     * Set Priority
     *
     * @param string $priority
     * @return TaskInterface
     */
    public function setPriority($priority);

    /**
     * Is Background Task
     *
     * @return bool
     */
    public function isBackground();

    /**
     * Set Background
     *
     * @param bool $isBg
     * @return TaskInterface
     */
    public function setBackground($isBg);

    /**
     * Get Function
     *
     * @return string
     */
    public function getFunction();

    /**
     * Set Function
     *
     * @param string $func
     * @return TaskInterface
     * @throws Exception\InvalidArgumentException
     */
    public function setFunction($func);

    /**
     * Get Workload
     *
     * @return string
     */
    public function getWorkload();

    /**
     * Set Workload
     *
     * @param string $workload
     * @return TaskInterface
     * @throws Exception\InvalidArgumentException
     */
    public function setWorkload($workload);

    /**
     * Get Unique
     *
     * @return scalar
     */
    public function getUnique();

    /**
     * Set Unique
     *
     * @param scalar $uniq
     * @return TaskInterface
     * @throws Exception\InvalidArgumentException
     */
    public function setUnique($uniq);

    /**
     * Get Context
     *
     * @return string
     */
    public function getContext();

    /**
     * Has Context
     *
     * @return bool
     */
    public function hasContext();

    /**
     * Set Context
     *
     * @param string $context
     * @return TaskInterface
     * @throws Exception\InvalidArgumentException
     */
    public function setContext($context);
}
