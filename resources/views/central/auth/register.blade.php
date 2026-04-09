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

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                <form action="{{ route('tenant.register.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="billing_cycle" value="{{ old('billing_cycle', 'monthly') }}">

                    <div>
                        <label for="company_name" class="block text-sm font-semibold text-slate-800">Company name</label>
                        <input
                            id="company_name"
                            name="company_name"
                            type="text"
                            value="{{ old('company_name') }}"
                            required
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
                                required
                                class="w-full rounded-l-lg border border-slate-300 border-r-0 px-3 py-2 text-sm lowercase focus:border-slate-500 focus:outline-none"
                                placeholder="acme"
                            >
                            <span class="inline-flex items-center rounded-r-lg border border-slate-300 bg-slate-50 px-3 text-sm text-slate-500">
                                .{{ config('tenancy.central_domains')[0] ?? 'localhost' }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label for="admin_name" class="block text-sm font-semibold text-slate-800">Admin full name</label>
                        <input
                            id="admin_name"
                            name="admin_name"
                            type="text"
                            value="{{ old('admin_name') }}"
                            required
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
                            value="{{ old('admin_email') }}"
                            required
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                            placeholder="admin@acme.com"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-800">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
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
                            required
                            autocomplete="new-password"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        >
                    </div>

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
                    @endif

                    <label class="flex items-start gap-2 text-sm text-slate-700">
                        <input
                            type="checkbox"
                            name="terms"
                            value="1"
                            class="mt-0.5"
                            {{ old('terms') ? 'checked' : '' }}
                            required
                        >
                        <span>I agree to the Terms of Service.</span>
                    </label>

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 transition-colors"
                    >
                        Create company workspace
                    </button>
                </form>
            </div>
        </div>
    </section>
</x-layouts.landing>
