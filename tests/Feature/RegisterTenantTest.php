<?php

test('registers a tenant successfully', function () {
    $response = $this->post('/register', [
        'company_name' => 'Acme Corp',
        'subdomain' => 'acmetest',
        'admin_name' => 'Admin User',
        'admin_email' => 'admin@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'terms' => '1',
    ]);
    
    if (session()->has('errors')) {
        dump("Errors below:");
        dump(session()->get('errors')->getBag('default')->messages());
    } else {
        dump('No errors');
    }
    
    $response->assertSessionHasNoErrors();
});
