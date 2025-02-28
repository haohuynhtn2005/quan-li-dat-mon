<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['user', 'table'])->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('role', '=', 'user')->get();
        $tables = Table::all();
        $mode = 'create';
        return view('orders.form', compact('mode', 'users', 'tables'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'table_id' => 'required|exists:tables,id',
            'discount' => 'numeric|min:0|max:100',
        ]);

        Order::create($request->all());

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $users = User::where('role', '=', 'user')->get();
        $tables = Table::all();
        $foodItems = FoodItem::all();
        return view('orders.show', compact('order', 'users', 'tables', 'foodItems'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $users = User::where('role', '=', 'user')->get();
        $tables = Table::all();
        $mode = 'update';
        return view('orders.form', compact('mode', 'order', 'users', 'tables'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'table_id' => 'required|exists:tables,id',
            'discount' => 'numeric|min:0',
        ]);
        $order->update([
            'paid' => $request->has('paid'),
            ...$request->all(),
        ]);

        return redirect()->route('orders.index')->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        try {
            $order->delete();
            return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('orders.index')->with('error', 'Error deleting order.');
        }
    }

    public function updatePaid(Request $request, Order $order)
    {
        $order->update(['paid' => $request->has('paid')]);
        return redirect()->route('orders.index')->with('success', 'Payment status updated successfully.');
    }


    public function addOrderDetail(Request $request, Order $order)
    {
        $request->validate([
            'food_item_id' => 'required|exists:food_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $foodItem = FoodItem::findOrFail($request->food_item_id);

        OrderDetail::create([
            'order_id' => $order->id,
            'food_item_id' => $request->food_item_id,
            'quantity' => $request->quantity,
            'price' => $foodItem->price,
            'status' => 'chuẩn bị',
        ]);

        return redirect()->route('orders.show', $order->id)->with('success', 'Order detail added successfully.');
    }

    public function updateOrderDetailStatus(Request $request, OrderDetail $orderDetail)
    {
        $request->validate([
            'status' => 'required|in:chuẩn bị,đã nấu,đã ra,đã hủy',
        ]);

        $orderDetail->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Order detail status updated successfully.');
    }

    public function removeOrderDetail(OrderDetail $orderDetail)
    {
        if ($orderDetail->status === 'đã ra') {
            return redirect()->back()->with('error', 'Không thể xóa vì món đã được phục vụ.');
        }
        $orderDetail->delete();

        return redirect()->back()->with('success', 'Chi tiết đơn hàng đã được xóa.');
    }
}
