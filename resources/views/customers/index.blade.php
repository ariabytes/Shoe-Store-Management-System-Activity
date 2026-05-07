<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Customers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Add / Edit Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        {{ isset($editCustomer) ? 'Edit Customer' : 'Add Customer' }}
                    </h3>

                    <form method="POST"
                        action="{{ isset($editCustomer) ? route('customers.update', $editCustomer->id) : route('customers.store') }}">
                        @csrf
                        @if(isset($editCustomer))
                            @method('PUT')
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">First Name</label>
                                <input type="text" name="first_name"
                                    value="{{ isset($editCustomer) ? $editCustomer->first_name : old('first_name') }}"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @error('first_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Middle Name</label>
                                <input type="text" name="middle_name"
                                    value="{{ isset($editCustomer) ? $editCustomer->middle_name : old('middle_name') }}"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @error('middle_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Last Name</label>
                                <input type="text" name="last_name"
                                    value="{{ isset($editCustomer) ? $editCustomer->last_name : old('last_name') }}"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @error('last_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                                <input type="text" name="contact_number"
                                    value="{{ isset($editCustomer) ? $editCustomer->contact_number : old('contact_number') }}"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @error('contact_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Address</label>
                                <input type="text" name="address"
                                    value="{{ isset($editCustomer) ? $editCustomer->address : old('address') }}"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                {{ isset($editCustomer) ? 'Update Customer' : 'Add Customer' }}
                            </button>
                            @if(isset($editCustomer))
                                <a href="{{ route('customers.index') }}"
                                    class="border-2 border-gray-400 text-gray-400 hover:bg-gray-400 hover:text-white px-4 py-2 rounded">
                                    Cancel
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Search --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('customers.index') }}">
                        <div class="flex gap-2">
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                placeholder="Search customers..."
                                class="w-full border-gray-300 rounded-md shadow-sm">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                Search
                            </button>
                            <a href="{{ route('customers.index') }}"
                                class="border-2 border-gray-400 text-gray-400 hover:bg-gray-400 hover:text-white px-4 py-2 rounded">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-700 uppercase">
                            <tr>
                                <th class="px-4 py-2">ID</th>
                                <th class="px-4 py-2">Full Name</th>
                                <th class="px-4 py-2">Contact Number</th>
                                <th class="px-4 py-2">Address</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $customer->id }}</td>
                                    <td class="px-4 py-2">
                                        {{ $customer->first_name }}
                                        {{ $customer->middle_name ? $customer->middle_name . ' ' : '' }}
                                        {{ $customer->last_name }}
                                    </td>
                                    <td class="px-4 py-2">{{ $customer->contact_number }}</td>
                                    <td class="px-4 py-2">{{ $customer->address }}</td>
                                    <td class="px-4 py-2 flex gap-2">
                                        <a href="{{ route('customers.index', ['edit_id' => $customer->id]) }}"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('customers.destroy', $customer->id) }}"
                                            onsubmit="return confirm('Delete this customer?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-gray-400">
                                        No customers found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>