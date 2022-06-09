<?php

namespace App\Redis;

use Redis;

class RedisClass
{
    private Redis $redis;

    public function __construct(){
        $this->redis = new Redis([
            'host' => 'redis',
            'port' => '6379',
            'auth' => ['pass'=>'Piotrek120']
        ]);
    }

    public function getRedis(){
        return $this->redis;
    }

}
