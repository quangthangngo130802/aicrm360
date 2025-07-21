<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    // Quan hệ: 1 nguồn có nhiều khách hàng
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
