<?php

namespace App\Models;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'description', 'manufacturer', 'expiry_date',  'product_id', 'nafdac_reg_no'
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
