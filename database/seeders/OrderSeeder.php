<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quantity = 10;
        for ($day = 1; $day <= $quantity; $day++) {
            Order::factory()->state([
                'created_at' => Carbon::create(2024, 1, 1)->addDays($day)
            ])
                ->has(OrderDetail::factory()->count(3))
                ->create();
        }
    }
}
