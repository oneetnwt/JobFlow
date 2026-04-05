<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (! Schema::hasColumn('tenants', 'subdomain')) {
                $table->string('subdomain')->nullable()->after('company_name');
            }

            if (! Schema::hasColumn('tenants', 'db_name')) {
                $table->string('db_name')->nullable()->after('subdomain');
            }

            if (! Schema::hasColumn('tenants', 'db_host')) {
                $table->string('db_host')->nullable()->after('db_name');
            }
        });

        if (Schema::hasTable('domains')) {
            $tenants = DB::table('tenants')->select('id')->get();

            foreach ($tenants as $tenant) {
                $domain = DB::table('domains')
                    ->where('tenant_id', $tenant->id)
                    ->orderBy('id')
                    ->value('domain');

                if (! $domain) {
                    continue;
                }

                $subdomain = explode('.', $domain)[0] ?? null;

                $existing = DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->first(['db_name', 'db_host']);

                DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->update([
                        'subdomain' => $subdomain,
                        'db_name' => $existing?->db_name ?: 'jobflow_' . $tenant->id,
                        'db_host' => $existing?->db_host ?: '127.0.0.1',
                    ]);
            }
        }

        Schema::create('tenant_users', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('email');
            $table->string('password_hash')->nullable();
            $table->string('role')->default('admin');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'email']);
            $table->index(['tenant_id', 'is_active']);
        });

        Schema::create('tenant_plans', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('plan_name');
            $table->timestamp('valid_until')->nullable();
            $table->json('feature_flags')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index('tenant_id');
        });

        if (Schema::hasTable('plans')) {
            $tenantRows = DB::table('tenants')
                ->leftJoin('plans', 'tenants.plan_id', '=', 'plans.id')
                ->select('tenants.id as tenant_id', 'plans.name as plan_name', 'plans.features')
                ->get();

            foreach ($tenantRows as $row) {
                DB::table('tenant_plans')->insert([
                    'tenant_id' => $row->tenant_id,
                    'plan_name' => $row->plan_name ?? 'Starter',
                    'valid_until' => null,
                    'feature_flags' => $row->features,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $existingTenantUsers = DB::table('tenants')
            ->whereNotNull('admin_email')
            ->select('id', 'admin_email')
            ->get();

        foreach ($existingTenantUsers as $tenantUser) {
            DB::table('tenant_users')->updateOrInsert(
                [
                    'tenant_id' => $tenantUser->id,
                    'email' => $tenantUser->admin_email,
                ],
                [
                    'password_hash' => null,
                    'role' => 'admin',
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_plans');
        Schema::dropIfExists('tenant_users');

        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'subdomain')) {
                $table->dropColumn('subdomain');
            }

            if (Schema::hasColumn('tenants', 'db_name')) {
                $table->dropColumn('db_name');
            }

            if (Schema::hasColumn('tenants', 'db_host')) {
                $table->dropColumn('db_host');
            }
        });
    }
};
