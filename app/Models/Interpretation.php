<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interpretation extends Model
{
    use HasFactory;
    protected $fillable = [
        'dream_id', 'interpreter_id', 'content', 'is_approved'
    ];

    // Relationship with the dream (the dream that is being interpreted)
    public function dream()
    {
        return $this->belongsTo(Dream::class);
    }
    
    // Relationship with the interpreter (the person who is interpreting the dream)
    public function interpreter()
    {
        return $this->belongsTo(Interpreter::class, 'interpreter_id');
    }
}
