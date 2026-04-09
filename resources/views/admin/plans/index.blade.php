<x-layouts.admin>
    <x-slot name="header">Pricing Models</x-slot>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div x-data="adminPlanManager()" class="flex flex-col h-full relative" @keydown.escape.window="closeDrawer()">
        
        <!-- Header & Action -->
        <div class="flex justify-between items-center mb-[24px]">
            <p class="text-[14px] text-[var(--color-text-secondary)]">Define subscription tiers, usage limits, and platform features.</p>
            <button @click="openDrawer(null)" class="h-[36px] px-[16px] bg-[var(--color-text-primary)] hover:bg-black text-[var(--color-bg)] rounded-[5px] text-[12px] font-[600] uppercase tracking-[0.08em] transition-colors shadow-[var(--shadow-1)]">
                Add New Plan
            </button>
        </div>

        @if(session('success'))
            <div class="mb-[24px] px-[16px] py-[12px] bg-[var(--color-success-subtle)] border border-[var(--color-success)] text-[var(--color-success)] text-[13px] font-[500] rounded-[5px] flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-[var(--color-success)] hover:opacity-70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        <!-- Plans Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[20px]">
            @foreach($plans as $plan)
                @php
                    $isTopTier = $plan->slug === 'professional' || $plan->slug === 'enterprise';
                    $borderColor = $isTopTier ? 'var(--color-accent)' : 'var(--color-border)';
                @endphp
                <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-[8px] shadow-[var(--shadow-sm)] flex flex-col relative overflow-hidden group hover:shadow-[var(--shadow-1)] transition-shadow">
                    <!-- Color Bar indicator -->
                    <div class="absolute top-0 left-0 w-full h-[3px]" style="background-color: {{ $borderColor }}"></div>
                    
                    <div class="p-[20px] flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-[16px]">
                            <div>
                                <h3 class="font-ui text-[16px] font-[600] tracking-[0.02em] text-[var(--color-text-primary)]">{{ $plan->name }}</h3>
                                <div class="text-[11px] text-[var(--color-text-muted)] font-mono mt-[2px]">{{ $plan->slug }}</div>
                            </div>
                            <div class="px-[8px] py-[2px] rounded-full text-[10px] font-bold uppercase tracking-wider
                                {{ $plan->status === 'active' ? 'bg-[var(--color-success-subtle)] text-[var(--color-success)]' : '' }}
                                {{ $plan->status === 'draft' ? 'bg-[var(--color-warning-subtle)] text-[var(--color-warning)]' : '' }}
                                {{ $plan->status === 'archived' ? 'bg-[var(--color-bg-subtle)] text-[var(--color-text-muted)]' : '' }}">
                                {{ $plan->status }}
                            </div>
                        </div>

                        <!-- Pricing Info -->
                        <div class="flex justify-between items-end mb-[20px]">
                            <div class="flex flex-col">
                                <span class="text-[11px] uppercase tracking-widest text-[var(--color-text-muted)] font-[600]">Monthly</span>
                                <span class="font-display text-[24px] text-[var(--color-text-primary)] leading-none mt-[4px]">
                                    @if($plan->is_contact_sales) Custom
                                    @elseif($plan->is_free) ₱0
                                    @else ₱{{ number_format($plan->monthly_price) }}
                                    @endif
                                </span>
                            </div>
                            @if(!$plan->is_contact_sales && !$plan->is_free)
                            <div class="flex flex-col text-right">
                                <span class="text-[11px] uppercase tracking-widest text-[var(--color-text-muted)] font-[600]">Annual</span>
                                <span class="font-display text-[18px] text-[var(--color-text-secondary)] leading-none mt-[4px]">₱{{ number_format($plan->annual_price) }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="w-full h-[1px] bg-[var(--color-border)] mb-[16px]"></div>

                        <!-- Metrics -->
                        <div class="flex flex-col gap-[12px] mb-[24px] flex-1">
                            <div class="flex justify-between items-center text-[13px]">
                                <span class="text-[var(--color-text-secondary)]">Active Tenants</span>
                                <span class="font-[600] text-[var(--color-text-primary)]">{{ $plan->tenants_count ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[13px]">
                                <span class="text-[var(--color-text-secondary)]">Max Job Orders</span>
                                <span class="font-[600] text-[var(--color-text-primary)]">{{ $plan->max_job_orders ?? '∞' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[13px]">
                                <span class="text-[var(--color-text-secondary)]">Max Workers</span>
                                <span class="font-[600] text-[var(--color-text-primary)]">{{ $plan->max_workers ?? '∞' }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-[8px] mt-auto">
                            <button @click="openDrawer({{ $plan->toJson() }})" class="flex-1 h-[32px] bg-[var(--color-bg-subtle)] hover:bg-[var(--color-border)] text-[var(--color-text-primary)] rounded-[4px] text-[11px] font-[600] uppercase tracking-widest transition-colors">
                                Edit
                            </button>
                            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="inline-block" data-confirm="Retire this plan?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-[32px] h-[32px] flex items-center justify-center border border-[var(--color-border)] hover:bg-[var(--color-error-subtle)] hover:text-[var(--color-error)] hover:border-[var(--color-error)] rounded-[4px] text-[var(--color-text-muted)] transition-colors">
                                    <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <template x-teleport="body">
            <div x-cloak x-show="drawerOpen" class="fixed inset-0 z-[9998]" role="dialog" aria-modal="true">
                <!-- Modal Backdrop -->
                <div x-show="drawerOpen" x-transition.opacity.duration.200ms class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="closeDrawer()"></div>

                <!-- Modal Dialog -->
                <div x-show="drawerOpen" x-transition.opacity.duration.200ms class="absolute inset-0 flex items-center justify-center p-4 sm:p-8">
                    <div class="w-full max-w-[980px] max-h-[90vh] bg-[var(--color-surface)] border border-[var(--color-border-strong)] shadow-[var(--shadow-3)] overflow-hidden flex flex-col" @click.stop>
                        <div class="flex items-center justify-between px-[24px] py-[18px] border-b border-[var(--color-border)] bg-[var(--color-bg)]">
                            <h2 class="font-display text-[20px] text-[var(--color-text-primary)]" x-text="isEditing ? 'Edit Plan' : 'Create New Plan'"></h2>
                            <button @click="closeDrawer()" class="text-[var(--color-text-muted)] hover:text-[var(--color-text-primary)] transition-colors" aria-label="Close dialog">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto p-[24px]">
                            <form id="planForm" method="POST" :action="formAction" class="grid grid-cols-1 lg:grid-cols-2 gap-[16px]">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" :value="isEditing ? 'PUT' : 'POST'">
                    
                    <!-- Basic Info Box -->
                    <div class="bg-[var(--color-bg-subtle)] border border-[var(--color-border)] rounded-[8px] p-[20px]">
                        <h3 class="text-[11px] uppercase tracking-widest font-[600] text-[var(--color-text-secondary)] mb-[16px]">Core Identification</h3>
                        
                        <div class="space-y-[16px]">
                            <div>
                                <label class="block text-[13px] font-[500] text-[var(--color-text-primary)] mb-[6px]">Plan Name *</label>
                                <input type="text" name="name" x-model="form.name" required class="w-full h-[40px] px-[12px] bg-[var(--color-surface)] border border-[var(--color-border)] rounded-[4px] text-[14px] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] outline-none transition-all">
                            </div>
                            
                            <div>
                                <label class="block text-[13px] font-[500] text-[var(--color-text-primary)] mb-[6px]">Tagline / Description</label>
                                <input type="text" name="tagline" x-model="form.tagline" class="w-full h-[40px] px-[12px] bg-[var(--color-surface)] border border-[var(--color-border)] rounded-[4px] text-[14px] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] outline-none transition-all">
                                <p class="text-[11px] text-[var(--color-text-muted)] mt-[4px]">Appears beneath the plan name on the pricing page.</p>
                            </div>

                            <div class="grid grid-cols-2 gap-[12px]">
                                <div>
                                    <label class="block text-[13px] font-[500] text-[var(--color-text-primary)] mb-[6px]">Badge Label</label>
                                    <input type="text" name="badge_label" x-model="form.badge_label" placeholder="e.g. Most Popular" class="w-full h-[40px] px-[12px] bg-[var(--color-surface)] border border-[var(--color-border)] rounded-[4px] text-[14px] focus:border-[var(--color-accent)] outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[13px] font-[500] text-[var(--color-text-primary)] mb-[6px]">Status</label>
                                    <select name="status" x-model="form.status" class="w-full h-[40px] px-[12px] bg-[var(--color-surface)] border border-[var(--color-border)] rounded-[4px] text-[14px] focus:border-[var(--color-accent)] outline-none transition-all">
                                        <option value="draft">Draft</option>
                                        <option value="active">Active</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Box -->
                    <div class="bg-[var(--color-bg-subtle)] border border-[var(--color-border)] rounded-[8px] p-[20px]">
                        <h3 class="text-[11px] uppercase tracking-widest font-[600] text-[var(--color-text-secondary)] mb-[16px]">Pricing Structure</h3>
                        
                        <div class="space-y-[16px]">
                            <div class="flex gap-[16px]">
                                <label class="flex items-center gap-[8px] cursor-pointer relative">
                                    <input type="checkbox" name="is_free" value="1" x-model="form.is_free" class="w-4 h-4 text-[var(--color-accent)] rounded border-[var(--color-border)] focus:ring-[var(--color-accent)]">
                                    <span class="text-[13px] font-[500] text-[var(--color-text-primary)]">Free Plan</span>
                                </label>
                                <label class="flex items-center gap-[8px] cursor-pointer relative">
                                    <input type="checkbox" name="is_contact_sales" value="1" x-model="form.is_contact_sales" class="w-4 h-4 text-[var(--color-accent)] rounded border-[var(--color-border)] focus:ring-[var(--color-accent)]">
                                    <span class="text-[13px] font-[500] text-[var(--color-text-primary)]">Contact Sales (Custom)</span>
                                </label>
                            </div>

                            <div x-show="!form.is_free && !form.is_contact_sales" class="grid grid-cols-2 gap-[12px]">
                                <div>
                                    <label class="block text-[13px] font-[500] text-[var(--color-text-primary)] mb-[6px]">Monthly Price</label>
                                    <div class="relative">
                                        <span class="absolute left-[12px] top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] font-mono">₱</span>
                                        <input type="number" step="0.01" name="monthly_price" x-model="form.monthly_price" class="w-full h-[40px] pl-[28px] pr-[12px] bg-[var(--color-surface)] border border-[var(--color-border)] rounded-[4px] text-[14px] focus:border-[var(--color-accent)] outline-none transition-all font-mono">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[13px] font-[500] text-[var(--color-text-primary)] mb-[6px]">Annual Price</label>
                                    <div class="relative">
                                        <span class="absolute left-[12px] top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] font-mono">₱</span>
                                        <input type="number" step="0.01" name="annual_price" x-model="form.annual_price" class="w-full h-[40px] pl-[28px] pr-[12px] bg-[var(--color-surface)] border border-[var(--color-border)] rounded-[4px] text-[14px] focus:border-[var(--color-accent)] outline-none transition-all font-mono">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Limits Box -->
                    <div class="bg-[var(--color-bg-subtle)] border border-[var(--color-border)] rounded-[8px] p-[20px]">
                        <h3 class="text-[11px] uppercase tracking-widest font-[600] text-[var(--color-text-secondary)] mb-[16px]">Usage Limits</h3>
                        
                        <div class="grid grid-cols-2 gap-[16px]">
                            <div>
                                <label class="block text-[13px] font-[500] text-[var(--color-text-primary)] mb-[6px]">Max Job Orders</label>
                                <input type="number" name="max_job_orders" x-model="form.max_job_orders" placeholder="Blank for unlimited" class="w-full h-[40px] px-[12px] bg-[var(--color-surface)] border border-[var(--color-border)] rounded-[4px] text-[14px] focus:border-[var(--color-accent)] outline-none transition-all font-mono text-[var(--color-text-secondary)]">
                            </div>
                            <div>
                                <label class="block text-[13px] font-[500] text-[var(--color-text-primary)] mb-[6px]">Max Workers</label>
                                <input type="number" name="max_workers" x-model="form.max_workers" placeholder="Blank for unlimited" class="w-full h-[40px] px-[12px] bg-[var(--color-surface)] border border-[var(--color-border)] rounded-[4px] text-[14px] focus:border-[var(--color-accent)] outline-none transition-all font-mono text-[var(--color-text-secondary)]">
                            </div>
                            <div>
                                <label class="block text-[13px] font-[500] text-[var(--color-text-primary)] mb-[6px]">Max Admin Seats</label>
                                <input type="number" name="max_admins" x-model="form.max_admins" placeholder="Blank for unlimited" class="w-full h-[40px] px-[12px] bg-[var(--color-surface)] border border-[var(--color-border)] rounded-[4px] text-[14px] focus:border-[var(--color-accent)] outline-none transition-all font-mono text-[var(--color-text-secondary)]">
                            </div>
                            <div>
                                <label class="block text-[13px] font-[500] text-[var(--color-text-primary)] mb-[6px]">Sort Order</label>
                                <input type="number" name="sort_order" x-model="form.sort_order" class="w-full h-[40px] px-[12px] bg-[var(--color-surface)] border border-[var(--color-border)] rounded-[4px] text-[14px] focus:border-[var(--color-accent)] outline-none transition-all font-mono text-[var(--color-text-secondary)]">
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="bg-[var(--color-bg-subtle)] border border-[var(--color-border)] rounded-[8px] p-[20px] lg:col-span-2">
                        <h3 class="text-[11px] uppercase tracking-widest font-[600] text-[var(--color-text-secondary)] mb-[16px]">Features</h3>
                        <p class="text-[12px] text-[var(--color-text-muted)] mb-[12px]">Enter one feature per line. These display with checkmarks on the pricing card.</p>
                        
                        <textarea name="features" x-model="form.featuresString" rows="5" class="w-full p-[12px] bg-[var(--color-surface)] border border-[var(--color-border)] rounded-[4px] text-[13px] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] outline-none transition-all font-mono leading-relaxed resize-none"></textarea>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end pt-[12px] lg:col-span-2 border-t border-[var(--color-border)] mt-[4px]">
                        <button type="button" @click="closeDrawer()" class="h-[40px] px-[20px] text-[13px] font-[600] text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] mr-[12px]">Cancel</button>
                        <button type="submit" class="h-[40px] px-[24px] bg-[var(--color-accent)] hover:opacity-90 text-white rounded-[5px] text-[13px] font-[600] tracking-wide transition-opacity shadow-[var(--shadow-1)]">
                            Save Configuration
                        </button>
                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>

    </div>

    <!-- Script strictly required to manipulate the Alpine.js state -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminPlanManager', () => ({
                drawerOpen: false,
                isEditing: false,
                formAction: '{{ route('admin.plans.store') }}',
                baseUrl: '{{ url('/admin/plans') }}',
                
                form: {
                    id: null,
                    name: '',
                    tagline: '',
                    badge_label: '',
                    status: 'active',
                    is_free: false,
                    is_contact_sales: false,
                    monthly_price: '',
                    annual_price: '',
                    max_job_orders: '',
                    max_workers: '',
                    max_admins: '',
                    sort_order: 0,
                    featuresString: ''
                },

                openDrawer(plan = null) {
                    if (plan) {
                        this.isEditing = true;
                        this.formAction = this.baseUrl + '/' + plan.id;
                        
                        // Map data
                        this.form.id = plan.id;
                        this.form.name = plan.name || '';
                        this.form.tagline = plan.tagline || '';
                        this.form.badge_label = plan.badge_label || '';
                        this.form.status = plan.status || 'active';
                        this.form.is_free = !!plan.is_free;
                        this.form.is_contact_sales = !!plan.is_contact_sales;
                        this.form.monthly_price = plan.monthly_price || '';
                        this.form.annual_price = plan.annual_price || '';
                        this.form.max_job_orders = plan.max_job_orders || '';
                        this.form.max_workers = plan.max_workers || '';
                        this.form.max_admins = plan.max_admins || '';
                        this.form.sort_order = plan.sort_order || 0;
                        
                        // Handle JSON features array to string conversion
                        let feats = plan.features || [];
                        if (typeof feats === 'string') {
                            try { feats = JSON.parse(feats); } catch(e) { feats = []; }
                        }
                        this.form.featuresString = Array.isArray(feats) ? feats.join('\n') : '';
                        
                    } else {
                        this.isEditing = false;
                        this.formAction = '{{ route('admin.plans.store') }}';
                        this.resetForm();
                    }
                    
                    this.drawerOpen = true;
                },

                closeDrawer() {
                    this.drawerOpen = false;
                    setTimeout(() => {
                        if (!this.drawerOpen) this.resetForm();
                    }, 300);
                },

                resetForm() {
                    this.form = {
                        id: null, name: '', tagline: '', badge_label: '', status: 'active',
                        is_free: false, is_contact_sales: false, monthly_price: '', annual_price: '',
                        max_job_orders: '', max_workers: '', max_admins: '', sort_order: 0, featuresString: ''
                    };
                }
            }));
        });
    </script>
</x-layouts.admin>