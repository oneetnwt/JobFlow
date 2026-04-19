<x-layouts.tenant-auth>
    <x-slot name="title">Verify Your Email</x-slot>
    <div class="mb-4 text-sm text-gray-600">
        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we
        just emailed to you? If you didn't receive the email, we will gladly send you another.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <div>
                <button type="submit" class="btn btn-primary">Resend Verification Email</button>
            </div>
        </form>

        <form method="POST" action="{{ route('tenant.logout') }}">
            @csrf
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                Log Out
            </button>
        </form>
    </div>

    <!-- Auto-refresh polling script -->
    <script>
        setInterval(() => {
            fetch('{{ route('verification.notice') }}', {
                method: 'GET',
                headers: {
                    'Accept': 'text/html',
                }
            }).then(response => {
                // If the fetch followed a redirect to the dashboard
                if (response.redirected && !response.url.includes('/email/verify')) {
                    window.location.replace(response.url);
                }
            }).catch(error => console.error('Error checking verification status:', error));
        }, 5000); // Check every 5 seconds
    </script>
</x-layouts.tenant-auth>