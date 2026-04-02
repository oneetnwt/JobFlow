<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Registration | {{ config('app.name', 'JobFlow OMS') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <style>
        [x-cloak] { display: none !important; }
        .animate-shake { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }
        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
        .step-transition { transition: opacity 150ms ease-out; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[var(--color-bg)]" x-data="registrationForm()">

    <div class="split-screen">
        <!-- Left Panel: Brand -->
        <div class="split-left flex flex-col justify-between p-[var(--space-12)] text-[var(--color-dark-text)]">
            <div class="flex items-center gap-[var(--space-4)]">
                <svg class="w-8 h-8 text-[var(--color-accent)]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                <div class="font-display text-[var(--text-2xl)] font-bold">JobFlow <span class="text-[var(--color-accent)]">OMS</span></div>
            </div>

            <div class="flex-1 flex flex-col justify-center max-w-[400px]">
                <h1 class="font-display text-[var(--text-5xl)] leading-[var(--leading-tight)] mb-[var(--space-6)] text-white">Industrial Operations Platform</h1>
                <p class="text-[var(--text-md)] text-[var(--color-dark-text-muted)] leading-[var(--leading-relaxed)]">
                    Create your organization's workspace. JobFlow OMS is the centralized control panel for managing job orders, dispatching workers, and tracking payroll processing. No consumer fluff, just robust industrial features.
                </p>
            </div>

            <div class="text-[var(--text-sm)] text-[var(--color-dark-text-muted)] font-medium">
                &copy; {{ date('Y') }} JobFlow Systems.
            </div>
        </div>

        <!-- Right Panel: Form -->
        <div class="split-right py-[var(--space-16)] px-[var(--space-6)] flex items-center justify-center">
            
            <div class="w-full" style="max-width: 420px; padding-bottom: 64px;">
                @if($errors->any())
                    <div class="mb-[var(--space-6)] p-[var(--space-4)] bg-[var(--color-error-subtle)] border border-[var(--color-error-border)] rounded-[var(--radius-md)]" role="alert">
                        <div class="flex items-start">
                            <div class="ml-3">
                                <h3 class="text-[var(--text-sm)] font-bold text-[var(--color-error)]">Please correct the following errors:</h3>
                                <ul class="mt-2 text-[var(--text-sm)] text-[var(--color-error)] list-disc pl-5 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form id="regForm" class="flex flex-col gap-[var(--space-6)]" action="{{ route('tenant.register.store') }}" method="POST" @submit="submitForm($event)">
                    @csrf
                    
                    <input type="hidden" name="plan_id" x-model="selectedPlanId">
                    <input type="hidden" name="billing_cycle" x-model="billing">

                    <!-- STEP 1: Company Info -->
                    <div x-show="step === 1" class="step-transition w-full" :class="{'animate-shake': stepError === 1}">
                        <div class="mb-[var(--space-8)]">
                            <h2 class="font-display text-[var(--text-3xl)] text-[var(--color-text-primary)] mb-[var(--space-2)]">Company Details</h2>
                            <p class="text-[var(--color-text-secondary)] text-[var(--text-sm)]">Let's set up your organization's workspace.</p>
                        </div>

                        <div class="flex flex-col gap-[var(--space-6)]">
                            <div class="form-group">
                                <label for="company_name" class="form-label">
                                    Organization Name <span class="text-[var(--color-error)] ml-1">*</span>
                                </label>
                                <input type="text" id="company_name" name="company_name" x-model="companyName" @input="!subdomainDirty ? autofillSubdomain() : null" class="form-input" placeholder="e.g. Acme Corp">
                            </div>

                            <div class="form-group">
                                <label for="industry" class="form-label">
                                    Industry <span class="text-[var(--color-error)] ml-1">*</span>
                                </label>
                                <select id="industry" name="industry" x-model="industry" class="form-input">
                                    <option value="" disabled hidden>Select industry...</option>
                                    <option value="Construction">Construction</option>
                                    <option value="Logistics">Logistics</option>
                                    <option value="Manufacturing">Manufacturing</option>
                                    <option value="Healthcare">Healthcare</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="size" class="form-label">
                                    Company Size <span class="text-[var(--color-error)] ml-1">*</span>
                                </label>
                                <select id="size" name="size" x-model="size" class="form-input">
                                    <option value="" disabled hidden>Select size...</option>
                                    <option value="1-10">1–10 employees</option>
                                    <option value="11-50">11–50 employees</option>
                                    <option value="51-200">51–200 employees</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="domain" class="form-label">
                                    Workspace URL <span class="text-[var(--color-error)] ml-1">*</span>
                                </label>
                                <div class="flex">
                                    <input type="text" id="domain" name="domain" x-model="subdomain" @input="formatSubdomain; subdomainDirty=true;" class="form-input flex-1 !rounded-r-none border-r-0 font-mono text-[var(--color-accent)] text-right" placeholder="acme">
                                    <span class="inline-flex items-center px-3 border border-[var(--color-border)] bg-[var(--color-bg-subtle)] text-[var(--color-text-muted)] text-[var(--text-sm)] rounded-r-[var(--radius-sm)]">
                                        .{{ config('tenancy.central_domains')[0] ?? 'jobflow.test' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Admin Setup -->
                    <div x-show="step === 2" class="step-transition w-full" x-cloak :class="{'animate-shake': stepError === 2}">
                        <div class="mb-[var(--space-8)]">
                            <h2 class="font-display text-[var(--text-3xl)] text-[var(--color-text-primary)] mb-[var(--space-2)]">Admin Account</h2>
                            <p class="text-[var(--color-text-secondary)] text-[var(--text-sm)]">Create the primary administrator account.</p>
                        </div>

                        <div class="flex flex-col gap-[var(--space-6)]">
                            <div class="form-group">
                                <label for="admin_name" class="form-label">
                                    Full Name <span class="text-[var(--color-error)] ml-1">*</span>
                                </label>
                                <input type="text" id="admin_name" name="admin_name" x-model="adminName" class="form-input" placeholder="Jane Doe">
                            </div>

                            <div class="form-group">
                                <label for="admin_email" class="form-label">
                                    Work Email <span class="text-[var(--color-error)] ml-1">*</span>
                                </label>
                                <input type="email" id="admin_email" name="admin_email" x-model="adminEmail" class="form-input" placeholder="jane@company.com">
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">
                                    Password <span class="text-[var(--color-error)] ml-1">*</span>
                                </label>
                                <input type="password" id="password" name="password" x-model="password" autocomplete="new-password" class="form-input font-mono tracking-widest">
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">
                                    Confirm Password <span class="text-[var(--color-error)] ml-1">*</span>
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation" x-model="confirm" autocomplete="new-password" class="form-input font-mono tracking-widest">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: Plan Selection -->
                    <div x-show="step === 3" class="step-transition w-full" x-cloak :class="{'animate-shake': stepError === 3}">
                        <div class="mb-[var(--space-8)]">
                            <h2 class="font-display text-[var(--text-3xl)] text-[var(--color-text-primary)] mb-[var(--space-2)]">Choose your plan</h2>
                            <p class="text-[var(--color-text-secondary)] text-[var(--text-sm)]">Select the right tier for your needs.</p>
                            
                            <!-- Billing Toggle -->
                            <div class="mt-6 inline-flex p-1 border border-[var(--color-border)] rounded-full bg-[var(--color-surface)]">
                                <button type="button" @click="billing = 'monthly'" :class="billing === 'monthly' ? 'bg-[var(--color-accent)] text-white' : 'text-[var(--color-text-secondary)]'" class="px-4 py-1.5 text-xs font-bold uppercase tracking-wider rounded-full transition-colors">Monthly</button>
                                <button type="button" @click="billing = 'annual'" :class="billing === 'annual' ? 'bg-[var(--color-accent)] text-white' : 'text-[var(--color-text-secondary)]'" class="px-4 py-1.5 text-xs font-bold uppercase tracking-wider rounded-full transition-colors">Annual</button>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3">
                            @foreach($plans as $plan)
                            <label class="relative flex items-center p-4 cursor-pointer rounded-[8px] transition-all bg-[var(--color-surface)] border"
                                   :class="selectedPlanId == '{{ $plan->id }}' ? 'border-[var(--color-accent)] bg-[var(--color-bg-subtle)] ring-1 ring-[var(--color-accent)]' : 'border-[var(--color-border)] hover:border-[var(--color-border-strong)]'">
                                
                                <input type="radio" x-model="selectedPlanId" value="{{ $plan->id }}" class="sr-only">
                                
                                <div class="flex items-center justify-center w-5 h-5 rounded-full border mr-4 transition-colors"
                                     :class="selectedPlanId == '{{ $plan->id }}' ? 'border-[var(--color-accent)] bg-[var(--color-accent)]' : 'border-[var(--color-border-strong)]'">
                                     <svg class="w-3 h-3 text-white" :class="selectedPlanId == '{{ $plan->id }}' ? 'opacity-100' : 'opacity-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>

                                <div class="flex-1">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-ui text-[14px] font-[600] uppercase tracking-wide text-[var(--color-text-primary)]">
                                            {{ $plan->name }}
                                        </span>
                                        @if($plan->is_contact_sales)
                                            <span class="font-display text-[18px] text-[var(--color-text-primary)]">Custom</span>
                                        @elseif($plan->is_free)
                                            <span class="font-display text-[18px] text-[var(--color-text-primary)]">₱0</span>
                                        @else
                                            <span x-show="billing === 'monthly'" class="font-display text-[18px] text-[var(--color-text-primary)]">₱{{ number_format($plan->monthly_price) }}<span class="text-[12px] font-sans text-[var(--color-text-muted)]">/mo</span></span>
                                            <span x-show="billing === 'annual'" class="font-display text-[18px] text-[var(--color-text-primary)]" style="display: none;">₱{{ number_format($plan->annual_price) }}<span class="text-[12px] font-sans text-[var(--color-text-muted)]">/yr</span></span>
                                        @endif
                                    </div>
                                    <p class="text-[12px] text-[var(--color-text-secondary)]">{{ $plan->tagline }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- STEP 4: Review -->
                    <div x-show="step === 4" class="step-transition w-full" x-cloak>
                        <div class="mb-[var(--space-8)]">
                            <h2 class="font-display text-[var(--text-3xl)] text-[var(--color-text-primary)] mb-[var(--space-2)]">Review Application</h2>
                        </div>
                        <div class="card p-[var(--space-6)] flex flex-col gap-[var(--space-4)] text-[var(--text-sm)]">
                            <div>
                                <div class="text-[var(--color-text-muted)] uppercase tracking-widest text-[var(--text-2xs)] font-bold mb-1">Company</div>
                                <div class="font-bold" x-text="companyName"></div>
                                <div class="font-mono text-[var(--text-xs)] text-[var(--color-accent)]" x-text="subdomain + '.{{ config('tenancy.central_domains')[0] ?? 'jobflow.test' }}'"></div>
                            </div>
                            <hr class="border-[var(--color-border)]">
                            <div>
                                <div class="text-[var(--color-text-muted)] uppercase tracking-widest text-[var(--text-2xs)] font-bold mb-1">Admin</div>
                                <div class="font-bold" x-text="adminName"></div>
                                <div class="text-[var(--color-text-secondary)]" x-text="adminEmail"></div>
                            </div>
                        </div>

                        <div class="mt-[var(--space-6)] flex items-center gap-[var(--space-3)]">
                            <input type="checkbox" id="terms" x-model="terms" class="w-4 h-4 text-[var(--color-accent)]">
                            <label for="terms" class="text-[var(--text-sm)] text-[var(--color-text-secondary)]">I agree to the Terms of Service.</label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-[var(--space-8)] flex justify-between items-center border-t border-[var(--color-border)] pt-[var(--space-6)]">
                        <button type="button" @click="prevStep" class="btn btn-ghost" :class="{'opacity-0 pointer-events-none': step === 1}">BACK</button>
                        <button type="button" @click="nextStep" x-show="step < 4" class="btn btn-primary">CONTINUE</button>
                        <button type="submit" x-show="step === 4" class="btn btn-primary" :disabled="!terms || submitting">SUBMIT APPLICATION</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('registrationForm', () => ({
                step: 1,
                companyName: '{!! addslashes(old('company_name', '')) !!}',
                industry: '{!! addslashes(old('industry', '')) !!}',
                size: '{!! addslashes(old('size', '')) !!}',
                subdomain: '{!! addslashes(old('domain', '')) !!}',
                adminName: '{!! addslashes(old('admin_name', '')) !!}',
                adminEmail: '{!! addslashes(old('admin_email', '')) !!}',
                password: '',
                confirm: '',
                selectedPlanId: '{{ $selectedPlanId ?? '' }}',
                billing: 'monthly',
                terms: false,
                stepError: 0,
                submitting: false,
                subdomainDirty: false,

                init() { if (this.subdomain.trim() !== '') this.subdomainDirty = true; },
                autofillSubdomain() { if (this.companyName && !this.subdomainDirty) this.subdomain = this.companyName.replace(/[^a-z0-9]/gi, '').toLowerCase(); },
                formatSubdomain() { this.subdomain = this.subdomain.replace(/[^a-z0-9-]/gi, '').toLowerCase(); },
                syncPlanSelection() { setTimeout(() => { const el = document.querySelector('input[name="plan_id"]:checked'); if (el) this.selectedPlanId = el.value; }, 50); },
                validateStep() { return true; }, // simplified for brevity, server handles validation
                nextStep() { if (this.step < 4) this.step++; },
                prevStep() { if (this.step > 1) this.step--; },
                submitForm(e) { if (!this.terms) { e.preventDefault(); return; } this.submitting = true; }
            }));
        });
    </script>
</body>
</html>