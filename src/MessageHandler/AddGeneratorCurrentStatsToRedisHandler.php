<?php

namespace App\MessageHandler;

use App\Message\AddGeneratorCurrentStatsToRedis;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddGeneratorCurrentStatsToRedisHandler implements MessageHandlerInterface
{
    private $redis;

    public function __construct()
    {
        $this->redis = RedisAdapter::createConnection($_ENV['REDIS_URL']);
    }

    public function __invoke(AddGeneratorCurrentStatsToRedis $addGeneratorCurrentStatsToRedis)
    {
        $uid = $addGeneratorCurrentStatsToRedis->getUid();
        $this->redis->hSet(
            'generator:'.$uid,
            'generator_id', $addGeneratorCurrentStatsToRedis->getGenerator()->getId()
        );
        $this->redis->hSet(
            'generator:'.$uid,
            'power', $addGeneratorCurrentStatsToRedis->getGeneratorPower()
        );
        $this->redis->hSet(
            'generator:'.$uid,
            'date', $addGeneratorCurrentStatsToRedis->getTime()
        );
        $this->redis->expire('generator:'.$uid, 7200);
    }
}
