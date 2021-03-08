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

    //Delete a product
    public function deleteProduct($id){
        if((Product::find($id))==null){
            return $this->sendError('product does not exist', 'product does not exist');
        }
        $deletedRecord = Product::where('id', $id)->delete();
        return $this->sendResponse("Product deleted", "Product deleted");
    }

    //Fetch specific product
    public function fetchProduct($id){
        if((Product::find($id))==null){
            return $this->sendError('product does not exist', 'product does not exist');
        }
        $product = Product::where('id', $id)->first();
        $detail = $product->detail;
        $images = $product->images;

        $info = [
            'product'=> $product,
            'detail'=> $detail,
            'images'=>$images
        ];
        return $this->sendResponse($info, "Product found");
    }

    //Fetch all categories
    public function fetchCategories(){
        $categories = Category::all();
        if($categories==null){
            return $this->sendError('No categories yet', 'No categories yet');
        }
       
        return $this->sendResponse($categories, "All categories");
    }

    //get products belonging to a category
    public function categoryProducts($cat){
        $category = Category::where('name', $cat)->first();

        if($category==null){
            return $this->sendError('Category does not exist', 'Category does not exist');
        }
        
        $products = Product::where('category_id', $category->id)->get();
        
        return $this->sendResponse($products, "All $cat products");
    }

    //get all products
    public function fetchAllProducts(){
        $products = Product::all();
        if($products==null){
            return $this->sendError('No products yet', 'No products yet');
        }
       
        return $this->sendResponse($products, "All products");
    } 

    //edit product
    public function edit(Request $request, $id){
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

        //update category: 
        
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

            $product = Product::where('id',$id)->first();
            if($product==null){
                return $this->sendError('product does not exist','product does not exist');
            }
            $product->name = $data['name'];
            $product->price = $data['price'];
            $product->category_id = $catId;

            //update details
            $product
            ->detail()
            ->update([
                'description' => $data['description'],
                'manufacturer' => $data['manufacturer'],
                'product_id' =>$product->id,
                'expiry_date'=> $data['expiry'],
                'nafdac_reg_no'=> $data['nafdac_no']
            ]);

            //upload images
            $image = ProductImage::where('product_id',$id)->first();
            $fileFieldNames = ['image1', 'image2', 'image3'];
            
            foreach ($fileFieldNames as $field){
                if ($request->hasFile($field))
                {
                    $this->uploadImage($request, $field, $image, $product);
                    $image->save();
                }
               
            };
            return $this->sendResponse("Update successful", "Update successful");
    }

    //delete an image
    public function deleteImage($id){
        if((ProductImage::find($id))==null){
            return $this->sendError('image does not exist', 'image does not exist');
        }
        $deletedImage = ProductImage::where('id', $id)->delete();
        return $this->sendResponse("Image deleted", "Image deleted");
    }

}
