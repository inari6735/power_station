<?php

namespace App\Service;

use DateInterval;
use DateTime;
use DatePeriod;

class DatePeriodService
{
    public function getAllDaysInGivenYear(int $year): DatePeriod {
        $start = new DateTime($year.'-01-01');
        $end = new DateTime($year.'-12-31');
        $interval = new DateInterval('P1D');
        return new DatePeriod($start, $interval, $end);
    }
}
