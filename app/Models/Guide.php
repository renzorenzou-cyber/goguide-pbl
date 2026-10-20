<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    protected $fillable = [
        'user_id', 'specialty', 'bio', 'languages',
        'experience_years', 'base_location',
        'photo', 'rating', 'review_count',
    ];

    public function user()  { return $this->belongsTo(User::class); }
    public function tours() { return $this->hasMany(Tour::class); }

    // Foto guide; kalau kosong pakai foto akun user. Null kalau dua-duanya nggak ada.
    public function getPhotoUrlAttribute(): ?string
    {
        $path = $this->photo ?: $this->user?->photo;

        return $path && file_exists(public_path($path))
            ? asset($path)
            : null;
    }

    public function getInitialsAttribute(): string
    {
        return $this->user?->initials ?? '';
    }

    // "Indonesia, English" -> ['Indonesia', 'English']
    public function getLanguageListAttribute(): array
    {
        return collect(explode(',', (string) $this->languages))
            ->map(fn ($l) => trim($l))
            ->filter()
            ->values()
            ->all();
    }
    
    public function reviews() { return $this->hasMany(Review::class); }
}