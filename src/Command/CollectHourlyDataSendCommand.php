<?php

namespace App\Command;

use App\Message\CollectHourlyDataFromRedis;
use App\Repository\GeneratorRepository;
use App\Service\ValidateDateService;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:collect-hourly-data:send',
    description: 'Collect hourly data from Redis',
)]
class CollectHourlyDataSendCommand extends Command
{
    private MessageBusInterface $messageBus;
    private ValidateDateService $validateDateService;
    private GeneratorRepository $generatorRepository;

    public function __construct(
        MessageBusInterface $messageBus,
        ValidateDateService $validateDateService,
        GeneratorRepository $generatorRepository
    )
    {
        parent::__construct(null);
        $this->messageBus = $messageBus;
        $this->validateDateService = $validateDateService;
        $this->generatorRepository = $generatorRepository;
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->progressStart();

        $hashes = [];
        $redis = RedisAdapter::createConnection($_ENV['REDIS_URL']);
        $redisKeys = $redis->keys('*');

        foreach ($redisKeys as $key) {
            $hash = $redis->hgetall($key);
            if(!isset($hash["date"])) {
                continue;
            };

            if ($this->validateDateService->isValidDateFromPreviousHour($hash["date"])) {
                $hashes = array_merge($hashes, [$hash]);
            };
        }

        $message = new CollectHourlyDataFromRedis($hashes);
        $this->messageBus->dispatch($message);

        $io->success("Zebrano dane z generatorów");

        return Command::SUCCESS;
    }
}
