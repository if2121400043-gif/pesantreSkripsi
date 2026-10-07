<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/psb/daftar', 'GET');
$response = $kernel->handle($request);
if ($response->exception) {
    echo "Exception: " . $response->exception->getMessage() . "\n";
    echo "File: " . $response->exception->getFile() . "\n";
    echo "Line: " . $response->exception->getLine() . "\n";
} else {
    echo "Status: " . $response->getStatusCode() . "\n";
}
