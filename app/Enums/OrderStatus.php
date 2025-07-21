<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Chờ xử lý',
            self::Processing => 'Đang xử lý',
            self::Completed => 'Hoàn tất',
            self::Cancelled => 'Đã hủy',
        };
    }

    public function class(): string
    {
        return match($this) {
            self::Pending => 'badge bg-warning text-dark',
            self::Processing => 'badge bg-info',
            self::Completed => 'badge bg-success',
            self::Cancelled => 'badge bg-danger',
        };
    }
}
