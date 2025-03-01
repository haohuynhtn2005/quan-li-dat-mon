<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'table'])->latest();
        $status = $request->has('status') ? urldecode(request('status')) : null;
        if (in_array($status, ['đang ăn', 'đã ăn', 'đã thanh toán'])) {
            $query->where('status', $status);
        }
        $orders = $query->paginate(10)->appends($request->query());
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
            'user_id' => 'nullable|exists:users,id',
            'table_id' => 'required|exists:tables,id',
            'discount' => 'numeric|min:0|max:100',
        ]);

        Order::create([
            'status' => 'đang ăn',
            ...$request->all(),
        ]);

        return redirect()->route('orders.index')->with('success', 'Tạo đơn thành công.');
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
            'status' => 'required|in:đang ăn,đã ăn,đã thanh toán',
            'discount' => 'numeric|min:0',
        ]);
        $allServed = $order->orderDetails()->where('status', '!=', 'đã ra')->doesntExist();
        if (!$allServed && $request->input('status') != 'đang ăn') {
            return redirect()->back()
                ->with('error', "Không thể {$request->input('status')}, có món chưa được phục vụ.");
        }
        $order->update([
            // 'paid' => $request->has('paid'),
            'status' => $request->input('status'),
            ...$request->all(),
        ]);

        return redirect()->back()->with('success', 'Cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        try {
            $order->delete();
            return redirect()->route('orders.index')->with('success', 'Đã xóa.');
        } catch (\Exception $e) {
            return redirect()->route('orders.index')->with('error', 'Lỗi xóa đơn.');
        }
    }

    public function updatePaid(Request $request, Order $order)
    {
        $allServed = $order->orderDetails()->where('status', '!=', 'đã ra')->doesntExist();
        if (!$allServed && $request->has('paid')) {
            return redirect()->route('orders.index')->with('error', 'Không thể thanh toán, có món chưa được phục vụ.');
        }
        $order->update(['paid' => $request->has('paid')]);
        return redirect()->route('orders.index')->with('success', 'Cập nhật thanh toán thành công.');
    }

    public function addOrderDetail(Request $request, Order $order)
    {
        $request->validate([
            'food_item_id' => 'required|exists:food_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($order->status != 'đang ăn') {
            return redirect()->route('orders.show', $order->id)
                ->with('error', Str::ucfirst($order->status) . ' không thể thêm');
        }

        $foodItem = FoodItem::with('ingredients')->findOrFail($request->food_item_id);
        foreach ($foodItem->ingredients as $ingredient) {
            $requiredQuantity = $ingredient->pivot->quantity * $request->quantity; // Adjust for order quantity
            if ($ingredient->quantity < $requiredQuantity) {
                return redirect()->route('orders.show', $order->id)
                    ->with('error', "Không đủ nguyên liệu: {$ingredient->name}");
            }
            $ingredient->decrement('quantity', $requiredQuantity);
        }

        OrderDetail::create([
            'order_id' => $order->id,
            'food_item_id' => $request->food_item_id,
            'quantity' => $request->quantity,
            'price' => $foodItem->price,
        ]);

        return redirect()->route('orders.show', $order->id)->with('success', 'Món ăn đã được thêm.');
    }


    public function updateOrderDetailStatus(Request $request, OrderDetail $orderDetail)
    {
        $request->validate([
            'status' => 'required|in:chuẩn bị,đã nấu,đã ra,đã hủy',
        ]);

        $orderDetail->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Cập nhật thành công');
    }

    public function removeOrderDetail(OrderDetail $orderDetail)
    {
        if ($orderDetail->status != 'chuẩn bị') {
            return redirect()->back()->with('error', 'Không thể xóa vì món ' . $orderDetail->status);
        }
        $foodItem = $orderDetail->foodItem;
        foreach ($foodItem->ingredients as $ingredient) {
            $usedQuantity = $ingredient->pivot->quantity;
            $ingredient->increment('quantity', $usedQuantity * $orderDetail->quantity);
        }
        $orderDetail->delete();

        return redirect()->back()->with('success', 'Món đã được xóa.');
    }
}
