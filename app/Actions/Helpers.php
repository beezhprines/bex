<?php

use Illuminate\Support\Facades\Storage;

function version()
{
    return Storage::exists('version') ? Storage::get('version') : null;
}
