<x-layouts.tenant>
    <x-slot name="header">
        Worker Profile
    </x-slot>

    <div class="max-w-4xl space-y-6">
        @if(session('success'))
            <div class="bg-green-50 text-green-800 border-l-4 border-green-500 p-4 text-sm rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-sm rounded-lg border border-slate-200 overflow-hidden">
            <!-- Profile Header -->
            <div class="px-6 py-8 border-b border-slate-100 bg-[#0F1B2D] text-white">
                <div class="flex items-center">
                    <div class="h-20 w-20 rounded-full bg-[#1E3A5F] border-2 border-[#2D7DD2] flex items-center justify-center text-2xl font-bold">
                        {{ substr($worker->name, 0, 1) }}
                    </div>
                    <div class="ml-6">
                        <h2 class="text-2xl font-bold">{{ $worker->name }}</h2>
                        <div class="flex items-center mt-1 space-x-3 text-slate-300">
                            <span class="text-sm">{{ $worker->email }}</span>
                            <span class="text-slate-500">•</span>
                            <span class="text-sm px-2 py-0.5 bg-[#1E3A5F] rounded-full text-[#2D7DD2] font-bold text-[10px] uppercase">
                                {{ $worker->role }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('tenant.workers.edit', $worker) }}" class="inline-flex items-center px-4 py-2 border border-[#2D7DD2] text-sm font-medium rounded-md text-white bg-transparent hover:bg-[#1E3A5F] transition-colors">
                            Edit Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profile Content -->
            <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- Column 1: Employment -->
                <div class="space-y-8">
                    <div>
                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Employment</h4>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-xs text-slate-500">Employee ID</dt>
                                <dd class="text-sm font-bold text-[#0F1B2D]">{{ $worker->profile?->employee_id ?: 'Not assigned' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-500">Department</dt>
                                <dd class="text-sm font-bold text-[#0F1B2D]">{{ $worker->profile?->department ?: 'General' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-500">Type</dt>
                                <dd class="text-sm font-bold text-[#0F1B2D] capitalize">{{ $worker->profile?->employment_type }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-500">Joined Date</dt>
                                <dd class="text-sm font-bold text-[#0F1B2D]">{{ $worker->profile?->joined_at?->format('F d, Y') ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Column 2: Financials & Skills -->
                <div class="space-y-8 md:col-span-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Financials</h4>
                            <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                                <p class="text-xs text-slate-500">Hourly Pay Rate</p>
                                <p class="text-2xl font-bold text-[#0F1B2D] mt-1">${{ number_format($worker->profile?->hourly_rate ?? 0, 2) }}</p>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Contact</h4>
                            <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                                <p class="text-xs text-slate-500">Phone Number</p>
                                <p class="text-sm font-bold text-[#0F1B2D] mt-1">{{ $worker->profile?->phone_number ?: 'No phone provided' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Skills & Certifications</h4>
                        <div class="flex flex-wrap gap-2">
                            @php $skills = array_filter(explode(',', $worker->profile?->skills ?? '')); @endphp
                            @forelse($skills as $skill)
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium bg-[#F9FAFB] text-[#1E3A5F] border border-slate-200">
                                    {{ trim($skill) }}
                                </span>
                            @empty
                                <p class="text-sm text-slate-400 italic">No skills listed.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-8 py-4 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                <form action="{{ route('tenant.workers.destroy', $worker) }}" method="POST" onsubmit="return confirm('Permanently remove this worker and all associated profile data?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700 transition-colors uppercase tracking-tight">
                        Terminate Records
                    </button>
                </form>
                <a href="{{ route('tenant.workers.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-tight">
                    Back to Directory
                </a>
            </div>
        </div>
    </div>
</x-layouts.tenant>
