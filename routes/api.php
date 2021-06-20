<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'AdminController@login');
    Route::post('register', 'AdminController@register');
});

Route::post('product/upload', 'ProductController@upload');
Route::post('product/delete/{id}', 'ProductController@deleteProduct');
Route::get('product/{id}', 'ProductController@fetchProduct');
Route::get('categories', 'CategoryController@fetchCategories');
Route::get('products/{catId}', 'ProductController@get');
Route::post('product/edit/{id}', 'ProductController@edit');
Route::post('delete/image/{id}', 'ProductController@deleteImage');
Route::get('featured/products', 'ProductController@featured');
Route::get('product/category/{catId}', 'productController@productCategory');
