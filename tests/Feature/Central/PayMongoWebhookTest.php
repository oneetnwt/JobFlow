<?php

use App\Models\Plan;
use App\Models\SubscriptionInvoice;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Setup a free basic plan
    $this->plan = Plan::create([
        'name' => 'Pro',
        'slug' => 'pro',
        'price' => 1999,
        'description' => 'A basic plan',
        'features' => json_encode(['feature1' => 'Feature 1']),
        'status' => 'active',
        'stripe_id' => 'abc',
        'is_featured' => true,
        'sort_order' => 1,
    ]);

    // Setup a tenant
    $this->tenant = Tenant::create([
        'id' => 'foo' . rand(1000, 9999),
        'company_name' => 'Foo Inc.',
        'company' => 'Foo Inc.',
        'tenancy_db_name' => 'foo_db_' . rand(1000, 9999),
    ]);

    // Setup their initial trial subscription
    $this->subscription = TenantSubscription::create([
        'tenant_id' => $this->tenant->id,
        'plan_id' => $this->plan->id,
        'status' => 'trialing',
        'trial_ends_at' => now()->addDays(14),
    ]);

    Config::set('services.paymongo.webhook_secret', 'test_secret');
});

it('rejects webhooks with an invalid signature', function () {
    $payload = json_encode(['data' => ['attributes' => ['type' => 'payment.paid']]]);
    $signature = 't=1234567890,te=invalid_signature';

    $response = $this->postJson('/webhooks/paymongo', json_decode($payload, true), [
        'Paymongo-Signature' => $signature,
    ]);

    $response->assertStatus(400);
    $response->assertJson(['error' => 'Invalid signature']);
});

it('successfully processes checkout paid events and activates the subscription', function () {
    $payload = [
        'data' => [
            'id' => 'evt_123',
            'attributes' => [
                'type' => 'checkout_session.payment.paid',
                'data' => [
                    'id' => 'pi_test123',
                    'attributes' => [
                        'reference_number' => "{$this->tenant->id}_{$this->plan->id}_" . time(),
                        'amount' => 199900, // 1999.00 PHP in cents
                        'receipt_url' => 'https://paymongo.com/receipts/123',
                    ],
                ],
            ],
        ],
    ];

    $jsonPayload = json_encode($payload);
    $timestamp = time();
    $signedPayload = $timestamp . '.' . $jsonPayload;
    $expectedSignature = hash_hmac('sha256', $signedPayload, 'test_secret');
    $signatureHeader = "t={$timestamp},te={$expectedSignature}";

    $response = $this->postJson('/webhooks/paymongo', $payload, [
        'Paymongo-Signature' => $signatureHeader,
    ]);

    $response->assertStatus(200);

    // Assert subscription was updated
    $this->subscription->refresh();
    expect($this->subscription->status)->toBe('active')
        ->and($this->subscription->plan_id)->toBe($this->plan->id)
        ->and($this->subscription->current_period_end)->not->toBeNull();

    // Assert invoice was correctly generated in central DB
    $invoice = SubscriptionInvoice::where('tenant_id', $this->tenant->id)->first();
    expect($invoice)->not->toBeNull()
        ->and($invoice->amount)->toEqual(1999.00)
        ->and((string) $invoice->status)->toBe('paid')
        ->and($invoice->invoice_url)->toBe('https://paymongo.com/receipts/123');
});

it('marks a subscription past_due on failure', function () {
    // Manually activate it first
    $this->subscription->update(['status' => 'active']);

    $payload = [
        'data' => [
            'id' => 'evt_456',
            'attributes' => [
                'type' => 'payment.failed',
                'data' => [
                    'id' => 'pi_fail123',
                    'attributes' => [
                        'reference_number' => "{$this->tenant->id}_{$this->plan->id}_" . time(),
                    ],
                ],
            ],
        ],
    ];

    $jsonPayload = json_encode($payload);
    $timestamp = time();
    $signedPayload = $timestamp . '.' . $jsonPayload;
    $expectedSignature = hash_hmac('sha256', $signedPayload, 'test_secret');
    $signatureHeader = "t={$timestamp},te={$expectedSignature}";

    $response = $this->postJson('/webhooks/paymongo', $payload, [
        'Paymongo-Signature' => $signatureHeader,
    ]);

    $response->assertStatus(200);

    $this->subscription->refresh();
    expect($this->subscription->status)->toBe('past_due');
});
