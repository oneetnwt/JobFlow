<x-layouts.tenant>
    <x-slot name="header">
        Job Details
    </x-slot>

    <div class="max-w-4xl space-y-6">
        @if(session('success'))
            <div class="bg-green-50 text-green-800 border-l-4 border-green-500 p-4 text-sm rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-sm rounded-lg border border-slate-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="text-lg font-bold text-[#0F1B2D]">{{ $job->title }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Order #{{ str_pad((string)$job->id, 5, '0', STR_PAD_LEFT) }} • Created on {{ $job->created_at->format('M d, Y') }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('tenant.jobs.edit', $job) }}" class="inline-flex items-center px-3 py-1.5 border border-slate-300 shadow-sm text-xs font-medium rounded text-[#1E3A5F] bg-white hover:bg-slate-50 focus:outline-none transition-colors">
                        Edit Job
                    </a>
                </div>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="md:col-span-2 space-y-6">
                    <!-- Progress Section -->
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Job Progress</h4>
                            <span class="text-xs font-bold text-[#2D7DD2]">{{ $job->progress }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-1.5">
                            <div class="bg-[#2D7DD2] h-1.5 rounded-full transition-all duration-500" style="width: {{ $job->progress }}%"></div>
                        </div>
                    </div>

                    <!-- Tasks Checklist -->
                    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                            <h4 class="text-xs font-bold text-[#0F1B2D] uppercase tracking-wider">Sub-Tasks Checklist</h4>
                            <span class="text-[10px] text-slate-500 font-medium">{{ $job->tasks->where('status', 'completed')->count() }} / {{ $job->tasks->count() }} Done</span>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse($job->tasks as $task)
                                <div class="px-4 py-3 flex items-center hover:bg-slate-50 group transition-colors">
                                    <form action="{{ route('tenant.tasks.toggle', $task) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex-shrink-0">
                                            @if($task->status === 'completed')
                                                <div class="h-5 w-5 rounded border border-[#2D7DD2] bg-[#2D7DD2] flex items-center justify-center">
                                                    <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="h-5 w-5 rounded border border-slate-300 bg-white hover:border-[#2D7DD2] transition-colors"></div>
                                            @endif
                                        </button>
                                    </form>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm {{ $task->status === 'completed' ? 'text-slate-400 line-through' : 'text-[#0F1B2D] font-medium' }}">
                                            {{ $task->title }}
                                        </p>
                                    </div>
                                    <form action="{{ route('tenant.tasks.destroy', $task) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-500">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="px-4 py-8 text-center text-slate-400 text-xs italic">
                                    No sub-tasks added yet.
                                </div>
                            @endforelse
                        </div>
                        <div class="p-4 border-t border-slate-100 bg-slate-50/30">
                            <form action="{{ route('tenant.tasks.store', $job) }}" method="POST" class="flex space-x-2">
                                @csrf
                                <input type="text" name="title" required placeholder="Add new task..." 
                                       class="flex-1 text-xs border-slate-200 rounded-md focus:ring-[#2D7DD2] focus:border-[#2D7DD2]">
                                <button type="submit" class="px-3 py-1.5 bg-[#0F1B2D] text-white rounded-md text-xs font-bold hover:bg-[#1E3A5F] transition-colors">
                                    Add
                                </button>
                            </form>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Description</h4>
                        <div class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-md border border-slate-100">
                            {!! nl2br(e($job->description)) ?: '<span class="text-slate-400 italic">No description provided.</span>' !!}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Created By</h4>
                            <p class="text-sm text-[#0F1B2D] font-medium">{{ $job->creator->name }}</p>
                            <p class="text-xs text-slate-500">{{ $job->creator->email }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Assigned To</h4>
                            @if($job->assignee)
                                <p class="text-sm text-[#0F1B2D] font-medium">{{ $job->assignee->name }}</p>
                                <p class="text-xs text-slate-500">{{ ucfirst($job->assignee->role) }}</p>
                            @else
                                <p class="text-sm text-slate-400 italic">No worker assigned</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-6 border-l border-slate-100 md:pl-8">
                    <div>
                        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Current Status</h4>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full 
                            {{ $job->status === 'completed' ? 'bg-green-100 text-green-800 border border-green-200' : '' }}
                            {{ $job->status === 'open' ? 'bg-blue-100 text-blue-800 border border-blue-200' : '' }}
                            {{ $job->status === 'assigned' ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : '' }}
                            {{ $job->status === 'cancelled' ? 'bg-red-100 text-red-800 border border-red-200' : '' }}
                            {{ $job->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : '' }}
                        ">
                            {{ strtoupper(str_replace('_', ' ', $job->status)) }}
                        </span>
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Priority</h4>
                        <span class="text-sm font-bold
                            {{ $job->priority === 'urgent' ? 'text-red-600' : '' }}
                            {{ $job->priority === 'high' ? 'text-orange-600' : '' }}
                            {{ $job->priority === 'medium' ? 'text-slate-600' : '' }}
                            {{ $job->priority === 'low' ? 'text-slate-400' : '' }}
                        ">
                            {{ strtoupper($job->priority) }}
                        </span>
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Deadline</h4>
                        <p class="text-sm {{ $job->deadline_at && $job->deadline_at->isPast() && $job->status !== 'completed' ? 'text-red-600 font-bold' : 'text-[#0F1B2D]' }}">
                            {{ $job->deadline_at?->format('F d, Y') ?? 'No deadline set' }}
                        </p>
                    </div>

                    @if($job->completed_at)
                        <div>
                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Completed On</h4>
                            <p class="text-sm text-green-600 font-medium">
                                {{ $job->completed_at->format('F d, Y • h:i A') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-between">
                <form action="{{ route('tenant.jobs.destroy', $job) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this job order?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800 transition-colors">
                        Delete Job Order
                    </button>
                </form>
                <a href="{{ route('tenant.jobs.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-700 transition-colors">
                    Back to List
                </a>
            </div>
        </div>
    </div>
</x-layouts.tenant>
