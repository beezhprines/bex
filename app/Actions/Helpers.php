<?php

use App\Models\Note;
use App\Services\WeekService;
use Illuminate\Support\Facades\Storage;

function week()
{
    return resolve(WeekService::class);
}

function daterange(string $from, string $to, bool $withLast = false)
{
    if ($withLast) {
        $to = date(config('app.iso_date'), strtotime($to . ' +1 day'));
    }
    return new DatePeriod(
        new DateTime($from),
        new DateInterval('P1D'),
        new DateTime($to)
    );
}

function version()
{
    return Storage::exists('version') ? Storage::get('version') : null;
}

function note(string $level, string $code, string $message, string $model = null, int $model_id = null, string $description = null)
{
    switch (config('app.note_sensitivity')) {

        case 'LOW':
            $allow = ['danger'];
            break;

        case 'MEDIUM':
            $allow = ['danger', 'warning'];
            break;

        case 'HIGH':
            $allow = ['danger', 'warning', 'info'];
            break;

        default:
            $allow = ['danger'];
            break;
    }

    if (in_array($level, $allow)) {
        Note::create([
            'level' => $level,
            'code' => $code,
            'message' => $message,
            'description' => $description,
            'model' => $model,
            'model_id' => $model_id,
        ]);
    }
}
