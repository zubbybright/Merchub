<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'name' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat($nbMaxDecimals = 5, $min = 0, $max = 20),
            'category_id' =>  Category::factory(),
            "availability" =>  $this->faker->randomElement(['IN_STOCK','SOLD_OUT']),
        ];
    }


}
