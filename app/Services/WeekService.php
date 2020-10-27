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

    public function range()
    {
        return $this->get()["range"];
    }

    public function monday(string $date)
    {
        return date(config('app.iso_date'), strtotime("this week Monday", strtotime($date)));
    }

    public function sunday(string $date)
    {
        return date(config('app.iso_date'), strtotime("this week Sunday", strtotime($date)));
    }

    public function last()
    {
        return isodate() < $this->end() ? isodate() : $this->end();
    }

    public function weekTitles()
    {
        return [
            'Mon' => 'Пн',
            'Tue' => 'Вт',
            'Wed' => 'Ср',
            'Thu' => 'Чт',
            'Fri' => 'Пт',
            'Sat' => 'Сб',
            'Sun' => 'Вс'
        ];
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
