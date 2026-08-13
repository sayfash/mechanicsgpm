<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\MaintenanceRecord;
use App\Models\RecordPartUsed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Get aggregated report summary for all branches or a specific branch.
     */
    public function getSummary(Request $request)
    {
        $branchId = $request->query('branch_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $period = $request->query('period', 'monthly');

        // Fetch records for date range (without branch filter for comparison tables)
        $dateFilteredQuery = MaintenanceRecord::with(['branch', 'parts.inventory', 'mechanic', 'vehicle.customer']);
        if (!empty($startDate)) {
            $dateFilteredQuery->whereDate('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if (!empty($endDate)) {
            $dateFilteredQuery->whereDate('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }
        $allBranchRecords = $dateFilteredQuery->get();

        // 1. Core Summary Counters (Scoped to selected branch if specified)
        $records = (!empty($branchId) && $branchId !== 'all')
            ? $allBranchRecords->where('branch_id', $branchId)
            : $allBranchRecords;

        $totalJobs = $records->count();
        $completedJobs = $records->where('status', 'COMPLETED')->count();
        $inProgressJobs = $records->where('status', 'IN_PROGRESS')->count();
        $cancelledJobs = $records->where('status', 'CANCELLED')->count();

        $totalLaborFee = $records->sum('labor_fee');
        $totalPartsBilled = $records->sum(function ($r) {
            return $r->parts->sum(function ($p) {
                return floatval($p->quantity_used) * floatval($p->price_at_use);
            });
        });
        $grandTotalRevenue = $totalLaborFee + $totalPartsBilled;
        $totalPartsQtyUsed = $records->sum(function ($r) {
            return $r->parts->sum('quantity_used');
        });

        // 2. Branch Breakdown Performance (Always contains all branches data)
        $branches = Branch::all();
        $branchSummary = $branches->map(function ($b) use ($allBranchRecords) {
            $bRecords = $allBranchRecords->where('branch_id', $b->id);
            $laborFee = $bRecords->sum('labor_fee');
            $partsBilled = $bRecords->sum(function ($r) {
                return $r->parts->sum(function ($p) {
                    return floatval($p->quantity_used) * floatval($p->price_at_use);
                });
            });
            return [
                'branch_id' => $b->id,
                'branch_name' => $b->name,
                'abbreviation' => $b->abbreviation ?: strtoupper(substr($b->name, 0, 3)),
                'total_jobs' => $bRecords->count(),
                'completed_jobs' => $bRecords->where('status', 'COMPLETED')->count(),
                'in_progress_jobs' => $bRecords->where('status', 'IN_PROGRESS')->count(),
                'labor_fee' => $laborFee,
                'parts_billed' => $partsBilled,
                'total_revenue' => $laborFee + $partsBilled,
                'parts_qty_used' => $bRecords->sum(function ($r) {
                    return $r->parts->sum('quantity_used');
                }),
            ];
        })->values();

        // 3. Top Most Billed Parts
        $topPartsMap = [];
        foreach ($records as $r) {
            foreach ($r->parts as $p) {
                $sku = $p->inventory->sku ?? 'N/A';
                $name = $p->inventory->part_name ?? $sku;
                $key = $sku . '_' . $name;
                if (!isset($topPartsMap[$key])) {
                    $topPartsMap[$key] = [
                        'sku' => $sku,
                        'part_name' => $name,
                        'total_qty' => 0,
                        'total_spent' => 0,
                    ];
                }
                $topPartsMap[$key]['total_qty'] += intval($p->quantity_used);
                $topPartsMap[$key]['total_spent'] += (intval($p->quantity_used) * floatval($p->price_at_use));
            }
        }

        $topParts = collect(array_values($topPartsMap))
            ->sortByDesc('total_spent')
            ->take(10)
            ->values();

        // 4. Vehicle Fleet & Rental Status Breakdown per Branch and Model
        $allVehicles = \App\Models\Vehicle::with(['customer', 'branch', 'maintenanceRecords'])->get();
        $inProgressVehicleIds = MaintenanceRecord::where('status', 'IN_PROGRESS')
            ->pluck('vehicle_id')
            ->filter()
            ->unique()
            ->toArray();

        $vehicleModels = $allVehicles->map(function ($v) {
            $m = trim($v->model);
            if (!$m || strtolower($m) === 'ev') {
                $m = trim($v->make) ?: (trim($v->vehicle_type) ?: 'Generic EV');
            }
            return $m;
        })->unique()->values()->filter()->toArray();

        if (empty($vehicleModels)) {
            $vehicleModels = ['Generic EV'];
        }

        $vehicleBreakdown = $branches->map(function ($b) use ($allVehicles, $inProgressVehicleIds, $vehicleModels) {
            // Determine vehicle's branch either directly via branch_id or from its maintenance records
            $bVehicles = $allVehicles->filter(function ($v) use ($b) {
                if (!empty($v->branch_id)) {
                    return $v->branch_id == $b->id;
                }
                // Fallback: check if latest maintenance record belongs to this branch
                $latestRecord = $v->maintenanceRecords->sortByDesc('created_at')->first();
                if ($latestRecord && $latestRecord->branch_id == $b->id) {
                    return true;
                }
                // Default fallback to branch ID 1 if unspecified
                return $b->id == 1;
            });

            $modelsMap = [];

            foreach ($vehicleModels as $m) {
                $modelsMap[$m] = [
                    'model' => $m,
                    'rented' => 0,
                    'ready_to_rent' => 0,
                    'under_repair' => 0,
                    'total_warehouse' => 0,
                    'total_fleet' => 0,
                ];
            }

            foreach ($bVehicles as $v) {
                $vModel = trim($v->model);
                if (!$vModel || strtolower($vModel) === 'ev') {
                    $vModel = trim($v->make) ?: (trim($v->vehicle_type) ?: 'Generic EV');
                }

                // Match with closest model key
                $matchedKey = null;
                foreach ($vehicleModels as $mKey) {
                    if (strtolower($mKey) === strtolower($vModel)) {
                        $matchedKey = $mKey;
                        break;
                    }
                }
                if (!$matchedKey) {
                    $matchedKey = $vehicleModels[0];
                }

                $isRented = !empty($v->customer_id);
                $isUnderRepair = in_array($v->id, $inProgressVehicleIds);

                if ($isRented) {
                    $modelsMap[$matchedKey]['rented']++;
                } else if ($isUnderRepair) {
                    $modelsMap[$matchedKey]['under_repair']++;
                } else {
                    $modelsMap[$matchedKey]['ready_to_rent']++;
                }
            }

            $modelsData = [];
            foreach ($modelsMap as $mKey => $mVal) {
                $mVal['total_warehouse'] = $mVal['ready_to_rent'] + $mVal['under_repair'];
                $mVal['total_fleet'] = $mVal['rented'] + $mVal['total_warehouse'];
                $modelsData[] = $mVal;
            }

            return [
                'branch_id' => $b->id,
                'branch_name' => $b->name,
                'abbreviation' => $b->abbreviation ?: strtoupper(substr($b->name, 0, 3)),
                'models' => $modelsData,
                'totals' => [
                    'rented' => array_sum(array_column($modelsData, 'rented')),
                    'ready_to_rent' => array_sum(array_column($modelsData, 'ready_to_rent')),
                    'under_repair' => array_sum(array_column($modelsData, 'under_repair')),
                    'total_warehouse' => array_sum(array_column($modelsData, 'total_warehouse')),
                    'total_fleet' => array_sum(array_column($modelsData, 'total_fleet')),
                ]
            ];
        })->values();

        return response()->json([
            'summary' => [
                'total_jobs' => $totalJobs,
                'completed_jobs' => $completedJobs,
                'in_progress_jobs' => $inProgressJobs,
                'cancelled_jobs' => $cancelledJobs,
                'total_labor_fee' => $totalLaborFee,
                'total_parts_billed' => $totalPartsBilled,
                'grand_total_revenue' => $grandTotalRevenue,
                'total_parts_qty_used' => $totalPartsQtyUsed,
            ],
            'branch_summary' => $branchSummary,
            'vehicle_breakdown' => $vehicleBreakdown,
            'vehicle_models' => $vehicleModels,
            'top_parts' => $topParts,
        ]);
    }
}
