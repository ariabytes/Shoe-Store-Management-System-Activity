<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shoes') }}
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
                        {{ isset($editShoe) ? 'Edit Shoe' : 'Add Shoe' }}
                    </h3>

                    <form method="POST"
                        action="{{ isset($editShoe) ? route('shoes.update', $editShoe->id) : route('shoes.store') }}">
                        @csrf
                        @if(isset($editShoe))
                            @method('PUT')
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Product Name</label>
                                <input type="text" name="product_name"
                                    value="{{ isset($editShoe) ? $editShoe->product_name : old('product_name') }}"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @error('product_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Brand</label>
                                <input type="text" name="brand"
                                    value="{{ isset($editShoe) ? $editShoe->brand : old('brand') }}"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @error('brand')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category</label>
                                <input type="text" name="category"
                                    value="{{ isset($editShoe) ? $editShoe->category : old('category') }}"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @error('category')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Size</label>
                                <input type="number" step="0.5" name="size"
                                    value="{{ isset($editShoe) ? $editShoe->size : old('size') }}"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @error('size')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Color</label>
                                <input type="text" name="color"
                                    value="{{ isset($editShoe) ? $editShoe->color : old('color') }}"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @error('color')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Stock</label>
                                <input type="number" name="stock"
                                    value="{{ isset($editShoe) ? $editShoe->stock : old('stock') }}"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @error('stock')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Price</label>
                                <input type="number" step="0.01" name="price"
                                    value="{{ isset($editShoe) ? $editShoe->price : old('price') }}"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @error('price')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <input type="text" name="description"
                                    value="{{ isset($editShoe) ? $editShoe->description : old('description') }}"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @error('description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                {{ isset($editShoe) ? 'Update Shoe' : 'Add Shoe' }}
                            </button>
                            @if(isset($editShoe))
                                <a href="{{ route('shoes.index') }}"
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
                    <form method="GET" action="{{ route('shoes.index') }}">
                        <div class="flex gap-2">
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                placeholder="Search shoes..."
                                class="w-full border-gray-300 rounded-md shadow-sm">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                Search
                            </button>
                            <a href="{{ route('shoes.index') }}"
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
                                <th class="px-4 py-2">Product Name</th>
                                <th class="px-4 py-2">Brand</th>
                                <th class="px-4 py-2">Category</th>
                                <th class="px-4 py-2">Size</th>
                                <th class="px-4 py-2">Color</th>
                                <th class="px-4 py-2">Stock</th>
                                <th class="px-4 py-2">Price</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shoes as $shoe)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $shoe->id }}</td>
                                    <td class="px-4 py-2">{{ $shoe->product_name }}</td>
                                    <td class="px-4 py-2">{{ $shoe->brand }}</td>
                                    <td class="px-4 py-2">{{ $shoe->category }}</td>
                                    <td class="px-4 py-2">{{ $shoe->size }}</td>
                                    <td class="px-4 py-2">{{ $shoe->color }}</td>
                                    <td class="px-4 py-2">{{ $shoe->stock }}</td>
                                    <td class="px-4 py-2">₱{{ number_format($shoe->price, 2) }}</td>
                                    <td class="px-4 py-2 flex gap-2">
                                        <a href="{{ route('shoes.index', ['edit_id' => $shoe->id]) }}"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('shoes.destroy', $shoe->id) }}"
                                            onsubmit="return confirm('Delete this shoe?')">
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
                                    <td colspan="9" class="px-4 py-4 text-center text-gray-400">
                                        No shoes found.
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