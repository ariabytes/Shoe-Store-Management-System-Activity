<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shoe;

class ShoeController extends Controller
{
    // Show all shoes 
    public function index(Request $request)
    {
        $search = $request->search;

        $shoes = Shoe::when($search, function ($query) use ($search) {
            $query->where('product_name', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%");
        })
            ->orderBy('id', 'desc')
            ->get();

        $editShoe = $request->edit_id
            ? Shoe::findOrFail($request->edit_id)
            : null;

        return view('shoes.index', compact('shoes', 'editShoe', 'search'));
    }

    // Save new shoe to DB
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'brand'        => 'required|string|max:255',
            'category'     => 'required|string|max:255',
            'size'         => 'required|numeric',
            'color'        => 'required|string|max:100',
            'stock'        => 'required|integer|min:0',
            'price'        => 'required|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        Shoe::create([
            'product_name' => $request->product_name,
            'brand'        => $request->brand,
            'category'     => $request->category,
            'size'         => $request->size,
            'color'        => $request->color,
            'stock'        => $request->stock,
            'price'        => $request->price,
            'description'  => $request->description,
        ]);

        return redirect()->route('shoes.index')->with('success', 'Shoe added successfully.');
    }

    // Update existing shoe
    public function update(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'brand'        => 'required|string|max:255',
            'category'     => 'required|string|max:255',
            'size'         => 'required|numeric',
            'color'        => 'required|string|max:100',
            'stock'        => 'required|integer|min:0',
            'price'        => 'required|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        $shoe = Shoe::findOrFail($id);
        $shoe->update([
            'product_name' => $request->product_name,
            'brand'        => $request->brand,
            'category'     => $request->category,
            'size'         => $request->size,
            'color'        => $request->color,
            'stock'        => $request->stock,
            'price'        => $request->price,
            'description'  => $request->description,
        ]);

        return redirect()->route('shoes.index')->with('success', 'Shoe updated successfully.');
    }

    // Delete a shoe
    public function destroy($id)
    {
        $shoe = Shoe::findOrFail($id);
        $shoe->delete();

        return redirect()->route('shoes.index')->with('success', 'Shoe deleted successfully.');
    }
}
