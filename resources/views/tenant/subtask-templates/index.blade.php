<x-layouts.tenant>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Checklist Templates
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" x-data="{ renameModal: false, deleteModal: false, previewModal: false, activeTemplate: {} }">
        
        @if(session('success'))
            <div class="mb-4 bg-green-50 text-green-800 border-l-4 border-green-500 p-4 text-sm rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-sm border border-slate-200 rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="text-lg font-bold text-[#0F1B2D]">Template Library</h3>
                    <p class="text-xs text-slate-500 mt-1">Saved checklists you can load into Job Orders to save time building structures.</p>
                </div>
            </div>
            
            <div class="divide-y divide-slate-100">
                @forelse($templates as $template)
                    <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50 transition-colors">
                        <div>
                            <h4 class="text-sm font-bold text-[#0F1B2D]">{{ $template->name }}</h4>
                            <p class="text-xs text-slate-500 mt-1">
                                Contains {{ $template->items_count }} item{{ $template->items_count !== 1 ? 's' : '' }}
                            </p>
                            @if($template->creator)
                                <p class="text-[10px] text-slate-400 mt-0.5">Created by {{ $template->creator->name }} on {{ $template->created_at->format('M d, Y') }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="activeTemplate = {{ $template->load('items')->toJson() }}; previewModal = true" class="text-xs font-bold text-slate-600 hover:text-[#2D7DD2] transition-colors">
                                Preview
                            </button>
                            <button @click="activeTemplate = {{ $template->toJson() }}; renameModal = true" class="text-xs font-bold text-[#2D7DD2] hover:text-[#1E3A5F] transition-colors bg-[#2D7DD2]/10 px-3 py-1.5 rounded">
                                Rename
                            </button>
                            <button @click="activeTemplate = {{ $template->toJson() }}; deleteModal = true" class="text-xs font-bold text-red-500 hover:text-red-700 transition-colors bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded">
                                Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-slate-500 italic">
                        No checklist templates saved yet.<br>
                        <span class="text-xs mt-2 block opacity-75">You can save a template directly from any Job Order detail view.</span>
                    </div>
                @endforelse
            </div>

            @if($templates->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $templates->links() }}
                </div>
            @endif
        </div>

        <!-- Rename Modal -->
        <div x-show="renameModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900 opacity-75 transition-opacity" @click="renameModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form :action="`/tenancy/subtask-templates/${activeTemplate.id}`" method="POST">
                        @csrf @method('PATCH')
                        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-bold text-[#0F1B2D] mb-4">Rename Template</h3>
                            <input type="text" name="name" :value="activeTemplate.name" required class="w-full border-slate-300 rounded focus:ring-[#2D7DD2] focus:border-[#2D7DD2] text-sm">
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md px-4 py-2 bg-[#2D7DD2] text-white hover:bg-[#1E3A5F] sm:ml-3 sm:w-auto sm:text-sm font-bold">Update</button>
                            <button type="button" @click="renameModal = false" class="mt-3 w-full inline-flex justify-center rounded-md px-4 py-2 bg-white text-slate-700 border sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <div x-show="previewModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900 opacity-75 transition-opacity" @click="previewModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-bold text-[#0F1B2D] mb-4" x-text="`${activeTemplate.name} — Items Preview`"></h3>
                        <div class="divide-y divide-slate-100 bg-slate-50 border border-slate-200 rounded-md max-h-96 overflow-y-auto">
                            <template x-for="item in activeTemplate.items" :key="item.id">
                                <div class="px-4 py-3 text-sm">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="font-medium text-[#0F1B2D]" x-text="item.title"></p>
                                            <p class="text-xs text-slate-500 mt-1" x-show="item.description" x-text="item.description"></p>
                                        </div>
                                        <span x-show="item.is_required" class="text-[9px] font-bold uppercase tracking-wider text-red-600 bg-red-50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!activeTemplate.items || activeTemplate.items.length === 0">
                                <div class="p-4 text-xs italic text-slate-500">No items available to display.</div>
                            </template>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                        <button type="button" @click="previewModal = false" class="w-full inline-flex justify-center rounded-md px-4 py-2 bg-white text-slate-700 border sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm font-bold">Close Preview</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div x-show="deleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900 opacity-75 transition-opacity" @click="deleteModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form :action="`/tenancy/subtask-templates/${activeTemplate.id}`" method="POST">
                        @csrf @method('DELETE')
                        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-bold text-red-600 mb-2">Delete Template</h3>
                            <p class="text-sm text-slate-600">Are you sure you want to permanently delete the template "<span class="font-bold" x-text="activeTemplate.name"></span>"? This cannot be undone, but will not impact job orders already containing these copied items.</p>
                        </div>
                        <div class="bg-red-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-red-100">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md px-4 py-2 bg-red-600 text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm font-bold">Yes, Delete</button>
                            <button type="button" @click="deleteModal = false" class="mt-3 w-full inline-flex justify-center rounded-md px-4 py-2 bg-white text-slate-700 border sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.tenant>
