<x-layouts.admin>
    <x-slot name="header">Tenant Profile: {{ $tenant->company_name }}</x-slot>

    <div class="max-w-5xl space-y-8">
        <!-- Back Link -->
        <a href="{{ route('admin.tenants.index') }}" class="inline-flex items-center text-xs font-bold text-slate-400 hover:text-[#2D7DD2] uppercase tracking-widest transition-colors group">
            <svg class="h-3 w-3 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Tenant Directory
        </a>

        <!-- Header Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-8 flex items-center justify-between border-b border-slate-100 bg-[#0F172A] text-white">
                <div class="flex items-center">
                    <div class="h-16 w-16 rounded-lg bg-[#1E293B] border border-slate-700 flex items-center justify-center text-xl font-bold text-[#2D7DD2]">
                        {{ substr($tenant->company_name, 0, 1) }}
                    </div>
                    <div class="ml-6">
                        <h2 class="text-2xl font-bold tracking-tight">{{ $tenant->company_name }}</h2>
                        <div class="flex items-center mt-1 space-x-4">
                            @php
                                $baseDomain = preg_replace('/:\\d+$/', '', (string) (config('tenancy.central_domains')[0] ?? 'localhost'));
                                $displayHost = $tenant->domains()->value('domain')
                                    ?? (($tenant->subdomain ?: $tenant->id) . '.' . $baseDomain);
                                $port = request()->getPort();
                                $portSegment = in_array((int) $port, [80, 443], true) ? '' : ':' . $port;
                            @endphp
                            <a
                                href="{{ (request()->secure() ? 'https://' : 'http://') . $displayHost . $portSegment }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-xs font-bold text-[#2D7DD2] tracking-widest uppercase hover:underline"
                            >
                                {{ $displayHost }}{{ $portSegment }}
                            </a>
                            <span class="h-1 w-1 rounded-full bg-slate-600"></span>
                            <span class="text-xs font-bold uppercase tracking-widest
                                {{ $tenant->status === 'active' ? 'text-emerald-400' : '' }}
                                {{ $tenant->status === 'pending' ? 'text-amber-400' : '' }}
                                {{ $tenant->status === 'suspended' ? 'text-rose-400' : '' }}
                            ">
                                ● {{ $tenant->status }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    @if($tenant->status !== 'active')
                        <form action="{{ route('admin.tenants.approve', $tenant) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-xs font-bold uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-900/20">
                                Approve Account
                            </button>
                        </form>
                    @endif
                    @if($tenant->status === 'active')
                        <form action="{{ route('admin.tenants.suspend', $tenant) }}" method="POST" onsubmit="return confirm('Immediately suspend this workspace?')">
                            @csrf
                            <button type="submit" class="px-4 py-2 border border-rose-500/50 text-rose-400 rounded-md text-xs font-bold uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all">
                                Suspend Access
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-slate-100 bg-slate-50/30">
                <div class="p-6 text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Active Workers</p>
                    <p class="mt-2 text-2xl font-black text-[#0F172A]">{{ number_format($metrics['workers_count']) }}</p>
                </div>
                <div class="p-6 text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Job Orders</p>
                    <p class="mt-2 text-2xl font-black text-[#0F172A]">{{ number_format($metrics['jobs_count']) }}</p>
                </div>
                <div class="p-6 text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sub-Tasks Tracked</p>
                    <p class="mt-2 text-2xl font-black text-[#0F172A]">{{ number_format($metrics['tasks_count']) }}</p>
                </div>
                <div class="p-6 text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Payroll Value</p>
                    <p class="mt-2 text-2xl font-black text-emerald-600">${{ number_format($metrics['total_payroll_value'], 2) }}</p>
                </div>
            </div>

            <!-- Detailed Info -->
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-12 border-t border-slate-100">
                <div>
                    <h3 class="text-xs font-bold text-[#0F172A] uppercase tracking-widest mb-6">Administrative Contact</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Primary Administrator</dt>
                            <dd class="mt-1 text-sm font-bold text-[#0F172A]">{{ $tenant->admin_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Registered Email</dt>
                            <dd class="mt-1 text-sm font-bold text-[#2D7DD2]">{{ $tenant->admin_email }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Database Identity</dt>
                            <dd class="mt-1 text-xs font-mono text-slate-500 bg-slate-100 px-2 py-1 rounded inline-block">jobflow_{{ $tenant->id }}</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h3 class="text-xs font-bold text-[#0F172A] uppercase tracking-widest mb-6">Platform Metadata</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Registration Date</dt>
                            <dd class="mt-1 text-sm font-bold text-[#0F172A]">{{ $tenant->created_at->format('F d, Y • h:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">System Lifetime</dt>
                            <dd class="mt-1 text-sm font-bold text-[#0F172A]">{{ $tenant->created_at->diffForHumans() }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Payroll Periods</dt>
                            <dd class="mt-1 text-sm font-bold text-[#0F172A]">{{ $metrics['payroll_periods_count'] }} cycles processed</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Branding Column -->
            <div class="md:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-xs font-bold text-[#0F172A] uppercase tracking-widest">Branding & Appearance</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="brand_color" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Primary Accent Color</label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" name="brand_color" id="brand_color" value="{{ old('brand_color', $tenant->brand_color) }}"
                                           class="h-10 w-10 border-none rounded cursor-pointer">
                                    <input type="text" value="{{ $tenant->brand_color }}" readonly class="flex-1 bg-slate-50 border-slate-200 rounded text-xs font-mono text-slate-500">
                                </div>
                            </div>
                            <div>
                                <label for="logo_url" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Logo URL (External)</label>
                                <input type="url" name="logo_url" id="logo_url" value="{{ old('logo_url', $tenant->logo_url) }}"
                                       placeholder="https://example.com/logo.png"
                                       class="w-full bg-slate-50 border-slate-200 rounded text-xs text-[#0F172A] focus:ring-[#2D7DD2]">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="text-[10px] font-bold bg-[#0F172A] text-white px-4 py-2 rounded uppercase tracking-widest hover:bg-slate-800 transition-all">
                                Save Branding
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Management Column -->
            <div class="space-y-8">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="text-xs font-bold text-[#0F172A] uppercase tracking-widest">Admin Operations</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <a href="{{ route('admin.tenants.impersonate', $tenant) }}" target="_blank"
                           class="flex items-center justify-center w-full px-4 py-3 bg-[#2D7DD2]/10 text-[#2D7DD2] rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-[#2D7DD2] hover:text-white transition-all group">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Impersonate Admin
                        </a>
                        <p class="text-[10px] text-slate-400 leading-relaxed text-center italic">
                            Opens a secure session as the tenant's primary administrator. Actions will be logged.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Infrastructure Status Section -->
        <div class="bg-slate-100 rounded-xl p-6 border border-slate-200">
            <div class="flex items-center space-x-3">
                <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Isolated Infrastructure is healthy and performing within nominal parameters.</p>
            </div>
        </div>
    </div>
</x-layouts.admin>
