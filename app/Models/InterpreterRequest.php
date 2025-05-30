<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterpreterRequest extends Model
{
    use HasFactory,Uuid;

      protected $fillable = [
        'uuid','user_uuid','name','email','phone','age', 'gender', 'years_of_experience',
        'memorized_quran_parts', 'languages', 'nationality',
        'country', 'city', 'status'
    ];

    protected $casts = [
        'languages' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
