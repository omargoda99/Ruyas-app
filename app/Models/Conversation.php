<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory , Uuid;
    protected $fillable = [
        'uuid',
        'user_id',
        'interpreter_id'
    ];
    // Conversation.php
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function interpreter()
    {
        return $this->belongsTo(Interpreter::class, 'interpreter_id');
    }
   // Conversation.php
    public function messages()
    {
        return $this->hasMany(ChMessage::class, 'conversation_id', 'uuid');
    }

}
