<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChMessage extends Model
{
    use HasFactory , Uuid;

    protected $table = 'ch_messages';

    // Define the fillable fields
    protected $fillable = [
    'uuid',
    'conversation_id',
    'from_id',
    'to_id',
    'body',
    'old_body',
    'attachment',
    'voice', // ✅ Add this line
    'seen',
    'sender_type',
    'receiver_type',
];

    // Define the polymorphic relationship to sender (User or Interpreter)
    public function sender()
    {
        return $this->morphTo('sender');
    }

    // Define the polymorphic relationship to receiver (User or Interpreter)
    public function receiver()
    {
        return $this->morphTo('receiver');
    }

    // Define relationship to Conversation
    // ChMessage.php
    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'uuid');
    }

}
