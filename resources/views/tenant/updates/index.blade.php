<x-layouts.tenant>
    <x-slot name="header">System Updates</x-slot>

    <div class="flex flex-col gap-[var(--space-6)] max-w-4xl">
        <div class="bg-[var(--color-surface)] border border-[var(--color-border)] p-[var(--space-6)]">
            <h2 class="text-xs uppercase tracking-widest font-bold text-[var(--color-text-secondary)] mb-1">Current
                Environment</h2>
            <div class="flex items-end justify-between">
                <div>
                    <h3 class="font-display text-2xl text-[var(--color-text-primary)]">
                        Version {{ $currentVersion ?? 'Default Release' }}
                    </h3>
                    <p class="text-sm text-[var(--color-text-muted)] mt-1">
                        Last updated: {{ optional($tenant->last_updated_at)->format('M d, Y h:i A') ?? 'N/A' }}
                    </p>
                </div>
                <div class="flex gap-[var(--space-3)]">
                    <span class="badge badge-active">Up to date functionality active</span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div
                class="bg-[var(--color-success-subtle)] border border-[var(--color-success-border)] text-[var(--color-success)] px-4 py-3 rounded text-sm font-semibold flex items-center justify-between">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div
                class="bg-[var(--color-error-subtle)] border border-[var(--color-error-border)] text-[var(--color-error)] px-4 py-3 rounded text-sm font-semibold flex items-center justify-between">
                {{ session('error') }}
            </div>
        @endif

        <h3
            class="text-lg font-bold text-[var(--color-text-primary)] mt-[var(--space-4)] border-b border-[var(--color-border-strong)] pb-2">
            Available Updates</h3>

        @forelse($versions as $version)
            <div class="card p-0 flex flex-col {{ $version->is_critical ? 'border-[var(--color-error-border)]' : '' }}">
                <div
                    class="bg-[var(--color-surface-raised)] border-b border-[var(--color-border)] p-[var(--space-4)] flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-display text-xl text-[var(--color-text-primary)]">{{ $version->version }} -
                                {{ $version->title }}
                            </h4>
                            @if($version->is_critical)
                                <span class="badge badge-error py-0.5">Critical Update</span>
                            @endif
                            @if($version->is_prerelease)
                                <span class="badge badge-pending py-0.5">Pre-Release</span>
                            @endif
                            @if($version->version === $currentVersion)
                                <span class="badge badge-success py-0.5">Currently Applied</span>
                            @endif
                        </div>
                        <p class="text-xs text-[var(--color-text-muted)] mt-1 uppercase tracking-widest font-semibold">
                            Released on {{ $version->released_at->format('M d, Y') }}</p>
                    </div>
                </div>
                <div class="p-[var(--space-6)]">
                    <div
                        class="prose prose-sm prose-slate max-w-none mb-[var(--space-6)] prose-h3:text-[var(--color-text-primary)] prose-h3:font-bold prose-h3:-mt-2 prose-h3:mb-4 prose-p:text-[var(--color-text-secondary)] prose-li:text-[var(--color-text-secondary)] prose-a:text-[var(--color-accent)] hover:prose-a:text-[var(--color-accent-mid)] prose-code:text-[var(--color-text-primary)] prose-code:bg-[var(--color-surface-raised)] prose-code:px-1 prose-code:rounded">
                        {!! \Illuminate\Support\Str::markdown($version->release_notes) !!}
                    </div>

                    @if($version->version !== $currentVersion)
                        <div x-data="updateProgress('{{ route('tenant.updates.apply', $version->version) }}', '{{ $version->version }}')"
                            class="pt-4 border-t border-[var(--color-border-strong)]">
                            <div x-show="!updating" class="flex justify-end">
                                <button type="button" @click="start()" class="btn btn-primary btn-sm">
                                    Update Now &rarr;
                                </button>
                            </div>

                            <div x-show="updating" x-cloak style="display: none;" class="w-full flex flex-col mt-2">
                                <div class="flex items-center justify-between mb-2">
                                    <span
                                        class="text-xs font-bold tracking-widest uppercase text-[var(--color-text-primary)]">Installing
                                        Update...</span>
                                    <span class="text-xs font-bold text-[var(--color-accent)]"
                                        x-text="Math.min(progress, 100) + '%'"></span>
                                </div>
                                <div
                                    class="w-full bg-[var(--color-bg-subtle)] h-2 border border-[var(--color-border)] shadow-inner">
                                    <div class="bg-[var(--color-accent)] h-full transition-all duration-300 ease-out"
                                        :style="`width: ${Math.min(progress, 100)}%`"></div>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <p class="text-[11px] text-[var(--color-text-muted)]" x-show="progress < 100">Running
                                        background tasks. Please do not close this window.</p>
                                    <p class="text-[11px] text-[var(--color-success)] font-bold" x-show="progress >= 100">Update
                                        complete! Refreshing...</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div
                class="card p-[var(--space-6)] text-center border-dashed items-center justify-center flex flex-col opacity-60">
                <svg class="h-10 w-10 text-[var(--color-text-muted)] mb-3" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                </svg>
                <h3 class="text-lg font-bold text-[var(--color-text-primary)]">You're all caught up!</h3>
                <p class="text-sm text-[var(--color-text-muted)] max-w-[300px] mt-1">There are no pending updates from the
                    deployment manager.</p>
            </div>
        @endforelse
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('updateProgress', (url, version) => ({
                updating: localStorage.getItem('updating_version') === version,
                progress: parseInt(localStorage.getItem('update_progress')) || 0,
                
                init() {
                    if (this.updating) {
                        this.startPolling();
                    }
                },

                async start() {
                    const confirmed = await window.appConfirm(`Are you sure you want to install update ${version}? This may cause a temporary interruption.`);
                    if (!confirmed) return;

                    this.updating = true;
                    this.progress = 5;
                    localStorage.setItem('updating_version', version);
                    localStorage.setItem('update_progress', this.progress);

                    let formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    this.startPolling();
                },

                startPolling() {
                    let pInterval = setInterval(() => {
                        if (this.progress < 90) {
                            this.progress += Math.floor(Math.random() * 5) + 2;
                            localStorage.setItem('update_progress', this.progress);
                        }
                    }, 800);

                    let poll = setInterval(async () => {
                        try {
                            let res = await window.fetch(window.location.href);
                            let text = await res.text();

                            if (text.includes(`Version ${version}`) && text.includes('Currently Applied')) {
                                clearInterval(poll);
                                clearInterval(pInterval);
                                this.progress = 100;
                                localStorage.removeItem('updating_version');
                                localStorage.removeItem('update_progress');
                                setTimeout(() => window.location.reload(), 1000);
                            }
                        } catch (e) { }
                    }, 3000);
                }
            }));
        });
    </script>
</x-layouts.tenant>