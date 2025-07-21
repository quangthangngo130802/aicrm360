<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'customer_id',
        'user_id',
        'status',
        'total_amount',
    ];

    // Quan hệ: đơn hàng thuộc về khách hàng
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Quan hệ: đơn hàng được tạo bởi nhân viên (user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'status' => OrderStatus::class,
    ];
}
