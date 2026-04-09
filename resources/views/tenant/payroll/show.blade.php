<x-layouts.tenant>
    <x-slot name="header">
        Payroll Details: {{ $period->name }}
    </x-slot>

    <div class="max-w-6xl space-y-6">
        @if(session('success'))
            <div class="bg-green-50 text-green-800 border-l-4 border-green-500 p-4 text-sm rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Summary Bar -->
        <div class="bg-white shadow-sm rounded-lg border border-slate-200 p-6 flex justify-between items-center">
            <div class="flex space-x-12">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Period Status</p>
                    <p class="mt-1 text-sm font-bold capitalize text-[#1E3A5F]">{{ $period->status }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Payroll</p>
                    <p class="mt-1 text-sm font-bold text-[#0F1B2D]">₱{{ number_format($period->total_amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Worker Count</p>
                    <p class="mt-1 text-sm font-bold text-[#0F1B2D]">{{ $period->payrolls->count() }}</p>
                </div>
            </div>
            
            <div class="flex space-x-3">
                @if($period->status === 'draft' || $period->status === 'processed')
                    <form action="{{ route('tenant.payroll.generate', $period) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-[#2D7DD2] text-white rounded-md text-sm font-bold hover:bg-[#1E3A5F] transition-colors shadow-sm">
                            {{ $period->status === 'processed' ? 'Re-calculate' : 'Generate Slips' }}
                        </button>
                    </form>
                @endif

                @if($period->status === 'processed')
                    <form action="{{ route('tenant.payroll.release', $period) }}" method="POST" data-confirm="Release payments? This will mark all slips as paid.">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-bold hover:bg-green-700 transition-colors shadow-sm">
                            Release Payroll
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Individual Slips -->
        <div class="bg-white shadow-sm rounded-lg border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-sm font-bold text-[#0F1B2D]">Worker Payslips</h3>
            </div>
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-white">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Worker</th>
                        <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Rate</th>
                        <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Hours</th>
                        <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Gross Pay</th>
                        <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($period->payrolls as $slip)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-[#0F1B2D]">{{ $slip->user->name }}</div>
                                <div class="text-[10px] text-slate-500 uppercase tracking-tight">{{ $slip->user->profile?->employee_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                ₱{{ number_format($slip->base_rate, 2) }}/hr
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-medium">
                                {{ number_format($slip->hours_worked, 1) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#0F1B2D]">
                                ₱{{ number_format($slip->gross_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="text-[10px] font-bold {{ $slip->status === 'paid' ? 'text-green-600' : 'text-orange-500' }} uppercase">
                                    {{ $slip->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No payslips generated for this period yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.tenant>
