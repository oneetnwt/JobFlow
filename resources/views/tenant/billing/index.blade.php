<x-layouts.tenant>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-[20px] font-bold text-foreground">Billing & Subscription</h1>
        </div>
    </x-slot>

    <div class="page-content">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Current Plan Status --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="card p-6 border-t-4 border-t-primary relative bg-surface">
                    <h2 class="text-xs font-bold text-muted uppercase tracking-widest mb-4">Current Status</h2>
                    
                    <div class="mb-6">
                        @if($subscription->status === 'trialing')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-500 border border-amber-500/30 uppercase tracking-wider">
                                Trial Active
                            </span>
                        @elseif($subscription->status === 'active')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-500 border border-emerald-500/30 uppercase tracking-wider">
                                Active Subscription
                            </span>
                        @elseif($subscription->status === 'past_due')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-500 border border-rose-500/30 uppercase tracking-wider">
                                Past Due
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-500 border border-rose-500/30 uppercase tracking-wider">
                                Expired / Canceled
                            </span>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-muted font-semibold uppercase tracking-wider">Current Plan</p>
                            <p class="text-lg font-bold text-foreground mt-1">{{ $subscription->plan->name }}</p>
                        </div>
                        
                        @if($subscription->status === 'trialing')
                            <div>
                                <p class="text-xs text-muted font-semibold uppercase tracking-wider">Trial Ends In</p>
                                <p class="text-lg font-bold text-foreground mt-1">
                                    {{ rtrim(str_replace('after', '', $subscription->trial_ends_at->diffForHumans()), ' ') }}
                                </p>
                                <p class="text-xs text-muted mt-1">{{ $subscription->trial_ends_at->format('M j, Y g:i A') }}</p>
                            </div>
                        @elseif($subscription->status === 'active' && $subscription->current_period_end)
                            <div>
                                <p class="text-xs text-muted font-semibold uppercase tracking-wider">Next Renewal</p>
                                <p class="text-lg font-bold text-foreground mt-1">
                                    {{ $subscription->current_period_end->format('F j, Y') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Invoice History --}}
                <div class="card overflow-hidden">
                    <div class="px-5 py-4 border-b border-border bg-surface-alt">
                        <h3 class="text-xs font-bold text-foreground uppercase tracking-widest">Billing History</h3>
                    </div>
                    
                    <div class="p-0">
                        @forelse($invoices as $invoice)
                            <div class="flex items-center justify-between p-4 border-b border-border last:border-0 hover:bg-surface-alt transition-colors">
                                <div>
                                    <p class="text-sm font-bold text-foreground">₱{{ number_format($invoice->amount, 2) }}</p>
                                    <p class="text-xs text-muted mt-1">{{ $invoice->created_at->format('M j, Y') }}</p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        {{ $invoice->status === 'paid' ? 'bg-emerald-500/20 text-emerald-500' : 'bg-amber-500/20 text-amber-500' }}">
                                        {{ $invoice->status }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-sm text-muted">
                                No invoice history available.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Plans Selection --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="card p-6">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-foreground mb-2">Available Plans</h2>
                        <p class="text-sm text-muted">Upgrade your workspace to unlock more features and capacity.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($plans as $plan)
                            @php
                                $isCurrentPlan = $subscription->plan_id === $plan->id;
                            @endphp
                            
                            <div class="relative rounded-xl border {{ $isCurrentPlan ? 'border-primary ring-1 ring-primary' : 'border-border' }} bg-surface p-6 flex flex-col h-full shadow-sm hover:border-primary/50 transition-colors">
                                @if($isCurrentPlan)
                                    <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                                        <span class="bg-primary text-primary-foreground text-[10px] font-bold uppercase tracking-wider py-1 px-3 rounded-full shadow-sm">
                                            Current Plan
                                        </span>
                                    </div>
                                @endif

                                <div class="mb-5">
                                    <h3 class="text-lg font-extrabold text-foreground">{{ $plan->name }}</h3>
                                    <div class="mt-3 flex items-baseline text-foreground">
                                        <span class="text-3xl font-black tracking-tight">₱{{ number_format($plan->price, 2) }}</span>
                                        <span class="ml-1 text-sm font-semibold text-muted">/mo</span>
                                    </div>
                                    <p class="mt-3 text-sm text-muted line-clamp-2 min-h-[40px]">{{ $plan->description }}</p>
                                </div>

                                <ul class="mt-2 mb-6 space-y-3 flex-1">
                                    @php
                                        // Decode features or use empty array
                                        $features = is_string($plan->features) ? json_decode($plan->features, true) : ($plan->features ?? []);
                                    @endphp
                                    
                                    @foreach($features as $featureKey => $featureName)
                                        <li class="flex items-start">
                                            <svg class="h-5 w-5 text-primary shrink-0 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-sm text-foreground">{{ is_string($featureName) ? $featureName : Str::headline($featureKey) }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="mt-auto pt-6 border-t border-border">
                                    @if($isCurrentPlan && $subscription->status === 'active')
                                        <button disabled class="w-full btn btn-outline opacity-50 cursor-not-allowed">
                                            Current Active Plan
                                        </button>
                                    @else
                                        <form action="{{ route('tenant.billing.checkout') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                            <button type="submit" class="w-full btn {{ $isCurrentPlan ? 'btn-primary' : 'btn-outline' }}">
                                                {{ $isCurrentPlan && $subscription->status === 'trialing' ? 'Activate / Pay Now' : 'Select Plan' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                @if($subscription->status === 'past_due' || $subscription->status === 'expired')
                    <div class="rounded-lg bg-rose-500/10 border border-rose-500/20 p-5 flex items-start gap-4">
                        <svg class="h-6 w-6 text-rose-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-bold text-rose-500 uppercase tracking-widest">Action Required</h3>
                            <p class="mt-1 text-sm text-rose-600/80">
                                Your account requires payment to restore full access. Please complete checking out one of the active plans above.
                            </p>
                        </div>
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</x-layouts.tenant>