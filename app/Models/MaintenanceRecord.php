<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RecordPartUsed;
use App\Models\Inventory;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::deleting(function ($record) {
            $oldParts = RecordPartUsed::where('maintenance_record_id', $record->id)->get();
            foreach ($oldParts as $op) {
                $inv = Inventory::find($op->inventory_id);
                if ($inv) {
                    $inv->increment('available_qty', $op->quantity_used);
                }
            }
            RecordPartUsed::where('maintenance_record_id', $record->id)->delete();
        });
    }

    protected $fillable = [
        'job_id',
        'vehicle_id',
        'branch_id',
        'daily_queue_number',
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
    public function parts()
    {
        return $this->hasMany(RecordPartUsed::class);
    }
}

