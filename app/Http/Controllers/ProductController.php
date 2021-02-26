<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Product;
use App\Models\Category;
use App\Models\Image;
use App\Models\ProductDetail;
use Carbon\Carbon;

class ProductController extends BaseController
{   
    private $catId;

    public function upload(Request $request){
        //A product can be uploaded

        //validate the input
        $data = $request->validate([
            'category'=>['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'string'],
            'description'=>['required', 'string'],
            'manufacturer'=>['required','string'],
            'nafdac_no'=>['nullable', 'string'],
            'expiry'=>['nullable', 'date'],
            'image1'=>['required', 'image', 'mimes:jpeg,png,jpg,gif','max:2048'],
            'image2'=>['nullable', 'image', 'mimes:jpeg,png,jpg,gif','max:2048'],
            'image3'=>['nullable', 'image', 'mimes:jpeg,png,jpg,gif','max:2048'],
        ]);

        //CREATE CATEGORY: 
        $category = new Category;   
        
        //if the category exists, get Id. Else, insert:
        $catExists = $category->where('name',$data['category'])->first();
        
        if ($catExists !== null)
        {
            $this->catId = $catExists->id;
        }
        else
        {
            $category->name = $data['category'];
            $category->save();
            $this->catId = $category->id;
        }
        
        //CREATE THE PRODUCT:
        $product = Product::create([
            'name' => $data['name'],
            'price'=> $data['price'],
            'availability'=>'in stock',
            'category_id' => $this->catId,
        ]);
        //CREATE OTHER PRODUCT DETAILS:
        $product
        ->detail()
        ->create([
            'description' => $data['description'],
            'manufacturer' => $data['manufacturer'],
            'product_id' =>$product->id,
            'expiry_date'=> $data['expiry'],
            'nafdac_reg_no'=> $data['nafdac_no']
        ]);
        
        //UPLOAD IMAGES
        $image = new Image;
        if ($request->hasFile('image1')) {
            $pic = $request->file('image1');
            $name = "Prod ".$product->id." (A)".".".$pic->guessExtension();
            $destinationPath = public_path('image1');
            $image->image1 = $name;
            $image->product_id = $product->id;
            $pic->move($destinationPath, $name);
            $image->save();
        }
        if ($request->hasFile('image2')) {
            $pic = $request->file('image2');
            $name = "Prod ".$product->id." (B)".".".$pic->guessExtension();
            $destinationPath = public_path('image2');
            $image->image2 = $name;
            $pic->move($destinationPath, $name);
            $image->save();
        }
        if ($request->hasFile('image3')) {
            $pic = $request->file('image3');
            $name = "Prod ".$product->id." (C)".".".$pic->guessExtension();
            $destinationPath = public_path('image3');
            $image->image3 = $name;
            $pic->move($destinationPath, $name);
            $image->save();
        }

        $info  = [
            'product' => $product,
            'category' => $product->category,
            'description'=> $product->detail,
            'images'=> $image
        ];
        return $this->sendResponse($info, "Product Uploaded.");
    }

    

}
