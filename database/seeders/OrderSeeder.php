<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
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
            $isUnregisteredGuest = rand(1, 100) <= 20;
            Order::factory()->state([
                'user_id' => $isUnregisteredGuest ? null
                    : User::where('role', '=', 'user')
                        ->inRandomOrder()->first()?->id,
                'created_at' => Carbon::create(2024, 1, 1)->addDays($day)
            ])
                ->has(OrderDetail::factory()->count(rand(2, 5)))
                ->create();
        }
    }
}
