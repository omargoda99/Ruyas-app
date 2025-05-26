<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dream extends Model
{
    use HasFactory ,Uuid;
    protected $fillable = ['uuid','user_id', 'title', 'description', 'is_favorite', 'is_shared', 'is_explained'];

    // Relationship with the user who is the seer of the dream
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'user_dream_favorites')
                    ->withTimestamps();  // To track when users favorited the dream
    }

     // Define a polymorphic relationship with AdminAction
     public function adminActions()
     {
         return $this->morphMany(AdminAction::class, 'target');
     }

     // Define the one-to-one relationship with the Interpretation model
    public function interpretation()
    {
        return $this->hasOne(Interpretation::class);
    }
}
