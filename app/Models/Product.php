<?php

namespace App\Models;
use App\Models\ProductDetail;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'price', 'availability',  'category_id'
    ];


    public function product_details()
    {
        return $this->hasOne(ProductDetail::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
