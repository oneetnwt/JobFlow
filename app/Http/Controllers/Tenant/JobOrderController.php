<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\JobOrderRequest;
use App\Models\JobOrder;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JobOrderController extends Controller
{
    /**
     * Display a listing of job orders.
     */
    public function index(): View
    {
        $jobs = JobOrder::with(['creator', 'assignee'])
            ->latest()
            ->paginate(15);

        return view('tenant.jobs.index', compact('jobs'));
    }

    /**
     * Show the form for creating a new job order.
     */
    public function create(): View
    {
        $workers = User::where('role', '!=', 'admin')->get();

        return view('tenant.jobs.create', compact('workers'));
    }

    /**
     * Store a newly created job order.
     */
    public function store(JobOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        // If a worker is assigned, status becomes 'assigned'
        if (! empty($validated['assigned_to'])) {
            $validated['status'] = 'assigned';
        }

        JobOrder::create($validated);

        return redirect()->route('tenant.jobs.index')
            ->with('success', 'Job order created successfully.');
    }

    /**
     * Display the specified job order.
     */
    public function show(JobOrder $job): View
    {
        $job->load(['creator', 'assignee']);

        return view('tenant.jobs.show', compact('job'));
    }

    /**
     * Show the form for editing the specified job order.
     */
    public function edit(JobOrder $job): View
    {
        $workers = User::where('role', '!=', 'admin')->get();

        return view('tenant.jobs.edit', compact('job', 'workers'));
    }

    /**
     * Update the specified job order.
     */
    public function update(JobOrderRequest $request, JobOrder $job): RedirectResponse
    {
        $validated = $request->validated();

        // Business logic: if assigned_to changes, update status
        if (isset($validated['assigned_to']) && $validated['assigned_to'] != $job->assigned_to) {
            $validated['status'] = 'assigned';
        }

        // Business logic: if status is completed, set completed_at
        // Ensure all required subtasks are completed before status shift to completed
        if (isset($validated['status']) && $validated['status'] === 'completed' && $job->status !== 'completed') {
            $uncompletedRequiredCount = $job->subtasks()->where('is_required', true)->whereDoesntHave('completion')->count();
            if ($uncompletedRequiredCount > 0) {
                return back()->withErrors(['status' => 'Cannot complete this Job Order until all required subtasks are fulfilled.'])->withInput();
            }
        }
        if (isset($validated['status']) && $validated['status'] === 'completed' && $job->status !== 'completed') {
            $validated['completed_at'] = now();
        }

        $job->update($validated);

        return redirect()->route('tenant.jobs.show', $job)
            ->with('success', 'Job order updated successfully.');
    }

    /**
     * Remove the specified job order.
     */
    public function destroy(JobOrder $job): RedirectResponse
    {
        $job->delete();

        return redirect()->route('tenant.jobs.index')
            ->with('success', 'Job order deleted successfully.');
    }
}
