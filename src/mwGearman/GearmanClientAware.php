<?php

namespace mwGearman;

use mwGearman\Client;

interface GearmanClientAware
{
    public function setGearmanClient(Client $client);
    public function getGearmanClient();
}
