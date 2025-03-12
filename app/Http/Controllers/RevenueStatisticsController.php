<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use App\Models\FoodType;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class RevenueStatisticsController extends Controller
{
    public function index(Request $request)
    {
        $type = urldecode($request->query('type', 'week'));
        $date = urldecode($request->query('date', ''));
        $foodTypeId = $request->query('foodTypeId');
        $selectedDate = Carbon::hasFormat($date, 'Y-m-d') ?
            Carbon::parse($date) : Carbon::now();

        if ($type == 'tháng') {
            return $this->getMonthlyRevenue($selectedDate, $foodTypeId);
        } elseif ($type == 'năm') {
            return $this->getYearlyRevenue($selectedDate, $foodTypeId);
        }
        return $this->getWeeklyRevenue($selectedDate, $foodTypeId);
    }

    private function getWeeklyRevenue(Carbon $date, $foodTypeId)
    {
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        $inStoreOrders = $this->getRevenueQuery($startOfWeek, $endOfWeek, $foodTypeId)
            ->select(
                DB::raw('DATE(orders.created_at) as day'),
                DB::raw('SUM(order_details.quantity * order_details.price) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as total_orders')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();
        $onlineOrders = $this->getOnlineRevenueQuery($startOfWeek, $endOfWeek, $foodTypeId)
            ->select(
                DB::raw('DATE(orders.created_at) as day'),
                DB::raw('SUM(order_details.quantity * order_details.price) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as total_orders')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $weekLabels = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
        $labels = [];
        $inStoreRevenueData = [];
        $onlineRevenueData = [];
        $inStoreOrderCountData = [];
        $onlineOrderCountData = [];

        for ($date = $startOfWeek; $date <= $endOfWeek; $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $dayIndex = $date->dayOfWeekIso - 1; // Monday = 1, Sunday = 7

            $labels[] = $weekLabels[$dayIndex];

            // Find data for in-store orders
            $inStoreData = $inStoreOrders->firstWhere('day', $formattedDate);
            $inStoreRevenueData[] = $inStoreData ? $inStoreData->total_revenue : 0;
            // $inStoreOrderCountData[] = $inStoreData ? $inStoreData->total_orders : 0;

            // Find data for online orders
            $onlineData = $onlineOrders->firstWhere('day', $formattedDate);
            $onlineRevenueData[] = $onlineData ? $onlineData->total_revenue : 0;
            // $onlineOrderCountData[] = $onlineData ? $onlineData->total_orders : 0;
        }
        $foodTypes = FoodType::get();

        return view('statistics.index', compact('labels', 'inStoreRevenueData', 'onlineRevenueData', 'foodTypes'));
        // return response()->json(['labels' => $labels, 'data' => $data]);
    }

    private function getMonthlyRevenue(Carbon $date, $foodTypeId)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $monthlyRevenue = $this->getRevenueQuery($startOfMonth, $endOfMonth, $foodTypeId)
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
        $foodTypes = FoodType::get();

        return view('statistics.index', compact('labels', 'data', 'foodTypes'));
        // return response()->json(['labels' => $labels, 'data' => $data]);
    }

    private function getYearlyRevenue(Carbon $date, $foodTypeId = null)
    {
        $startOfYear = $date->copy()->startOfYear();
        $endOfYear = $date->copy()->endOfYear();

        $yearlyRevenue = $this->getRevenueQuery($startOfYear, $endOfYear, $foodTypeId)
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
        $foodTypes = FoodType::get();

        return view('statistics.index', compact('labels', 'data', 'foodTypes'));
        // return response()->json(['labels' => $labels, 'data' => $data]);
    }

    public function getRevenueQuery(Carbon $startDate, Carbon $endDate, $foodTypeId)
    {
        return
            DB::table('orders')
                ->where('paid', true)
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->join('food_items', 'order_details.food_item_id', '=', 'food_items.id')
                ->when($foodTypeId, function ($query) use ($foodTypeId) {
                    return $query->where('food_items.food_type_id', '=', $foodTypeId);
                });
    }

    public function getOnlineRevenueQuery(Carbon $startDate, Carbon $endDate, $foodTypeId)
    {
        return
            DB::table('online_orders')
                ->where('paid', true)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->join('online_orders_items', 'online_orders.id', '=', 'online_orders_items.order_id')
                ->join('food_items', 'online_orders_items.food_item_id', '=', 'food_items.id')
                ->when($foodTypeId, function ($query) use ($foodTypeId) {
                    return $query->where('food_items.food_type_id', '=', $foodTypeId);
                });
    }

}
