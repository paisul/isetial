<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JerseyOrderItem extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(JerseyProduct::class, 'jersey_product_id');
    }

    public function size()
    {
        return $this->belongsTo(JerseySize::class, 'jersey_size_id');
    }
}
