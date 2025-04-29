<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    protected $fillable = [
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
    public function messages()
    {
        return $this->hasMany(ChMessage::class);
    }
}
