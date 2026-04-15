<?php

$ch = curl_init('http://localhost:8000/siswa/masuk');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Inertia: true',
    'X-Inertia-Version: '
]);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
echo "cURL Error: " . curl_error($ch) . "\n";
echo "Response:\n" . substr($response, 0, 1000) . "\n";
