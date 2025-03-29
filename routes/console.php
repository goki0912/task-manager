<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\SendTaskDueReminders;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 👇 ジョブスケジューリングを追加
app()->booted(function () {
    app(Schedule::class)
        ->job(new SendTaskDueReminders())
        ->everyMinute();
});
