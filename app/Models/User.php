<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
   protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'phone',
    'photo',
];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getPhotoUrlAttribute(): ?string
{
    return $this->photo && file_exists(public_path($this->photo))
        ? asset($this->photo)
        : null;
}

public function getInitialsAttribute(): string
{
    return collect(explode(' ', trim($this->name)))
        ->filter()
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->take(2)
        ->implode('');
}

public function guide()
{
    return $this->hasOne(Guide::class);
}
}
