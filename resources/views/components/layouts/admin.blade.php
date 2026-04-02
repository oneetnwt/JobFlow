<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Super Admin | JobFlow OMS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[var(--color-bg)] text-[var(--color-text-primary)]">

    <!-- Fixed Header -->
    <header class="app-header flex items-center justify-between px-[var(--space-6)]">
        <div class="flex items-center gap-[var(--space-3)] text-white w-[var(--sidebar-width)]">
            <svg class="w-6 h-6 text-[var(--color-accent)]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            <span class="font-display text-[var(--text-xl)] tracking-tight">Platform <span class="text-[var(--color-accent)]">Admin</span></span>
        </div>
        
        <div class="flex items-center gap-[var(--space-4)]">
            <div class="badge badge-active">System Online</div>
            <div class="w-px h-[var(--space-6)] bg-[var(--color-dark-border)]"></div>
            <div class="flex items-center gap-[var(--space-3)] group cursor-pointer">
                <div class="w-8 h-8 rounded-[var(--radius-sm)] bg-[var(--color-accent)] text-white flex items-center justify-center font-bold text-[var(--text-xs)]">
                    SA
                </div>
                <form action="{{ route('admin.logout') }}" method="POST" class="m-0 flex items-center">
                    @csrf
                    <button type="submit" class="text-[var(--text-sm)] font-medium text-[var(--color-dark-text-muted)] group-hover:text-[var(--color-dark-text)] transition-colors">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Fixed Sidebar -->
    <aside class="app-sidebar p-[var(--space-4)] flex flex-col justify-between">
        <nav class="flex flex-col gap-[var(--space-1)]">
            <div class="sidebar-section-label">General</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
            
            <div class="sidebar-section-label mt-[var(--space-4)]">Management</div>
            <a href="{{ route('admin.tenants.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}">
                Tenants
            </a>
            <a href="{{ route('admin.plans.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                Pricing Plans
            </a>
            
            <div class="sidebar-section-label mt-[var(--space-4)]">System</div>
            <a href="{{ route('admin.logs.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.logs.index') ? 'active' : '' }}">
                Audit Trail
            </a>
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
