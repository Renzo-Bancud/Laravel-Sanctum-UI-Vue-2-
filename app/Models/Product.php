<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function inventory(){
        return $this->belongsTo(Inventory::class);
    }

     /**
     * Define a many-to-many relationship with the Variation model.
     */
    public function variations()
    {
        return $this->belongsToMany(Variation::class, 'product_variation')
        ->withPivot('variation_option_id');
    }
}
