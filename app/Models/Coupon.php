<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'is_expired'];
    
     // Relationship with users and subscriptions via the pivot table
     public function userSubscriptions()
     {
         return $this->belongsToMany(User::class, 'user_subscription_coupon')
                     ->withPivot('used_at', 'starts_at', 'ends_at', 'is_active');
     }
}
