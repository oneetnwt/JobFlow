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
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "April 2026 - Week 1"
            $table->date('start_date');
            $table->date('end_date');

            // Status: draft, processed, released
            $table->string('status')->default('draft');

            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The worker

            $table->decimal('base_rate', 10, 2); // Snapshot of hourly_rate at time of generation
            $table->decimal('hours_worked', 8, 2)->default(0.00);
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('net_amount', 12, 2);

            // Status: pending, approved, paid
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('payroll_periods');
    }
};
