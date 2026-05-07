<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;

class PaymentController extends Controller
{
    // Show all payments / transaction history 
    public function index(Request $request)
    {
        $search = $request->search;

        $payments = Payment::with(['order.customer'])
            ->when($search, function ($query) use ($search) {
                $query->where('status', 'like', "%{$search}%")
                    ->orWhereHas('order.customer', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('id', 'desc')
            ->get();

        $orders = Order::with('customer')
            ->whereDoesntHave('payment')
            ->orWhereHas('payment', fn($q) => $q->where('status', 'Partial'))
            ->orderBy('id', 'desc')
            ->get();

        $editPayment = $request->edit_id
            ? Payment::findOrFail($request->edit_id)
            : null;

        return view('payments.index', compact('payments', 'editPayment', 'search', 'orders'));
    }

    // Show payment form for a specific order
    public function create(Request $request)
    {
        $orders = Order::with('customer')
            ->whereDoesntHave('payment')
            ->orWhereHas('payment', function ($q) {
                $q->where('status', 'Partial');
            })
            ->orderBy('id', 'desc')
            ->get();

        $selectedOrderId = $request->order_id;

        return view('payments.create', compact('orders', 'selectedOrderId'));
    }

    // Save new payment to DB
    public function store(Request $request)
    {
        $request->validate([
            'order_id'    => 'required|exists:orders,id',
            'paid_amount' => 'required|numeric|min:0.01',
            'status'      => 'required|in:Unpaid,Partial,Paid',
        ]);

        $order = Order::findOrFail($request->order_id);

        $balance = $order->total - $request->paid_amount;

        if ($request->paid_amount <= 0) {
            $status = 'Unpaid';
        } elseif ($balance > 0) {
            $status = 'Partial';
        } else {
            $status = 'Paid';
        }

        Payment::create([
            'order_id'    => $request->order_id,
            'paid_amount' => $request->paid_amount,
            'status'      => $status,
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully.');
    }


    // Update payment
    public function update(Request $request, $id)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0.01',
            'status'      => 'required|in:Unpaid,Partial,Paid',
        ]);

        $payment = Payment::findOrFail($id);
        $order = $payment->order;

        $balance = $order->total - $request->paid_amount;

        if ($request->paid_amount <= 0) {
            $status = 'Unpaid';
        } elseif ($balance > 0) {
            $status = 'Partial';
        } else {
            $status = 'Paid';
        }

        $payment->update([
            'paid_amount' => $request->paid_amount,
            'status'      => $status,
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    // Delete a payment record
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }
}
