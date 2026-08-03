<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'vehicle_id',
        'branch_id',
        'mechanic_id',
        'repair_category',
        'description',
        'km_reached',
        'common_issues',
        'other_issues',
        'service_sku',
        'service_name',
        'labor_fee',
        'other_expenses_category',
        'other_expenses_fee',
        'repair_date',
        'check_in_time',
        'check_out_time',
        'notes',
        'payment_method',
        'parts_labor_paid',
        'grand_total',
        'photo_path',
        'status',
        'start_time',
        'end_time',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function partsUsed()
    {
        return $this->hasMany(RecordPartUsed::class);
    }
}
