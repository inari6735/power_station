<?php

namespace App\DataFixtures;

use App\Entity\Generator;
use App\Entity\GeneratorStats;
use App\Service\DatePeriodService;
use App\Service\RandomFloatNumberGeneratorService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Repository\GeneratorRepository;

class AppFixtures extends Fixture
{
    private GeneratorRepository $generatorRepository;
    private DatePeriodService $datePeriodService;
    private RandomFloatNumberGeneratorService $randomFloatNumberGeneratorService;

    public function __construct(
        GeneratorRepository $generatorRepository,
        DatePeriodService $datePeriodService,
        RandomFloatNumberGeneratorService $randomFloatNumberGeneratorService
    )
    {

        $this->generatorRepository = $generatorRepository;
        $this->datePeriodService = $datePeriodService;
        $this->randomFloatNumberGeneratorService = $randomFloatNumberGeneratorService;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $generator = (new Generator())->setName("Gen".$i);
            $manager->persist($generator);
        }
        $manager->flush();
        $batchSize = 100;
        $counter = 1;
        $generator = $this->generatorRepository->findAll();
        foreach ($this->datePeriodService->getAllDaysInGivenYear(2019) as $date) {
            for ($i = 0; $i < count($generator); $i++) {
                for($j = 1; $j <= 24; $j++) {
                    $generatorStats = (new GeneratorStats())
                        ->setAveragePower($this->randomFloatNumberGeneratorService->getRandomFloatNumber())
                        ->setGeneratorId($generator[$i]->getId())
                        ->setDate($date)
                        ->setHour($j);
                    $manager->persist($generatorStats);
                    $counter++;
                    if (($i % $batchSize) === 0) {
                        $manager->flush();
                        $manager->clear();
                    }
                }
            }
        }
        $manager->flush();
        $manager->clear();
    }
}
