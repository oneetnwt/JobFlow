<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/register', 'POST', [
    'company_name' => 'Acme Corp',
    'subdomain' => 'acme',
    'admin_name' => 'Admin User',
    'admin_email' => 'admin@example.com',
    'password' => ' Password123! ', // wait maybe it trims it?
    'password_confirmation' => 'Password123!',
    'terms' => '1'
]);

$response = $kernel->handle($request);
