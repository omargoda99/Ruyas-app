<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscriptionCoupon extends Model
{
    use HasFactory ,Uuid;
    protected $table = 'user_subscription_coupon';

    protected $fillable = [
        'uuid',
        'user_id',
        'plan_id',
        'coupon_id',
        'starts_at',
        'ends_at',
        'is_active',
        'purchased_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'purchased_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}
