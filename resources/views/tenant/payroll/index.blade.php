<x-layouts.tenant>
    <x-slot name="header">
        Payroll Management
    </x-slot>

    <div class="flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <p class="text-sm text-slate-500">Manage payroll periods and generate payslips for your workers.</p>
            <a href="{{ route('tenant.payroll.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#0F1B2D] hover:bg-[#1E3A5F] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2D7DD2] transition-colors">
                New Period
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 text-green-800 border-l-4 border-green-500 p-4 text-sm rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-sm rounded-lg border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Payroll Period</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Dates</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Amount</th>
                        <th scope="col" class="relative px-6 py-3 text-right">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($periods as $period)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-[#0F1B2D]">{{ $period->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 inline-flex text-[10px] font-bold rounded-full 
                                    {{ $period->status === 'released' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $period->status === 'processed' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $period->status === 'draft' ? 'bg-slate-100 text-slate-600' : '' }}
                                ">
                                    {{ strtoupper($period->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#0F1B2D]">
                                ₱{{ number_format($period->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('tenant.payroll.show', $period) }}" class="text-[#2D7DD2] hover:text-[#1E3A5F]">View Slips</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                No payroll periods found. Create one to start processing.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($periods->hasPages())
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                    {{ $periods->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.tenant>
