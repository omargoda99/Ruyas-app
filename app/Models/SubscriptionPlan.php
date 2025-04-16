<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'price', 'features', 'is_active'];

      // Relationship with users via the pivot table
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_subscription_coupon')
                    ->withPivot('used_at', 'starts_at', 'ends_at', 'is_active', 'coupon_id');
    }

     // Define a polymorphic relationship with AdminAction
     public function adminActions()
     {
         return $this->morphMany(AdminAction::class, 'target');
     }
}
