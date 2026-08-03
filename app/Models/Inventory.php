<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    
    protected $table = 'inventory';

    protected $fillable = [
        'branch_id',
        'sku',
        'part_name',
        'unit',
        'category',
        'connected_service',
        'description',
        'available_qty',
        'price',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
