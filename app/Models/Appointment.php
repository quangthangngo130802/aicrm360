<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'user_id',
        'scheduled_at',
        'status',
        'note',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending'   => 'Đã lên lịch',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã huỷ',
            default     => 'Không xác định',
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'pending'   => 'badge-waiting',
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
            default     => 'badge-secondary',
        };
    }
}
