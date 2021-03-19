<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Database\Seeders\ProductSeeder;
use Database\Seeders\CategorySeeder;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;

class ProductTest extends TestCase
{   
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    const UPLOAD_URL = '/api/product/upload';
    const DELETE_URL = '/api/product/delete/';
    const EDIT_URL = '/api/product/edit/';

    protected $img1, $img2, $img3, $product, $category;

    protected function setUp(): void{
        parent::setUp();
        $this->seed(ProductSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->product = Product::first();
        $this->category = Category::first();

        Storage::fake('images');
        $this->img1 = UploadedFile::fake()->image('image1.jpg');
        $this->img2 = UploadedFile::fake()->image('image2.jpg');
        $this->img3 = UploadedFile::fake()->image('image3.jpg');
    }

    public function test_a_product_can_be_uploaded(){

        $response = $this->postjson(self::UPLOAD_URL,[
            'category'=>'Cosmetics',
            'name' => 'Mac eye shadow',
            'price' => '4500',
            'description'=>'very good for you',
            'manufacturer'=>'Mac cosmetics',
            'nafdac_no'=>'09-39495',
            'expiry'=>'23-09-23',
            'image1' => $this->img1,
            'image2' => $this->img2,
            'image3' => $this->img3
        ]);
        $response->assertStatus(200);
        
    }

    public function test_compulsory_fileds_must_be_included_in_upload(){

        $response = $this->postjson(self::UPLOAD_URL,[
            'category'=>' ',
            'name' => ' ',
            'price' => ' ',
            'description'=>' ',
            'manufacturer'=>' ',
            'nafdac_no'=>'09-39495',
            'expiry'=>'23-09-23',
            'image1' => ' ',
            'image2' => $this->img2,
            'image3' => $this->img3
        ]);
        $response->assertStatus(422);
        $response->assertJsonFragment([
            'category' => ["The category field is required."],
            'price' => ["The price field is required."],
            'description' => ["The description field is required."],
            'name'=>["The name field is required."],
            'manufacturer'=>["The manufacturer field is required."],
            'image1'=>["The image1 field is required."]
        ]);
    }

    public function test_image_fields_must_contain_image_files_during_upload(){
        $file = UploadedFile::fake()->image('file.mp3');
        
        $response = $this->postjson(self::UPLOAD_URL,[
            'category'=>'Cosmetics',
            'name' => 'Mac eye shadow',
            'price' => '4500',
            'description'=>'very good for you',
            'manufacturer'=>'Mac cosmetics',
            'nafdac_no'=>'09-39495',
            'expiry'=>'23-09-23',
            'image1' => $file,
            'image2' => ' ',
            'image3' => ' '
        ]);
        $response->assertStatus(422);
        $response->assertJsonFragment([
            'image1'=>[
                "The image1 must be a file of type: jpeg, png, jpg, gif.",
                "The image1 must be an image."
            ]
        ]);

    }

    public function test_upload_text_inputs_must_be_in_string_format(){
        $response = $this->postjson(self::UPLOAD_URL,[
            'category'=>102000300,
            'name' => 9484949900004,
            'price' => 400040040,
            'description'=>94000499400,
            'manufacturer'=>102000300,
            'nafdac_no'=>102000300,
            'expiry'=>102000300,
            'image1' => $this->img1,
            'image2' => $this->img2,
            'image3' => $this->img3
        ]);
        $response->assertStatus(422);
    }
    
    public function test_a_product_can_be_deleted(){
        $response = $this->post(self::DELETE_URL.$this->product->id);
        $response->assertStatus(200);
    }

    public function test_product_must_exist_to_be_deleted(){
        $response = $this->post(self::DELETE_URL.'15');
        $response->assertStatus(200);
    }

    public function test_a_product_can_be_edited(){
       
        $file1 = UploadedFile::fake()->image('file1.jpg');
        $response = $this->postjson(self::EDIT_URL.$this->product->id,[
            'category'=>'Cosmetics',
            'name' => 'Mac eye shadow',
            'price' => '4500',
            'description'=>'very good for you',
            'manufacturer'=>'Mac cosmetics',
            'nafdac_no'=>'09-39495',
            'expiry'=>'23-09-23',
            'image1' => $file1,
            'image2' => ' ',
            'image3' => ' '
        ]);
      
        $response->assertStatus(200);
    }

    public function test_a_product_can_be_fetched_by_id(){
        $response = $this->get('/api/product/'.$this->product->id);

        $response->assertStatus(200);
    }

    public function test_a_product_cannot_be_fetched_with_wrong_id(){
        $response = $this->get('/api/product/100');
        $response->assertStatus(404);
    }

    public function test_products_can_be_fectched_by_category(){

        $response = $this->get('/api/products/'.$this->category->id);
        $response->assertStatus(200);
    }

    public function test_products_cannot_be_fectched_by_wrong_category(){

        $response = $this->get('/api/products/50');
        $response->assertStatus(200);
    }

    public function test_all_categories_can_be_fetched(){
        $response = $this->get('/api/categories');
        $response->assertStatus(200);
    }

    public function test_an_image_can_be_deleted(){

        $imageId = ProductImage::where('product_id',$this->product->id)->first();
        $response = $this->post('/api/delete/image/'.$imageId->id);
        $response->assertStatus(200);
    }

    public function test_an_image_must_first_exist_to_be_deleted(){
        $response = $this->post('/api/delete/image/50');
        $response->assertStatus(200);
    }
}
