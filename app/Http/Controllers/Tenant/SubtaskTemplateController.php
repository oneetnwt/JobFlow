<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\JobOrder;
use App\Models\JobOrderAudit;
use App\Models\JobOrderSubtask;
use App\Models\SubtaskTemplate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubtaskTemplateController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', SubtaskTemplate::class);

        $templates = SubtaskTemplate::withCount('items')->latest()->paginate(20);

        return view('tenant.subtask-templates.index', compact('templates'));
    }

    /**
     * Save the current job order's checklist as a new template.
     */
    public function saveFromJob(Request $request, JobOrder $job): RedirectResponse
    {
        $this->authorize('create', SubtaskTemplate::class);

        $validated = $request->validate([
            'template_name' => ['required', 'string', 'max:255'],
        ]);

        if ($job->subtasks()->count() === 0) {
            return back()->withErrors(['error' => 'Cannot save an empty checklist as a template.']);
        }

        $template = SubtaskTemplate::create([
            'name' => $validated['template_name'],
            'created_by' => auth()->id(),
        ]);

        foreach ($job->subtasks as $subtask) {
            $template->items()->create([
                'title' => $subtask->title,
                'description' => $subtask->description,
                'order' => $subtask->order,
                'is_required' => $subtask->is_required,
            ]);
        }

        JobOrderAudit::create([
            'job_order_id' => $job->id,
            'performed_by' => auth()->id(),
            'action' => 'template_saved',
            'new_value' => json_encode(['template_id' => $template->id, 'name' => $template->name]),
        ]);

        return back()->with('success', 'Checklist saved as a reusable template.');
    }

    /**
     * Load a saved template into the specified job order.
     */
    public function loadIntoJob(Request $request, JobOrder $job): RedirectResponse
    {
        // Require both template load permission and subtask create permission
        $this->authorize('viewAny', SubtaskTemplate::class);
        $this->authorize('create', JobOrderSubtask::class);

        if (in_array($job->status, ['completed', 'archived'])) {
            return back()->withErrors(['error' => 'Cannot modify checklists on a completed or archived job order.']);
        }

        $validated = $request->validate([
            'template_id' => ['required', 'exists:subtask_templates,id'],
        ]);

        $template = SubtaskTemplate::with('items')->findOrFail($validated['template_id']);

        if ($template->items->isEmpty()) {
            return back()->withErrors(['error' => 'The selected template is empty.']);
        }

        $maxOrder = $job->subtasks()->max('order') ?? -1;

        foreach ($template->items as $item) {
            $maxOrder++;
            $job->subtasks()->create([
                'title' => $item->title,
                'description' => $item->description,
                'order' => $maxOrder,
                'is_required' => $item->is_required,
                'created_by' => auth()->id(),
            ]);
        }

        JobOrderAudit::create([
            'job_order_id' => $job->id,
            'performed_by' => auth()->id(),
            'action' => 'template_loaded',
            'new_value' => json_encode(['template_id' => $template->id, 'name' => $template->name]),
        ]);

        return back()->with('success', 'Template checklist loaded successfully.');
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, SubtaskTemplate $template): RedirectResponse
    {
        $this->authorize('update', $template);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $template->update($validated);

        return back()->with('success', 'Template renamed successfully.');
    }

    /**
     * Remove the specified template.
     */
    public function destroy(SubtaskTemplate $template): RedirectResponse
    {
        $this->authorize('delete', $template);

        $template->delete();

        return back()->with('success', 'Template deleted successfully.');
    }
}
