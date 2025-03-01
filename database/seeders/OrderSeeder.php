<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Table;
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
        $date = Carbon::create(2024, 1, 1);
        $quantity = 10;
        for ($day = 1; $day <= $quantity; $day++) {
            $isUnregisteredGuest = rand(1, 100) <= 20;
            Order::factory()->state([
                'user_id' => $isUnregisteredGuest ? null
                    : User::where('role', '=', 'user')
                        ->inRandomOrder()->first()?->id,
                'created_at' => $date->addDays($day),
                // 'paid' => true,
                'status' => 'đã thanh toán',
            ])
                ->has(
                    OrderDetail::factory()
                        ->state(['status' => 'đã ra',])
                        ->count(rand(2, 5))
                )
                ->create();
        }
        $date->addDays(1);
        $tableIds = Table::pluck('id')->shuffle();
        foreach ($tableIds as $idx => $tableId) {
            $isUnregisteredGuest = rand(1, 100) <= 20;
            $statuses = ['đang ăn', 'đã ăn', 'đã thanh toán'];
            $status = $statuses[random_int(0, count($statuses) - 1)];
            // $paid = rand(1, 100) <= 30;
            $orderDetailState = $status != 'đang ăn' ? ['status' => 'đã ra'] : [];
            Order::factory()->state([
                'user_id' => $isUnregisteredGuest ? null
                    : User::where('role', '=', 'user')
                        ->inRandomOrder()->first()?->id ?? User::factory(),
                'created_at' => $date->addDays($idx),
                // 'paid' => $paid,
                'status' => $status,
            ])
                ->has(
                    OrderDetail::factory()
                        ->state($orderDetailState)
                        ->count(rand(2, 5))
                )
                ->create();
        }
    }
}
