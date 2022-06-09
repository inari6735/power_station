<?php

namespace App\Service;

class ValidateDateService
{
    public function isValidDateFromPreviousHour(string $date) {
        $formatedDate = date('Y-m-d H', strtotime($date));
        $previousHourDate = date("Y-m-d H", strtotime("-1 hours"));

        if ($formatedDate == $previousHourDate) {
           return True;
        }
        return False;
    }
}
