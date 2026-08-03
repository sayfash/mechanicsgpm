<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'make',
        'model',
        'vehicle_type',
        'color',
        'year',
        'license_plate',
        'vin',
        'engine_number',
        'controller_number',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }
}
