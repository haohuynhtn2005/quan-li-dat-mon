<?php

namespace Database\Seeders;

use App\Models\FoodType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoodTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $foodTypes = [
            'Món nước',
            'Món cơm',
            'Món chay',
            'Hải sản',
            'Món nướng',
            'Món hấp',
            'Món xào',
            'Món gỏi',
            'Bánh & ăn vặt',
            'Đồ uống',
        ];
        foreach ($foodTypes as $idx => $item) {
            FoodType::factory()->create([
                'name' => $item,
            ]);
        }
    }
}
