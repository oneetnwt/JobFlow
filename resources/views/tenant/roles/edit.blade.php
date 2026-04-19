<x-layouts.tenant>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Role: {{ $role->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <!-- Session Errors -->
                @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <form action="{{ route('tenant.roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Role Name</label>
                        <input type="text" name="name" value="{{ old('name', $role->name) }}" class="shadow appearance-none border border-gray-300 bg-gray-100 rounded w-full py-2 px-3 text-gray-500 leading-tight cursor-not-allowed" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Unique Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $role->slug) }}" class="shadow appearance-none border border-gray-300 bg-gray-100 rounded w-full py-2 px-3 text-gray-500 leading-tight cursor-not-allowed" readonly>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-blue-500">{{ old('description', $role->description) }}</textarea>
                    </div>

                    <h3 class="text-xl font-semibold mb-4 border-b pb-2">Assign Permissions</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($permissions as $group => $perms)
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h4 class="font-bold text-gray-600 mb-3">{{ $group ?? 'Other' }}</h4>
                                @foreach($perms as $permission)
                                    <div class="mb-2 flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                {{ (is_array(old('permissions')) ? in_array($permission->id, old('permissions')) : in_array($permission->id, $rolePermissions)) ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label class="font-medium text-gray-700">{{ $permission->name }}</label>
                                            <p class="text-gray-500">{{ $permission->description }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 pt-5 border-t border-gray-200">
                        <button type="submit" class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                            Update Role
                        </button>
                        <a href="{{ route('tenant.roles.index') }}" class="ml-3 text-sm font-medium text-gray-700 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.tenant>
