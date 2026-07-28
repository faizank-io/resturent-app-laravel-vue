<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'is_available',
        'prep_time_minutes',
    ];

    protected $casts = [
        'price' => 'float',
        'is_available' => 'boolean',
        'prep_time_minutes' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
