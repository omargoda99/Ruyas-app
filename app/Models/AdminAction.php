<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAction extends Model
{
    use HasFactory , Uuid;

    protected $fillable = [
        'uuid','admin_id', 'action_type', 'target_id', 'target_type', 'details', 'performed_at'
    ];

     // Relationship with the admin (who performed the action)
     public function admin()
     {
         return $this->belongsTo(Admin::class, 'admin_id');
     }

      // Polymorphic relationship with the target (could be user, dream, chat, subscription)
    public function target()
    {
        return $this->morphTo(); // This will resolve the target type dynamically
    }

}
