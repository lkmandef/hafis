<?php

use App\Models\Apar;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('hafis:sync-expired-apar', function () {
    $count = Apar::syncExpiredStatus();
    $this->info("Sinkronisasi selesai: {$count} APAR diperbarui menjadi Perlu Perhatian.");
})->purpose('Sinkronisasi status APAR yang seminggu sebelum expired (H-7) atau sudah kadaluarsa');

Schedule::command('hafis:sync-expired-apar')->dailyAt('00:01');
