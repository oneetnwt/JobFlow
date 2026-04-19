<x-layouts.landing :show-topbar="false">
    <section class="min-h-screen bg-linear-to-b from-slate-50 to-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Create your workspace</h1>
                <p class="mt-2 text-sm text-slate-600">Start with the essentials. You can configure everything else later.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6" x-data="registerForm()">
                <form action="{{ route('tenant.register.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="billing_cycle" value="{{ old('billing_cycle', 'monthly') }}">

                    <!-- Step Progress Indicator -->
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex flex-col items-center">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold transition-colors" :class="step >= 1 ? 'bg-[var(--color-accent)] text-white' : 'bg-slate-100 text-slate-400'">1</div>
                            <span class="mt-2 text-xs font-medium" :class="step >= 1 ? 'text-slate-900' : 'text-slate-400'">Company</span>
                        </div>
                        <div class="h-px flex-1 bg-slate-200 mx-4" :class="step >= 2 ? 'bg-[var(--color-accent)]' : ''"></div>
                        <div class="flex flex-col items-center">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold transition-colors" :class="step >= 2 ? 'bg-[var(--color-accent)] text-white' : 'bg-slate-100 text-slate-400'">2</div>
                            <span class="mt-2 text-xs font-medium" :class="step >= 2 ? 'text-slate-900' : 'text-slate-400'">Admin</span>
                        </div>
                        <div class="h-px flex-1 bg-slate-200 mx-4" :class="step >= 3 ? 'bg-[var(--color-accent)]' : ''"></div>
                        <div class="flex flex-col items-center">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold transition-colors" :class="step >= 3 ? 'bg-[var(--color-accent)] text-white' : 'bg-slate-100 text-slate-400'">3</div>
                            <span class="mt-2 text-xs font-medium" :class="step >= 3 ? 'text-slate-900' : 'text-slate-400'">Plan</span>
                        </div>
                    </div>

                    <!-- Step 1: Company Information -->
                    <div x-show="step === 1" x-transition.opacity.duration.300ms class="space-y-5">
                        <div>
                            <label for="company_name" class="block text-sm font-semibold text-slate-800">Company name</label>
                            <input
                                id="company_name"
                                name="company_name"
                                type="text"
                                value="{{ old('company_name') }}"
                                :required="step === 1"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                                placeholder="Acme Construction"
                            >
                        </div>

                        <div>
                            <label for="subdomain" class="block text-sm font-semibold text-slate-800">Subdomain</label>
                            <div class="mt-1 flex items-stretch">
                                <input
                                    id="subdomain"
                                    name="subdomain"
                                    type="text"
                                    value="{{ old('subdomain') }}"
                                    :required="step === 1"
                                    class="w-full rounded-l-lg border border-slate-300 border-r-0 px-3 py-2 text-sm lowercase focus:border-slate-500 focus:outline-none"
                                    placeholder="acme"
                                >
                                <span class="inline-flex items-center rounded-r-lg border border-slate-300 bg-slate-50 px-3 text-sm text-slate-500">
                                    .{{ config('tenancy.central_domains')[0] ?? 'localhost' }}
                                </span>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="nextStep(1)"
                            class="w-full rounded-lg bg-[var(--color-accent)] !text-white px-4 py-2.5 text-sm font-semibold hover:opacity-90 transition-colors"
                        >
                            Next Step
                        </button>
                    </div>

                    <!-- Step 2: Admin Tenant -->
                    <div x-show="step === 2" x-cloak x-transition.opacity.duration.300ms class="space-y-5">
                        <div>
                            <label for="admin_name" class="block text-sm font-semibold text-slate-800">Admin full name</label>
                            <input
                                id="admin_name"
                                name="admin_name"
                                type="text"
                                value="{{ old('admin_name') }}"
                                :required="step === 2"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                                placeholder="Jane Doe"
                            >
                        </div>

                        <div>
                            <label for="admin_email" class="block text-sm font-semibold text-slate-800">Admin email</label>
                            <input
                                id="admin_email"
                                name="admin_email"
                                type="email"
                                x-model="email"
                                :required="step === 2"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                                placeholder="admin@acme.com"
                            >
                        </div>

                        <div>
                            <label for="verification_code" class="block text-sm font-semibold text-slate-800">Verification code</label>
                            <div class="mt-1 flex gap-2">
                                <input
                                    id="verification_code"
                                    name="verification_code"
                                    type="text"
                                    :required="step === 2"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm tracking-widest text-center focus:border-slate-500 focus:outline-none"
                                    placeholder="123456"
                                >
                                <button
                                    type="button"
                                    @click="sendCode"
                                    class="shrink-0 rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 disabled:opacity-50"
                                    :disabled="sendingCode || !email"
                                    x-text="sendingCode ? 'Sending...' : (codeSent ? 'Resend code' : 'Send code')"
                                >
                                    Send code
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-emerald-600" x-show="codeSent" x-cloak>Verification code sent to your email.</p>
                            <p class="mt-1 text-xs text-red-600" x-show="codeError" x-text="codeError" x-cloak></p>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-800">Password</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                :required="step === 2"
                                autocomplete="new-password"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                            >
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-slate-800">Confirm password</label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                :required="step === 2"
                                autocomplete="new-password"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                            >
                        </div>

                        <div class="flex gap-3">
                            <button
                                type="button"
                                @click="step = 1"
                                class="w-1/3 rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors"
                            >
                                Back
                            </button>
                            <button
                                type="button"
                                @click="nextStep(2)"
                                class="w-2/3 rounded-lg bg-[var(--color-accent)] !text-white px-4 py-2.5 text-sm font-semibold hover:opacity-90 transition-colors"
                            >
                                Next Step
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Plan & Finalize -->
                    <div x-show="step === 3" x-cloak x-transition.opacity.duration.300ms class="space-y-5">
                        @if(isset($plans) && $plans->isNotEmpty())
                            <div>
                                <label class="block text-sm font-semibold text-slate-800 mb-2">Choose a plan</label>
                                <div class="space-y-2">
                                    @foreach($plans as $plan)
                                        <label class="flex items-start gap-3 rounded-lg border border-slate-200 p-3 cursor-pointer hover:border-slate-400 transition-colors">
                                            <input
                                                type="radio"
                                                name="plan_id"
                                                value="{{ $plan->id }}"
                                                class="mt-1"
                                                {{ (string) old('plan_id', $selectedPlanId ?? '') === (string) $plan->id ? 'checked' : '' }}
                                            >
                                            <span class="flex-1">
                                                <span class="flex items-center justify-between">
                                                    <span class="text-sm font-semibold text-slate-900">{{ $plan->name }}</span>
                                                    @if($plan->is_free)
                                                        <span class="text-sm font-bold text-emerald-700">Free</span>
                                                    @elseif($plan->is_contact_sales)
                                                        <span class="text-sm font-bold text-slate-700">Custom</span>
                                                    @else
                                                        <span class="text-sm font-bold text-slate-700">₱{{ number_format((float) $plan->monthly_price, 2) }}/mo</span>
                                                    @endif
                                                </span>
                                                @if(!empty($plan->tagline))
                                                    <span class="mt-0.5 block text-xs text-slate-600">{{ $plan->tagline }}</span>
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                                No plans available. Please contact support.
                            </div>
                        @endif

                        <label class="flex items-start gap-2 text-sm text-slate-700">
                            <input
                                type="checkbox"
                                name="terms"
                                value="1"
                                class="mt-0.5"
                                {{ old('terms') ? 'checked' : '' }}
                                :required="step === 3"
                            >
                            <span>I agree to the Terms of Service.</span>
                        </label>

                        <div class="flex gap-3">
                            <button
                                type="button"
                                @click="step = 2"
                                class="w-1/3 rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors"
                            >
                                Back
                            </button>
                            <button
                                type="submit"
                                class="w-2/3 rounded-lg bg-[var(--color-accent)] !text-white px-4 py-2.5 text-sm font-semibold hover:bg-slate-800 transition-colors disabled:opacity-75 disabled:cursor-not-allowed"
                                :disabled="sendingCode"
                            >
                                Create company workspace
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('registerForm', () => ({
                step: 1,
                email: '{{ old('admin_email') }}',
                sendingCode: false,
                codeSent: false,
                codeError: '',
                nextStep(currentStep) {
                    // Basic validation before allowing next step to evaluate
                    let isValid = true;
                    
                    if (currentStep === 1) {
                        const cName = document.getElementById('company_name');
                        const sDom = document.getElementById('subdomain');
                        if (!cName.checkValidity()) { cName.reportValidity(); isValid = false; }
                        else if (!sDom.checkValidity()) { sDom.reportValidity(); isValid = false; }
                    } else if (currentStep === 2) {
                        const aName = document.getElementById('admin_name');
                        const aEmail = document.getElementById('admin_email');
                        const vCode = document.getElementById('verification_code');
                        const pwd = document.getElementById('password');
                        const pwdConfirm = document.getElementById('password_confirmation');
                        
                        // Clear custom validity so it doesn't fail from previous checks
                        pwdConfirm.setCustomValidity("");

                        if (!aName.checkValidity()) { aName.reportValidity(); isValid = false; }
                        else if (!aEmail.checkValidity()) { aEmail.reportValidity(); isValid = false; }
                        else if (!vCode.checkValidity()) { vCode.reportValidity(); isValid = false; }
                        else if (!pwd.checkValidity()) { pwd.reportValidity(); isValid = false; }
                        else if (pwd.value !== pwdConfirm.value) {
                            pwdConfirm.setCustomValidity("Passwords don't match");
                            pwdConfirm.reportValidity();
                            isValid = false;
                        } else if (!pwdConfirm.checkValidity()) { 
                            pwdConfirm.reportValidity(); 
                            isValid = false; 
                        }
                    }

                    if (isValid) {
                        this.step = currentStep + 1;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },
                async sendCode() {
                    if (!this.email) {
                        this.codeError = 'Please enter an email first.';
                        return;
                    }
                    
                    this.sendingCode = true;
                    this.codeError = '';
                    this.codeSent = false;
                    
                    try {
                        const payload = { email: this.email };
                        const res = await fetch('{{ route('tenant.register.send-code') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ email: this.email })
                        });
                        
                        if (res.ok) {
                            this.codeSent = true;
                            setTimeout(() => document.getElementById('verification_code').focus(), 100);
                        } else {
                            const data = await res.json().catch(() => ({}));
                            this.codeError = data.message || 'Failed to send verification code. Please try again.';
                        }
                    } catch (e) {
                        this.codeError = 'An error occurred. Please try again later.';
                    }
                    this.sendingCode = false;
                }
            }))
        })
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-layouts.landing>
