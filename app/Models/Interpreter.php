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
     public function interpretations()
    {
        return $this->hasMany(Interpretation::class);
    }
    public function complains()
    {
        return $this->hasMany(Complain::class);
    }

    public function updateRating()
    {
        $this->rating_avg = $this->feedbacks()->avg('rating') ?? 0;
        $this->save();
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}
