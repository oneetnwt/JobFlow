<x-layouts.admin>
    <x-slot name="header">Tenant Management</x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div
                class="bg-emerald-50 text-emerald-800 border-l-4 border-emerald-500 p-4 text-sm font-bold rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Organization Details</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Subdomain</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Plan</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Plan Period</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tenants as $tenant)
                        @php
                            $baseDomain = preg_replace('/:\\d+$/', '', (string) (config('tenancy.central_domains')[0] ?? 'localhost'));
                            $displayHost = $tenant->domains()->value('domain')
                                ?? (($tenant->subdomain ?: $tenant->id) . '.' . $baseDomain);
                            $port = request()->getPort();
                            $portSegment = in_array((int) $port, [80, 443], true) ? '' : ':' . $port;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-[#0F172A]">{{ $tenant->company_name }}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tight mt-0.5">UID:
                                    {{ $tenant->id }}</div>
                                <div class="text-[11px] text-slate-500 mt-1">Admin: {{ $tenant->admin_email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ (request()->secure() ? 'https://' : 'http://') . $displayHost . $portSegment }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-xs font-bold text-[#2D7DD2] hover:underline">
                                    {{ $displayHost }}{{ $portSegment }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $planName = $tenant->tenantPlan?->plan_name ?? $tenant->plan?->name;
                                @endphp
                                @if($planName)
                                    <span
                                        class="text-xs font-bold uppercase tracking-widest text-[#0F172A]">{{ $planName }}</span>
                                @else
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">No plan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($tenant->tenantPlan)
                                    <div class="text-[11px] text-slate-500 uppercase tracking-widest font-bold">Start</div>
                                    <div class="text-sm font-semibold text-[#0F172A]">
                                        {{ optional($tenant->tenantPlan->created_at)->format('M d, Y') }}</div>
                                    <div class="text-[11px] text-slate-500 uppercase tracking-widest font-bold mt-1">End</div>
                                    <div class="text-sm font-semibold text-[#0F172A]">
                                        {{ optional($tenant->tenantPlan->valid_until)->format('M d, Y') }}</div>
                                @else
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">No plan
                                        data</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-2 flex items-center justify-end">
                                <a href="{{ route('admin.tenants.show', $tenant) }}"
                                    class="text-[10px] font-bold text-[#2D7DD2] uppercase hover:underline mr-4">View
                                    Profile</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($tenants->hasPages())
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                    {{ $tenants->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>