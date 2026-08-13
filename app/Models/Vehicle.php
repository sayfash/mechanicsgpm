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
        'branch_id',
        'make',
        'model',
        'vehicle_type',
        'color',
        'year',
        'activate_date',
        'license_plate',
        'vin',
        'engine_number',
        'controller_number',
    ];

    protected static function booted()
    {
        static::saving(function ($vehicle) {
            if (!empty($vehicle->customer_id)) {
                // Rule: 1 person can only be bound to 1 vehicle max.
                // Unbind any other vehicles previously bound to this customer.
                static::where('customer_id', $vehicle->customer_id)
                    ->where('id', '!=', $vehicle->id)
                    ->update(['customer_id' => null]);
            }
        });
    }

    public static function enforceSingleCustomerBinding()
    {
        $duplicateCustomerIds = static::whereNotNull('customer_id')
            ->select('customer_id', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('customer_id')
            ->having('count', '>', 1)
            ->pluck('customer_id');

        $unboundCount = 0;
        foreach ($duplicateCustomerIds as $custId) {
            $vehicles = static::where('customer_id', $custId)
                ->orderBy('updated_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            if ($vehicles->count() > 1) {
                $olderIds = $vehicles->slice(1)->pluck('id');
                $unboundCount += static::whereIn('id', $olderIds)->update(['customer_id' => null]);
            }
        }
        return $unboundCount;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getWarrantyStatusAttribute(): string
    {
        return static::determineWarrantyCategory($this->activate_date ?? $this->created_at, $this->maintenanceRecords()->max('km_reached') ?? 0);
    }

    public static function determineWarrantyCategory($activateDate, int $currentKm): string
    {
        if (!$activateDate) {
            return 'Unclaimable / No Warranty';
        }
        $boughtDate = \Carbon\Carbon::parse($activateDate);
        $now = \Carbon\Carbon::now();
        $diffMonths = $boughtDate->diffInMonths($now);

        // 1. Warranty A: under 6 months old or Odometer <= 10,000 km
        if ($diffMonths <= 6 || $currentKm <= 10000) {
            return 'Warranty A';
        }
        // 2. Warranty B: under 1 year old from activate_date or Odometer <= 10,000 km
        if ($diffMonths <= 12 || $currentKm <= 10000) {
            return 'Warranty B';
        }
        // 3. Warranty C: under 2 years old from activate_date or Odometer <= 20,000 km
        if ($diffMonths <= 24 || $currentKm <= 20000) {
            return 'Warranty C';
        }
        // 4. Unclaimable / No Warranty
        return 'Unclaimable / No Warranty';
    }

    public function isWarrantyClaimableForPart(string $partWarrantyCategory, ?int $currentKm = null): bool
    {
        $vehicleCategory = static::determineWarrantyCategory($this->activate_date ?? $this->created_at, $currentKm ?? ($this->maintenanceRecords()->max('km_reached') ?? 0));
        return static::isCategoryEligible($vehicleCategory, $partWarrantyCategory);
    }

    public static function isCategoryEligible(string $vehicleCategory, string $partWarrantyCategory): bool
    {
        $order = [
            'Warranty A' => 1,
            'Warranty B' => 2,
            'Warranty C' => 3,
            'Unclaimable / No Warranty' => 4,
        ];
        
        $vLevel = $order[$vehicleCategory] ?? 4;
        $pLevel = $order[$partWarrantyCategory] ?? 4;

        if ($pLevel === 4) return false;
        return $vLevel <= $pLevel;
    }
}

