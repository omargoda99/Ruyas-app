<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscriptionCoupon extends Model
{
    use HasFactory;
    protected $table = 'user_subscription_coupon';

    protected $fillable = ['user_id', 'plan_id', 'coupon_id', 'starts_at', 'ends_at', 'is_active', 'used_at', 'purchased_at'];
}
