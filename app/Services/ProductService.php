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

}
