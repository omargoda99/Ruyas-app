<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory , Uuid;

    // Define the table associated with the model (optional, Laravel assumes plural of model name)
    protected $table = 'ads';

    // Specify the columns that can be mass-assigned
    protected $fillable = [
        'uuid',
        'ad_title',
        'ad_description',
        'start_date',
        'end_date',
        'ad_image_path',
        'link',
        'status'
    ];

}
