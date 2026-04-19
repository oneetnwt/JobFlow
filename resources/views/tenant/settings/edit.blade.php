<x-layouts.tenant>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Tenant Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Workspace Options') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Update your workspace details, colors, and branding.") }}
                            </p>
                        </header>

                        @if (session('success'))
                            <div class="mt-4 mb-4 text-sm font-medium text-green-600">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="post" action="{{ route('tenant.settings.update') }}" enctype="multipart/form-data"
                            class="mt-6 space-y-6">
                            @csrf
                            @method('put')

                            <div>
                                <label class="block font-medium text-sm text-gray-700">App Version</label>
                                <div
                                    class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 text-gray-500 shadow-sm px-3 py-2 sm:text-sm">
                                    {{ $tenant->current_version ?? 'Unknown' }}
                                </div>
                            </div>

                            <div>
                                <label for="company_name" class="block font-medium text-sm text-gray-700">Company
                                    Name</label>
                                <input id="company_name" name="company_name" type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    value="{{ old('company_name', $tenant->company_name) }}" required autofocus />
                                @error('company_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="admin_name" class="block font-medium text-sm text-gray-700">Admin
                                    Name</label>
                                <input id="admin_name" name="admin_name" type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    value="{{ old('admin_name', $tenant->admin_name) }}" />
                                @error('admin_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="admin_email" class="block font-medium text-sm text-gray-700">Admin
                                    Email</label>
                                <input id="admin_email" name="admin_email" type="email"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    value="{{ old('admin_email', $tenant->admin_email) }}" />
                                @error('admin_email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="brand_color" class="block font-medium text-sm text-gray-700">Brand
                                    Color</label>
                                <input id="brand_color" name="brand_color" type="color"
                                    class="mt-1 h-10 w-16 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    value="{{ old('brand_color', $tenant->brand_color ?? '#2D7DD2') }}" />
                                @error('brand_color')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="logo" class="block font-medium text-sm text-gray-700">Company Logo (Max:
                                    2MB)</label>

                                <div class="mt-2 flex items-center gap-4">
                                    @if($tenant->logo_url)
                                        <div
                                            class="h-16 w-16 bg-gray-50 rounded border overflow-hidden flex items-center justify-center">
                                            <img src="{{ $tenant->logo_url }}" alt="Logo"
                                                class="max-h-full max-w-full object-contain">
                                        </div>
                                    @endif

                                    <input id="logo" name="logo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100" />
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Upload a logo to replace the current one.</p>
                                @error('logo')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-layouts.tenant>