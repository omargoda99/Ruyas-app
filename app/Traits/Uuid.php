<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Uuid
{
    protected static function bootUuid()
    {
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // If you're using a non-incrementing UUID primary key
    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
