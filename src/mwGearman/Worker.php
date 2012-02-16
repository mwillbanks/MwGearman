<?php

namespace mwGearman;

interface Worker
{
    // connection related
    // @todo move connection related items into their own connection class
    // connection related
    public function addServer($host, $port);
    public function clearServers();
    public function getServers();
    public function setServers(array $servers);
    public function connect();
    public function close();

    public function getTimeout();
    public function setTimeout($timeout);

    public function register($func);
    public function unregister($func);
    public function write($workload);
    public function wait();
    public function work();
    public function getError();
}
