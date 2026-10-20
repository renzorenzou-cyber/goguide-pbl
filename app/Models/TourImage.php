<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourImage extends Model
{
    protected $fillable = ['tour_id', 'path', 'caption', 'sort_order'];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    // URL publik, null kalau file-nya nggak ada di /public
    public function getUrlAttribute(): ?string
    {
        return $this->path && file_exists(public_path($this->path))
            ? asset($this->path)
            : null;
    }
}