<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interpreter extends Model
{
    use HasFactory;

    // Fillable attributes
    protected $fillable = [
        'email', 'password', 'name', 'age', 'gender', 'country', 'city',
        'status', 'languages', 'years_of_experience', 'memorized_quran_parts',
        'nationality', 'certifications_id', 'interpretations_id'
    ];

    // Cast the 'languages' attribute to an array for easy access
    protected $casts = [
        'languages' => 'array', // Automatically cast JSON to array
    ];

        // In Interpreter model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship with certifications (one-to-many)
     */
    public function certifications()
    {
        return $this->hasMany(Certification::class, 'interpreter_id');
    }

    /**
     * Relationship with interpretations (one-to-many)
     */
    public function interpretations()
    {
        return $this->hasMany(Interpretation::class);
    }

    /**
     * Relationship with complains (one-to-many)
     */
    public function complains()
    {
        return $this->hasMany(Complain::class);
    }

    /**
     * Relationship with feedbacks (one-to-many)
     */
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Calculate and update the average rating of the interpreter.
     */
    public function updateRating()
    {
        $this->rating_avg = $this->feedbacks()->avg('rating') ?? 0;
        $this->save();
    }

    /**
     * Add languages to the 'languages' attribute.
     */
    public function addLanguages(array $languages)
    {
        // Merge new languages with existing ones and remove duplicates
        $this->languages = array_unique(array_merge($this->languages ?? [], $languages));
        $this->save();
    }
     // Define the relationship to messages sent by the interpreter (as the sender)
     public function sentMessages()
     {
         return $this->hasMany(ChMessage::class, 'from_id');
     }

     // Define the relationship to messages received by the interpreter (as the receiver)
     public function receivedMessages()
     {
         return $this->hasMany(ChMessage::class, 'to_id');
     }
      public function conversations()
     {
         return $this->hasMany(Conversation::class, 'interpreter_id');
     }
}
