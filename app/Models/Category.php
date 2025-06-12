<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    public $primaryKey = 'id';
    public $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'status',
    ];

    public function product() {
        return $this->hasOne(Product::class);
    }
}
