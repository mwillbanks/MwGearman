<?php

namespace mwGearman;

interface ClientInterface
{
    // task related
    public function addTask(TaskInterface $task);
    public function doTask(TaskInterface $task);
    public function runTasks();

    // global task callbacks (tasks additionally can have callbacks)
    /**
     * @todo implementation of callbacks
    public function clearCallbacks();
    public function getCallbacks($type = null);
    public function setCallback($type, $callback);
    public function setCallbacks(array $callbacks);
     */

    // application specific
    public function getContext();
    public function setContext($context);

    public function ping($workload);
}
