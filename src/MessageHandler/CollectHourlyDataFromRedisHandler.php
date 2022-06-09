<?php

namespace App\MessageHandler;

use App\Entity\GeneratorStats;
use App\Message\CollectHourlyDataFromRedis;
use App\Repository\GeneratorRepository;
use App\Service\CalculateAveragePowerService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CollectHourlyDataFromRedisHandler implements MessageHandlerInterface
{
    private $redis;
    private GeneratorRepository $generatorRepository;
    private CalculateAveragePowerService $addHourlyDataToDatabaseService;
    private EntityManagerInterface $entityManager;

    public function __construct(
        GeneratorRepository          $generatorRepository,
        CalculateAveragePowerService $addHourlyDataToDatabaseService,
        EntityManagerInterface       $entityManager
    )
    {
        $this->redis = RedisAdapter::createConnection($_ENV['REDIS_URL']);
        $this->generatorRepository = $generatorRepository;
        $this->addHourlyDataToDatabaseService = $addHourlyDataToDatabaseService;
        $this->entityManager = $entityManager;
    }

    public function __invoke(CollectHourlyDataFromRedis $collectHourlyDataFromRedis)
    {
        $entities = $collectHourlyDataFromRedis->getEntities();
        $gen1 = $gen2 = $gen3 = $gen4 = $gen5 = $gen6 = $gen7 = $gen8 = $gen9 = $gen10 = [];
        $gen11 = $gen12 = $gen13 = $gen14 = $gen15 = $gen16 = $gen17 = $gen18 = $gen19 = $gen20 = [];

        $generatorIds = $this->generatorRepository->getAllGeneratorIds();

        foreach ($entities as $entity) {
            switch((int)$entity["generator_id"]) {
                case $generatorIds[0]["id"]:
                    $gen1 = array_merge($gen1, [$entity]);
                    break;
                case $generatorIds[1]["id"]:
                    $gen2 = array_merge($gen2, [$entity]);
                    break;
                case $generatorIds[2]["id"]:
                    $gen3 = array_merge($gen3, [$entity]);
                    break;
                case $generatorIds[3]["id"]:
                    $gen4 = array_merge($gen4, [$entity]);
                    break;
                case $generatorIds[4]["id"]:
                    $gen5 = array_merge($gen5, [$entity]);
                    break;
                case $generatorIds[5]["id"]:
                    $gen6 = array_merge($gen6, [$entity]);
                    break;
                case $generatorIds[6]["id"]:
                    $gen7 = array_merge($gen7, [$entity]);
                    break;
                case $generatorIds[7]["id"]:
                    $gen8 = array_merge($gen8, [$entity]);
                    break;
                case $generatorIds[8]["id"]:
                    $gen9 = array_merge($gen9, [$entity]);
                    break;
                case $generatorIds[9]["id"]:
                    $gen10 = array_merge($gen10, [$entity]);
                    break;
                case $generatorIds[10]["id"]:
                    $gen11 = array_merge($gen11, [$entity]);
                    break;
                case $generatorIds[11]["id"]:
                    $gen12 = array_merge($gen12, [$entity]);
                    break;
                case $generatorIds[12]["id"]:
                    $gen13 = array_merge($gen13, [$entity]);
                    break;
                case $generatorIds[13]["id"]:
                    $gen14 = array_merge($gen14, [$entity]);
                    break;
                case $generatorIds[14]["id"]:
                    $gen15 = array_merge($gen15, [$entity]);
                    break;
                case $generatorIds[15]["id"]:
                    $gen16 = array_merge($gen16, [$entity]);
                    break;
                case $generatorIds[16]["id"]:
                    $gen17 = array_merge($gen17, [$entity]);
                    break;
                case $generatorIds[17]["id"]:
                    $gen18 = array_merge($gen18, [$entity]);
                    break;
                case $generatorIds[18]["id"]:
                    $gen19 = array_merge($gen19, [$entity]);
                    break;
                case $generatorIds[19]["id"]:
                    $gen20 = array_merge($gen20, [$entity]);
                    break;
            }
        }
        $generators = [
            $gen1, $gen2, $gen3, $gen4, $gen5, $gen6, $gen7, $gen8, $gen9, $gen10,
            $gen11, $gen12, $gen13, $gen14, $gen15, $gen16, $gen17, $gen18, $gen19, $gen20
        ];

        foreach ($generators as $generator) {
            $generatorStats = (new GeneratorStats())
                ->setHour(date("H", strtotime("-1 hours")))
                ->setGeneratorId((int)$generator[0]["generator_id"])
                ->setAveragePower($this->addHourlyDataToDatabaseService->calculateAveragePower($generator))
                ->setDate((new DateTime())->modify("-1 hours"));

            $this->entityManager->persist($generatorStats);
            $this->entityManager->flush();
        }
    }
}
