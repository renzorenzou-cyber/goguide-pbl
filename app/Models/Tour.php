<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable = ['region_id','guide_id','title','slug','description','price','duration','image','rating','review_count'];

    public function region() { return $this->belongsTo(Region::class); }
    public function guide()  { return $this->belongsTo(Guide::class); }
    public function getImageUrlAttribute(): ?string
{
    return $this->image && file_exists(public_path($this->image))
        ? asset($this->image)
        : null;
}
}
