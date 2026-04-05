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
        if (! Schema::hasColumn('tenants', 'status')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('status')->default('pending')->after('company_name');
            });
        }

        if (! Schema::hasColumn('tenants', 'admin_name')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('admin_name')->nullable()->after('company_name');
            });
        }

        if (! Schema::hasColumn('tenants', 'admin_email')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('admin_email')->nullable()->after('admin_name');
            });
        }

        if (! Schema::hasColumn('tenants', 'plan_id')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->unsignedBigInteger('plan_id')->nullable()->after('status');
            });
        }

        if (! Schema::hasColumn('tenants', 'billing_cycle')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('billing_cycle')->default('monthly')->after('plan_id');
            });
        }

        if (! Schema::hasColumn('tenants', 'subdomain')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('subdomain')->nullable()->after('company_name');
            });
        }

        if (! Schema::hasColumn('tenants', 'db_name')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('db_name')->nullable()->after('subdomain');
            });
        }

        if (! Schema::hasColumn('tenants', 'db_host')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('db_host')->nullable()->after('db_name');
            });
        }

        if (! Schema::hasColumn('tenants', 'brand_color')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('brand_color')->default('#2D7DD2')->after('status');
            });
        }

        if (! Schema::hasColumn('tenants', 'logo_url')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('logo_url')->nullable()->after('brand_color');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tenants', 'logo_url')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('logo_url');
            });
        }

        if (Schema::hasColumn('tenants', 'brand_color')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('brand_color');
            });
        }

        if (Schema::hasColumn('tenants', 'db_host')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('db_host');
            });
        }

        if (Schema::hasColumn('tenants', 'db_name')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('db_name');
            });
        }

        if (Schema::hasColumn('tenants', 'subdomain')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('subdomain');
            });
        }

        if (Schema::hasColumn('tenants', 'billing_cycle')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('billing_cycle');
            });
        }

        if (Schema::hasColumn('tenants', 'plan_id')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('plan_id');
            });
        }

        if (Schema::hasColumn('tenants', 'admin_email')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('admin_email');
            });
        }

        if (Schema::hasColumn('tenants', 'admin_name')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('admin_name');
            });
        }

        if (Schema::hasColumn('tenants', 'status')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
