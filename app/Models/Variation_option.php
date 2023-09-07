<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation_option extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }
}
