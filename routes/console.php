<?php

use App\Console\Commands\BackupCommand;
use App\Console\Commands\SendSubscriptionExpiryNotifications;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SendSubscriptionExpiryNotifications::class)->daily();
Schedule::command(BackupCommand::class)->daily()->at('03:00');
