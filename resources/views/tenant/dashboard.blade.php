<x-layouts.tenant>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-[20px] font-bold text-foreground">Workspace Overview</h1>
            <div class="flex items-center space-x-2">
                <a href="{{ route('tenant.jobs.create') }}" class="btn btn-primary text-xs">Create Job</a>
                <a href="{{ route('tenant.workers.create') }}" class="btn btn-outline text-xs">Add Worker</a>
            </div>
        </div>
    </x-slot>

    <div class="page-content">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="card p-5 border-t-4 border-t-primary relative">
                <dt class="text-xs font-bold text-muted uppercase tracking-widest">Active Jobs</dt>
                <dd class="mt-2 text-3xl font-extrabold text-foreground flex items-baseline">
                    {{ $stats['active_jobs'] ?? 0 }}
                    <span class="ml-2 text-xs font-medium text-muted">pending</span>
                </dd>
            </div>

            <div class="card p-5 border-t-4 border-t-info relative">
                <dt class="text-xs font-bold text-muted uppercase tracking-widest">Total Workers</dt>
                <dd class="mt-2 text-3xl font-extrabold text-foreground">
                    {{ $stats['total_workers'] ?? 0 }}
                </dd>
            </div>

            <div class="card p-5 border-t-4 border-t-warning relative">
                <dt class="text-xs font-bold text-muted uppercase tracking-widest">Pending Payroll</dt>
                <dd class="mt-2 text-3xl font-extrabold text-foreground">
                    ₱{{ number_format($stats['pending_payroll'] ?? 0, 2) }}
                </dd>
            </div>

            <div class="card p-5 border-t-4 border-t-success relative">
                <dt class="text-xs font-bold text-muted uppercase tracking-widest">Jobs Completed</dt>
                <dd class="mt-2 text-3xl font-extrabold text-success">
                    {{ $stats['completed_jobs_count'] ?? 0 }}
                </dd>
            </div>
        </div>

        <!-- Main Dashboard Area -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Recent Jobs -->
            <div class="lg:col-span-2 card overflow-hidden">
                <div class="px-5 py-4 border-b border-border bg-surface-alt flex justify-between items-center">
                    <h3 class="text-xs font-bold text-foreground uppercase tracking-widest">Recent Job Orders</h3>
                    <a href="{{ route('tenant.jobs.index') }}" class="btn btn-outline text-xs px-2 py-1">View All</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Job Details</th>
                                <th>Status</th>
                                <th>Assignee</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_jobs as $job)
                                <tr>
                                    <td>
                                        <div class="text-sm font-bold text-foreground">{{ $job->title }}</div>
                                        <div class="text-[10px] text-muted uppercase tracking-tight mt-1">Order
                                            #{{ str_pad((string) $job->id, 5, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge 
                                                {{ $job->status === 'completed' ? 'badge-success' : 'badge-primary' }}">
                                            {{ strtoupper(str_replace('_', ' ', $job->status)) }}
                                        </span>
                                    </td>
                                    <td class="text-xs text-muted">
                                        {{ $job->assignee?->name ?? 'Unassigned' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-12 text-center text-muted text-sm">No recent jobs found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sidebar / Mini stats -->
            <div class="space-y-6">
                <!-- Removed Quick Actions from here since they were moved to Header -->

                <div class="card p-6">
                    <h3 class="text-xs font-bold text-foreground uppercase tracking-widest mb-4">Worker Roles</h3>
                    <div class="space-y-4">
                        @php
                            $roles = \App\Models\Role::withCount([
                                'users' => function ($query) {
                                    // optional: filter for just workers if needed, but users works
                                }
                            ])->get();
                        @endphp
                        @forelse($roles as $role)
                            <div
                                class="flex justify-between items-center bg-surface-alt p-3 rounded-lg border border-border">
                                <span class="text-xs font-medium text-foreground capitalize">{{ $role->name }}s</span>
                                <span class="badge badge-outline">{{ $role->users_count }}</span>
                            </div>
                        @empty
                            <div class="text-center text-muted text-xs">No roles found</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.tenant>