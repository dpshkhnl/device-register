<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::withCount('deviceModels')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        Product::create($this->validated($request));

        return redirect()->route('admin.products.index')->with('status', 'Product created.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product));

        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Product deleted.');
    }

    protected function validated(Request $request, ?Product $product = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('name'), '_')]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:50', 'unique:products,slug'.($product ? ','.$product->id : '')],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['name'] = trim($data['name']);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        return $data;
    }
}
