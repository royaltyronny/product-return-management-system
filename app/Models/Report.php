<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    // Specify the table name if it doesn't follow Laravel's plural convention
    protected $table = 'reports';

    // Specify which attributes are mass assignable
    protected $fillable = ['title', 'content'];  // Adjust these based on your actual database schema
}
