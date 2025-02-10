<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'shop_id',
        'is_active'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
