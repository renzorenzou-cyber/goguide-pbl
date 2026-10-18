<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    protected $fillable = ['user_id','specialty','bio','languages','photo','rating','review_count'];

    public function user()  { return $this->belongsTo(User::class); }
    public function tours() { return $this->hasMany(Tour::class); }
}
