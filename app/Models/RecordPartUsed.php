<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordPartUsed extends Model
{
    use HasFactory;

    protected $table = 'record_parts_used';

    protected $fillable = [
        'maintenance_record_id',
        'inventory_id',
        'quantity_used',
        'price_at_use',
    ];

    public function maintenanceRecord()
    {
        return $this->belongsTo(MaintenanceRecord::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
