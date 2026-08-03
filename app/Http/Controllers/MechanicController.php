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
        $nextId = MaintenanceRecord::max('id') ?? 0;
        $nextId += 1;
        
        return response()->json([
            'next_id' => $nextId,
            'formatted' => sprintf("JOB-%s-%04d", date('Ymd'), $nextId)
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

        $customers = Customer::where('id', $query)
            ->orWhere('id_card_number', $query)
            ->orWhereHas('vehicles', function($q) use ($query) {
                $q->where('license_plate', $query);
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
            // Find last completed repair
            $lastRepair = MaintenanceRecord::where('vehicle_id', $vehicle->id)
                ->where('status', 'completed')
                ->orderByRaw('COALESCE(end_time, created_at) DESC')
                ->first();

            $customerData['vehicles'][] = [
                'vehicle_id' => $vehicle->id,
                'license_plate' => $vehicle->license_plate,
                'vehicle_type' => $vehicle->vehicle_type,
                'frame_number' => $vehicle->vin, // mapped to frame_number in DB?
                'controller_number' => $vehicle->controller_number,
                'last_repair_time' => $lastRepair ? ($lastRepair->end_time ?? $lastRepair->created_at) : null,
                'category_repairs' => [] // Stubbed for now to save complexity
            ];
        }

        return response()->json($customerData);
    }
}
