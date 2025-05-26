<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SubscriptionPlan extends Model
{
    use HasFactory ,Uuid ;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'price',
        'features',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'float',
    ];

    /**
     * Users subscribed to this plan via pivot table.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_subscription_coupon')
            ->withPivot([
                'starts_at',
                'ends_at',
                'is_active',
                'coupon_id',
                'purchased_at',
            ])
            ->withTimestamps();
    }

    /**
     * Coupons used with this plan via the pivot.
     */
    public function coupons(): HasManyThrough
    {
        return $this->hasManyThrough(
            Coupon::class,
            UserSubscriptionCoupon::class,
            'plan_id',   // Foreign key on UserSubscriptionCoupon
            'id',        // Foreign key on Coupon
            'id',        // Local key on SubscriptionPlan
            'coupon_id'  // Local key on UserSubscriptionCoupon
        );
    }

    /**
     * Admin actions related to this subscription plan.
     */
    public function adminActions(): MorphMany
    {
        return $this->morphMany(AdminAction::class, 'target');
    }
}
