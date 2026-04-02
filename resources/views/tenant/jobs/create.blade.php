<x-layouts.tenant>
    <x-slot name="header">
        New Job Order
    </x-slot>

    <div class="max-w-3xl">
        <form action="{{ route('tenant.jobs.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white shadow-sm rounded-lg border border-slate-200 overflow-hidden">
                <div class="p-6 space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-[#1E3A5F]">Job Title</label>
                        <input type="text" name="title" id="title" required value="{{ old('title') }}"
                               class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm"
                               placeholder="e.g. Electrical Maintenance - Level 2">
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-[#1E3A5F]">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm"
                                  placeholder="Provide detailed instructions for this job...">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="priority" class="block text-sm font-medium text-[#1E3A5F]">Priority</label>
                            <select id="priority" name="priority"
                                    class="mt-1 block w-full bg-white border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority') === 'medium' || !old('priority') ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="deadline_at" class="block text-sm font-medium text-[#1E3A5F]">Deadline (Optional)</label>
                            <input type="date" name="deadline_at" id="deadline_at" value="{{ old('deadline_at') }}"
                                   class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                            @error('deadline_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-[#1E3A5F]">Assign to Worker (Optional)</label>
                        <select id="assigned_to" name="assigned_to"
                                class="mt-1 block w-full bg-white border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                            <option value="">-- Select Worker --</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}" {{ old('assigned_to') == $worker->id ? 'selected' : '' }}>
                                    {{ $worker->name }} ({{ ucfirst($worker->role) }})
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 text-right space-x-3">
                    <a href="{{ route('tenant.jobs.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-800">Cancel</a>
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#0F1B2D] hover:bg-[#1E3A5F] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2D7DD2] transition-colors">
                        Create Job Order
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.tenant>
