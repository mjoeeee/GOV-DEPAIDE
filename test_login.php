<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/login', 'POST', [
    'email' => 'jean.veloso@deped.gov.ph',
    'password' => 'password'
]);
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo $response->getContent();
