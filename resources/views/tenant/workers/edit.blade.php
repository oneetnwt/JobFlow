<x-layouts.tenant>
    <x-slot name="header">
        Edit Worker Profile
    </x-slot>

    <div class="max-w-4xl">
        <form action="{{ route('tenant.workers.update', $worker) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Account Information -->
            <div class="bg-white shadow-sm rounded-lg border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-bold text-[#0F1B2D]">Login & Authentication</h3>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <label for="name" class="block text-sm font-medium text-[#1E3A5F]">Full Name</label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $worker->name) }}"
                            class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-1">
                        <label for="email" class="block text-sm font-medium text-[#1E3A5F]">Work Email</label>
                        <input type="email" name="email" id="email" required value="{{ old('email', $worker->email) }}"
                            class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-1">
                        <label for="password" class="block text-sm font-medium text-[#1E3A5F]">Update Password
                            (Optional)</label>
                        <input type="password" name="password" id="password"
                            class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm"
                            placeholder="Leave blank to keep current">
                        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-1">
                        <label for="role" class="block text-sm font-medium text-[#1E3A5F]">Access Role</label>
                        <select id="role" name="role"
                            class="mt-1 block w-full bg-white border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                            @foreach($roles as $role)
                                <option value="{{ $role->slug }}" {{ old('role') === $role->slug || (empty(old('role')) && $worker->hasRole($role->slug)) ? 'selected' : '' }}>
                                    {{ $role->name }} - {{ Str::limit($role->description, 50) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="bg-white shadow-sm rounded-lg border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-bold text-[#0F1B2D]">Employment Profile</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-[#1E3A5F]">Employee
                                ID</label>
                            <input type="text" name="employee_id" id="employee_id"
                                value="{{ old('employee_id', $worker->profile?->employee_id) }}"
                                class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                            @error('employee_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="department" class="block text-sm font-medium text-[#1E3A5F]">Department</label>
                            <input type="text" name="department" id="department"
                                value="{{ old('department', $worker->profile?->department) }}"
                                class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                            @error('department') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="employment_type" class="block text-sm font-medium text-[#1E3A5F]">Employment
                                Type</label>
                            <select id="employment_type" name="employment_type"
                                class="mt-1 block w-full bg-white border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                                <option value="full-time" {{ old('employment_type', $worker->profile?->employment_type) === 'full-time' ? 'selected' : '' }}>Full-time
                                </option>
                                <option value="part-time" {{ old('employment_type', $worker->profile?->employment_type) === 'part-time' ? 'selected' : '' }}>Part-time
                                </option>
                                <option value="contract" {{ old('employment_type', $worker->profile?->employment_type) === 'contract' ? 'selected' : '' }}>Contract
                                </option>
                                <option value="seasonal" {{ old('employment_type', $worker->profile?->employment_type) === 'seasonal' ? 'selected' : '' }}>Seasonal
                                </option>
                            </select>
                            @error('employment_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="hourly_rate" class="block text-sm font-medium text-[#1E3A5F]">Hourly Rate
                                (₱)</label>
                            <input type="number" step="0.01" name="hourly_rate" id="hourly_rate"
                                value="{{ old('hourly_rate', $worker->profile?->hourly_rate ?? '0.00') }}"
                                class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                            @error('hourly_rate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-[#1E3A5F]">Phone
                                Number</label>
                            <input type="text" name="phone_number" id="phone_number"
                                value="{{ old('phone_number', $worker->profile?->phone_number) }}"
                                class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                            @error('phone_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="joined_at" class="block text-sm font-medium text-[#1E3A5F]">Joined Date</label>
                            <input type="date" name="joined_at" id="joined_at"
                                value="{{ old('joined_at', $worker->profile?->joined_at?->format('Y-m-d')) }}"
                                class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                            @error('joined_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="skills" class="block text-sm font-medium text-[#1E3A5F]">Skills &
                            Certifications</label>
                        <textarea id="skills" name="skills" rows="3"
                            class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">{{ old('skills', $worker->profile?->skills) }}</textarea>
                        @error('skills') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 text-right space-x-3">
                    <a href="{{ route('tenant.workers.show', $worker) }}"
                        class="text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Cancel</a>
                    <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#0F1B2D] hover:bg-[#1E3A5F] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2D7DD2] transition-colors">
                        Update Worker Profile
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.tenant>