<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $fillable = [
        'user_id', 
        'total_price',
    ];

    public function details() {
        return $this->hasMany(CartDetail::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, CartDetail::class);
    }

    public function user()
{
    return $this->belongsTo(User::class);
}
}
