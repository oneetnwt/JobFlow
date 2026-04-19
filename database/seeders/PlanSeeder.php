<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding pricing plans...');

        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'tagline' => 'Try JobFlow OMS at no cost — ideal for small teams getting started',
                'monthly_price' => 0.00,
                'annual_price' => 0.00, // Monthly × 10 formula, 0*10=0
                'currency' => 'PHP',
                // Using exact integer max values. Null represents unlimited.
                'max_workers' => 3,
                'max_job_orders' => 20,
                'has_payroll' => false,
                'has_priority_support' => false,
                'has_custom_integrations' => false,
                // Free is the only plan that auto-approves upon registration
                'auto_approve' => true,
                'is_contact_sales' => false,
                'badge_label' => 'Free Forever',
                'status' => 'active',
                'sort_order' => 1,
                'is_free' => true,
                'features' => [
                    'Up to 3 workers',
                    '20 job orders per month',
                    'Basic job order management',
                    'Single admin account',
                    'Centralized dashboard',
                    'Community support',
                ],
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'tagline' => 'For small businesses ready to organize their operations',
                'monthly_price' => 999.00,
                // Annual pricing formula: monthly price × 10 (2 months free standard PH SaaS)
                'annual_price' => 9990.00,
                'currency' => 'PHP',
                'max_workers' => 10,
                'max_job_orders' => 150,
                'has_payroll' => false,
                'has_priority_support' => false,
                'has_custom_integrations' => false,
                'auto_approve' => false,
                'is_contact_sales' => false,
                // Use null for no badge per guidelines, do not use empty string
                'badge_label' => null,
                'status' => 'active',
                // Keep sort_order sequential
                'sort_order' => 2,
                'is_free' => false,
                'features' => [
                    'Up to 10 workers',
                    '150 job orders per month',
                    'Job order creation and management',
                    'Worker assignment and scheduling',
                    'Real-time job tracking',
                    'Up to 2 admin accounts',
                    'Email support',
                    'Centralized dashboard',
                    'Basic reporting',
                ],
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'tagline' => 'The complete platform for growing operations teams',
                'monthly_price' => 2499.00,
                // Annual pricing: 2499 × 10 = 24990
                'annual_price' => 24990.00,
                'currency' => 'PHP',
                'max_workers' => 50,
                // Null represents unlimited
                'max_job_orders' => null,
                'has_payroll' => true,
                'has_priority_support' => false,
                'has_custom_integrations' => false,
                'auto_approve' => false,
                'is_contact_sales' => false,
                'badge_label' => 'Most Popular',
                'status' => 'active',
                'sort_order' => 3,
                'is_free' => false,
                'features' => [
                    'Up to 50 workers',
                    'Unlimited job orders',
                    'Full job order management',
                    'Worker assignment and scheduling',
                    'Real-time job tracking',
                    'Integrated payroll processing',
                    'Automated compensation computation',
                    'Up to 5 admin accounts',
                    'Priority email support',
                    'Advanced dashboard and reporting',
                    'Data export (CSV, PDF)',
                ],
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'tagline' => 'Unlimited scale with dedicated support for large organizations',
                // Utilizing null instead of 0.00 for contact sales pricing
                'monthly_price' => null,
                'annual_price' => null,
                'currency' => 'PHP',
                // Null represents unlimited workers and resources
                'max_workers' => null,
                'max_job_orders' => null,
                'has_payroll' => true,
                'has_priority_support' => true,
                'has_custom_integrations' => true,
                'auto_approve' => false,
                // true only for Enterprise context
                'is_contact_sales' => true,
                'badge_label' => 'Contact Sales',
                'status' => 'active',
                'sort_order' => 4,
                'is_free' => false,
                'features' => [
                    'Unlimited workers',
                    'Unlimited job orders',
                    'Full job order management',
                    'Worker assignment and scheduling',
                    'Real-time job tracking',
                    'Integrated payroll processing',
                    'Automated compensation computation',
                    'Unlimited admin accounts',
                    'Dedicated account manager',
                    'Priority phone and email support',
                    'Custom integrations and API access',
                    'Custom SLA agreement',
                    'Advanced dashboard and reporting',
                    'Data export (CSV, PDF, Excel)',
                    'SSO / SAML support',
                    'Audit logs and compliance reporting',
                    'Onboarding and training assistance',
                ],
            ],
        ];

        foreach ($plans as $planData) {
            // Check existence based on slug to correctly log Created vs Updated
            $exists = Plan::where('slug', $planData['slug'])->exists();

            // Upsert mechanism guarantees idempotency
            Plan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );

            $action = $exists ? 'UPDATED' : 'CREATED';

            if ($planData['is_contact_sales']) {
                $priceStr = 'Contact Sales';
            } else {
                $priceStr = '₱'.number_format($planData['monthly_price'], 2).'/month';
            }

            $this->command->info("[{$action}] {$planData['name']} — {$priceStr}");
        }

        $this->command->info('Pricing plans seeded successfully.');
    }
}
