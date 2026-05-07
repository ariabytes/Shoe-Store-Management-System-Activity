<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Shoe;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $orders = Order::with(['customer', 'shoes'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('customer', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })
                    ->orWhere('status', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->get();

        $editOrder = $request->edit_id
            ? Order::with('shoes')->findOrFail($request->edit_id)
            : null;

        $customers = Customer::orderBy('first_name')->get();
        $shoes = Shoe::where('stock', '>', 0)->orderBy('product_name')->get();

        return view('orders.index', compact('orders', 'editOrder', 'customers', 'shoes', 'search'));
    }

    // Save new order to DB
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status'      => 'required|in:Pending,Shipped,Delivered',
            'shoes'       => 'required|array|min:1',
            'shoes.*'     => 'exists:shoes,id',
            'quantities'  => 'required|array|min:1',
            'quantities.*' => 'integer|min:1',
        ]);

        $order = Order::create([
            'customer_id' => $request->customer_id,
            'quantity'    => array_sum($request->quantities),
            'total'       => 0,
            'status'      => $request->status,
        ]);

        $pivotData = [];
        foreach ($request->shoes as $index => $shoeId) {
            $pivotData[$shoeId] = ['quantity' => $request->quantities[$index]];
        }

        $order->shoes()->attach($pivotData);
        $order->total = $order->calculateTotal();
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }

    // Show order details
    public function show($id)
    {
        $order = Order::with(['customer', 'shoes', 'payment'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    // Update existing order
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status'      => 'required|in:Pending,Shipped,Delivered',
            'shoes'       => 'required|array|min:1',
            'shoes.*'     => 'exists:shoes,id',
            'quantities'  => 'required|array|min:1',
            'quantities.*' => 'integer|min:1',
        ]);

        $order = Order::findOrFail($id);
        $order->update([
            'customer_id' => $request->customer_id,
            'quantity'    => array_sum($request->quantities),
            'status'      => $request->status,
        ]);

        $pivotData = [];
        foreach ($request->shoes as $index => $shoeId) {
            $pivotData[$shoeId] = ['quantity' => $request->quantities[$index]];
        }
        $order->shoes()->sync($pivotData);

        $order->total = $order->calculateTotal();
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Order updated successfully.');
    }

    // Delete an order
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->shoes()->detach();
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }
}
