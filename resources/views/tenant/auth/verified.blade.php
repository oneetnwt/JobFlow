<x-layouts.tenant-auth>
    <x-slot name="title">Email Verified Successfully</x-slot>

    <div class="mb-4 text-center">
        <svg class="mx-auto h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h2 class="mt-4 text-2xl font-bold text-gray-900">Email Verified!</h2>
        <p class="mt-2 text-sm text-gray-600">
            Your email has been verified successfully. You may now return to the app and refresh your page to continue.
        </p>
    </div>

    <div class="mt-6 flex justify-center">
        <a href="{{ route('tenant.dashboard') }}" class="btn btn-primary">
            Go to Dashboard
        </a>
    </div>
</x-layouts.tenant-auth>