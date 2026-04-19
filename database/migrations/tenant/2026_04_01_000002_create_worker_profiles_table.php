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
        Schema::create('worker_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('employee_id')->nullable()->unique();
            $table->string('department')->nullable();
            $table->text('skills')->nullable(); // Can be comma-separated or JSON

            // Employment: full-time, part-time, contract, seasonal
            $table->string('employment_type')->default('full-time');

            $table->string('phone_number')->nullable();
            $table->date('joined_at')->nullable();

            // Financials for Payroll Module
            $table->decimal('hourly_rate', 10, 2)->default(0.00);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_profiles');
    }
};
