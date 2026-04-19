<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. job_order_subtasks table
        Schema::create('job_order_subtasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_order_id')->constrained('job_orders')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. job_order_subtask_completions table
        Schema::create('job_order_subtask_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subtask_id')->constrained('job_order_subtasks')->cascadeOnDelete();
            $table->foreignId('job_order_id')->constrained('job_orders')->cascadeOnDelete();
            $table->foreignId('checked_by')->constrained('users');
            $table->timestamp('checked_at')->useCurrent();
            $table->string('note')->nullable();
            $table->timestamps();

            // unique constraint on subtask_id + job_order_id
            $table->unique(['subtask_id', 'job_order_id']);
        });

        // 3. subtask_templates table
        Schema::create('subtask_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // name of the reusable template
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // 4. subtask_template_items table
        Schema::create('subtask_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('subtask_templates')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->timestamps();
        });

        // 5. job_order_audits
        Schema::create('job_order_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_order_id')->nullable()->constrained('job_orders')->nullOnDelete();
            $table->foreignId('subtask_id')->nullable()->constrained('job_order_subtasks')->nullOnDelete();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // created, checked, unchecked, reordered, deleted, edit
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_order_audits');
        Schema::dropIfExists('subtask_template_items');
        Schema::dropIfExists('subtask_templates');
        Schema::dropIfExists('job_order_subtask_completions');
        Schema::dropIfExists('job_order_subtasks');
    }
};
