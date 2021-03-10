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
Route::get('product/fetch/{id}', 'ProductController@fetchProduct');
Route::get('product/categories', 'ProductController@fetchCategories');
Route::get('products/all', 'ProductController@fetchAllProducts');
Route::get('category/products/{id}', 'ProductController@categoryProducts');
Route::post('product/edit/{id}', 'ProductController@edit');
Route::post('delete/image/{id}', 'ProductController@deleteImage');
