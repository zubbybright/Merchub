<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class CategoryController extends BaseController
{
    //

       //Fetch all categories
    public function fetchCategories(){
        $categories = Category::all();
        return $this->sendResponse($categories, "All categories");
    }

   
}
