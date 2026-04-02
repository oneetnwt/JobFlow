<x-layouts.admin>
    <x-slot name="header">Platform Audit Trail</x-slot>

    <div class="space-y-6">
        <p class="text-sm text-slate-500">Comprehensive history of all administrative and system-wide events.</p>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest w-48">Timestamp</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest w-40">Initiator</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest w-40">Event Type</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Description</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest w-32">Context</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-slate-500">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-6 w-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 border border-slate-200">
                                        {{ substr($log->admin?->name ?? 'S', 0, 1) }}
                                    </div>
                                    <span class="ml-2 text-xs font-bold text-[#0F172A]">{{ $log->admin?->name ?? 'System' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-slate-100 text-slate-600 border border-slate-200 uppercase tracking-tight">
                                    {{ str_replace('.', ': ', $log->event) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 leading-relaxed">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-[#2D7DD2]">
                                @if($log->tenant_id)
                                    <a href="{{ route('admin.tenants.show', $log->tenant_id) }}" class="hover:underline">{{ $log->tenant_id }}</a>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($logs->hasPages())
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
