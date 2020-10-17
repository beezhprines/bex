<?php

use App\Models\Note;
use Illuminate\Support\Facades\Storage;

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
