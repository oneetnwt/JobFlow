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
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700;9..40,800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .grid-blueprint {
            background-image: radial-gradient(#E2E8F0 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-[#0F172A] bg-white selection:bg-[#2D7DD2] selection:text-white">

    @if($showTopbar)
        <!-- Navigation -->
        <nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center">
                        <span class="text-xl font-extrabold tracking-tighter text-[#0F172A]">
                            JobFlow <span class="text-[#2D7DD2]">OMS</span>
                        </span>
                    </div>
                    <div class="hidden md:flex space-x-8 items-center text-sm font-semibold text-slate-600">
                        <a href="{{ request()->routeIs('home') ? '#features' : route('home').'#features' }}" class="hover:text-[#0F172A] transition-colors">Features</a>
                        <a href="{{ route('pricing') }}" class="hover:text-[#0F172A] transition-colors">Pricing</a>
                        <a href="{{ route('tenant.register.create') }}" class="px-4 py-2 bg-[#0F172A] text-white rounded-md hover:bg-slate-800 transition-all shadow-sm">
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
    <footer class="bg-[#0F172A] text-white py-20">
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
