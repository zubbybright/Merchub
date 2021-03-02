<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductDetail;
use Carbon\Carbon;

class ProductController extends BaseController
{   

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
        
        //if the category exists, get Id. Else, insert:
        $catExists = Category::where('name',$data['category'])->first();
        $catId = "";
        if ($catExists !== null)
        {
            $catId = $catExists->id;
        }
        else
        {   
            $category = new Category;
            $category->name = $data['category'];
            $category->save();
            $catId = $category->id;
        }
        
        //CREATE THE PRODUCT:
        $product = Product::create([
            'name' => $data['name'],
            'price'=> $data['price'],
            'availability'=>'IN_STOCK',
            'category_id' => $catId,
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
        $image = new ProductImage;
        $fileFieldNames = ['image1', 'image2', 'image3'];
        
        foreach ($fileFieldNames as $field){
            if ($request->hasFile($field))
            {
                $this->uploadImage($request, $field, $image, $product);
                $image->save();
            }
           
        };

        $info  = [
            'product' => $product,
            'category' => $product->category,
            'description'=> $product->detail,
            'images'=> $image
        ];
        return $this->sendResponse($info, "Product Uploaded.");
    }

    private function uploadImage(Request $request,$imgFieldName, $image, $product){
        $pic = $request->file($imgFieldName);
        $name = "Prod ".$product->id." ".$imgFieldName.".".$pic->guessExtension();
        $destinationPath = public_path($imgFieldName);
        $image->$imgFieldName = $name;
        $image->product_id = $product->id;
        $pic->move($destinationPath, $name);
    }

    public function delete($id){

        $record = Product::where('id', $id)->delete();
        return $this->sendResponse($record, "Product deleted");
    }

}
