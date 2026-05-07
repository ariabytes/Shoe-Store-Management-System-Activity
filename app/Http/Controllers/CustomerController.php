<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    // Show all customers 
    public function index(Request $request)
    {
        $search = $request->search;

        $customers = Customer::when($search, function ($query) use ($search) {
            $query->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('contact_number', 'like', "%{$search}%");
        })
            ->orderBy('id', 'desc')
            ->get();

        $editCustomer = $request->edit_id
            ? Customer::findOrFail($request->edit_id)
            : null;

        return view('customers.index', compact('customers', 'editCustomer', 'search'));
    }

    // Save new customer to DB
    public function store(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string|max:255',
            'middle_name'    => 'nullable|string|max:255',
            'last_name'      => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'address'        => 'required|string|max:500',
        ]);

        Customer::create([
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name,
            'last_name'      => $request->last_name,
            'contact_number' => $request->contact_number,
            'address'        => $request->address,
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    // Update existing customer
    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name'     => 'required|string|max:255',
            'middle_name'    => 'nullable|string|max:255',
            'last_name'      => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'address'        => 'required|string|max:500',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update([
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name,
            'last_name'      => $request->last_name,
            'contact_number' => $request->contact_number,
            'address'        => $request->address,
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    // Delete a customer
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
