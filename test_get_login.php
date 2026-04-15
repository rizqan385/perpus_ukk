<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/siswa/masuk', 'GET');
$request->headers->set('X-Inertia', 'true');
$request->headers->set('X-Inertia-Version', '');

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Headers: \n";
print_r($response->headers->all());
echo "Content: \n" . substr($response->getContent(), 0, 1000) . "...\n";
