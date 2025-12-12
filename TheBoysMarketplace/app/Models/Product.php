<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'image',
        'description',
        'quantity',
        'category_id',
    ];

    public function category() 
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems() 
    {
        return $this->hasMany(CartItem::class);
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && file_exists(public_path($this->image))) {
            return asset($this->image);
        }

        return asset('images/default_product.png');
    }
}
