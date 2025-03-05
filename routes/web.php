<?php

use App\Http\Controllers\FoodIngredientController;
use App\Http\Controllers\FoodItemController;
use App\Http\Controllers\FoodTypeController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RevenueStatisticsController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu', [HomeController::class, 'menu'])->name('menu');

Route::middleware(['auth', 'verified', 'role:staff,admin'])->prefix('manage')->group(function () {
    Route::get('/', function () {
        return view('layouts.dash');
    })->name('manage.dashboard');

    Route::resource('tables', TableController::class);
    Route::resource('food-types', FoodTypeController::class);
    Route::resource('food-items', FoodItemController::class);
    Route::resource('ingredients', IngredientController::class);
    Route::resource('food_ingredients', FoodIngredientController::class);
    Route::resource('orders', OrderController::class);

    Route::put('/orders/update-paid/{order}', [OrderController::class, 'updatePaid'])->name('orders.updatePaid');
    Route::patch('/orders/update-paid/{order}', [OrderController::class, 'updatePaid'])->name('orders.updatePaid');
    Route::post('/orders/{order}/details', [OrderController::class, 'addOrderDetail'])->name('orders.addDetail');
    Route::put('/order-details/{orderDetail}/status', [OrderController::class, 'updateOrderDetailStatus'])
        ->name('order-details.updateStatus');
    Route::patch('/order-details/{orderDetail}/status', [OrderController::class, 'updateOrderDetailStatus'])
        ->name('order-details.updateStatus');
    Route::delete('/order-details/{orderDetail}', [OrderController::class, 'removeOrderDetail'])
        ->name('order-details.destroy');
    Route::get('/statistics', [RevenueStatisticsController::class, 'index'])->name('statistics.index');


    Route::get('/department', [DepartmentController::class, 'index'])->name('department.index');
    Route::get('/department/create', [DepartmentController::class, 'create'])->name('department.create');
    Route::post('/department', [DepartmentController::class, 'store'])->name('department.store');
    Route::get('/department/{id}/edit', [DepartmentController::class, 'edit'])->name('department.edit');
    Route::put('/department/{id}', [DepartmentController::class, 'update'])->name('department.update');
    Route::delete('/department/{id}', [DepartmentController::class, 'destroy'])->name('department.destroy');

    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
    Route::get('/employee/create', [EmployeeController::class, 'create'])->name('employee.create');
    Route::post('/employee', [EmployeeController::class, 'store'])->name('employee.store');
    Route::get('/employee/edit/{id}', [EmployeeController::class, 'edit'])->name('employee.edit');
    Route::put('/employee/{id}', [EmployeeController::class, 'update'])->name('employee.update');
    Route::delete('/employee/{id}', [EmployeeController::class, 'destroy'])->name('employee.destroy');

    Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('/customer/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/customer/edit/{id}', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::put('/customer/{id}', [CustomerController::class, 'update'])->name('customer.update');
    Route::delete('/customer/{id}', [CustomerController::class, 'destroy'])->name('customer.destroy');
});

Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
Route::get('/invoice/create', [InvoiceController::class, 'create'])->name('invoice.create');
Route::post('/invoice/store', [InvoiceController::class, 'store'])->name('invoice.store');
Route::delete('/invoice/{id}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');
Route::get('/invoice/{id}/edit', [InvoiceController::class, 'edit'])->name('invoice.edit');
Route::put('/invoice/{id}', [InvoiceController::class, 'update'])->name('invoice.update');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
