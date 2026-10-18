<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name', 'slug'];

    public function tours()
    {
        return $this->hasMany(Tour::class);
    }
}

