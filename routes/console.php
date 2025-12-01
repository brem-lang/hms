<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:checkout-alert-command')->everyMinute();

Schedule::command('app:check-no-shows')->everyMinute();

Schedule::command('app:apply-overdue-charges')->everyMinute();
