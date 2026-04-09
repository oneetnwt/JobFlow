<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Super Admin Login | JobFlow OMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .industrial-grid {
            background-size: 24px 24px;
            background-image:
                linear-gradient(to right, rgba(28, 25, 23, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(28, 25, 23, 0.05) 1px, transparent 1px);
        }
    </style>
</head>
<body class="h-full industrial-theme bg-[var(--color-bg)] industrial-grid flex flex-col justify-center py-[var(--space-12)] sm:px-6 lg:px-8 relative">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center relative z-10">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-[var(--space-3)] mb-[var(--space-6)] text-[var(--color-text-primary)] hover:text-[var(--color-accent)] transition-colors">
            <svg class="w-8 h-8 text-[var(--color-accent)]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            <span class="font-display text-[var(--text-3xl)] tracking-tight">JobFlow <span class="text-[var(--color-accent)]">OMS</span></span>
        </a>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-[400px] relative z-10">
        <div class="card p-[var(--space-10)] shadow-[var(--shadow-2)]">
            <h2 class="font-display text-[var(--text-xl)] text-center text-[var(--color-text-primary)] mb-[var(--space-6)]">Admin Portal Authorization</h2>
            <form class="flex flex-col gap-[var(--space-6)]" action="{{ route('admin.login.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Admin Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required autofocus
                           class="form-input">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Security Token</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="form-input font-mono tracking-widest text-[var(--text-lg)]">
                </div>

                <div class="flex items-center gap-[var(--space-3)] mt-[-var(--space-2)]">
                    <input id="remember" name="remember" type="checkbox" class="w-4 h-4 text-[var(--color-accent)] rounded-[var(--radius-xs)] border-[var(--color-border)]">
                    <label for="remember" class="text-[var(--text-sm)] text-[var(--color-text-secondary)] font-medium">Keep session active</label>
                </div>

                <button type="submit" class="btn btn-primary w-full justify-center btn-lg mt-[var(--space-2)] shadow-[var(--shadow-1)]">
                    Authorize Access
                </button>
            </form>
        </div>
        
        <p class="mt-[var(--space-6)] text-center text-[var(--text-sm)] text-[var(--color-text-muted)] font-medium">
            New organization? <a href="{{ route('tenant.register.create') }}" class="text-[var(--color-accent)] hover:text-[var(--color-accent-mid)] font-semibold transition-colors">Start here.</a>
        </p>
    </div>

</body>
</html>
