<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->text,
            'manufacturer' => $this->faker->name,
            'expiry_date' => now(),
            'nafdac_reg_no' => $this->faker->randomDigit,
            
            'product_id' =>  Product::factory()->product_details()
        ];
    }
}
