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
            'Món chay' => [

            ],
            'Hải sản' => [
                [
                    'name' => 'Ghẹ hấp bia',
                    'image' => 'ghe-hap-bia.jpg',
                ],
                [
                    'name' => 'Mực nướng muối ớt',
                    'image' => 'muc-nuong-muoi-ot.jpg',
                ],
                [
                    'name' => 'Tôm sú nướng',
                    'image' => 'tom-su-nuong.jpg',
                ],
                [
                    'name' => 'Ốc hương rang muối',
                    'image' => 'oc-huong-rang-muoi.jpg',
                ],
            ],
            'Món nướng' => [
                [
                    'name' => 'Ba chỉ nướng sa tế',
                    'image' => 'ba-chi-nuong-sa-te.jpg',
                ],
                [
                    'name' => 'Bò nướng lá lốt',
                    'image' => 'bo-nuong-la-lot.jpg',
                ],
                [
                    'name' => 'Khô mực nướng',
                    'image' => 'kho-muc-nuong.jpg',
                ],
            ],
            'Món hấp' => [
                [
                    'name' => 'Ngao hấp sả',
                    'image' => 'ngao-hap-sa.jpg',
                ],
                [
                    'name' => 'Bạch tuộc hấp gừng',
                    'image' => 'bach-tuoc-hap-gung.jpg',
                ],
                [
                    'name' => 'Gà hấp hành',
                    'image' => 'ga-hap-hanh.jpg',
                ],
            ],
            'Món xào' => [
                [
                    'name' => 'Mì xào hải sản',
                    'image' => 'mi-xao-hai-san.jpg',
                ],
                [
                    'name' => 'Rau muống xào tỏi',
                    'image' => 'rau-muong-xao-toi.jpg',
                ],
            ],
            'Món gỏi' => [

            ],
            'Bánh & ăn vặt' => [
                [
                    'name' => 'Đậu hũ chiên giòn',
                    'image' => 'dau-hu-chien-gion.jpg',
                ],
                [
                    'name' => 'Bò khô lá chanh',
                    'image' => 'bo-kho-la-chanh.jpg',
                ],
            ],
            'Đồ uống' => [
                [
                    'name' => 'Bia Tiger',
                    'image' => 'bia-tiger.webp',
                ],
                [
                    'name' => 'Bia Heineken',
                    'image' => 'heineken.webp',
                ],
                [
                    'name' => 'Rượu nếp than',
                    'image' => 'ruou-nep-than.jpeg',
                ],
                [
                    'name' => 'Rượu chuối hột',
                    'image' => 'ruou-chuoi-hot.jpg',
                ],
                [
                    'name' => 'Nước ngọt Coca-Cola',
                    'image' => 'coca-cola.jpg',
                ],
                [
                    'name' => 'Trà đá',
                    'image' => 'tra-da.jpg',
                ],
            ],
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
