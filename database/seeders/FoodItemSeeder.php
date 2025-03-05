<?php

namespace Database\Seeders;

use App\Models\FoodIngredient;
use App\Models\FoodItem;
use App\Models\FoodType;
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
            'Món nước' => [
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
            'Món cơm' => [
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
            'Món chay' => [
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
            'Hải sản' => [
                [
                    'name' => 'Ghẹ hấp bia',
                    'image' => 'ghe-hap-bia.jpg',
                ],
                [
                    'name' => 'Mực nướng muối ớt',
                    'image' => 'muc-nuong-muoi-ot.jpg',
                ],
            ],
            'Món nướng' => [],
            'Món hấp' => [],
            'Món xào' => [],
            'Món gỏi' => [],
            'Bánh & ăn vặt' => [],
            'Đồ uống' => [],
        ];
        foreach ($foodTypes as $key => $foodItems) {
            $foodType = FoodType::factory()->create([
                'name' => $key,
            ]);
            foreach ($foodItems as $item) {
                $foodItem = FoodItem::factory()->create([
                    'name' => $item['name'],
                    'image' => $item['image'],
                    'food_type_id' => $foodType->id,
                ]);
                FoodIngredient::factory(2)->create([
                    'food_item_id' => $foodItem->id,
                ]);
            }
        }
    }
}
