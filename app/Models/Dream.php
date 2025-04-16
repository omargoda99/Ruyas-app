<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dream extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'title', 'description', 'is_favorite', 'is_shared', 'is_explained'];

    // Relationship with the user who is the seer of the dream
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

     // Define a polymorphic relationship with AdminAction
     public function adminActions()
     {
         return $this->morphMany(AdminAction::class, 'target');
     }
}
