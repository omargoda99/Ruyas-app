<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory , Uuid;
    protected $fillable = [
        'uuid', 'name','email', 'password'
    ];
}
