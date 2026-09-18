<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JerseySize extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(JerseyProduct::class, 'jersey_product_id');
    }
}
