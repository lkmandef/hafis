<?php

$ch = curl_init("http://127.0.0.1:8000/apar/1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
if (preg_match('/<title>(.*?)<\/title>/is', $res, $m)) {
    echo "Title: " . $m[1] . "\n";
}
if (preg_match('/<span class="text-gray-900 font-semibold">(.*?)<\/span>/is', $res, $m)) {
    echo "Error snippet: " . $m[1] . "\n";
}
// Check laravel.log tail
$logFile = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $tail = array_slice($lines, -25);
    echo "--- LOG TAIL ---\n" . implode("", $tail);
}
