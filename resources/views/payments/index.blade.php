<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payments') }}
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
                        {{ isset($editPayment) ? 'Edit Payment' : 'Record Payment' }}
                    </h3>

                    <form method="POST"
                        action="{{ isset($editPayment) ? route('payments.update', $editPayment->id) : route('payments.store') }}">
                        @csrf
                        @if(isset($editPayment))
                            @method('PUT')
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            @if(!isset($editPayment))
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Order</label>
                                    <select name="order_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">-- Select Order --</option>
                                        @foreach($orders as $order)
                                            <option value="{{ $order->id }}"
                                                {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                                #{{ $order->id }} —
                                                {{ $order->customer->first_name }} {{ $order->customer->last_name }}
                                                (₱{{ number_format($order->total, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('order_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @else
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Order</label>
                                    <input type="text"
                                        value="#{{ $editPayment->order->id }} — {{ $editPayment->order->customer->first_name }} {{ $editPayment->order->customer->last_name }} (₱{{ number_format($editPayment->order->total, 2) }})"
                                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm bg-gray-50"
                                        disabled>
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Paid Amount (₱)</label>
                                <input type="number" step="0.01" name="paid_amount"
                                    value="{{ isset($editPayment) ? $editPayment->paid_amount : old('paid_amount') }}"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @error('paid_amount')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    @foreach(['Unpaid', 'Partial', 'Paid'] as $s)
                                        <option value="{{ $s }}"
                                            {{ (isset($editPayment) ? $editPayment->status : old('status')) == $s ? 'selected' : '' }}>
                                            {{ $s }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                {{ isset($editPayment) ? 'Update Payment' : 'Record Payment' }}
                            </button>
                            @if(isset($editPayment))
                                <a href="{{ route('payments.index') }}"
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
                    <form method="GET" action="{{ route('payments.index') }}">
                        <div class="flex gap-2">
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                placeholder="Search by customer name or status..."
                                class="w-full border-gray-300 rounded-md shadow-sm">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                Search
                            </button>
                            <a href="{{ route('payments.index') }}"
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
                                <th class="px-4 py-2">Order #</th>
                                <th class="px-4 py-2">Customer</th>
                                <th class="px-4 py-2">Order Total</th>
                                <th class="px-4 py-2">Paid Amount</th>
                                <th class="px-4 py-2">Balance</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Date</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $payment->id }}</td>
                                    <td class="px-4 py-2">#{{ $payment->order->id }}</td>
                                    <td class="px-4 py-2">
                                        {{ $payment->order->customer->first_name }}
                                        {{ $payment->order->customer->last_name }}
                                    </td>
                                    <td class="px-4 py-2">₱{{ number_format($payment->order->total, 2) }}</td>
                                    <td class="px-4 py-2">₱{{ number_format($payment->paid_amount, 2) }}</td>
                                    <td class="px-4 py-2">
                                        ₱{{ number_format($payment->order->total - $payment->paid_amount, 2) }}
                                    </td>
                                    <td class="px-4 py-2">
                                        @php
                                            $statusColor = match($payment->status) {
                                                'Paid'    => 'bg-green-100 text-green-700',
                                                'Partial' => 'bg-yellow-100 text-yellow-700',
                                                default   => 'bg-red-100 text-red-700',
                                            };
                                        @endphp
                                        <span class="px-2 py-1 rounded text-xs font-medium {{ $statusColor }}">
                                            {{ $payment->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">{{ $payment->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-2 flex gap-2">
                                        <a href="{{ route('payments.index', ['edit_id' => $payment->id]) }}"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('payments.destroy', $payment->id) }}"
                                            onsubmit="return confirm('Delete this payment record?')">
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
                                        No payments found.
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