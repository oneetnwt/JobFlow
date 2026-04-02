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
        // Update central users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('password');
        });

        // Update tenants table (using JSON column for custom data if needed, but adding status explicitly)
        Schema::table('tenants', function (Blueprint $table) {
            // Status: pending, active, suspended
            $table->string('status')->default('pending')->after('company_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_super_admin');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
