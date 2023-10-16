<?php
// src/Utility/RedisUtility.php

namespace App\Utility;

use Cake\Core\Configure;
use Predis\Client;

class RedisUtility
{
    protected $redis;

    public function __construct($connection = 'default')
    {
        $config = Configure::read("Redis.$connection");
        $this->redis = new Client($config);
    }

    public function getRedis()
    {
        return $this->redis;
    }
}
