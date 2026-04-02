<x-layouts.tenant>
    <x-slot name="header">
        Edit Job Order
    </x-slot>

    <div class="max-w-3xl">
        <form action="{{ route('tenant.jobs.update', $job) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white shadow-sm rounded-lg border border-slate-200 overflow-hidden">
                <div class="p-6 space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-[#1E3A5F]">Job Title</label>
                        <input type="text" name="title" id="title" required value="{{ old('title', $job->title) }}"
                               class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-[#1E3A5F]">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">{{ old('description', $job->description) }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="status" class="block text-sm font-medium text-[#1E3A5F]">Status</label>
                            <select id="status" name="status"
                                    class="mt-1 block w-full bg-white border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                                <option value="draft" {{ old('status', $job->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="open" {{ old('status', $job->status) === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="assigned" {{ old('status', $job->status) === 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="in_progress" {{ old('status', $job->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $job->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $job->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="priority" class="block text-sm font-medium text-[#1E3A5F]">Priority</label>
                            <select id="priority" name="priority"
                                    class="mt-1 block w-full bg-white border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                                <option value="low" {{ old('priority', $job->priority) === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', $job->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority', $job->priority) === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority', $job->priority) === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="deadline_at" class="block text-sm font-medium text-[#1E3A5F]">Deadline</label>
                            <input type="date" name="deadline_at" id="deadline_at" value="{{ old('deadline_at', $job->deadline_at?->format('Y-m-d')) }}"
                                   class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                            @error('deadline_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="assigned_to" class="block text-sm font-medium text-[#1E3A5F]">Assignee</label>
                            <select id="assigned_to" name="assigned_to"
                                    class="mt-1 block w-full bg-white border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                                <option value="">-- Unassigned --</option>
                                @foreach($workers as $worker)
                                    <option value="{{ $worker->id }}" {{ old('assigned_to', $job->assigned_to) == $worker->id ? 'selected' : '' }}>
                                        {{ $worker->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 text-right space-x-3">
                    <a href="{{ route('tenant.jobs.show', $job) }}" class="text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Cancel</a>
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#0F1B2D] hover:bg-[#1E3A5F] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2D7DD2] transition-colors">
                        Update Job Order
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.tenant>
