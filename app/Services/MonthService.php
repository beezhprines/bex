<?php

namespace App\Services;

class MonthService
{
    function start(string $date)
    {
        return date('m-01-Y', strtotime($date));
    }

    function end(string $date)
    {
        return date('m-t-Y', strtotime($date));
    }
}
