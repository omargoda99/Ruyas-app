<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;

    protected $fillable = ['interpreter_id', 'name', 'issuing_organization', 'issue_date', 'credential_id', 'credential_url'];

    // Relationship with interpreter (many-to-one)
    public function interpreter()
    {
        return $this->belongsTo(Interpreter::class, 'interpreter_id');
    }
}
