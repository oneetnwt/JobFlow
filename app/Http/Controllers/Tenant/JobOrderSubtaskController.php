<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\JobOrder;
use App\Models\JobOrderAudit;
use App\Models\JobOrderSubtask;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobOrderSubtaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Store a newly created subtask for a specific job order.
     */
    public function store(Request $request, JobOrder $job): RedirectResponse
    {
        $this->authorize('create', JobOrderSubtask::class);

        if (in_array($job->status, ['completed', 'archived'])) {
            return back()->withErrors(['error' => 'Cannot add subtasks to a completed or archived job order.']);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        $maxOrder = $job->subtasks()->max('order') ?? -1;

        $subtask = $job->subtasks()->create([
            ...$validated,
            'is_required' => $request->boolean('is_required', false),
            'order' => $maxOrder + 1,
            'created_by' => auth()->id(),
        ]);

        $this->logAudit($job->id, clone $subtask, 'created', null, $subtask->toJson());

        return back()->with('success', 'Subtask added successfully.');
    }

    /**
     * Update the specified subtask.
     */
    public function update(Request $request, JobOrder $job, JobOrderSubtask $subtask): RedirectResponse
    {
        $this->authorize('update', $subtask);

        if (in_array($job->status, ['completed', 'archived'])) {
            return back()->withErrors(['error' => 'Cannot edit subtasks on a completed or archived job order.']);
        }

        if ($subtask->job_order_id !== $job->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        $oldValue = $subtask->toJson();

        $subtask->update([
            ...$validated,
            'is_required' => $request->boolean('is_required', false),
            'updated_by' => auth()->id(),
        ]);

        $this->logAudit($job->id, $subtask, 'edit', $oldValue, $subtask->toJson());

        return back()->with('success', 'Subtask updated successfully.');
    }

    /**
     * Remove the specified subtask.
     */
    public function destroy(Request $request, JobOrder $job, JobOrderSubtask $subtask): RedirectResponse
    {
        $this->authorize('delete', $subtask);

        if (in_array($job->status, ['completed', 'archived'])) {
            return back()->withErrors(['error' => 'Cannot delete subtasks from a completed or archived job order.']);
        }

        if ($subtask->job_order_id !== $job->id) {
            abort(404);
        }

        if ($subtask->completion()->exists() && ! $request->has('confirm_deletion')) {
            $checker = $subtask->completion->checker;

            return back()->withErrors([
                'confirm_subtask_deletion' => "Subtask '{$subtask->title}' has already been checked by {$checker->name}. Are you sure you want to delete it?",
            ])->withInput(['subtask_id_to_delete' => $subtask->id]);
        }

        $oldValue = $subtask->toJson();
        $subtask->delete();

        $this->logAudit($job->id, $subtask, 'deleted', $oldValue, null);

        return back()->with('success', 'Subtask deleted successfully.');
    }

    /**
     * Reorder subtasks via AJAX.
     */
    public function reorder(Request $request, JobOrder $job): JsonResponse
    {
        $this->authorize('reorder', JobOrderSubtask::class);

        if (in_array($job->status, ['completed', 'archived'])) {
            return response()->json(['error' => 'Cannot reorder subtasks on a completed or archived job order.'], 403);
        }

        $validated = $request->validate([
            'ordered_ids' => ['required', 'array'],
            'ordered_ids.*' => ['integer', 'exists:job_order_subtasks,id'],
        ]);

        $order = 0;
        foreach ($validated['ordered_ids'] as $subtaskId) {
            $subtask = $job->subtasks()->find($subtaskId);
            if ($subtask) {
                $subtask->update(['order' => $order]);
                $order++;
            }
        }

        $this->logAudit($job->id, null, 'reordered', null, json_encode($validated['ordered_ids']));

        return response()->json(['message' => 'Subtasks reordered successfully.']);
    }

    /**
     * Check or uncheck a subtask item via AJAX.
     */
    public function toggle(Request $request, JobOrder $job, JobOrderSubtask $subtask): JsonResponse
    {
        $this->authorize('check', JobOrderSubtask::class);

        if (in_array($job->status, ['completed', 'archived'])) {
            return response()->json(['error' => 'Cannot check or uncheck items on a completed or archived job order.'], 403);
        }

        if ($subtask->job_order_id !== $job->id) {
            abort(404);
        }

        $validated = $request->validate([
            'checked' => ['required', 'boolean'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validated['checked']) {
            if (! $subtask->completion()->exists()) {
                $subtask->completion()->create([
                    'job_order_id' => $job->id,
                    'checked_by' => auth()->id(),
                    'note' => $validated['note'] ?? null,
                ]);
                $this->logAudit($job->id, $subtask, 'checked', null, $validated['note'] ?? null);
            }
        } else {
            if ($subtask->completion()->exists()) {
                $completion = $subtask->completion;
                $oldNote = $completion->note;
                $completion->delete();

                $this->logAudit($job->id, $subtask, 'unchecked', $oldNote, null);
            }
        }

        // Re-calculate completion logic if needed, but the model has `getProgressAttribute` naturally dynamically calculating this right now.

        return response()->json([
            'message' => 'Subtask updated successfully.',
            'progress' => $job->progress,
        ]);
    }

    /**
     * Helper to log actions to the job_order_audits table.
     */
    private function logAudit($jobId, ?JobOrderSubtask $subtask, $action, $oldValue = null, $newValue = null)
    {
        JobOrderAudit::create([
            'job_order_id' => $jobId,
            'subtask_id' => $subtask?->id,
            'performed_by' => auth()->id(),
            'action' => $action,
            'old_value' => $oldValue,
            'new_value' => $newValue,
        ]);
    }
}
