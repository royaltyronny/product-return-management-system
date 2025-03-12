<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shoe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'size',
        'price',
        'image_url',
    ];

    protected $casts = [
        'price' => 'float',
        'size' => 'integer',
    ];
}
