<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            
            // Pricing
            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->decimal('annual_price', 10, 2)->default(0);
            
            // Limits
            $table->integer('max_workers')->nullable(); // null = unlimited
            $table->integer('max_jobs_per_month')->nullable(); // null = unlimited
            
            // Feature Toggles
            $table->boolean('has_payroll')->default(false);
            $table->boolean('has_priority_support')->default(false);
            $table->boolean('has_custom_integrations')->default(false);
            
            // Dynamic Features List
            $table->json('features')->nullable();
            
            // Metadata & Status
            $table->string('badge_label')->nullable(); // e.g., "Most Popular"
            $table->string('status')->default('draft'); // draft, active, archived
            
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('status')->constrained('plans');
            $table->string('billing_cycle')->default('monthly')->after('plan_id'); // monthly, annual
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn(['plan_id', 'billing_cycle']);
        });
        Schema::dropIfExists('plans');
    }
};
