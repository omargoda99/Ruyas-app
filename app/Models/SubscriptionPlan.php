<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'features', 'is_active'];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'float'
    ];

    // Relationship with users via the pivot table
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_subscription_coupon')
            ->withPivot([
                'starts_at',
                'ends_at',
                'is_active',
                'coupon_id',
                'purchased_at'
            ]);
    }

    // Relationship with coupons
    public function coupons()
    {
        return $this->hasManyThrough(
            Coupon::class,
            UserSubscriptionCoupon::class,
            'plan_id',
            'id',
            'id',
            'coupon_id'
        );
    }

    // Define a polymorphic relationship with AdminAction
    public function adminActions()
    {
        return $this->morphMany(AdminAction::class, 'target');
    }
}
