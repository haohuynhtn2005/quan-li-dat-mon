<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class RevenueStatisticsController extends Controller
{
    public function index(Request $request)
    {
        $type = urldecode($request->query('type', 'week'));
        $date = urldecode($request->query('date', ''));
        $foodItemId = $request->query('foodItemId');
        $selectedDate = Carbon::hasFormat($date, 'Y-m-d') ?
            Carbon::parse($date) : Carbon::now();

        if ($type == 'tháng') {
            return $this->getMonthlyRevenue($selectedDate, $foodItemId);
        } elseif ($type == 'năm') {
            return $this->getYearlyRevenue($selectedDate, $foodItemId);
        }
        return $this->getWeeklyRevenue($selectedDate, $foodItemId);
    }

    private function getWeeklyRevenue(Carbon $date, $foodItemId)
    {
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        $weeklyRevenue = $this->getRevenueQuery($startOfWeek, $endOfWeek, $foodItemId)
            ->select(
                DB::raw('DATE(orders.created_at) as day'),
                DB::raw('SUM(order_details.quantity * order_details.price) as total_revenue')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();


        $weekLabels = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
        $labels = [];
        $data = [];

        for ($date = $startOfWeek; $date <= $endOfWeek; $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $dayIndex = $date->dayOfWeekIso - 1; // Monday = 1, Sunday = 7

            $labels[] = $weekLabels[$dayIndex];

            // Find revenue for the day
            $revenue = $weeklyRevenue->firstWhere('day', $formattedDate);
            $data[] = $revenue ? $revenue->total_revenue : 0;
        }
        $foodItems = FoodItem::get();

        return view('statistics.index', compact('labels', 'data', 'foodItems'));
        // return response()->json(['labels' => $labels, 'data' => $data]);
    }

    private function getMonthlyRevenue(Carbon $date, $foodItemId)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $monthlyRevenue = $this->getRevenueQuery($startOfMonth, $endOfMonth, $foodItemId)
            ->select(
                DB::raw('(1 + FLOOR((DAY(orders.created_at) - 1) / 7)) as week_number'),
                DB::raw('SUM(order_details.price * order_details.quantity) as total_revenue'),
            )
            ->groupBy('week_number')
            ->get();

        $weekCount = ceil($startOfMonth->daysInMonth / 7);
        $labels = [];
        $data = [];

        for ($week = 1; $week <= $weekCount; $week++) {
            $labels[] = "Tuần $week";
            $revenue = $monthlyRevenue->firstWhere('week_number', '=', $week);
            $data[] = $revenue ? $revenue->total_revenue : 0;
        }
        $foodItems = FoodItem::get();

        return view('statistics.index', compact('labels', 'data', 'foodItems'));
        // return response()->json(['labels' => $labels, 'data' => $data]);
    }

    private function getYearlyRevenue(Carbon $date, $foodItemId = null)
    {
        $startOfYear = $date->copy()->startOfYear();
        $endOfYear = $date->copy()->endOfYear();

        $yearlyRevenue = $this->getRevenueQuery($startOfYear, $endOfYear, $foodItemId)
            ->select(
                DB::raw('MONTH(orders.created_at) as month'),
                DB::raw('SUM(order_details.price * order_details.quantity) as total_revenue')
            )
            ->groupBy('month')
            ->get();

        $labels = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $revenue = $yearlyRevenue->firstWhere('month', $month);
            $data[] = $revenue ? $revenue->total_revenue : 0;
        }
        $foodItems = FoodItem::get();

        return view('statistics.index', compact('labels', 'data', 'foodItems'));
        // return response()->json(['labels' => $labels, 'data' => $data]);
    }

    public function getRevenueQuery(Carbon $startDate, Carbon $endDate, $foodItemId)
    {
        return
            DB::table('orders')
                ->where('orders.status', 'đã thanh toán')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->joinSub(
                    DB::table('order_details')
                        ->when($foodItemId, function ($query) use ($foodItemId) {
                            return $query->where('order_details.food_item_id', '=', $foodItemId);
                        }),
                    'order_details',
                    'orders.id',
                    '=',
                    'order_details.order_id'
                );
    }

}
