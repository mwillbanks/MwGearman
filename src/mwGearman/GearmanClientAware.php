<?php

namespace mwGearman;
use mwGearman\Client\Pecl;

interface GearmanClientAware
{
    public function setGearmanClient(Pecl $client);
    public function getGearmanClient();
}
