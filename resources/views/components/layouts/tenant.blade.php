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
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .tenant-theme-override {
            --color-accent: {{ $tenantAccent }};
            --color-accent-mid: color-mix(in srgb, {{ $tenantAccent }} 84%, black);
            --color-accent-light: color-mix(in srgb, {{ $tenantAccent }} 86%, white);
            --color-accent-subtle: color-mix(in srgb, {{ $tenantAccent }} 14%, transparent);
            --color-accent-border: color-mix(in srgb, {{ $tenantAccent }} 44%, white);
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full industrial-theme tenant-theme-override bg-[var(--color-bg)] text-[var(--color-text-primary)]">

    @if(session()->has('tenancy_impersonating'))
        <div class="fixed top-0 left-0 right-0 h-[var(--space-8)] bg-[var(--color-warning)] text-white text-[var(--text-xs)] font-bold uppercase tracking-widest flex justify-center items-center z-[calc(var(--z-fixed)+1)]">
            <svg class="h-4 w-4 mr-[var(--space-2)]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Impersonation Session Active
            <a href="/tenancy/stop-impersonating" class="ml-[var(--space-4)] underline hover:text-[var(--color-warning-subtle)]">Exit Session</a>
        </div>
        <style>
            .app-header { top: var(--space-8); }
            .app-sidebar { top: calc(var(--header-height) + var(--space-8)); }
            .app-main { margin-top: calc(var(--header-height) + var(--space-8)); }
        </style>
    @endif

    <!-- Fixed Header -->
    <header class="app-header flex items-center justify-start px-[var(--space-6)]">
        <div class="flex items-center gap-[var(--space-3)] text-white w-[var(--sidebar-width)]">
            @if(tenant('logo_url'))
                <img class="h-8 w-auto" src="{{ tenant('logo_url') }}" alt="{{ tenant('company_name') }}">
                <span class="font-display text-[var(--text-xl)] text-foreground tracking-tight">{{ tenant('company_name') }} <span class="text-[var(--color-accent)]">OMS</span></span>
            @else
                <svg class="w-6 h-6 text-[var(--color-accent)]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                <span class="font-display text-[var(--text-xl)] text-foreground tracking-tight">{{ tenant('company_name') }} <span class="text-[var(--color-accent)]">OMS</span></span>
            @endif
        </div>
    </header>

    <!-- Fixed Sidebar -->
    <aside class="app-sidebar p-[var(--space-4)] flex flex-col justify-between">
        <nav class="flex flex-col gap-[var(--space-1)]">
            <div class="sidebar-section-label">Operations</div>
            
            <a href="{{ route('tenant.dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4.5 10.5V21h15V10.5"/></svg>
                Dashboard
            </a>
            
            <a href="{{ route('tenant.jobs.index') }}" class="sidebar-nav-item {{ request()->routeIs('tenant.jobs.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Job Orders
            </a>

            @can('manage-workers')
                <div class="sidebar-section-label mt-[var(--space-4)]">Workforce</div>
                <a href="{{ route('tenant.workers.index') }}" class="sidebar-nav-item {{ request()->routeIs('tenant.workers.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100-8 4 4 0 000 8zm13 14v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                    Workers
                </a>
            @endcan

            @can('manage-payroll')
                <div class="sidebar-section-label mt-[var(--space-4)]">Finance</div>
                <a href="{{ route('tenant.payroll.index') }}" class="sidebar-nav-item {{ request()->routeIs('tenant.payroll.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 .895-4 2s1.79 2 4 2 4 .895 4 2-1.79 2-4 2m0-10v12m0-12c2.21 0 4 .895 4 2"/></svg>
                    Payroll
                </a>
            @endcan
        </nav>

        <form action="{{ route('tenant.logout') }}" method="POST" class="mt-[var(--space-6)]">
            @csrf
            <button type="submit" class="sidebar-nav-item w-full text-left">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/></svg>
                Logout
            </button>
        </form>
    </aside>

    <!-- Main Content — Always Offset -->
    <main class="app-main">
        <div class="page-content">
            @if(isset($header))
                <div class="mb-2">
                    <h1 class="font-display text-3xl text-[#0F1B2D] tracking-tight">{{ $header }}</h1>
                </div>
            @endif
            
            {{ $slot }}
        </div>
    </main>
</body>
</html>
