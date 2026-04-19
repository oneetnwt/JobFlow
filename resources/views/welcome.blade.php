<x-layouts.landing>

    <!-- Hero Section -->
    <section class="relative pt-24 pb-20 lg:pt-28 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 grid-blueprint opacity-[0.4] -z-10"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div
                class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-[#2D7DD2] text-[10px] font-bold uppercase tracking-widest mb-8 border border-slate-200">
                <span class="relative flex h-2 w-2 mr-2">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#2D7DD2] opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-[#2D7DD2]"></span>
                </span>
                Now available for enterprise teams
            </div>
            <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tighter text-[#0F172A] leading-[1.1] mb-6">
                Precision Operations.<br />
                <span class="text-[#2D7DD2]">Zero Friction.</span>
            </h1>
            <p class="max-w-2xl mx-auto text-lg lg:text-xl text-slate-500 leading-relaxed mb-10 font-medium">
                The multitenant Job Order Management System engineered for high-stakes logistics, workforce scheduling,
                and automated payroll.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4 mb-20">
                <a href="{{ route('tenant.register.create') }}"
                    class="px-8 py-4 bg-[var(--color-accent)] !text-white rounded-lg font-bold text-lg hover:bg-[var(--color-accent-mid)] hover:!text-white transition-all shadow-xl shadow-slate-200">
                    Create Your Workspace
                </a>
                <a href="#features"
                    class="px-8 py-4 bg-white text-[#0F172A] border border-slate-200 rounded-lg font-bold text-lg hover:bg-slate-50 transition-all">
                    View Features
                </a>
            </div>

            <!-- CSS UI Mockup (Dashboard Preview) -->
            <div class="relative max-w-5xl mx-auto">
                <div
                    class="rounded-xl border border-slate-200 bg-white shadow-2xl overflow-hidden aspect-[16/10] sm:aspect-[16/8] flex">
                    <!-- Sidebar Mockup -->
                    <div class="w-16 sm:w-48 bg-[#0F172A] hidden sm:flex flex-col p-4 space-y-4">
                        <div class="h-8 w-32 bg-slate-700/50 rounded-md mb-4"></div>
                        <div class="space-y-2">
                            <div class="h-4 w-full bg-slate-700/30 rounded"></div>
                            <div class="h-4 w-full bg-slate-700/30 rounded"></div>
                            <div class="h-4 w-full bg-slate-700/30 rounded"></div>
                        </div>
                    </div>
                    <!-- Content Mockup -->
                    <div class="flex-1 bg-slate-50 p-6 sm:p-10 text-left">
                        <div class="flex justify-between items-center mb-8">
                            <div class="h-8 w-48 bg-slate-200 rounded-md"></div>
                            <div class="h-8 w-24 bg-[#2D7DD2]/20 border border-[#2D7DD2]/30 rounded-md"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mb-8">
                            <div class="h-24 bg-white border border-slate-200 rounded-lg p-4">
                                <div class="h-3 w-12 bg-slate-100 rounded mb-2"></div>
                                <div class="h-6 w-8 bg-slate-300 rounded"></div>
                            </div>
                            <div class="h-24 bg-white border border-slate-200 rounded-lg p-4">
                                <div class="h-3 w-12 bg-slate-100 rounded mb-2"></div>
                                <div class="h-6 w-8 bg-slate-300 rounded"></div>
                            </div>
                            <div class="h-24 bg-white border border-slate-200 rounded-lg p-4">
                                <div class="h-3 w-12 bg-slate-100 rounded mb-2"></div>
                                <div class="h-6 w-8 bg-slate-300 rounded"></div>
                            </div>
                        </div>
                        <div class="h-48 bg-white border border-slate-200 rounded-lg">
                            <div class="h-10 border-b border-slate-100 flex items-center px-4">
                                <div class="h-3 w-32 bg-slate-100 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Floaties -->
                <div
                    class="absolute -top-6 -right-6 h-32 w-48 bg-white border border-slate-200 rounded-lg shadow-xl hidden lg:block p-4">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-2">Payroll Status</div>
                    <div class="flex items-center text-xs font-bold text-green-600 mb-1">
                        <span class="h-2 w-2 rounded-full bg-green-500 mr-2"></span> Released
                    </div>
                    <div class="text-xl font-bold text-[#0F172A]">₱12,450.00</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Banner -->
    <section class="bg-[#0F172A] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-3xl font-extrabold text-white tracking-tight">99.9%</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1 text-center">Uptime SLA
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-white tracking-tight">500ms</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1 text-center">Latency
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-white tracking-tight">10k+</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1 text-center">Jobs
                        Tracked</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-white tracking-tight">256-bit</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1 text-center">Encryption
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Grid -->
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-16">
                <h2 class="text-xs font-bold text-[#2D7DD2] uppercase tracking-[0.2em] mb-4">Core Capabilities</h2>
                <p class="text-3xl lg:text-4xl font-extrabold tracking-tighter text-[#0F172A] max-w-xl">
                    Everything you need to command your workforce at scale.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 border-t border-l border-slate-100">
                <!-- Feature 1 -->
                <div class="p-10 border-r border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <div
                        class="h-10 w-10 bg-[#2D7DD2]/10 rounded-lg flex items-center justify-center text-[#2D7DD2] mb-6">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#0F172A] mb-3">Job Orchestration</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Full lifecycle management from draft to completion. Track sub-tasks, progress bars, and
                        high-priority deadlines.
                    </p>
                </div>
                <!-- Feature 2 -->
                <div class="p-10 border-r border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <div
                        class="h-10 w-10 bg-[#2D7DD2]/10 rounded-lg flex items-center justify-center text-[#2D7DD2] mb-6">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#0F172A] mb-3">Worker Intelligence</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Detailed worker profiles with skill tracking, department grouping, and individual performance
                        analytics.
                    </p>
                </div>
                <!-- Feature 3 -->
                <div class="p-10 border-r border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <div
                        class="h-10 w-10 bg-[#2D7DD2]/10 rounded-lg flex items-center justify-center text-[#2D7DD2] mb-6">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#0F172A] mb-3">Automated Payroll</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Automatic compensation engine. Compute pay based on rendered tasks or hours with seamless period
                        releases.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-24 bg-slate-50 border-y border-slate-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-extrabold tracking-tighter text-[#0F172A] mb-6">
                Ready to optimize your workflow?
            </h2>
            <p class="text-lg text-slate-500 mb-10">
                Join the operations teams that have moved from spreadsheets to JobFlow OMS. Start your 14-day isolated
                workspace trial.
            </p>
            <div class="flex justify-center">
                <a href="{{ route('tenant.register.create') }}"
                    class="px-10 py-5 bg-[#2D7DD2] text-white rounded-lg font-bold text-xl hover:bg-[#1E3A5F] transition-all shadow-xl shadow-blue-100">
                    Get Started for Free
                </a>
            </div>
        </div>
    </section>

</x-layouts.landing>