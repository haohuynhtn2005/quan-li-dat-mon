<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'table_id',
        'discount',
        'paid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function getTotalAttribute()
    {
        return $this->orderDetails()
            ->where('status', '!=', 'chuẩn bị')
            ->sum(\DB::raw('quantity * price'));
    }
}
