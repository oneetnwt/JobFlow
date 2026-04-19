@php
    $audits = $job->audits()->with('performer', 'subtask')->latest()->get();
@endphp

@if(auth()->user()->can('subtasks.create') || auth()->user()->hasRole('admin'))
<div class="mt-8 bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden" x-data="{ showLogs: false }">
    <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center cursor-pointer" @click="showLogs = !showLogs">
        <h4 class="text-xs font-bold text-[#0F1B2D] uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Activity Log
            <span class="text-[10px] bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full ml-1">{{ $audits->count() }}</span>
        </h4>
        <button class="text-slate-400 hover:text-slate-600">
            <svg class="h-4 w-4 transform transition-transform duration-200" :class="{'rotate-180': showLogs}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>
    </div>

    <div x-show="showLogs" class="divide-y divide-slate-100 max-h-64 overflow-y-auto" style="display: none;">
        @forelse($audits as $audit)
            <div class="px-4 py-3 hover:bg-slate-50 transition-colors text-sm">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 opacity-50 flex-shrink-0">
                        @if($audit->action === 'created')
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        @elseif($audit->action === 'checked')
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @elseif($audit->action === 'unchecked')
                            <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @elseif($audit->action === 'deleted')
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-slate-800">
                            <span class="font-bold border-b border-dashed border-slate-300 pb-0.5">{{ $audit->performer->name ?? 'Unknown User' }}</span> 
                            <span class="text-slate-600">{{ $audit->action }}</span> 
                            subtask: <span class="italic text-slate-500">"{{ $audit->subtask ? $audit->subtask->title : ($audit->old_value ? json_decode($audit->old_value)->title ?? 'Deleted item' : 'Unknown item') }}"</span>
                        </p>
                        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider font-semibold">{{ $audit->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-4 py-8 text-center text-slate-400 text-xs italic">
                No activity recorded yet.
            </div>
        @endforelse
    </div>
</div>
@endif
