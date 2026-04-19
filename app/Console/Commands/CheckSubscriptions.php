<?php

namespace App\Console\Commands;

use App\Models\TenantSubscription;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('subscriptions:check')]
#[Description('Scan all subscriptions to update trial expirations or past-due renewals automatically.')]
class CheckSubscriptions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting subscription status check...');

        // 1. Expire trials that have passed 'trial_ends_at'
        $expiredTrials = TenantSubscription::where('status', 'trialing')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<', now())
            ->update([
                'status' => 'expired',
            ]);

        $this->info("Expired {$expiredTrials} trailing subscriptions.");

        // 2. Mark active subscriptions that have passed 'current_period_end' but no payment was made as past_due.
        $pastDueActive = TenantSubscription::where('status', 'active')
            ->whereNotNull('current_period_end')
            ->where('current_period_end', '<', now()->subDays(3))
            ->update([
                'status' => 'past_due',
            ]);

        $this->info("Marked {$pastDueActive} active subscriptions as past due.");

        $this->info('Subscription check complete.');
    }
}
