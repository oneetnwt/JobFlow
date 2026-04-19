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
        Schema::table('plans', function (Blueprint $table) {
            if (! Schema::hasColumn('plans', 'trial_days')) {
                $table->integer('trial_days')->default(14)->after('status');
            }
        });

        Schema::create('tenant_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->string('status')->default('trialing'); // trialing, active, past_due, cancelled, expired
            $table->timestamp('trial_starts_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('paymongo_subscription_id')->nullable();
            $table->string('paymongo_customer_id')->nullable();
            $table->timestamps();
        });

        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->string('paymongo_payment_id')->nullable();
            $table->string('paymongo_link_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('PHP');
            $table->string('status')->default('pending'); // pending, paid, failed, refunded
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
        Schema::dropIfExists('tenant_subscriptions');

        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'trial_days')) {
                $table->dropColumn('trial_days');
            }
        });
    }
};
