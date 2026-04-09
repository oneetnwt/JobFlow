<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ tenant('company_name') ?? 'JobFlow' }} | JobFlow OMS</title>

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

        .industrial-grid {
            background-size: 24px 24px;
            background-image:
                linear-gradient(to right, rgba(28, 25, 23, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(28, 25, 23, 0.05) 1px, transparent 1px);
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full industrial-theme tenant-theme-override bg-[var(--color-bg)] industrial-grid flex flex-col justify-center py-[var(--space-12)] sm:px-6 lg:px-8 relative">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center relative z-10">
        <div class="inline-flex items-center gap-[var(--space-3)] mb-[var(--space-6)] text-[var(--color-text-primary)]">
            <svg class="w-8 h-8 text-[var(--color-accent)]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            <span class="font-display text-[var(--text-3xl)] tracking-tight">{{ tenant('company_name') ?? 'JobFlow' }} <span class="text-[var(--color-accent)]">OMS</span></span>
        </div>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-[400px] relative z-10">
        <div class="card p-[var(--space-10)] shadow-[var(--shadow-2)]">
            <h2 class="font-display text-[var(--text-xl)] text-center text-[var(--color-text-primary)] mb-[var(--space-6)]">Workspace Authorization</h2>
            
            {{ $slot }}
            
        </div>
        
        <p class="mt-[var(--space-6)] text-center text-[var(--text-sm)] text-[var(--color-text-muted)] font-medium">
            New organization? <a href="{{ config('app.url') }}/register" class="text-[var(--color-accent)] hover:text-[var(--color-accent-mid)] font-semibold transition-colors">Start here.</a>
        </p>
    </div>

</body>
</html>