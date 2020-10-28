<?php

use App\Models\Note;
use App\Services\MonthService;
use App\Services\WeekService;
use Illuminate\Support\Facades\Storage;

function isodate(string $date = null)
{
    if (is_null($date)) return date(config('app.iso_date'));
    return date(config('app.iso_date'), strtotime($date));
}

function viewdate(string $date = null)
{
    if (is_null($date)) return date(config('app.view_date'));
    return date(config('app.view_date'), strtotime($date));
}

function price($value)
{
    if (is_float($value) || is_int($value)) {
        return number_format($value, 0, '.', ' ');
    };

    return $value;
}

function week()
{
    return resolve(WeekService::class);
}

function month()
{
    return resolve(MonthService::class);
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
    switch (config('app.note_sence')) {

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

function translit($value)
{
    $converter = array(
        'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
        'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
        'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
        'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
        'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
        'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
        'э' => 'e',    'ю' => 'yu',   'я' => 'ya',

        'А' => 'A',    'Б' => 'B',    'В' => 'V',    'Г' => 'G',    'Д' => 'D',
        'Е' => 'E',    'Ё' => 'E',    'Ж' => 'Zh',   'З' => 'Z',    'И' => 'I',
        'Й' => 'Y',    'К' => 'K',    'Л' => 'L',    'М' => 'M',    'Н' => 'N',
        'О' => 'O',    'П' => 'P',    'Р' => 'R',    'С' => 'S',    'Т' => 'T',
        'У' => 'U',    'Ф' => 'F',    'Х' => 'H',    'Ц' => 'C',    'Ч' => 'Ch',
        'Ш' => 'Sh',   'Щ' => 'Sch',  'Ь' => '',     'Ы' => 'Y',    'Ъ' => '',
        'Э' => 'E',    'Ю' => 'Yu',   'Я' => 'Ya',
    );

    $value = strtr(str_replace(" ", "", $value), $converter);
    return $value;
}

function secondsToTime(int $seconds)
{
    return sprintf('%02d:%02d', ($seconds / 3600), ($seconds / 60 % 60));
}
