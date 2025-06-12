<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'category_id',
        'price',
        'discount_price',
        'quantity',
        'status',

    ];

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

   
}
