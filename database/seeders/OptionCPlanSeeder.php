<?php

namespace Database\Seeders;

use App\Enums\PlanFeature;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class OptionCPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter',
                'tagline' => 'Great for small teams to get started.',
                'monthly_price' => 999.00,
                'annual_price' => 9990.00,
                'max_workers' => 5,
                'max_job_orders' => 50,
                'trial_days' => 14,
                'status' => 'active',
                'sort_order' => 1,
                'features' => [],
            ]
        );

        Plan::updateOrCreate(
            ['slug' => 'professional'],
            [
                'name' => 'Professional',
                'tagline' => 'For medium teams with no job limits and advanced features.',
                'monthly_price' => 2999.00,
                'annual_price' => 29990.00,
                'max_workers' => 20,
                'max_job_orders' => null, // unlimited
                'trial_days' => 14,
                'badge_label' => 'Most Popular',
                'status' => 'active',
                'sort_order' => 2,
                'features' => [
                    PlanFeature::CUSTOM_BRANDING->value,
                    PlanFeature::ADVANCED_REPORTS->value,
                    PlanFeature::UNLIMITED_JOBS->value,
                ],
            ]
        );

        Plan::updateOrCreate(
            ['slug' => 'enterprise'],
            [
                'name' => 'Enterprise',
                'tagline' => 'Unlimited scaling for enterprise businesses.',
                'monthly_price' => 9999.00,
                'annual_price' => 99990.00,
                'max_workers' => null, // unlimited
                'max_job_orders' => null, // unlimited
                'trial_days' => 14,
                'status' => 'active',
                'sort_order' => 3,
                'features' => [
                    PlanFeature::CUSTOM_BRANDING->value,
                    PlanFeature::ADVANCED_REPORTS->value,
                    PlanFeature::API_ACCESS->value,
                    PlanFeature::WEBHOOK->value,
                    PlanFeature::PRIORITY_SUPPORT->value,
                    PlanFeature::UNLIMITED_USERS->value,
                    PlanFeature::UNLIMITED_JOBS->value,
                    PlanFeature::CUSTOM_DOMAIN->value,
                ],
            ]
        );
    }
}
