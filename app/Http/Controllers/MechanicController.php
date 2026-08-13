<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Branch;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\RecordPartUsed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MechanicController extends Controller
{
    /**
     * Mechanic check-in to a branch
     */
    public function mechanicCheckIn(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id'
        ]);

        $user = Auth::user();
        if ($user->role !== 'mechanic') {
            return response()->json(['error' => 'Access denied. Insufficient permissions.'], 403);
        }

        $user->branch_id = $request->branch_id;
        $user->save();

        // Update session if needed (Laravel handles session based auth, but we can set it if we want)
        session(['branch_id' => $request->branch_id]);

        return response()->json([
            'message' => 'Checked into branch successfully.',
            'branch_id' => $request->branch_id
        ]);
    }

    /**
     * Get jobs for the logged in mechanic
     */
    public function getMechanicJobs(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'mechanic') {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $jobs = MaintenanceRecord::with('vehicle')
            ->where('mechanic_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($jobs);
    }

    /**
     * Get the next job ID
     */
    public function getNextJobId(Request $request)
    {
        $user = auth()->user();
        $branchId = $request->query('branch_id') ?? ($user ? $user->branch_id : null);
        
        $abbr = 'JOB';
        if ($branchId) {
            $branch = \App\Models\Branch::find($branchId);
            if ($branch && !empty($branch->abbreviation)) {
                $abbr = strtoupper($branch->abbreviation);
            }
        }

        // Count existing jobs for this branch to get branch job increment
        $branchJobCount = 1;
        if ($branchId) {
            $branchJobCount = MaintenanceRecord::where('branch_id', $branchId)->count() + 1;
        } else {
            $branchJobCount = (MaintenanceRecord::max('id') ?? 0) + 1;
        }

        $formatted = sprintf("%s-%s-%04d", $abbr, date('dmY'), $branchJobCount);

        return response()->json([
            'next_id' => $branchJobCount,
            'formatted' => $formatted
        ]);
    }

    /**
     * Lookup customer by ID card or license plate
     */
    public function lookupCustomerByIdCard(Request $request)
    {
        $query = $request->input('query');
        if (empty($query)) {
            return response()->json(['error' => 'Search parameter is required.'], 400);
        }

        $user = auth()->user();
        $custQuery = Customer::query();

        if ($user && $user->branch_id && !in_array(strtolower($user->role), ['super_admin', 'superadmin'])) {
            $custQuery->where(function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id)
                  ->orWhereNull('branch_id');
            });
        }

        $customers = $custQuery->where(function ($sub) use ($query) {
                $sub->where('id', $query)
                    ->orWhere('id_card_number', $query)
                    ->orWhereHas('vehicles', function ($q) use ($query) {
                        $q->where('license_plate', $query);
                    });
            })
            ->with('vehicles')
            ->get();

        if ($customers->isEmpty()) {
            return response()->json(['error' => 'Customer, ID Card, or Vehicle Plate not found.'], 404);
        }

        // Just returning the first match for simplicity based on previous logic
        $customer = $customers->first();
        
        $customerData = [
            'customer_id' => $customer->id,
            'name' => $customer->name,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'id_card_number' => $customer->id_card_number,
            'customer_status' => $customer->customer_status ?? 'Retail',
            'vehicles' => []
        ];

        foreach ($customer->vehicles as $vehicle) {
            // 1. Cari tanggal perbaikan/servis umum terakhir
            $lastRepair = MaintenanceRecord::where('vehicle_id', $vehicle->id)
                ->where('status', 'completed')
                ->orderByRaw('COALESCE(end_time, created_at) DESC')
                ->first();

            // 2. Query mengambil tanggal perbaikan terakhir PER KATEGORI SPAREPART
            $categoryRepairs = DB::table('maintenance_records as mr')
                ->join('record_parts_used as rpu', 'mr.id', '=', 'rpu.maintenance_record_id')
                ->join('inventory as inv', 'rpu.inventory_id', '=', 'inv.id')
                ->where('mr.vehicle_id', $vehicle->id)
                ->where('mr.status', 'completed')
                ->select(
                    DB::raw('LOWER(TRIM(inv.category)) as category_name'),
                    DB::raw('MAX(COALESCE(mr.end_time, mr.created_at)) as last_used_time')
                )
                ->groupBy(DB::raw('LOWER(TRIM(inv.category))'))
                ->pluck('last_used_time', 'category_name')
                ->toArray();

            $categoryMap = [];
            foreach ($categoryRepairs as $catName => $lastTime) {
                $cleanKey = strtolower(trim($catName));
                $categoryMap[$cleanKey] = $lastTime;
            }

            $findCatTime = function(array $keywords) use ($categoryMap) {
                foreach ($keywords as $kw) {
                    foreach ($categoryMap as $catKey => $time) {
                        if (str_contains($catKey, strtolower($kw))) {
                            return $time;
                        }
                    }
                }
                return null;
            };

            $customerData['vehicles'][] = [
                'vehicle_id' => $vehicle->id,
                'license_plate' => $vehicle->license_plate,
                'vehicle_type' => $vehicle->vehicle_type,
                'frame_number' => $vehicle->vin,
                'controller_number' => $vehicle->controller_number,
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_phone' => $customer->phone,
                'customer_idcard' => $customer->id_card_number,
                'last_repair_time' => $lastRepair ? ($lastRepair->end_time ?? $lastRepair->created_at) : null,
                'category_repairs' => [
                    'tire'    => $findCatTime(['tire', 'ban', 'roda', 'wheel']),
                    'shock'   => $findCatTime(['shock', 'breaker', 'suspension', 'suspensi', 'peredam', 'absorber']),
                    'bearing' => $findCatTime(['bearing', 'laher', 'lahar', 'bushing', 'as ']),
                    'brake'   => $findCatTime(['brake', 'rem', 'kampas', 'pad', 'disc', 'tromol', 'lining']),
                    'all'     => $categoryMap
                ]
            ];
        }

        return response()->json($customerData);
    }
}
