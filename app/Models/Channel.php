<?php

namespace App\Models;

use App\Models\CustomerCare;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function customerCares()
    {
        return $this->hasMany(CustomerCare::class);
    }
}
