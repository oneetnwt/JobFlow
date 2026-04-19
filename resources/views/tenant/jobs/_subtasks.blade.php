@php
    $canManage = auth()->user()->can('subtasks.create');
    $canCheck = auth()->user()->can('subtasks.check');
    $subtasks = $job->subtasks;
    $completedCount = $job->subtaskCompletions()->count();
    $totalCount = $subtasks->count();
@endphp

<div class="bg-white border border-slate-200 rounded-lg shadow-sm" x-data="subtaskManager({{ $job->id }})">
    <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
        <h4 class="text-xs font-bold text-[#0F1B2D] uppercase tracking-wider">
            Checklist 
            @if($totalCount > 0)
                <span class="text-[10px] bg-[#2D7DD2] text-white px-2 py-0.5 rounded-full ml-2" x-text="`${completedCount} / ${totalCount}`">{{ $completedCount }} / {{ $totalCount }}</span>
            @endif
        </h4>
        @if($canManage && !in_array($job->status, ['completed', 'archived']))
            <div class="flex items-center gap-2">
                <div x-data="{ templateDropdown: false }" class="relative">
                    <button @click="templateDropdown = !templateDropdown" @click.away="templateDropdown = false" class="text-slate-500 hover:text-slate-700 text-xs font-bold flex items-center gap-1 transition-colors border border-slate-200 px-2 py-1 rounded bg-white shadow-sm">
                        Templates
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="templateDropdown" style="display: none;" class="absolute right-0 mt-1 w-48 bg-white border border-slate-200 rounded-md shadow-lg z-10 py-1">
                        <button @click="templateDropdown = false; openLoadModal = true" class="block w-full text-left px-4 py-2 text-xs text-slate-700 font-bold hover:bg-slate-50 transition-colors">
                            Load Template
                        </button>
                        @if($totalCount > 0)
                            <button @click="templateDropdown = false; openSaveModal = true" class="block w-full text-left px-4 py-2 text-xs text-slate-700 font-bold hover:bg-slate-50 transition-colors border-t border-slate-100">
                                Save as Template
                            </button>
                        @endif
                        <a href="{{ route('tenant.subtask-templates.index') }}" class="block w-full text-left px-4 py-2 text-xs text-[#2D7DD2] font-bold hover:bg-slate-50 transition-colors border-t border-slate-100">
                            Manage Templates &rarr;
                        </a>
                    </div>
                </div>

                <button @click="openModal = true; isEditing = false; currentSubtask = {}" 
                    class="text-[#2D7DD2] hover:text-[#1E3A5F] text-xs font-bold flex items-center gap-1 transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Item
                </button>
            </div>
        @endif
    </div>

    <!-- Errors -->
    @if ($errors->any())
        <div class="bg-red-50 text-red-800 p-3 text-xs border-b border-red-100">
            <ul class="list-disc pl-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="divide-y divide-slate-100" id="subtask-list" x-ref="list">
        @forelse($subtasks as $subtask)
            @php $isChecked = $subtask->completion()->exists(); @endphp
            <div class="px-4 py-3 flex items-start group transition-colors hover:bg-slate-50" data-id="{{ $subtask->id }}">
                
                @if($canManage && !in_array($job->status, ['completed', 'archived']))
                    <div class="mr-2 mt-1 cursor-grab text-slate-300 hover:text-slate-500 drag-handle">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                    </div>
                @endif

                <div class="mt-0.5 flex-shrink-0">
                    <button type="button" 
                        @click="toggleSubtask({{ $subtask->id }}, {{ $isChecked ? 'true' : 'false' }})"
                        @if(!$canCheck || in_array($job->status, ['completed', 'archived'])) disabled @endif
                        class="h-5 w-5 rounded border flex items-center justify-center transition-colors
                            {{ $isChecked ? 'bg-[#2D7DD2] border-[#2D7DD2]' : 'bg-white border-slate-300 hover:border-[#2D7DD2]' }}
                            {{ !$canCheck || in_array($job->status, ['completed', 'archived']) ? 'opacity-50 cursor-not-allowed' : '' }}">
                        @if($isChecked)
                            <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                    </button>
                </div>
                
                <div class="ml-3 flex-1">
                    <div class="flex items-center gap-2">
                        <p class="text-sm {{ $isChecked ? 'text-slate-400 line-through' : 'text-[#0F1B2D] font-medium' }}">
                            {{ $subtask->title }}
                        </p>
                        @if($subtask->is_required)
                            <span class="text-[9px] font-bold uppercase tracking-wider text-red-600 bg-red-50 px-1.5 py-0.5 rounded">Required</span>
                        @endif
                    </div>
                    @if($subtask->description)
                        <p class="text-xs text-slate-500 mt-1 {{ $isChecked ? 'opacity-50' : '' }}">{{ $subtask->description }}</p>
                    @endif

                    @if($isChecked && $canManage)
                        <p class="text-[10px] text-slate-400 mt-2 italic">
                            Completed by {{ $subtask->completion->checker->name }} on {{ $subtask->completion->checked_at->format('M d, Y h:ia') }}
                        </p>
                    @endif
                </div>

                @if($canManage && !in_array($job->status, ['completed', 'archived']))
                    <div class="ml-4 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-2">
                        <button type="button" @click="editSubtask({{ $subtask->toJson() }})" class="text-slate-400 hover:text-[#2D7DD2]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <form action="{{ route('tenant.subtasks.destroy', [$job, $subtask]) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            @if(session('confirm_subtask_deletion') && old('subtask_id_to_delete') == $subtask->id)
                                <input type="hidden" name="confirm_deletion" value="1">
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold border border-red-200 px-2 rounded bg-red-50">Confirm Delete</button>
                            @else
                                <button type="submit" class="text-slate-400 hover:text-red-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            @endif
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="px-4 py-8 text-center text-slate-400 text-xs italic">
                No checklist items defined yet.
            </div>
        @endforelse
    </div>

    <!-- Admin Modals -->
    @if($canManage)
        <!-- Add/Edit Modal -->
        <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="openModal" class="fixed inset-0 transition-opacity" @click="openModal = false">
                    <div class="absolute inset-0 bg-slate-900 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="openModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form :action="isEditing ? `{{ url('jobs/'.$job->id.'/subtasks') }}/${currentSubtask.id}` : `{{ route('tenant.subtasks.store', $job) }}`" method="POST">
                        @csrf
                        <template x-if="isEditing"><input type="hidden" name="_method" value="PATCH"></template>
                        
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-bold text-[#0F1B2D] mb-4" x-text="isEditing ? 'Edit Checklist Item' : 'Add Checklist Item'"></h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Title <span class="text-red-500">*</span></label>
                                    <input type="text" name="title" x-model="currentSubtask.title" required class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Description (Optional)</label>
                                    <textarea name="description" x-model="currentSubtask.description" rows="2" class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm text-slate-600"></textarea>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_required" id="is_required" value="1" x-model="currentSubtask.is_required" class="h-4 w-4 text-[#2D7DD2] focus:ring-[#2D7DD2] border-slate-300 rounded">
                                    <label for="is_required" class="ml-2 block text-sm text-slate-700 font-medium">
                                        Required for completion
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#0F1B2D] text-base font-medium text-white hover:bg-[#1E3A5F] focus:outline-none sm:ml-3 sm:w-auto sm:text-sm font-bold transition-colors">
                                Save
                            </button>
                            <button type="button" @click="openModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Save Template Modal -->
        @if($totalCount > 0)
        <div x-show="openSaveModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="openSaveModal" class="fixed inset-0 transition-opacity" @click="openSaveModal = false">
                    <div class="absolute inset-0 bg-slate-900 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="openSaveModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form action="{{ route('tenant.subtask-templates.save-from-job', $job) }}" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-bold text-[#0F1B2D] mb-4">Save Checklist as Template</h3>
                            <p class="text-sm text-slate-600 mb-4">Save these {{ $totalCount }} items as a reusable template to load into future job orders.</p>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Template Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required placeholder="e.g. Standard Website Setup" class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#2D7DD2] text-base font-medium text-white hover:bg-[#1E3A5F] focus:outline-none sm:ml-3 sm:w-auto sm:text-sm font-bold transition-colors">
                                Save Template
                            </button>
                            <button type="button" @click="openSaveModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- Load Template Modal -->
        <div x-show="openLoadModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="openLoadModal" class="fixed inset-0 transition-opacity" @click="openLoadModal = false">
                    <div class="absolute inset-0 bg-slate-900 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="openLoadModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form action="{{ route('tenant.subtask-templates.load-into-job', $job) }}" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-bold text-[#0F1B2D] mb-4">Load Template</h3>
                            <p class="text-sm text-slate-600 mb-4">Append predefined checklist items to this job order.</p>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Select Template <span class="text-red-500">*</span></label>
                                @php
                                    $availableTemplates = \App\Models\SubtaskTemplate::all();
                                @endphp
                                @if($availableTemplates->count() > 0)
                                    <select name="template_id" required class="mt-1 block w-full pl-3 pr-10 py-2 border-slate-300 text-base focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm rounded-md">
                                        <option value="" disabled selected>Choose a template...</option>
                                        @foreach($availableTemplates as $template)
                                            <option value="{{ $template->id }}">{{ $template->name }} ({{ $template->items()->count() }} items)</option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="mt-1 p-3 bg-yellow-50 text-yellow-800 text-sm rounded border border-yellow-200">
                                        No templates available yet.
                                    </div>
                                @endif
                                <div class="mt-3 flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="replace" name="replace" type="checkbox" value="1" class="focus:ring-[#2D7DD2] h-4 w-4 text-[#2D7DD2] border-slate-300 rounded">
                                    </div>
                                    <div class="ml-2 text-sm">
                                        <label for="replace" class="font-medium text-slate-700">Clear existing checklist</label>
                                        <p class="text-slate-500 text-xs mt-0.5">Warning: This will delete the current incomplete items on the job.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                            <button type="submit" @if($availableTemplates->isEmpty()) disabled @endif class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#2D7DD2] text-base font-medium text-white hover:bg-[#1E3A5F] focus:outline-none sm:ml-3 sm:w-auto sm:text-sm font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                Load Items
                            </button>
                            <button type="button" @click="openLoadModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('subtaskManager', (jobId) => ({
            openModal: false,
            openSaveModal: false,
            openLoadModal: false,
            isEditing: false,
            currentSubtask: {},
            totalCount: {{ $totalCount }},
            completedCount: {{ $completedCount }},

            init() {
                @if($canManage && !in_array($job->status, ['completed', 'archived']))
                    new Sortable(this.$refs.list, {
                        handle: '.drag-handle',
                        animation: 150,
                        ghostClass: 'bg-slate-100',
                        onEnd: (evt) => this.saveOrder()
                    });
                @endif
            },

            editSubtask(subtask) {
                this.currentSubtask = subtask;
                this.currentSubtask.is_required = subtask.is_required == 1;
                this.isEditing = true;
                this.openModal = true;
            },

            async toggleSubtask(subtaskId, isCurrentlyChecked) {
                // Optimistic UI update
                const newStatus = !isCurrentlyChecked;
                
                try {
                    const response = await fetch(`/tenancy/jobs/${jobId}/subtasks/${subtaskId}/toggle`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ checked: newStatus })
                    });

                    if (response.ok) {
                        window.location.reload(); // Reload cleanly to update the progress bar securely and prevent state desync
                    } else {
                        alert('Could not update checklist item. Please check your permissions.');
                        window.location.reload();
                    }
                } catch (error) {
                    console.error('Toggle Error:', error);
                }
            },

            async saveOrder() {
                const items = Array.from(this.$refs.list.children);
                const orderedIds = items.map(item => item.getAttribute('data-id')).filter(id => id !== null);

                try {
                    await fetch(`/tenancy/jobs/${jobId}/subtasks/reorder`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ ordered_ids: orderedIds })
                    });
                } catch (error) {
                    console.error('Reorder Error:', error);
                }
            }
        }));
    });
</script>
