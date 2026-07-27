<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        return Inertia::render('Admin/ProductList', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'stock_quantity' => 'required|integer|min:0',
            'badge' => 'nullable|string|max:255',
            'units_per_package' => 'nullable|integer|min:1',
            'is_event' => 'nullable|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img/products'), $filename);
            $validated['image_path'] = '/img/products/' . $filename;
        }

        unset($validated['image']);
        $validated['is_active'] = true;
        $validated['is_event'] = $request->boolean('is_event');

        Product::create($validated);

        return redirect()->back()->with('success', 'Producto creado');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'stock_quantity' => 'required|integer|min:0',
            'badge' => 'nullable|string|max:255',
            'units_per_package' => 'nullable|integer|min:1',
            'is_event' => 'nullable|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image_path && file_exists(public_path($product->image_path))) {
                unlink(public_path($product->image_path));
            }
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img/products'), $filename);
            $validated['image_path'] = '/img/products/' . $filename;
            unset($validated['image']);
        } else {
            unset($validated['image']);
        }

        $validated['is_event'] = $request->boolean('is_event');
        $product->update($validated);

        return redirect()->back()->with('success', 'Producto actualizado');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path && file_exists(public_path($product->image_path))) {
            unlink(public_path($product->image_path));
        }

        $product->delete();

        return redirect()->back()->with('success', 'Producto eliminado');
    }
}
