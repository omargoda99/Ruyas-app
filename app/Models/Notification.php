<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory,Uuid;

    protected $fillable = [
        'uuid',
        'title',
        'description',
        'link',
        'link_type',
        'timestamp',
        'img_path',
        'read_at',  // Add read_at to fillable to allow mass assignment
    ];
}
