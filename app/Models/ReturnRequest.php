<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    use HasFactory;

    // Add these fields to allow mass assignment
    protected $fillable = [
        'order_id',
        'reason',
        'status',
    ];
}
