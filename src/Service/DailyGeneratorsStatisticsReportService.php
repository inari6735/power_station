<?php

namespace App\Service;

use App\Repository\GeneratorStatsRepository;

class DailyGeneratorsStatisticsReportService
{
    public function createDailyStatisticsTemplate(array $statistics): string {
        $html = "
            <div style='padding: 10px'>
                <table style='border: 1px solid; border-collapse: collapse; text-align: center;margin-left:auto;margin-right:auto'>
                    <tr style='border: 1px solid;'>
                        <th style='border: 1px solid; padding: 5px;'>GeneratorId</th>
                        <th style='border: 1px solid; padding: 5px;'>Name</th>
                        <th style='border: 1px solid; padding: 5px;'>Average Power [MW]</th>
                        <th style='border: 1px solid; padding: 5px;'>Hour</th>
                        <th style='border: 1px solid; padding: 5px;'>Date</th>
                    </tr>
            ";

        foreach($statistics as $statistic) {
            $statistic['date'] = date_format($statistic["date"], "Y-m-d");
            $statistic['power_MW'] = round($statistic['power_MW'], 6);
            $html .= "
                <tr style='border: 1px solid;'>
                            <td style='border: 1px solid; padding: 5px;'>{$statistic['generator_id']}</td>
                            <td style='border: 1px solid; padding: 5px;'>{$statistic['name']}</td>
                            <td style='border: 1px solid; padding: 5px;'>{$statistic['power_MW']}</td>
                            <td style='border: 1px solid; padding: 5px;'>{$statistic['hour']}</td>
                            <td style='border: 1px solid; padding: 5px;'>{$statistic['date']}</td>
                        </tr>
            ";
        }

        $html .= "</table></div>";
        return $html;
    }
}
