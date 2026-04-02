<x-layouts.tenant>
    <x-slot name="header">
        Create Payroll Period
    </x-slot>

    <div class="max-w-xl">
        <form action="{{ route('tenant.payroll.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white shadow-sm rounded-lg border border-slate-200 overflow-hidden">
                <div class="p-6 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-[#1E3A5F]">Period Name</label>
                        <input type="text" name="name" id="name" required value="{{ old('name', date('F Y') . ' - Week 1') }}"
                               class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm"
                               placeholder="e.g. April 2026 - First Half">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-[#1E3A5F]">Start Date</label>
                            <input type="date" name="start_date" id="start_date" required value="{{ old('start_date') }}"
                                   class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                            @error('start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-[#1E3A5F]">End Date</label>
                            <input type="date" name="end_date" id="end_date" required value="{{ old('end_date') }}"
                                   class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2D7DD2] focus:border-[#2D7DD2] sm:text-sm">
                            @error('end_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <p class="text-xs text-slate-500">Creating a period allows you to aggregate worker hours and generate payslips for these specific dates.</p>
                </div>

                <div class="px-6 py-4 bg-slate-50 text-right space-x-3">
                    <a href="{{ route('tenant.payroll.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Cancel</a>
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#0F1B2D] hover:bg-[#1E3A5F] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2D7DD2] transition-colors">
                        Create Period
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.tenant>
