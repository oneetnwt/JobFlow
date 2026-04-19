<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Super Admin | JobFlow OMS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full industrial-theme bg-[var(--color-bg)] text-[var(--color-text-primary)]">

    <!-- Fixed Header -->
    <header class="app-header flex items-center justify-between px-[var(--space-6)]">
        <div class="flex items-center gap-[var(--space-3)] text-white w-[var(--sidebar-width)]">
            <svg class="w-6 h-6 text-[var(--color-accent)]" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
            </svg>
            <span class="font-display text-[var(--text-xl)] text-foreground tracking-tight">Platform <span
                    class="text-[var(--color-accent)]">Admin</span></span>
        </div>
    </header>

    <!-- Fixed Sidebar -->
    <aside class="app-sidebar p-[var(--space-4)] flex flex-col justify-between">
        <nav class="flex flex-col gap-[var(--space-1)]">
            <div class="sidebar-section-label">General</div>
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l9-9 9 9M4.5 10.5V21h15V10.5" />
                </svg>
                Dashboard
            </a>

            <div class="sidebar-section-label mt-[var(--space-4)]">Management</div>
            <a href="{{ route('admin.tenants.index') }}"
                class="sidebar-nav-item {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100-8 4 4 0 000 8zm13 14v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                </svg>
                Tenants
            </a>
            <a href="{{ route('admin.plans.index') }}"
                class="sidebar-nav-item {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-2.21 0-4 .895-4 2s1.79 2 4 2 4 .895 4 2-1.79 2-4 2m0-10v12m0-12c2.21 0 4 .895 4 2" />
                </svg>
                Pricing Plans
            </a>

            <div class="sidebar-section-label mt-[var(--space-4)]">System</div>
            <a href="{{ route('admin.logs.index') }}"
                class="sidebar-nav-item {{ request()->routeIs('admin.logs.index') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-6m4 6V7m4 10v-4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Audit Trail
            </a>
        </nav>

        <form action="{{ route('admin.logout') }}" method="POST" class="mt-[var(--space-6)]">
            @csrf
            <button type="submit" class="sidebar-nav-item w-full text-left">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                </svg>
                Logout
            </button>
        </form>
    </aside>

    <!-- Main Content — Always Offset -->
    <main class="app-main">
        <div class="page-content">
            @if(isset($header))
                <div class="mb-[var(--space-8)]">
                    <header class="font-display text-[var(--text-3xl)] text-[var(--color-text-primary)] tracking-tight">
                        {{ $header }}</header>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>
</body>

</html>