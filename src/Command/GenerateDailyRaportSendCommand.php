<?php

namespace App\Command;

use App\Repository\GeneratorStatsRepository;
use App\Service\DailyGeneratorsStatisticsReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Dompdf\Dompdf;

#[AsCommand(
    name: 'app:generate-daily-raport:send',
    description: 'Generate daily raport',
)]
class GenerateDailyRaportSendCommand extends Command
{
    private GeneratorStatsRepository $generatorStatsRepository;
    private DailyGeneratorsStatisticsReportService $dailyGeneratorsStatisticsReportService;

    public function __construct(
        GeneratorStatsRepository $generatorStatsRepository,
        DailyGeneratorsStatisticsReportService $dailyGeneratorsStatisticsReportService
    )
    {
        parent::__construct(null);
        $this->generatorStatsRepository = $generatorStatsRepository;
        $this->dailyGeneratorsStatisticsReportService = $dailyGeneratorsStatisticsReportService;
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->progressStart();

        $date = date("Y-m-d", strtotime("-1 days"));
        $statistics = $this->generatorStatsRepository->getDailyGeneratorsStatsInMW("2019-01-01");
        $html = $this->dailyGeneratorsStatisticsReportService->createDailyStatisticsTemplate($statistics);

        $title = 'report_'.$date.'.pdf';
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        file_put_contents("/srv/app/daily_reports/".$title, $dompdf->output());

        $io->success('Utworzono dzienny raport');

        return Command::SUCCESS;
    }
}
