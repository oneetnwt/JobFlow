<?php

use App\Models\JobOrder;
use App\Models\JobOrderSubtask;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\TenantRBACSeeder;

beforeEach(function () {
    $this->tenant = Tenant::create(['id' => 'test-tenant']);
    tenancy()->initialize($this->tenant);

    // Seed essential roles and permissions since testing resets DB
    app(TenantRBACSeeder::class)->run();

    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->admin->assignRole('admin');

    $this->worker = User::factory()->create(['role' => 'worker']);
    $this->worker->assignRole('worker');

    $this->job = JobOrder::create([
        'title' => 'Test Job',
        'status' => 'open',
        'priority' => 'high',
        'created_by' => $this->admin->id,
        'assigned_to' => $this->worker->id,
    ]);
});

afterEach(function () {
    tenancy()->end();
});

it('allows admin to create a subtask', function () {
    $response = $this->actingAs($this->admin)->post(route('tenant.subtasks.store', $this->job), [
        'title' => 'Admin Subtask',
        'is_required' => true,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('job_order_subtasks', [
        'job_order_id' => $this->job->id,
        'title' => 'Admin Subtask',
        'is_required' => 1,
    ]);
});

it('prevents worker from creating a subtask', function () {
    $response = $this->actingAs($this->worker)->post(route('tenant.subtasks.store', $this->job), [
        'title' => 'Worker Subtask',
        'is_required' => true,
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('job_order_subtasks', [
        'title' => 'Worker Subtask',
    ]);
});

it('allows worker to check off a subtask', function () {
    $subtask = JobOrderSubtask::create([
        'job_order_id' => $this->job->id,
        'title' => 'Task to Check',
        'order_index' => 0,
        'is_required' => true,
    ]);

    $response = $this->actingAs($this->worker)->post(route('tenant.subtasks.toggle', [$this->job, $subtask]), [
        'checked' => true,
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('job_order_subtask_completions', [
        'subtask_id' => $subtask->id,
        'checked_by' => $this->worker->id,
    ]);
});

it('fails to complete a job if required subtasks are unchecked', function () {
    $subtask = JobOrderSubtask::create([
        'job_order_id' => $this->job->id,
        'title' => 'Required Task',
        'order_index' => 0,
        'is_required' => true,
    ]);

    $response = $this->actingAs($this->admin)->patch(route('tenant.jobs.update', $this->job), [
        'title' => 'Test Job',
        'status' => 'completed',
        'priority' => 'high',
    ]);

    $response->assertSessionHasErrors(['status']); // Should return back with validation error on status

    expect($this->job->fresh()->status)->not->toBe('completed');
});
