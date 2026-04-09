@props(['showTopbar' => true])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white antialiased scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>JobFlow OMS | Enterprise Operations Management</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .grid-blueprint {
            background-image:
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 28px 28px;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full industrial-theme industrial-landing text-[var(--color-text-primary)] bg-[var(--color-bg)] selection:bg-[var(--color-accent)] selection:text-[var(--color-text-inverse)]">

    @if($showTopbar)
        <!-- Navigation -->
        <nav class="fixed top-0 w-full z-50 bg-[var(--color-surface)]/95 backdrop-blur-md border-b border-[var(--color-border-strong)]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center">
                        <span class="text-2xl font-extrabold tracking-[0.08em] text-[var(--color-text-primary)]">
                            JobFlow <span class="text-[var(--color-accent)]">OMS</span>
                        </span>
                    </div>
                    <div class="hidden md:flex space-x-8 items-center text-sm font-semibold text-[var(--color-text-secondary)] uppercase tracking-widest">
                        <a href="/#features" class="hover:text-[var(--color-text-primary)] transition-colors">Features</a>
                        <a href="/pricing" class="hover:text-[var(--color-text-primary)] transition-colors">Pricing</a>
                        <a href="{{ route('tenant.register.create') }}" class="px-4 py-2 bg-[var(--color-accent)] !text-white border border-[var(--color-accent)] hover:bg-[var(--color-accent-mid)] hover:!text-white transition-all">
                            Get Started
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    @endif

    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-[var(--color-surface-raised)] text-[var(--color-text-primary)] py-20 border-t border-[var(--color-border-strong)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 border-b border-slate-800 pb-12">
                <div class="col-span-2">
                    <span class="text-2xl font-extrabold tracking-tighter">JobFlow OMS</span>
                    <p class="mt-4 text-slate-400 max-w-sm text-sm leading-relaxed">
                        The definitive operations management system for modern workforce logistics. Scalable, multitenant, and high-precision.
                    </p>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-4">Platform</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="#" class="hover:text-white transition-colors">Job Tracking</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Worker Portal</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Payroll Engine</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-4">Company</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="#" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('tenant.register.create') }}" class="hover:text-white transition-colors font-bold text-[#2D7DD2]">Register Tenant</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 flex justify-between items-center text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} JobFlow OMS. Engineered for efficiency.</p>
                <div class="flex space-x-6">
                    <span>v{{ app()->version() }}</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
