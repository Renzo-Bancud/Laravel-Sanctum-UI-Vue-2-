<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->sentence(4);
        $slug = Str::slug($title);
        return [
            'title' => $title,
            'slug' => $slug,
            'category_id' => rand(1, 5),
            'price' => rand(100, 300),
            'image' => 'https://placekitten.com/640/360',
            'description' => $this->faker->text(30),
        ];
    }
}
