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

            if ($product->image && file_exists($product->image)) {
                unlink(public_path($product->image));
            }
        } else {
            unset($data['image']);
        }

        $product->update($data);

        return $product;
    }

}
