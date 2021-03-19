<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'in_stock_count', 'sold_out_count'
    ];

    public function products(){
        return $this->hasMany(Product::class);
    }
    public function productDetail(){
        return $this->hasManyThrough(ProductDetail::class, Product::Class);
    }
    public function productImages(){
        return $this->hasManyThrough(ProductImage::class, Product::class);
    }

}
