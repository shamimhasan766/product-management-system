<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['image','name', 'sku', 'price', 'stock'];
    protected $appends = ['image_url'];

    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->image) {
                return null;
            }
            if(env('APP_ENV') === 'local'){
                return asset($this->image);
            }
            else{
                return asset('public/' . $this->image);
            }
        });
    }
}
