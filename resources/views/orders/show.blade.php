<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Order Info --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Order #{{ $order->id }}</h3>
                        <span class="px-3 py-1 rounded text-sm font-medium
                            {{ $order->status === 'Delivered' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $order->status === 'Shipped' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $order->status === 'Pending' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                            {{ $order->status }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 font-medium">Customer</p>
                            <p class="text-gray-800">
                                {{ $order->customer->first_name }}
                                {{ $order->customer->middle_name ? $order->customer->middle_name . ' ' : '' }}
                                {{ $order->customer->last_name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 font-medium">Contact Number</p>
                            <p class="text-gray-800">{{ $order->customer->contact_number }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-500 font-medium">Address</p>
                            <p class="text-gray-800">{{ $order->customer->address }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 font-medium">Order Date</p>
                            <p class="text-gray-800">{{ $order->created_at->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 font-medium">Last Updated</p>
                            <p class="text-gray-800">{{ $order->updated_at->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ordered Shoes --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Ordered Shoes</h3>
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-700 uppercase">
                            <tr>
                                <th class="px-4 py-2">Product Name</th>
                                <th class="px-4 py-2">Brand</th>
                                <th class="px-4 py-2">Size</th>
                                <th class="px-4 py-2">Color</th>
                                <th class="px-4 py-2">Unit Price</th>
                                <th class="px-4 py-2">Qty</th>
                                <th class="px-4 py-2">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->shoes as $shoe)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $shoe->product_name }}</td>
                                    <td class="px-4 py-2">{{ $shoe->brand }}</td>
                                    <td class="px-4 py-2">{{ $shoe->size }}</td>
                                    <td class="px-4 py-2">{{ $shoe->color }}</td>
                                    <td class="px-4 py-2">₱{{ number_format($shoe->price, 2) }}</td>
                                    <td class="px-4 py-2">{{ $shoe->pivot->quantity }}</td>
                                    <td class="px-4 py-2">₱{{ number_format($shoe->price * $shoe->pivot->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t font-semibold bg-gray-50">
                                <td colspan="5" class="px-4 py-2 text-right">Total Quantity:</td>
                                <td class="px-4 py-2">{{ $order->quantity }}</td>
                                <td class="px-4 py-2"></td>
                            </tr>
                            <tr class="font-semibold bg-gray-50">
                                <td colspan="6" class="px-4 py-2 text-right">Grand Total:</td>
                                <td class="px-4 py-2 text-blue-600">₱{{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Payment Info --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Payment</h3>

                    @if($order->payment)
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 font-medium">Amount Paid</p>
                                <p class="text-gray-800 text-lg font-semibold">₱{{ number_format($order->payment->paid_amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 font-medium">Balance</p>
                                <p class="text-gray-800 text-lg font-semibold">
                                    ₱{{ number_format(max(0, $order->total - $order->payment->paid_amount), 2) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500 font-medium">Payment Status</p>
                                <span class="px-2 py-0.5 rounded text-xs font-medium
                                    {{ $order->payment->status === 'Paid' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $order->payment->status === 'Partial' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $order->payment->status === 'Unpaid' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ $order->payment->status }}
                                </span>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm">No payment recorded for this order.</p>
                        <a href="{{ route('payments.index') }}"
                            class="mt-3 inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                            Record Payment
                        </a>
                    @endif
                </div>
            </div>

            {{-- Back Button --}}
            <div>
                <a href="{{ route('orders.index') }}"
                    class="border-2 border-gray-400 text-gray-400 hover:bg-gray-400 hover:text-white px-4 py-2 rounded">
                    ← Back to Orders
                </a>
            </div>

        </div>
    </div>
</x-app-layout>