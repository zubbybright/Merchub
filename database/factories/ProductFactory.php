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
            'name' => $this->faker->text,
            'price' => $this->faker->randomDigit,
            'availability' => now(),

            'category_id' =>  Category::factory()->product()
        ];
    }

    public function in_stock()
    {
        return $this->state(function (array $attributes) {
            return [
                'availability' => 'in_stock',
            ];
        });
    }

    public function sold_out()
    {
        return $this->state(function (array $attributes) {
            return [
                'availability' => 'sold_out',
            ];
        });
    }
}
