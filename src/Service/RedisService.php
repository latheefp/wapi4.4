<?php
// src/Service/RedisService.php

// src/Service/RedisService.php

namespace App\Service;

use Predis\Client;

class RedisService
{
    protected $redis;

    public function __construct()
    {
        $config = Configure::read('Redis.default');
        $this->redis = new Client($config);
    }

    public function getRedis()
    {
        return $this->redis;
    }
}
