<?php

namespace App\Controller;

use App\Message\AddGeneratorCurrentStatsToRedis;
use App\Message\CollectHourlyDataFromRedis;
use App\Repository\GeneratorRepository;
use App\Repository\GeneratorStatsRepository;
use App\Service\RandomFloatNumberGeneratorService;
use App\Service\ValidateDateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class GeneratorStatsRedisController extends AbstractController
{
    #[Route('/generator/stats/redis', name: 'app_generator_stats_redis')]
    public function make_measurement(
        MessageBusInterface $messageBus,
        GeneratorRepository $generatorRepository,
        RandomFloatNumberGeneratorService $randomFloatNumberGeneratorService
    ): Void
    {
        $generator = $generatorRepository->findAll();
        while(True) {
            for($j = 0; $j < 2; $j++) {
                for ($i = 0; $i < count($generator); $i++) {
                    $message = new AddGeneratorCurrentStatsToRedis($generator[$i], $randomFloatNumberGeneratorService->getRandomFloatNumber());
                    $messageBus->dispatch($message);
                }
                usleep(500000);
            }
        }
    }
}
