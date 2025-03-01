<?php

namespace Database\Seeders;

use App\Models\FoodIngredient;
use App\Models\FoodItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoodItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $foodTypes = [
            // Món nước (Noodle soups)
            1 => [
                [
                    'name' => 'Lẩu thái',
                    'image' => 'lau-thai.jpg',
                ],
                [
                    'name' => 'Lẩu tomyum',
                    'image' => 'lau-tomyum.jpg',
                ],
            ],

            // Món cơm (Rice dishes)
            2 => [
                [
                    'name' => 'Cơm chiên nước mắm',
                    'image' => 'com-chien-nuoc-mam.jpg',
                ],
                [
                    'name' => 'Cơm chiên trân châu',
                    'image' => 'com-chien-tran-chau.jpg',
                ],
            ],

            // Món xào (Stir-fried dishes)
            3 => [
                [
                    'name' => 'Mì xào hải sản',
                    'image' => 'mi-xao-hai-san.jpg',
                ],
                [
                    'name' => 'Rau muống xào tỏi',
                    'image' => 'rau-muong-xao-toi.jpg',
                ],
            ],

            // Hải sản (Seafood)
            4 => [
                [
                    'name' => 'Ghẹ hấp bia',
                    'image' => 'ghe-hap-bia.jpg',
                ],
                [
                    'name' => 'Mực nướng muối ớt',
                    'image' => 'muc-nuong-muoi-ot.jpg',
                ],
            ],
        ];
        foreach ($foodTypes as $idx => $foodItems) {
            foreach ($foodItems as $item) {
                $foodItem = FoodItem::factory()->create([
                    'name' => $item['name'],
                    'image' => $item['image'],
                    'food_type_id' => $idx,
                ]);
                FoodIngredient::factory(2)->create([
                    'food_item_id' => $foodItem->id,
                ]);
            }
        }
    }
}
