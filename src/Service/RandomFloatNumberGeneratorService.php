<?php

namespace App\Service;

class RandomFloatNumberGeneratorService
{
    public function getRandomFloatNumber(): float {
        return mt_rand(0, 10000) / 10000;
    }
}
