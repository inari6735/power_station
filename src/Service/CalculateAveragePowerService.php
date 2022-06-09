<?php

namespace App\Service;


class CalculateAveragePowerService
{
    public function calculateAveragePower(array $entities): ?float {
        $sum = 0;
        foreach ($entities as $entity) {
            $sum += (float)$entity["power"];
        }
        return ($sum / count($entities));
    }
}
