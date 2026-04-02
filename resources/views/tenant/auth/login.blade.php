<x-layouts.tenant-auth>

    @if(session('status'))
        <div class="mb-[var(--space-4)] p-[var(--space-3)] bg-[var(--color-info-subtle)] text-[var(--color-info)] border-l-4 border-[var(--color-info-border)] text-[var(--text-sm)] rounded-[var(--radius-xs)] font-medium">
            {{ session('status') }}
        </div>
    @endif

    <form class="flex flex-col gap-[var(--space-6)]" action="{{ route('tenant.login.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Work Email</label>
            <input id="email" name="email" type="email" autocomplete="email" required autofocus value="{{ old('email') }}" class="form-input">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required class="form-input font-mono tracking-widest text-[var(--text-lg)]">
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between mt-[-var(--space-2)]">
            <div class="flex items-center gap-[var(--space-3)]">
                <input id="remember" name="remember" type="checkbox"
                       class="w-4 h-4 text-[var(--color-accent)] rounded-[var(--radius-xs)] border-[var(--color-border)]">
                <label for="remember" class="text-[var(--text-sm)] text-[var(--color-text-secondary)] font-medium">
                    Remember me
                </label>
            </div>

            <a href="#" class="text-[var(--text-sm)] font-medium text-[var(--color-accent)] hover:text-[var(--color-accent-mid)] transition-colors">
                Forgot password?
            </a>
        </div>

        <button type="submit" class="btn btn-primary w-full justify-center btn-lg mt-[var(--space-2)] shadow-[var(--shadow-1)]">
            Sign in
        </button>
    </form>

</x-layouts.tenant-auth>