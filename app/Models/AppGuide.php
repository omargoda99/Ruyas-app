<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppGuide extends Model
{
    use HasFactory ,Uuid;

      // Define the table associated with the model (optional, Laravel assumes plural of model name)
      protected $table = 'app_guides';

      // Specify the columns that can be mass-assigned
      protected $fillable = [
          'uuid',
          'view_title',
          'description',
          'order',
          'image_path'
      ];
}
