<x-layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-[20px] font-bold text-foreground">System Overview</h1>
        </div>
    </x-slot>

    <div class="page-content">
        <!-- Stat Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="card p-6 border-t-4 border-t-primary relative">
                <p class="text-[10px] font-bold text-muted uppercase tracking-widest">Total Tenants</p>
                <p class="mt-2 text-3xl font-extrabold text-foreground">{{ $stats['total_tenants'] }}</p>
            </div>

            <div class="card p-6 border-t-4 border-t-warning relative">       
                <p class="text-[10px] font-bold text-muted uppercase tracking-widest">Pending Approvals</p>
                <p class="mt-2 text-3xl font-extrabold text-warning">{{ $stats['pending_approvals'] }}</p>
            </div>

            <div class="card p-6 border-t-4 border-t-success relative">   
                <p class="text-[10px] font-bold text-muted uppercase tracking-widest">Active Workspaces</p>
                <p class="mt-2 text-3xl font-extrabold text-success">{{ $stats['active_tenants'] }}</p>
            </div>

            <div class="card p-6 border-t-4 border-t-danger relative">
                <p class="text-[10px] font-bold text-muted uppercase tracking-widest">Suspended</p>
                <p class="mt-2 text-3xl font-extrabold text-danger">{{ $stats['suspended_tenants'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Recent Registrations -->
            <div class="lg:col-span-2 card overflow-hidden">
                <div class="px-6 py-4 border-b border-border bg-surface-alt flex justify-between items-center">
                    <h3 class="text-xs font-bold text-foreground uppercase tracking-widest">Recent Registrations</h3>
                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline text-xs px-2 py-1">Manage All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Status</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_tenants as $tenant)
                                <tr>    
                                    <td>        
                                        <div class="text-sm font-bold text-foreground">{{ $tenant->company_name }}</div>
                                        <div class="text-[10px] text-muted uppercase font-medium mt-1">{{ $tenant->id }}.localhost:8000</div>
                                    </td>
                                    <td>        
                                        <span class="badge 
                                            {{ $tenant->status === 'active' ? 'badge-success' : '' }}
                                            {{ $tenant->status === 'pending' ? 'badge-warning' : '' }}
                                            {{ $tenant->status === 'suspended' ? 'badge-danger' : '' }}
                                        ">
                                            {{ strtoupper($tenant->status) }}       
                                        </span>
                                    </td>
                                    <td class="text-xs text-muted font-medium">
                                        {{ $tenant->created_at->diffForHumans() }}  
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-muted text-sm italic">No tenants registered yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Activity Feed -->
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-border bg-surface-alt">    
                    <h3 class="text-xs font-bold text-foreground uppercase tracking-widest">Platform Activity</h3>
                </div>
                <div class="p-6">
                    <ul class="space-y-6">
                        @forelse($recent_logs as $log)
                            <li class="relative flex gap-x-4">
                                <div class="absolute left-0 top-0 flex w-6 justify-center -bottom-6">
                                    <div class="w-px bg-border"></div>
                                </div>
                                <div class="relative flex h-6 w-6 flex-none items-center justify-center bg-surface">
                                    <div class="h-1.5 w-1.5 rounded-full bg-border ring-1 ring-border"></div>
                                </div>
                                <div class="flex-auto py-0.5 text-xs leading-5">    
                                    <p class="text-foreground">
                                        <strong class="font-bold">{{ $log->admin?->name ?? 'System' }}</strong>
                                        <span class="text-muted">{{ $log->description }}</span>
                                    </p>
                                    <time datetime="{{ $log->created_at }}" class="flex-none text-muted text-[10px] font-bold uppercase block mt-1">{{ $log->created_at->diffForHumans() }}</time>
                                </div>
                            </li>
                        @empty
                            <li class="text-center py-4 text-muted text-xs italic">No activity recorded.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
