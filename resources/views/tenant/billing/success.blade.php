<x-layouts.tenant>
    <x-slot name="header">
        <h1 class="text-[20px] font-bold text-foreground">Checkout Success</h1>
    </x-slot>

    <div class="page-content flex justify-center py-12">
        <div
            class="card p-8 max-w-lg w-full text-center space-y-6 border-t-4 border-t-success relative shadow-lg bg-surface">

            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-success/20">
                <svg class="h-10 w-10 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    aria-hidden="true" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <div>
                <h2 class="text-2xl font-black text-foreground">Payment Successful!</h2>
                <p class="mt-2 text-sm text-muted">Thank you for your subscription. Your workspace has been updated
                    successfully and all features for your selected plan are now unlocked.</p>
            </div>

            <div class="bg-surface-alt rounded-lg p-5 flex flex-col gap-2 border border-border">
                <div class="flex justify-between text-sm">
                    <span class="text-muted font-medium">Session ID</span>
                    <span class="font-bold text-foreground truncate pl-4"
                        title="{{ $sessionId }}">{{ Str::limit($sessionId, 15) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-muted font-medium">Status</span>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-500 uppercase tracking-wider">Active</span>
                </div>
            </div>

            <div class="pt-2">
                <a href="{{ route('tenant.dashboard') }}" class="btn btn-primary w-full shadow-sm">
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>
</x-layouts.tenant>