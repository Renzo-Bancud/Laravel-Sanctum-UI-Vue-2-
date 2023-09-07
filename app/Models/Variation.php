<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function options()
    {
        return $this->hasMany(Variation_option::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('variation_option_id');
    }
}