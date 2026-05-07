<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Orders') }}
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
                        {{ isset($editOrder) ? 'Edit Order' : 'Add Order' }}
                    </h3>

                    <form method="POST"
                        action="{{ isset($editOrder) ? route('orders.update', $editOrder->id) : route('orders.store') }}">
                        @csrf
                        @if(isset($editOrder))
                            @method('PUT')
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Customer</label>
                                <select name="customer_id"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">-- Select Customer --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ isset($editOrder) && $editOrder->customer_id == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->first_name }} {{ $customer->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    @foreach(['Pending', 'Shipped', 'Delivered'] as $s)
                                        <option value="{{ $s }}"
                                            {{ isset($editOrder) && $editOrder->status == $s ? 'selected' : '' }}>
                                            {{ $s }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Shoe Rows --}}
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Shoes</label>
                            <div id="shoe-rows" class="space-y-2">
                                @if(isset($editOrder) && $editOrder->shoes->count())
                                    @foreach($editOrder->shoes as $s)
                                        <div class="flex gap-2 shoe-row">
                                            <select name="shoes[]"
                                                class="w-2/3 border-gray-300 rounded-md shadow-sm">
                                                <option value="">-- Select Shoe --</option>
                                                @foreach($shoes as $shoe)
                                                    <option value="{{ $shoe->id }}"
                                                        {{ $s->id == $shoe->id ? 'selected' : '' }}>
                                                        {{ $shoe->product_name }} ({{ $shoe->brand }}) — Size {{ $shoe->size }} — ₱{{ number_format($shoe->price, 2) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="number" name="quantities[]"
                                                value="{{ $s->pivot->quantity }}" min="1"
                                                placeholder="Qty"
                                                class="w-1/3 border-gray-300 rounded-md shadow-sm">
                                            <button type="button" onclick="removeRow(this)"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                                ✕
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex gap-2 shoe-row">
                                        <select name="shoes[]"
                                            class="w-2/3 border-gray-300 rounded-md shadow-sm">
                                            <option value="">-- Select Shoe --</option>
                                            @foreach($shoes as $shoe)
                                                <option value="{{ $shoe->id }}">
                                                    {{ $shoe->product_name }} ({{ $shoe->brand }}) — Size {{ $shoe->size }} — ₱{{ number_format($shoe->price, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="number" name="quantities[]" min="1"
                                            placeholder="Qty"
                                            class="w-1/3 border-gray-300 rounded-md shadow-sm">
                                        <button type="button" onclick="removeRow(this)"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                            ✕
                                        </button>
                                    </div>
                                @endif
                            </div>

                            @error('shoes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror

                            <button type="button" onclick="addRow()"
                                class="mt-2 border-2 border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white px-3 py-1 rounded text-sm">
                                + Add Shoe
                            </button>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                {{ isset($editOrder) ? 'Update Order' : 'Add Order' }}
                            </button>
                            @if(isset($editOrder))
                                <a href="{{ route('orders.index') }}"
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
                    <form method="GET" action="{{ route('orders.index') }}">
                        <div class="flex gap-2">
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                placeholder="Search by customer name or status..."
                                class="w-full border-gray-300 rounded-md shadow-sm">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                Search
                            </button>
                            <a href="{{ route('orders.index') }}"
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
                                <th class="px-4 py-2">Customer</th>
                                <th class="px-4 py-2">Shoes</th>
                                <th class="px-4 py-2">Total Qty</th>
                                <th class="px-4 py-2">Total</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $order->id }}</td>
                                    <td class="px-4 py-2">
                                        {{ $order->customer->first_name }} {{ $order->customer->last_name }}
                                    </td>
                                    <td class="px-4 py-2">
                                        @foreach($order->shoes as $shoe)
                                            <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded mr-1 mb-1">
                                                {{ $shoe->product_name }} x{{ $shoe->pivot->quantity }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-2">{{ $order->quantity }}</td>
                                    <td class="px-4 py-2">₱{{ number_format($order->total, 2) }}</td>
                                    <td class="px-4 py-2">
                                        @php
                                            $statusColor = match($order->status) {
                                                'Delivered' => 'bg-green-100 text-green-700',
                                                'Shipped'   => 'bg-blue-100 text-blue-700',
                                                default     => 'bg-yellow-100 text-yellow-700',
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 rounded text-xs font-medium {{ $statusColor }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 flex gap-2">
                                        <a href="{{ route('orders.show', $order->id) }}"
                                            class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded">
                                            View
                                        </a>
                                        <a href="{{ route('orders.index', ['edit_id' => $order->id]) }}"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('orders.destroy', $order->id) }}"
                                            onsubmit="return confirm('Delete this order?')">
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
                                    <td colspan="7" class="px-4 py-4 text-center text-gray-400">
                                        No orders found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Hidden template row for JS cloning --}}
    <template id="shoe-row-template">
        <div class="flex gap-2 shoe-row">
            <select name="shoes[]" class="w-2/3 border-gray-300 rounded-md shadow-sm">
                <option value="">-- Select Shoe --</option>
                @foreach($shoes as $shoe)
                    <option value="{{ $shoe->id }}">
                        {{ $shoe->product_name }} ({{ $shoe->brand }}) — Size {{ $shoe->size }} — ₱{{ number_format($shoe->price, 2) }}
                    </option>
                @endforeach
            </select>
            <input type="number" name="quantities[]" min="1" placeholder="Qty"
                class="w-1/3 border-gray-300 rounded-md shadow-sm">
            <button type="button" onclick="removeRow(this)"
                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">✕</button>
        </div>
    </template>

    <script>
        function addRow() {
            const template = document.getElementById('shoe-row-template');
            const clone = template.content.cloneNode(true);
            document.getElementById('shoe-rows').appendChild(clone);
        }

        function removeRow(btn) {
            const rows = document.querySelectorAll('.shoe-row');
            if (rows.length > 1) {
                btn.closest('.shoe-row').remove();
            }
        }
    </script>

</x-app-layout>