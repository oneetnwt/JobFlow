<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionInvoice;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayMongoWebhookController extends Controller
{
    /**
     * Handle incoming webhooks from PayMongo.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        $signature = $request->header('Paymongo-Signature');

        if (! $this->verifySignature($signature, $request->getContent())) {
            Log::warning('Invalid PayMongo Webhook Signature', ['ip' => $request->ip()]);

            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Webhook structure usually nests event inside data.attributes
        $event = $payload['data']['attributes'] ?? null;
        if (! $event) {
            return response()->json(['status' => 'ignored']);
        }

        $eventType = $event['type'] ?? null;
        $eventData = $event['data'] ?? null;

        Log::info("PayMongo Webhook Received: {$eventType}");

        try {
            switch ($eventType) {
                // Checkout Session Paid
                case 'checkout_session.payment.paid':
                    $this->handlePaymentPaid($eventData);
                    break;
                case 'payment.paid':
                    $this->handlePaymentPaid($eventData);
                    break;

                case 'payment.failed':
                    $this->handlePaymentFailed($eventData);
                    break;

                default:
                    Log::info("Unhandled PayMongo Webhook Event: {$eventType}");
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Error processing PayMongo webhook: '.$e->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Verify the PayMongo webhook signature.
     */
    protected function verifySignature(?string $signatureHeader, string $payload): bool
    {
        if (! $signatureHeader) {
            return false;
        }

        $secret = config('services.paymongo.webhook_secret');
        if (! $secret) {
            Log::warning('PayMongo webhook secret is not configured. Bypassing signature verification.');

            return true;
        }

        preg_match('/t=(.*?),(v1|te)=(.*)/', $signatureHeader, $matches);
        if (count($matches) < 4) {
            return false;
        }

        $timestamp = $matches[1];
        $signature = $matches[3];

        $signedPayload = $timestamp.'.'.$payload;
        $expectedSignature = hash_hmac('sha256', $signedPayload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Handle successful payment logic.
     */
    protected function handlePaymentPaid($data)
    {
        $attributes = $data['attributes'] ?? [];
        $reference = $attributes['reference_number'] ?? null;

        if (! $reference) {
            Log::warning('Webhook missing reference_number', $data);

            return;
        }

        $parts = explode('_', $reference);
        if (count($parts) < 2) {
            Log::warning('Malformed reference_number', ['reference' => $reference]);

            return;
        }

        $tenantId = $parts[0];
        $planId = $parts[1];

        $tenant = Tenant::find($tenantId);
        if (! $tenant || ! $tenant->subscription) {
            Log::warning('Tenant or subscription not found for webhook processing', ['tenant_id' => $tenantId]);

            return;
        }

        $subscription = $tenant->subscription;

        // Activate the subscription
        $subscription->update([
            'plan_id' => $planId,
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        // Create the invoice directly in central DB
        SubscriptionInvoice::create([
            'tenant_id' => $tenant->id,
            'tenant_subscription_id' => $subscription->id,
            'amount' => ($attributes['amount'] ?? 0) / 100, // from cents to PHP
            'status' => 'paid',
            'invoice_url' => $attributes['receipt_url'] ?? '#',
            'payment_intent_id' => $data['id'] ?? 'pi_'.time(),
            'paid_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info("Successfully processed paid webhook for tenant {$tenant->id}");
    }

    /**
     * Handle failed payment logic.
     */
    protected function handlePaymentFailed($data)
    {
        $attributes = $data['attributes'] ?? [];
        $reference = $attributes['reference_number'] ?? null;

        if (! $reference) {
            return;
        }

        $parts = explode('_', $reference);
        $tenantId = $parts[0];

        $tenant = Tenant::find($tenantId);
        if ($tenant && $tenant->subscription) {
            $tenant->subscription->update([
                'status' => 'past_due',
            ]);
            Log::info("Marked subscription past_due for tenant {$tenant->id} on payment failure.");
        }
    }
}
