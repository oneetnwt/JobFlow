<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/register', 'POST', [
    'company_name' => 'Acme Corp',
    'subdomain' => 'acmetest9',
    'admin_name' => 'Admin User',
    'admin_email' => 'admin9@example.com',
    'password' => 'Test1234@',
    'password_confirmation' => 'Test1234@',
    'terms' => '1',
    'plan_id' => 1
]);

$response = $kernel->handle($request);
if ($response->isRedirect()) {
    $session = $request->getSession();
    if ($session && $session->has('errors')) {
        print_r($session->get('errors')->getBag('default')->messages());
    } else {
        echo "Success redirect";
    }
}
