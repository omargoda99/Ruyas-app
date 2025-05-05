<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'interpreter_id',
        'interpretation_id',
        'dream_id',
        'feedback_text',
        'rating',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function interpreter()
    {
        return $this->belongsTo(Interpreter::class);
    }

    public function interpretation()
    {
        return $this->belongsTo(Interpretation::class);
    }

    public function dream()
    {
        return $this->belongsTo(Dream::class);
    }
}
