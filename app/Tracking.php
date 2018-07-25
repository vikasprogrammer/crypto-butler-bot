<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    protected $guarded = [];

    public function scopeActive($query) {
        return $query->where('status', 1);
    }

    public function histories() {
        return $this->hasMany(History::class);
    }
}
