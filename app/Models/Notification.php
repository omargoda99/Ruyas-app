<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'link',
        'link_type',
        'timestamp',
        'read_at',  // Add read_at to fillable to allow mass assignment
    ];
}
