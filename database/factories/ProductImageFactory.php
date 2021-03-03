<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductImage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'product_id' =>  Product::factory(),
            'image1' => $this->faker->image(public_path(),640,480, null, false),
            'image2' => $this->faker->image(public_path(),640,480, null, false),
            'image3' => $this->faker->image(public_path(),640,480, null, false),
        ];
    }
}
