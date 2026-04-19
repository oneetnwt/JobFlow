<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ tenant('company_name') }} | JobFlow OMS</title>

    @php
        $tenantAccent = tenant('brand_color') ?: '#c0440e';
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        .tenant-theme-override {
            --color-accent:
                {{ $tenantAccent }}
            ;
            --color-accent-mid: color-mix(in srgb,
                    {{ $tenantAccent }}
                    84%, black);
            --color-accent-light: color-mix(in srgb,
                    {{ $tenantAccent }}
                    86%, white);
            --color-accent-subtle: color-mix(in srgb,
                    {{ $tenantAccent }}
                    14%, transparent);
            --color-accent-border: color-mix(in srgb,
                    {{ $tenantAccent }}
                    44%, white);
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full industrial-theme tenant-theme-override bg-[var(--color-bg)] text-[var(--color-text-primary)]">
    @php
        $tenantSub = tenant()->subscription;
    @endphp
    @if($tenantSub && $tenantSub->status === 'trialing' && $tenantSub->trial_ends_at)
        @php
            $daysLeft = (int) ceil(now()->diffInDays($tenantSub->trial_ends_at, false));
            $bannerColor = $daysLeft > 7 ? 'bg-emerald-600' : ($daysLeft > 3 ? 'bg-amber-600' : 'bg-rose-600');
        @endphp
        @if($daysLeft >= 0)
            <div
                class="fixed top-0 left-0 right-0 h-[var(--space-8)] {{ $bannerColor }} text-white text-[11px] font-bold uppercase tracking-widest flex justify-center items-center z-[calc(var(--z-fixed)+1)] shadow-md">
                <span>{{ $daysLeft }} days left in your {{ $tenantSub->plan->name ?? 'Free' }} Trial</span>
                <a href="{{ route('tenant.billing.index') }}"
                    class="ml-4 bg-white/20 hover:bg-white/30 !text-white px-3 py-1 rounded-full transition-colors border border-white/30 font-extrabold shadow-sm hover:!text-white">Review
                    Plans</a>
            </div>
            <style>
                .app-header {
                    top: var(--space-8) !important;
                }

                .app-sidebar {
                    top: calc(var(--header-height) + var(--space-8)) !important;
                }

                .app-main {
                    margin-top: calc(var(--header-height) + var(--space-8)) !important;
                }
            </style>
        @endif
    @endif

    @if(session()->has('tenancy_impersonating'))
        <div
            class="fixed top-0 left-0 right-0 h-[var(--space-8)] bg-[var(--color-warning)] text-white text-[var(--text-xs)] font-bold uppercase tracking-widest flex justify-center items-center z-[calc(var(--z-fixed)+1)]">
            <svg class="h-4 w-4 mr-[var(--space-2)]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Impersonation Session Active
            <a href="/tenancy/stop-impersonating"
                class="ml-[var(--space-4)] underline hover:text-[var(--color-warning-subtle)]">Exit Session</a>
        </div>
        <style>
            .app-header {
                top: var(--space-8);
            }

            .app-sidebar {
                top: calc(var(--header-height) + var(--space-8));
            }

            .app-main {
                margin-top: calc(var(--header-height) + var(--space-8));
            }
        </style>
    @endif

    <!-- Fixed Header -->
    <header class="app-header flex items-center justify-start px-[var(--space-6)]">
        <div class="flex items-center gap-[var(--space-3)] text-white w-[var(--sidebar-width)]">
            @if(tenant('logo_url'))
                <img class="h-8 w-auto" src="{{ tenant('logo_url') }}" alt="{{ tenant('company_name') }}">
                <span class="font-display text-[var(--text-xl)] text-foreground tracking-tight">{{ tenant('company_name') }}
                    <span class="text-[var(--color-accent)]">OMS</span></span>
            @else
                <svg class="w-6 h-6 text-[var(--color-accent)]" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                </svg>
                <span class="font-display text-[var(--text-xl)] text-foreground tracking-tight">{{ tenant('company_name') }}
                    <span class="text-[var(--color-accent)]">OMS</span></span>
            @endif
        </div>
    </header>

    <!-- Fixed Sidebar -->
    <aside class="app-sidebar p-[var(--space-4)] flex flex-col justify-between">
        <nav class="flex flex-col gap-[var(--space-1)]">
            <div class="sidebar-section-label">Operations</div>

            <a href="{{ route('tenant.dashboard') }}"
                class="sidebar-nav-item {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l9-9 9 9M4.5 10.5V21h15V10.5" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('tenant.jobs.index') }}"
                class="sidebar-nav-item {{ request()->routeIs('tenant.jobs.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Job Orders
            </a>

            @can('manage-workers')
                <div class="sidebar-section-label mt-[var(--space-4)]">Workforce</div>
                <a href="{{ route('tenant.workers.index') }}"
                    class="sidebar-nav-item {{ request()->routeIs('tenant.workers.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100-8 4 4 0 000 8zm13 14v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                    </svg>
                    Workers
                </a>
            @endcan

            @can('manage-payroll')
                <div class="sidebar-section-label mt-[var(--space-4)]">Finance</div>
                <a href="{{ route('tenant.payroll.index') }}"
                    class="sidebar-nav-item {{ request()->routeIs('tenant.payroll.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-2.21 0-4 .895-4 2s1.79 2 4 2 4 .895 4 2-1.79 2-4 2m0-10v12m0-12c2.21 0 4 .895 4 2" />
                    </svg>
                    Payroll
                </a>
            @endcan

            @if(auth()->user()->hasRole('admin'))
                <div class="sidebar-section-label mt-[var(--space-4)]">Settings</div>
                <a href="{{ route('tenant.settings.edit') }}"
                    class="sidebar-nav-item {{ request()->routeIs('tenant.settings.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings
                </a>
                <a href="{{ route('tenant.roles.index') }}"
                    class="sidebar-nav-item {{ request()->routeIs('tenant.roles.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Roles & Permissions
                </a>
            @endif

            @can('billing.view')
                <div class="sidebar-section-label mt-[var(--space-4)]">Workspace</div>
                <a href="{{ route('tenant.billing.index') }}"
                    class="sidebar-nav-item {{ request()->routeIs('tenant.billing.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Billing & Subscription
                </a>
            @endcan
        </nav>

        <div class="mt-auto flex flex-col gap-2">
            @if(isset($pendingUpdate))
                <div
                    class="flex flex-col items-center justify-center p-5 rounded-2xl border border-[var(--color-accent-border)] bg-[var(--color-bg-subtle)] text-center mb-2 mx-1 shadow-sm">
                    <svg class="w-10 h-10 mb-3 text-[var(--color-accent)] opacity-90" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                    <span class="text-[14px] font-bold text-[var(--color-text-primary)]">Version
                        {{ $pendingUpdate->version }}</span>
                    <span class="text-[13px] text-[var(--color-text-secondary)] mt-1 mb-4">Pending update available</span>
                    <a href="{{ route('tenant.updates.index') }}"
                        class="w-full block py-2 rounded-lg border border-[var(--color-accent-border)] bg-white hover:bg-gray-50 transition-colors text-sm font-semibold text-[var(--color-text-primary)] text-center shadow-sm">
                        Review Update
                    </a>
                </div>
            @endif

            <form action="{{ route('tenant.logout') }}" method="POST" class="mt-[var(--space-2)]">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-[var(--color-bg-subtle)] transition-colors text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-sm font-medium truncate">{{ auth()->user()->name ?? 'Account' }}</span>
                    </div>
                    <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content — Always Offset -->
    <main class="app-main">
        <div class="page-content">
            @if(isset($header))
                <div class="mb-2">
                    <header class="font-display text-3xl text-[#0F1B2D] tracking-tight">{{ $header }}</header>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>
</body>

</html>