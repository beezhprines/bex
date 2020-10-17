<?php

namespace App\Services;

use Carbon\Carbon;

class WeekService
{
    public function start()
    {
        return session('week')['start'] ?? null;
    }

    public function end()
    {
        return session('week')['end'] ?? null;
    }

    public function get()
    {
        return session('week');
    }

    public function set(string $start = null, string $end = null)
    {
        $now = Carbon::now();
        $start = $start ?: $now->startOfWeek()->format(config('app.iso_date'));
        $end = $end ?: $now->endOfWeek()->format(config('app.iso_date'));

        $range = [];

        foreach (collect(daterange($start, $end, true)) as $date) {
            $range[date_format($date, "D")] = $date;
        }

        session([
            'week' => [
                'start' => $start,
                'end' => $end,
                'range' => $range
            ]
        ]);
    }
}
