<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Tour extends Model
{
    protected $fillable = ['region_id','guide_id','title','slug','description','price','duration','image','rating','review_count'];

    public function region() { return $this->belongsTo(Region::class); }
    public function guide()  { return $this->belongsTo(Guide::class); }

    public function images()
    {
        return $this->hasMany(TourImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image && file_exists(public_path($this->image))
            ? asset($this->image)
            : null;
    }

    // Semua foto buat galeri: foto utama dulu, lalu foto tambahan. File yang hilang dilewati.
    public function getGalleryAttribute(): Collection
    {
        $items = collect();

        if ($this->image_url) {
            $items->push(['url' => $this->image_url, 'caption' => null]);
        }

        foreach ($this->images as $img) {
            if ($img->url) {
                $items->push(['url' => $img->url, 'caption' => $img->caption]);
            }
        }

        return $items->unique('url')->values();
    }
}