<?php

use App\Jobs\CheckDomain;
use App\Models\Domain;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Domain::with('latestCheck')->chunk(100, function ($domains) {
        foreach ($domains as $domain) {
            $lastCheck = $domain->latestCheck;
            $isDue = ! $lastCheck
                || $lastCheck->checked_at->addMinutes($domain->check_interval)->lte(now());

            if ($isDue) {
                CheckDomain::dispatch($domain);
            }
        }
    });
})->everyMinute()->name('dispatch-domain-checks')->withoutOverlapping();
