<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    public static function valueOf(string $key, mixed $default = null): mixed
    {
        return static::whereNull('masjid_id')->where('key', $key)->value('value') ?? $default;
    }
}
