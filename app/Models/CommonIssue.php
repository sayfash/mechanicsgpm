<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonIssue extends Model
{
    use HasFactory;
    
    // Disable timestamps since they might not exist in the simple table
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
