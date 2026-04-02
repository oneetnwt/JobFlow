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
            $table->renameColumn('description', 'tagline');
            $table->renameColumn('max_jobs_per_month', 'max_job_orders');
            $table->string('currency')->default('PHP')->after('annual_price');
            $table->boolean('is_contact_sales')->default(false)->after('auto_approve');
            $table->integer('sort_order')->default(0)->after('status');
        });

        // Have to separate change() in some Laravel versions if sqlite/mysql differences exist, but usually fine in another closure or same.
        Schema::table('plans', function (Blueprint $table) {
            $table->decimal('monthly_price', 10, 2)->nullable()->change();
            $table->decimal('annual_price', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->renameColumn('tagline', 'description');
            $table->renameColumn('max_job_orders', 'max_jobs_per_month');
            $table->dropColumn(['currency', 'is_contact_sales', 'sort_order']);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->decimal('monthly_price', 10, 2)->nullable(false)->default(0)->change();
            $table->decimal('annual_price', 10, 2)->nullable(false)->default(0)->change();
        });
    }
};
