<?php

namespace mwGearman;
use mwGearman\Worker;

interface GearmanWorkerAware
{
    public function setGearmanWorker(Worker $worker);
    public function getGearmanWorker();
}
