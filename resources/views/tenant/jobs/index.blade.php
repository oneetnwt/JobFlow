<x-layouts.tenant>
    <x-slot name="header">
        Job Orders
    </x-slot>

    <div class="flex flex-col">
        <div class="flex justify-between items-center mb-3">
            <p class="text-sm text-slate-500">Manage and track work requests for your organization.</p>
            <a href="{{ route('tenant.jobs.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#2D7DD2] hover:bg-[#1E3A5F] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2D7DD2] transition-colors">
                New Job Order
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Job Details</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Priority</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Assigned To</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Deadline</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($jobs as $job)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-[#0F1B2D]">{{ $job->title }}</div>
                                <div class="text-xs text-slate-500">#{{ str_pad((string)$job->id, 5, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $job->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $job->status === 'open' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $job->status === 'assigned' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                    {{ $job->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $job->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs font-medium 
                                    {{ $job->priority === 'urgent' ? 'text-red-600 font-bold' : '' }}
                                    {{ $job->priority === 'high' ? 'text-orange-600' : '' }}
                                    {{ $job->priority === 'medium' ? 'text-slate-600' : '' }}
                                    {{ $job->priority === 'low' ? 'text-slate-400' : '' }}
                                ">
                                    {{ ucfirst($job->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $job->assignee?->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $job->deadline_at?->format('M d, Y') ?? 'No deadline' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('tenant.jobs.show', $job) }}" class="text-[#2D7DD2] hover:text-[#1E3A5F]">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                No job orders found. Start by <a href="{{ route('tenant.jobs.create') }}" class="text-[#2D7DD2] hover:underline">creating one</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($jobs->hasPages())
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                    {{ $jobs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.tenant>
