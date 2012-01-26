<?php

namespace \mwGearman;

interface Task
{
    public function getPriority();
    public function setPriority($priority);
    public function isBackground();
    public function setBackground($isBg);
    public function getFunction();
    public function setFunction($func);
    public function getWorkload();
    public function setWorkload($workload);
    public function getUnique();
    public function setUnique($uniq);
    public function getContext();
    public function hasContext();
    public function setContext($context);
}
