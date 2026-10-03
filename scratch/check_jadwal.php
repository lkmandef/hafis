<?php

use App\Models\Jadwal;
use Illuminate\Contracts\Console\Kernel;

require_once __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$j = Jadwal::where('status', 'berjalan')->first();
echo 'Jadwal ID: '.($j ? $j->id : 'null')."\n";
echo 'Jadwal nama: '.($j ? $j->nama : 'null')."\n";
$ch = curl_init('http://127.0.0.1:8000/jadwal/'.$j->id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo 'Status code: '.$code."\n";
echo substr($res, 0, 400)."\n";
if ($code === 500) {
    if (preg_match('/<title>(.*?)<\/title>/is', $res, $m)) {
        echo 'Title: '.$m[1]."\n";
    }
}
