<?php

namespace App\Models;

use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'career',
        'area',
        'source_id',
        'company_name',
        'company_phone',
        'company_tax_code',
        'address',
        'tax_code',
        'facebook_link',
        'youtube_link',
        'instagram_link',

    ];

    // Nếu có quan hệ với bảng `sources`
    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
