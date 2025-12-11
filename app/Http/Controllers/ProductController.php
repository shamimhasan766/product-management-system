<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService){
        $this->productService = $productService;
    }

    public function index(Request $request){
        $products = Product::select('id', 'name', 'sku', 'image', 'price', 'stock', 'created_at')
                     ->when($request->search, function ($query) use ($request) {
                        $search = $request->search;
                        $query->where(function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                        });
                    })
                    ->latest()
                    ->paginate(10);
        return Inertia::render('Dashboard', [
        'products' => $products
    ]);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name'  => 'required|string',
            'sku'   => ['required', 'unique:products,sku', 'regex:/^\S+$/'],
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120'
        ],[
            'sku.regex' => "SKU should not have space"
        ]);

        $this->productService->createProduct($validated);

        return back()->with('success', 'Product created successfully!');
    }

    public function update(Request $request, Product $product){
        $validated = $request->validate([
            'name'  => 'required|string',
            'sku'   => ['required', 'unique:products,sku,' . $product->id, 'regex:/^\S+$/'],
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ],[
            'sku.regex' => "SKU should not have space"
        ]);

        $this->productService->updateProduct($product, $validated);
        
        return back()->with('success', 'Product updated successfully!');
    }
}
