<x-layouts.landing>
    <x-slot name="header">Pricing</x-slot>

    <div x-data="{ billing: 'monthly' }" class="w-full bg-[var(--color-bg)] py-[80px]">
        <!-- Page Header Section -->
        <div class="max-w-[1100px] mx-auto px-4 sm:px-6 flex flex-col items-center">
            <h2 class="text-[11px] uppercase tracking-widest text-[var(--color-accent)] font-[600] mb-4">Pricing</h2>
            <h1 class="font-display text-[42px] leading-[1.15] text-[var(--color-text-primary)] text-center mb-4">Simple, transparent pricing</h1>
            <p class="text-[16px] text-[var(--color-text-secondary)] text-center max-w-[520px] mb-8">
                Choose the plan that fits your organization. Upgrade or downgrade at any time.
            </p>

            <!-- Billing Toggle -->
            <div class="relative flex items-center p-[3px] border border-[var(--color-border)] rounded-full bg-[var(--color-surface)] mb-12">
                <button @click="billing = 'monthly'" 
                        :class="billing === 'monthly' ? 'bg-[var(--color-accent)] text-white' : 'bg-transparent text-[var(--color-text-secondary)]'"
                        class="relative z-10 px-[20px] py-[6px] text-sm font-medium rounded-full transition-colors duration-200">
                    Monthly
                </button>
                <div class="flex items-center">
                    <button @click="billing = 'annual'" 
                            :class="billing === 'annual' ? 'bg-[var(--color-accent)] text-white' : 'bg-transparent text-[var(--color-text-secondary)]'"
                            class="relative z-10 px-[20px] py-[6px] text-sm font-medium rounded-full transition-colors duration-200">
                        Annual
                    </button>
                    <!-- Annual savings callout -->
                    <span class="absolute -right-24 bg-[var(--color-success-subtle)] text-[var(--color-success)] text-[10px] font-bold px-2 py-0.5 rounded-full whitespace-nowrap transition-opacity duration-200"
                          :class="billing === 'annual' ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                        Save 17%
                    </span>
                </div>
            </div>

            <!-- Pricing Cards Grid -->
            <div class="w-full grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-[20px] items-start">
                @foreach($plans as $plan)
                    @php
                        $isFeatured = $plan->slug === 'professional';
                        $bg = $isFeatured ? 'var(--color-dark-bg)' : 'var(--color-surface)';
                        $border = $isFeatured ? 'var(--color-accent)' : ($plan->slug === 'enterprise' ? 'var(--color-border-strong)' : 'var(--color-border)');
                        $shadow = $isFeatured ? 'var(--shadow-3)' : ($plan->is_free ? 'none' : 'var(--shadow-1)');
                        $textPrimary = $isFeatured ? 'var(--color-dark-text)' : 'var(--color-text-primary)';
                        $textMuted = $isFeatured ? 'var(--color-dark-text-muted)' : 'var(--color-text-muted)';
                        $textSecondary = $isFeatured ? 'var(--color-dark-text-muted)' : 'var(--color-text-secondary)';
                        $divider = $isFeatured ? 'var(--color-dark-border)' : 'var(--color-border)';
                        $scale = $isFeatured ? 'scale(1.03)' : 'none';
                        $checkColor = $isFeatured ? '#86EFAC' : 'var(--color-success)';
                    @endphp

                    <div class="relative flex flex-col rounded-[10px] p-6 transition-all duration-150 group"
                         style="background: {{ $bg }}; border: 1px solid {{ $border }}; box-shadow: {{ $shadow }}; "
                         :style="window.innerWidth >= 768 ? 'transform: {{ $scale }};' : ''"
                         :class="{'hover:-translate-y-[2px]': true, 'hover:shadow-[var(--shadow-2)]': !{{ $isFeatured ? 'true' : 'false' }}, 'hover:shadow-[0_0_0_1px_var(--color-accent)]': {{ $isFeatured ? 'true' : 'false' }}}">
                         
                        @if($plan->badge_label)
                            @php
                                $badgeBg = $isFeatured ? 'var(--color-accent)' : 'var(--color-info-subtle)';
                                $badgeText = $isFeatured ? 'white' : 'var(--color-info)';
                            @endphp
                            <div class="absolute right-[16px] top-[-1px] -translate-y-1/2 px-[10px] py-[3px] rounded-full text-[10px] font-bold tracking-[0.10em] uppercase border"
                                 style="background: {{ $badgeBg }}; color: {{ $badgeText }}; border-color: currentcolor;">
                                {{ $plan->badge_label }}
                            </div>
                        @endif

                        <!-- Header -->
                        <div class="flex justify-between items-start">
                            <h3 class="font-ui text-[15px] font-[600] tracking-[0.04em] uppercase" style="color: {{ $textPrimary }}">{{ $plan->name }}</h3>
                            <div class="text-right flex flex-col relative h-[60px] w-full items-end">
                                @if($plan->is_contact_sales)
                                    <div class="font-display text-[32px] leading-none" style="color: {{ $textPrimary }}">Custom</div>
                                    <div class="text-[12px] font-[400] mt-1 whitespace-nowrap" style="color: {{ $textMuted }}">Contact our team</div>
                                @elseif($plan->is_free)
                                    <div class="font-display text-[40px] leading-none" style="color: {{ $textPrimary }}">
                                        <span class="text-[20px] align-top">₱</span>0<span class="text-[14px] font-[400]" style="color: {{ $textMuted }}">/month</span>
                                    </div>
                                @else
                                    <!-- Monthly Price -->
                                    <div class="absolute right-0 top-0 transition-opacity duration-200"
                                         :class="billing === 'monthly' ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                                        <div class="font-display text-[40px] leading-none whitespace-nowrap" style="color: {{ $textPrimary }}">
                                            <span class="text-[20px] align-top">₱</span>{{ number_format($plan->monthly_price) }}<span class="text-[14px] font-[400]" style="color: {{ $textMuted }}">/month</span>
                                        </div>
                                    </div>
                                    <!-- Annual Price --><!-- Animation requirements explicitly requested: fade out up, new price fades in down - 200ms -->
                                    <div class="absolute right-0 top-0 text-right transition-opacity duration-200"
                                         :class="billing === 'annual' ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                                        <div class="font-display text-[32px] leading-none whitespace-nowrap" style="color: {{ $textPrimary }}">
                                            <span class="text-[18px] align-top">₱</span>{{ number_format($plan->annual_price) }}<span class="text-[14px] font-[400]" style="color: {{ $textMuted }}">/year</span>
                                        </div>
                                        <div class="text-[12px] font-[400] mt-1" style="color: {{ $textMuted }}">₱{{ number_format($plan->monthly_price * 0.833) }}/mo</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <p class="font-ui text-[13px] mt-[4px] mb-[20px]" style="color: {{ $textSecondary }}">{{ $plan->tagline }}</p>

                        <div class="w-full h-[1px] my-[20px]" style="background-color: {{ $divider }}"></div>

                        <!-- Features -->
                        <ul class="flex-1 flex flex-col gap-[10px]">
                            @if(is_array($plan->features))
                                @foreach($plan->features as $feature)
                                    <li class="flex items-start gap-[10px]">
                                        <svg class="w-[14px] h-[14px] mt-0.5 flex-shrink-0" style="color: {{ $checkColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        <span class="text-[13px]" style="color: {{ $textPrimary }}">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            @endif
                        </ul>

                        <!-- CTA -->
                        <a href="{{ route('tenant.register') }}?plan={{ $plan->slug }}" 
                           class="mt-[24px] w-full h-[40px] flex items-center justify-center text-[12px] font-[600] uppercase tracking-[0.08em] rounded-[5px] transition-colors"
                           style="
                                @if($isFeatured)
                                    background: var(--color-accent); color: white; border: none;
                                @elseif($plan->is_free || $plan->is_contact_sales)
                                    background: transparent; color: var(--color-text-primary); border: 1px solid var(--color-border);
                                @else
                                    background: var(--color-surface-raised); color: var(--color-text-primary); border: 1px solid var(--color-border);
                                @endif
                           ">
                            @if($plan->is_contact_sales)
                                Contact Sales
                            @elseif($plan->is_free)
                                Get Started Free
                            @else
                                Get Started
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Trust Signals Below Cards -->
            <div class="mt-[40px] flex justify-center items-center gap-2 text-[12px] text-[var(--color-text-muted)]">
                <span>No credit card required for Free plan</span>
                <span class="w-1 h-1 rounded-full bg-[var(--color-border-strong)] mx-2"></span>
                <span>Cancel anytime</span>
                <span class="w-1 h-1 rounded-full bg-[var(--color-border-strong)] mx-2"></span>
                <span>Secure · Philippine-hosted data</span>
            </div>

            <!-- Feature Comparison Table -->
            <div class="w-full mt-[80px] overflow-x-auto pb-8">
                <div class="text-center mb-12">
                    <h2 class="text-[11px] uppercase tracking-widest text-[var(--color-accent)] font-[600] mb-4">DEEP DIVE</h2>
                    <h3 class="font-display text-[28px] text-[var(--color-text-primary)]">Compare all features</h3>
                </div>

                <table class="w-full min-w-[800px] border-collapse">
                    <thead>
                        <tr>
                            <th class="p-4 text-left border-b border-[var(--color-border)] w-1/3"></th>
                            @foreach($plans as $plan)
                                <th class="p-4 text-center border-b border-[var(--color-border)]" style="{{ $plan->slug === 'professional' ? 'background: var(--color-dark-bg); color: white; border-top-left-radius: 7px; border-top-right-radius: 7px;' : '' }}">
                                    <div class="font-ui text-[15px] font-[600] tracking-[0.04em] uppercase" style="{{ $plan->slug === 'professional' ? 'color: var(--color-dark-text)' : 'color: var(--color-text-primary)' }}">{{ $plan->name }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Group: Job Management -->
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-[11px] uppercase tracking-widest text-[var(--color-text-muted)] bg-[var(--color-bg-subtle)] border-t-[2px] border-[var(--color-border-strong)]">Job Management</td>
                        </tr>
                        @php
                            $featuresMatrix = [
                                'Job order creation' => ['Free' => '20/mo', 'Starter' => '150/mo', 'Professional' => 'Unlimited', 'Enterprise' => 'Unlimited'],
                                'Worker assignment' => ['Free' => '3 workers', 'Starter' => '10 workers', 'Professional' => '50 workers', 'Enterprise' => 'Unlimited'],
                                'Real-time tracking' => ['Free' => true, 'Starter' => true, 'Professional' => true, 'Enterprise' => true],
                                'Status pipeline' => ['Free' => true, 'Starter' => true, 'Professional' => true, 'Enterprise' => true],
                            ];
                        @endphp
                        @foreach($featuresMatrix as $featureName => $availability)
                            <tr class="border-b border-[var(--color-border)] {{ $loop->index % 2 == 0 ? 'bg-[var(--color-surface)]' : 'bg-[var(--color-bg-subtle)]' }}">
                                <td class="px-4 py-3 text-[13px] text-[var(--color-text-primary)] sticky left-0 {{ $loop->index % 2 == 0 ? 'bg-[var(--color-surface)]' : 'bg-[var(--color-bg-subtle)]' }} h-[48px]">{{ $featureName }}</td>
                                @foreach($plans as $plan)
                                    <td class="px-4 py-3 text-center text-[13px] {{ $plan->slug === 'professional' ? 'bg-[var(--color-dark-bg)] border-b-[var(--color-dark-border)]' : '' }}">
                                        @if(is_bool($availability[$plan->name]))
                                            @if($availability[$plan->name])
                                                <div class="flex justify-center"><svg class="w-4 h-4 {{ $plan->slug === 'professional' ? 'text-[#86EFAC]' : 'text-[var(--color-success)]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>
                                            @else
                                                <div class="text-[var(--color-text-muted)]">—</div>
                                            @endif
                                        @else
                                            <span class="{{ $plan->slug === 'professional' ? 'text-[var(--color-dark-text)]' : 'text-[var(--color-text-secondary)]' }}">{{ $availability[$plan->name] }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        <!-- Group: Administration -->
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-[11px] uppercase tracking-widest text-[var(--color-text-muted)] bg-[var(--color-bg-subtle)] border-t-[2px] border-[var(--color-border-strong)]">Administration</td>
                        </tr>
                        @php
                            $featuresMatrix = [
                                'Admin accounts' => ['Free' => '1', 'Starter' => '2', 'Professional' => '5', 'Enterprise' => 'Unlimited'],
                                'Dashboard access' => ['Free' => true, 'Starter' => true, 'Professional' => true, 'Enterprise' => true],
                                'Audit logs' => ['Free' => false, 'Starter' => false, 'Professional' => false, 'Enterprise' => true],
                            ];
                        @endphp
                        @foreach($featuresMatrix as $featureName => $availability)
                            <tr class="border-b border-[var(--color-border)] {{ $loop->index % 2 == 0 ? 'bg-[var(--color-surface)]' : 'bg-[var(--color-bg-subtle)]' }}">
                                <td class="px-4 py-3 text-[13px] text-[var(--color-text-primary)] sticky left-0 {{ $loop->index % 2 == 0 ? 'bg-[var(--color-surface)]' : 'bg-[var(--color-bg-subtle)]' }} h-[48px]">{{ $featureName }}</td>
                                @foreach($plans as $plan)
                                    <td class="px-4 py-3 text-center text-[13px] {{ $plan->slug === 'professional' ? 'bg-[var(--color-dark-bg)] border-b-[var(--color-dark-border)]' : '' }}">
                                        @if(is_bool($availability[$plan->name]))
                                            @if($availability[$plan->name])
                                                <div class="flex justify-center"><svg class="w-4 h-4 {{ $plan->slug === 'professional' ? 'text-[#86EFAC]' : 'text-[var(--color-success)]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>
                                            @else
                                                <div class="text-[var(--color-text-muted)]">—</div>
                                            @endif
                                        @else
                                            <span class="{{ $plan->slug === 'professional' ? 'text-[var(--color-dark-text)]' : 'text-[var(--color-text-secondary)]' }}">{{ $availability[$plan->name] }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        <!-- Group: Payroll -->
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-[11px] uppercase tracking-widest text-[var(--color-text-muted)] bg-[var(--color-bg-subtle)] border-t-[2px] border-[var(--color-border-strong)]">Payroll</td>
                        </tr>
                        @php
                            $featuresMatrix = [
                                'Payroll processing' => ['Free' => false, 'Starter' => false, 'Professional' => true, 'Enterprise' => true],
                                'Automated computation' => ['Free' => false, 'Starter' => false, 'Professional' => true, 'Enterprise' => true],
                                'Export formats' => ['Free' => false, 'Starter' => false, 'Professional' => 'CSV, PDF', 'Enterprise' => 'CSV, PDF, Excel'],
                            ];
                        @endphp
                        @foreach($featuresMatrix as $featureName => $availability)
                            <tr class="border-b border-[var(--color-border)] {{ $loop->index % 2 == 0 ? 'bg-[var(--color-surface)]' : 'bg-[var(--color-bg-subtle)]' }}">
                                <td class="px-4 py-3 text-[13px] text-[var(--color-text-primary)] sticky left-0 {{ $loop->index % 2 == 0 ? 'bg-[var(--color-surface)]' : 'bg-[var(--color-bg-subtle)]' }} h-[48px]">{{ $featureName }}</td>
                                @foreach($plans as $plan)
                                    <td class="px-4 py-3 text-center text-[13px] {{ $plan->slug === 'professional' ? 'bg-[var(--color-dark-bg)] border-b-[var(--color-dark-border)]' : '' }}">
                                        @if(is_bool($availability[$plan->name]))
                                            @if($availability[$plan->name])
                                                <div class="flex justify-center"><svg class="w-4 h-4 {{ $plan->slug === 'professional' ? 'text-[#86EFAC]' : 'text-[var(--color-success)]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>
                                            @else
                                                <div class="text-[var(--color-text-muted)]">—</div>
                                            @endif
                                        @else
                                            <span class="{{ $plan->slug === 'professional' ? 'text-[var(--color-dark-text)]' : 'text-[var(--color-text-secondary)]' }}">{{ $availability[$plan->name] }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        <!-- Group: Support -->
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-[11px] uppercase tracking-widest text-[var(--color-text-muted)] bg-[var(--color-bg-subtle)] border-t-[2px] border-[var(--color-border-strong)]">Support</td>
                        </tr>
                        @php
                            $featuresMatrix = [
                                'Community' => ['Free' => true, 'Starter' => true, 'Professional' => true, 'Enterprise' => true],
                                'Email' => ['Free' => false, 'Starter' => true, 'Professional' => true, 'Enterprise' => true],
                                'Priority email' => ['Free' => false, 'Starter' => false, 'Professional' => true, 'Enterprise' => true],
                                'Dedicated account manager' => ['Free' => false, 'Starter' => false, 'Professional' => false, 'Enterprise' => true],
                            ];
                        @endphp
                        @foreach($featuresMatrix as $featureName => $availability)
                            <tr class="border-b border-[var(--color-border)] {{ $loop->index % 2 == 0 ? 'bg-[var(--color-surface)]' : 'bg-[var(--color-bg-subtle)]' }}">
                                <td class="px-4 py-3 text-[13px] text-[var(--color-text-primary)] sticky left-0 {{ $loop->index % 2 == 0 ? 'bg-[var(--color-surface)]' : 'bg-[var(--color-bg-subtle)]' }} h-[48px]">{{ $featureName }}</td>
                                @foreach($plans as $plan)
                                    <td class="px-4 py-3 text-center text-[13px] {{ $plan->slug === 'professional' ? 'bg-[var(--color-dark-bg)] border-b-[var(--color-dark-border)]' : '' }}">
                                        @if(is_bool($availability[$plan->name]))
                                            @if($availability[$plan->name])
                                                <div class="flex justify-center"><svg class="w-4 h-4 {{ $plan->slug === 'professional' ? 'text-[#86EFAC]' : 'text-[var(--color-success)]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>
                                            @else
                                                <div class="text-[var(--color-text-muted)]">—</div>
                                            @endif
                                        @else
                                            <span class="{{ $plan->slug === 'professional' ? 'text-[var(--color-dark-text)]' : 'text-[var(--color-text-secondary)]' }}">{{ $availability[$plan->name] }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        <!-- Group: Advanced -->
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-[11px] uppercase tracking-widest text-[var(--color-text-muted)] bg-[var(--color-bg-subtle)] border-t-[2px] border-[var(--color-border-strong)]">Advanced</td>
                        </tr>
                        @php
                            $featuresMatrix = [
                                'Custom integrations' => ['Free' => false, 'Starter' => false, 'Professional' => false, 'Enterprise' => true],
                                'SSO/SAML' => ['Free' => false, 'Starter' => false, 'Professional' => false, 'Enterprise' => true],
                                'API access' => ['Free' => false, 'Starter' => false, 'Professional' => false, 'Enterprise' => true],
                                'Custom SLA' => ['Free' => false, 'Starter' => false, 'Professional' => false, 'Enterprise' => true],
                            ];
                        @endphp
                        @foreach($featuresMatrix as $featureName => $availability)
                            <tr class="border-b border-[var(--color-border)] {{ $loop->index % 2 == 0 ? 'bg-[var(--color-surface)]' : 'bg-[var(--color-bg-subtle)]' }}">
                                <td class="px-4 py-3 text-[13px] text-[var(--color-text-primary)] sticky left-0 {{ $loop->index % 2 == 0 ? 'bg-[var(--color-surface)]' : 'bg-[var(--color-bg-subtle)]' }} h-[48px]">{{ $featureName }}</td>
                                @foreach($plans as $plan)
                                    <td class="px-4 py-3 text-center text-[13px] {{ $plan->slug === 'professional' ? 'bg-[var(--color-dark-bg)] border-b-[var(--color-dark-border)] rounded-b-md' : '' }}">
                                        @if(is_bool($availability[$plan->name]))
                                            @if($availability[$plan->name])
                                                <div class="flex justify-center"><svg class="w-4 h-4 {{ $plan->slug === 'professional' ? 'text-[#86EFAC]' : 'text-[var(--color-success)]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>
                                            @else
                                                <div class="text-[var(--color-text-muted)]">—</div>
                                            @endif
                                        @else
                                            <span class="{{ $plan->slug === 'professional' ? 'text-[var(--color-dark-text)]' : 'text-[var(--color-text-secondary)]' }}">{{ $availability[$plan->name] }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-layouts.landing>
