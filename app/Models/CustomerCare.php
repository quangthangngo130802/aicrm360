<?php

namespace App\Models;

use App\Models\Channel;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCare extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'user_id',
        'channel_id',
        'care_date',
        'result_id',
        'note'
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
    public function result()
    {
        return $this->belongsTo(Result::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
