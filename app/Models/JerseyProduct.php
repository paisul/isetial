<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class JerseyProduct extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function sizes()
    {
        return $this->hasMany(JerseySize::class);
    }

    public function imageUrl(): string
    {
        if (! $this->image_path) return asset('images/jersey/model-pria-pendek-v2.jpg');

        return str_starts_with($this->image_path, 'images/') ? asset($this->image_path) : Storage::url($this->image_path);
    }
}
