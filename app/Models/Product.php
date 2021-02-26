<?php

namespace App\Models;
use App\Models\ProductDetail;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'price', 'availability',  'category_id'
    ];


    public function detail()
    {
        return $this->hasOne(ProductDetail::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function images(){
        return $this->hasMany(Image::class);
    }
}
