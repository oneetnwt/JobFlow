<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\JobOrder;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Store a newly created task for a specific job order.
     */
    public function store(Request $request, JobOrder $job): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $job->tasks()->create($validated);

        return back()->with('success', 'Task added successfully.');
    }

    /**
     * Toggle the status of a task.
     */
    public function toggle(Task $task): RedirectResponse
    {
        if ($task->status === 'completed') {
            $task->update([
                'status' => 'pending',
                'completed_at' => null,
            ]);
        } else {
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        // Auto-update job status if needed
        $job = $task->jobOrder;
        if ($job->progress === 100 && $job->status !== 'completed') {
            $job->update(['status' => 'completed', 'completed_at' => now()]);
        }

        return back();
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return back()->with('success', 'Task removed.');
    }
}
