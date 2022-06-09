<?php

namespace App\Message;

class CollectHourlyDataFromRedis
{
    private array $entities;

    public function __construct(array $entities)
    {
        $this->entities = $entities;
    }

    public function getEntities(): array {
        return $this->entities;
    }
}
