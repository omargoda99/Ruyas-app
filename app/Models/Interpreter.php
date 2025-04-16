<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interpreter extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'password', 'name', 'age', 'gender', 'ip_address', 'country', 'region', 'city', 'postal_code', 'status'];

     // Relationship with certifications (one-to-many)
     public function certifications()
     {
         return $this->hasMany(Certification::class, 'interpreter_id');
     }
}
