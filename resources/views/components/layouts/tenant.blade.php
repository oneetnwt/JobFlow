<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ tenant('company_name') }} | JobFlow OMS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[var(--color-bg)] text-[var(--color-text-primary)]">

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
    <header class="app-header flex items-center justify-between px-[var(--space-6)]">
        <div class="flex items-center gap-[var(--space-3)] text-white w-[var(--sidebar-width)]">
            @if(tenant('logo_url'))
                <img class="h-8 w-auto" src="{{ tenant('logo_url') }}" alt="{{ tenant('company_name') }}">
            @else
                <svg class="w-6 h-6 text-[var(--color-accent)]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                <span class="font-display text-[var(--text-xl)] tracking-tight">{{ tenant('company_name') }} <span class="text-[var(--color-accent)]">OMS</span></span>
            @endif
        </div>
        
        <div class="flex items-center gap-[var(--space-4)]">
            <div class="flex items-center">
                <span class="text-[var(--text-xs)] font-bold text-[var(--color-dark-text-muted)] uppercase tracking-widest border-b-[2px] border-[var(--color-accent)] pb-1">{{ tenant('company_name') }}</span>
            </div>
            <div class="w-px h-[var(--space-6)] bg-[var(--color-dark-border)]"></div>
            <div class="flex items-center gap-[var(--space-3)] group cursor-pointer">
                <div class="w-8 h-8 rounded-[var(--radius-sm)] bg-[var(--color-accent)] text-white flex items-center justify-center font-bold text-[var(--text-xs)]">
                    {{ substr(auth()->user()->name, 0, 2) }}
                </div>
                <form action="{{ route('tenant.logout') }}" method="POST" class="m-0 flex items-center">
                    @csrf
                    <button type="submit" class="text-[var(--text-sm)] font-medium text-[var(--color-dark-text-muted)] group-hover:text-[var(--color-dark-text)] transition-colors">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Fixed Sidebar -->
    <aside class="app-sidebar p-[var(--space-4)] flex flex-col justify-between">
        <nav class="flex flex-col gap-[var(--space-1)]">
            <div class="sidebar-section-label">Operations</div>
            
            <a href="{{ route('tenant.dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
            
            <a href="{{ route('tenant.jobs.index') }}" class="sidebar-nav-item {{ request()->routeIs('tenant.jobs.*') ? 'active' : '' }}">
                Job Orders
            </a>

            @can('manage-workers')
                <div class="sidebar-section-label mt-[var(--space-4)]">Workforce</div>
                <a href="{{ route('tenant.workers.index') }}" class="sidebar-nav-item {{ request()->routeIs('tenant.workers.*') ? 'active' : '' }}">
                    Workers
                </a>
            @endcan

            @can('manage-payroll')
                <div class="sidebar-section-label mt-[var(--space-4)]">Finance</div>
                <a href="{{ route('tenant.payroll.index') }}" class="sidebar-nav-item {{ request()->routeIs('tenant.payroll.*') ? 'active' : '' }}">
                    Payroll
                </a>
            @endcan
        </nav>
    </aside>

    <!-- Main Content — Always Offset -->
    <main class="app-main">
        <div class="page-content">
            @if(isset($header))
                <div class="mb-[var(--space-8)]">
                    <h1 class="font-display text-[var(--text-3xl)] text-[var(--color-text-primary)] tracking-tight">{{ $header }}</h1>
                </div>
            @endif
            
            {{ $slot }}
        </div>
    </main>
</body>
</html>
