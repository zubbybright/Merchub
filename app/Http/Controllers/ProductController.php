<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Product;
use App\Models\Category;
use App\Models\Image;
use App\Models\ProductDetail;

class ProductController extends BaseController
{   
    
    public function upload(){
        //A product can be uploaded

        //validate the input
        $data = $request->validate([
            'category'=>['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'string'],
            'image1'=>['required', 'image', 'mimes:jpeg,png,jpg,gif','max:2048'],
            'image2'=>['nullable', 'image', 'mimes:jpeg,png,jpg,gif','max:2048'],
            'image3'=>['nullable', 'image', 'mimes:jpeg,png,jpg,gif','max:2048'],
            'description'=>['required', 'string'],
            'manufacturer'=>['required','string'],
            'nafdac_no'=>['nullable', 'string'],
            'expiry'=>['nullable', 'date']
        ]);

        //create category
        $category = Category::create([
            'name' => $data['category']
        ]);

        //create the product
        $product = Product::create([
            'name' => $data['name'],
            'price'=> $data['price'],
            'availability'=>'in stock',
            'category_id' => $category->id,
        ]);
        
        //create other product details
        $details = ProductDetail::create([
            'description' => $data['description'],
            'manufacturer' => $data['manufacturer'],
            'product_id' =>$product->id,
            'expiry_date'=> $data['expiry'],
            'nafdac_reg_no'=> $data['nafdac_no']
        ]);

        //upload image
        $image = new Image;
        if ($request->hasFile('image1')) {
            $pic = $request->file('image1');
            $name = $pic->guessExtension();
            $destinationPath = public_path('image1');
            $image->image1 = $name;
            $pic->move($destinationPath, $name);
            $image->save();
        }
        if ($request->hasFile('image2')) {
            $pic = $request->file('image2');
            $name = $pic->guessExtension();
            $destinationPath = public_path('image2');
            $image->image2 = $name;
            $pic->move($destinationPath, $name);
            $image->save();
        }
        if ($request->hasFile('image3')) {
            $pic = $request->file('image3');
            $name = $pic->guessExtension();
            $destinationPath = public_path('image3');
            $image->image3 = $name;
            $pic->move($destinationPath, $name);
            $image->save();
        }
        
        $info  = [
            'product' => $product,
            'category' => $category,
            'description'=> $details,
            'images'=> $image
        ];
        return $this->sendResponse($info, "Product Uploaded.");
    }
}
