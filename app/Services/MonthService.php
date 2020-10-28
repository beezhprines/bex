<?php

namespace App\Services;

class MonthService
{
    function start(string $date)
    {
        return date('Y-m-01', strtotime($date));
    }

    function end(string $date)
    {
        return date('Y-m-t', strtotime($date));
    }
}
