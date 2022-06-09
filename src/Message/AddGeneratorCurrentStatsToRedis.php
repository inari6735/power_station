<?php

namespace App\Message;

use App\Entity\Generator;
use Symfony\Component\Uid\Uuid;

class AddGeneratorCurrentStatsToRedis
{
    private Generator $generator;
    private float $generatorPower;
    private Uuid $uid;

    public function __construct(Generator $generator, float $generatorPower)
    {
        $this->generator = $generator;
        $this->generatorPower = $generatorPower;
        $this->uid = Uuid::v4();
    }

    public function getGenerator(): Generator
    {
        return $this->generator;
    }

    public function getGeneratorPower(): float
    {
        return $this->generatorPower;
    }

    public function getTime(): string
    {
        return \DateTime::createFromFormat('U.u', microtime(TRUE))->format('Y-m-d H:i:s.u');
    }

    public function getUid(): Uuid
    {
        return $this->uid;
    }
}
