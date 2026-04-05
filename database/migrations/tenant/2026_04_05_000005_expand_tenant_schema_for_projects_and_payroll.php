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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('client_name')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->decimal('budget', 14, 2)->nullable();
            $table->timestamps();
        });

        Schema::table('job_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('job_orders', 'project_id')) {
                $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete()->after('id');
            }

            if (! Schema::hasColumn('job_orders', 'jo_number')) {
                $table->string('jo_number')->nullable()->after('project_id');
                $table->index('jo_number');
            }

            if (! Schema::hasColumn('job_orders', 'date_issued')) {
                $table->date('date_issued')->nullable()->after('status');
            }

            if (! Schema::hasColumn('job_orders', 'date_needed')) {
                $table->date('date_needed')->nullable()->after('date_issued');
            }

            if (! Schema::hasColumn('job_orders', 'assigned_foreman_id')) {
                $table->foreignId('assigned_foreman_id')->nullable()->constrained('users')->nullOnDelete()->after('assigned_to');
            }
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->string('full_name');
            $table->string('position')->nullable();
            $table->string('employment_type')->default('regular');
            $table->decimal('daily_rate', 12, 2)->nullable();
            $table->decimal('hourly_rate', 12, 2)->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('jo_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('assigned_date')->nullable();
            $table->string('role_on_job')->nullable();
            $table->timestamps();

            $table->unique(['job_order_id', 'employee_id']);
        });

        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_order_id')->constrained()->onDelete('cascade');
            $table->date('work_date');
            $table->decimal('hours_worked', 8, 2)->default(0);
            $table->string('work_type')->default('regular');
            $table->timestamps();

            $table->index(['employee_id', 'work_date']);
            $table->index(['job_order_id', 'work_date']);
        });

        Schema::table('payroll_periods', function (Blueprint $table) {
            if (! Schema::hasColumn('payroll_periods', 'period_start')) {
                $table->date('period_start')->nullable()->after('name');
            }

            if (! Schema::hasColumn('payroll_periods', 'period_end')) {
                $table->date('period_end')->nullable()->after('period_start');
            }

            if (! Schema::hasColumn('payroll_periods', 'period_type')) {
                $table->string('period_type')->default('weekly')->after('period_end');
            }

            if (! Schema::hasColumn('payroll_periods', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('status');
            }
        });

        Schema::create('payroll_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->decimal('total_hours', 8, 2)->default(0);
            $table->decimal('gross_pay', 14, 2)->default(0);
            $table->decimal('net_pay', 14, 2)->default(0);
            $table->string('payment_status')->default('pending');
            $table->timestamps();

            $table->unique(['payroll_period_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_records');

        Schema::table('payroll_periods', function (Blueprint $table) {
            if (Schema::hasColumn('payroll_periods', 'period_start')) {
                $table->dropColumn('period_start');
            }

            if (Schema::hasColumn('payroll_periods', 'period_end')) {
                $table->dropColumn('period_end');
            }

            if (Schema::hasColumn('payroll_periods', 'period_type')) {
                $table->dropColumn('period_type');
            }

            if (Schema::hasColumn('payroll_periods', 'processed_at')) {
                $table->dropColumn('processed_at');
            }
        });

        Schema::dropIfExists('timesheets');
        Schema::dropIfExists('jo_assignments');
        Schema::dropIfExists('employees');

        Schema::table('job_orders', function (Blueprint $table) {
            if (Schema::hasColumn('job_orders', 'assigned_foreman_id')) {
                $table->dropConstrainedForeignId('assigned_foreman_id');
            }

            if (Schema::hasColumn('job_orders', 'date_needed')) {
                $table->dropColumn('date_needed');
            }

            if (Schema::hasColumn('job_orders', 'date_issued')) {
                $table->dropColumn('date_issued');
            }

            if (Schema::hasColumn('job_orders', 'jo_number')) {
                $table->dropColumn('jo_number');
            }

            if (Schema::hasColumn('job_orders', 'project_id')) {
                $table->dropConstrainedForeignId('project_id');
            }
        });

        Schema::dropIfExists('projects');
    }
};
