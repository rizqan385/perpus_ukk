<?php

$ch = curl_init('http://localhost:8000/siswa/masuk');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
$response = curl_exec($ch);
echo "cURL Error: " . curl_error($ch) . "\n";
echo "Response Length: " . strlen($response) . "\n";
echo "Title: ";
if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $response, $matches)) {
    echo $matches[1];
} else {
    echo "No title found";
}
echo "\n";
