<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'valid_from',
        'valid_until',
        'usage_limit',
        'times_used'
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime'
    ];

    public function subscriptionUsages()
    {
        return $this->hasMany(UserSubscriptionCoupon::class);
    }
}
