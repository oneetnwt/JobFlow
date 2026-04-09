<x-layouts.tenant>
    <x-slot name="header">
        Worker Directory
    </x-slot>

    <div class="flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <p class="text-sm text-slate-500">View and manage all employees and contractors in your workspace.</p>
            <a href="{{ route('tenant.workers.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#0F1B2D] hover:bg-[#1E3A5F] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2D7DD2] transition-colors">
                Add Worker
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 text-green-800 border-l-4 border-green-500 p-4 text-sm rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($workers as $worker)
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-slate-200 hover:border-[#2D7DD2]/50 transition-all group">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center text-[#0F1B2D] font-bold text-lg group-hover:bg-[#2D7DD2] group-hover:text-white transition-colors">
                                {{ substr($worker->name, 0, 1) }}
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-bold text-[#0F1B2D]">{{ $worker->name }}</h3>
                                <p class="text-xs text-slate-500">{{ $worker->profile?->department ?? 'General' }}</p>
                            </div>
                            <div>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $worker->role === 'manager' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ strtoupper($worker->role) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="mt-6 border-t border-slate-50 pt-4 space-y-2">
                            <div class="flex items-center text-xs text-slate-500">
                                <span class="font-semibold w-24">Employee ID:</span>
                                <span>{{ $worker->profile?->employee_id ?: 'N/A' }}</span>
                            </div>
                            <div class="flex items-center text-xs text-slate-500">
                                <span class="font-semibold w-24">Type:</span>
                                <span class="capitalize">{{ $worker->profile?->employment_type }}</span>
                            </div>
                            <div class="flex items-center text-xs text-slate-500">
                                <span class="font-semibold w-24">Hourly Rate:</span>
                                <span class="text-[#0F1B2D] font-medium">₱{{ number_format($worker->profile?->hourly_rate ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-3 flex justify-between items-center border-t border-slate-100">
                        <div class="text-[10px] text-slate-400">
                            Joined {{ $worker->profile?->joined_at?->format('M Y') ?? 'N/A' }}
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('tenant.workers.show', $worker) }}" class="text-xs font-medium text-[#2D7DD2] hover:text-[#1E3A5F]">Profile</a>
                            <a href="{{ route('tenant.workers.edit', $worker) }}" class="text-xs font-medium text-slate-400 hover:text-slate-600">Edit</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white py-12 px-4 shadow-sm rounded-lg border border-slate-200 text-center">
                    <p class="text-sm text-slate-500">No workers found in the directory.</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-8">
            {{ $workers->links() }}
        </div>
    </div>
</x-layouts.tenant>
