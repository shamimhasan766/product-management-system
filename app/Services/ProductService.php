<?php

namespace App\Services;

use App\Models\Product;

class ProductService {

    public function createProduct(array $data): Product
    {
        if (isset($data['image'])) {
            $file = $data['image'];
            $filename = $data['sku'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $data['image'] = $file->storeAs('uploads/products', $filename);
        }

        return Product::create($data);
    }

    public function updateProduct(Product $product, array $data): Product
    {
        if (isset($data['image'])) {
            $file = $data['image'];
            $filename = $data['sku'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $data['image'] = $file->storeAs('uploads/products', $filename);

            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }
        } else {
            unset($data['image']);
        }

        $product->update($data);

        return $product;
    }

    public function restoreProduct($id): string
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return 'Product restored';
    }

    public function forceDeleteProduct($id): string
    {
        $product = Product::withTrashed()->findOrFail($id);

        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }

        $product->forceDelete();

        return 'Product permanently deleted';
    }

}
