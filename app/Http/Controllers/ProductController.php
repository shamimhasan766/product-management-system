<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService){
        $this->productService = $productService;
    }

    public function index(){
        return Inertia::render('Dashboard');
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
}
