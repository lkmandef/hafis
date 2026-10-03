<?php

$ch = curl_init("http://127.0.0.1:8000/apar/1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Status code: " . $code . "\n";
echo "Response length: " . strlen($res) . "\n";
echo substr($res, 0, 500) . "\n";
