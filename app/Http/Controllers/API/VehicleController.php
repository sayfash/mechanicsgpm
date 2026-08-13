<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        Vehicle::enforceSingleCustomerBinding();
        $user = auth()->user();

        $vehQuery = Vehicle::with(['customer', 'branch']);
        $custQuery = \App\Models\Customer::with('vehicles:id,customer_id,license_plate,vehicle_type,make,model', 'branch:id,name')->withCount('vehicles');

        if ($user && $user->branch_id && !in_array(strtolower($user->role), ['super_admin', 'superadmin'])) {
            $vehQuery->where(function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id)
                  ->orWhereNull('branch_id');
            });
            $custQuery->where(function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id)
                  ->orWhereNull('branch_id');
            });
        }

        $vehicles = $vehQuery->orderBy('license_plate', 'asc')->get();
        $customers = $custQuery->orderBy('name', 'asc')->get();

        return response()->json([
            'vehicles' => $vehicles,
            'customers' => $customers,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'nullable|string|max:50',
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate',
            'customer_id' => 'nullable|string|exists:customers,id',
            'branch_id' => 'nullable',
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'vehicle_type' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:30',
            'year' => 'nullable|integer',
            'vin' => 'nullable|string|max:50|unique:vehicles,vin',
            'engine_number' => 'nullable|string|max:50',
            'controller_number' => 'nullable|string|max:50',
            'activate_date' => 'nullable|date',
        ]);

        $customId = $request->vehicle_id;
        
        if ($customId) {
            $existing = Vehicle::find($customId);
            if ($existing) {
                return response()->json(['error' => 'Vehicle ID already exists.'], 400);
            }
        }

        $id = $customId ?: 'VEH-' . strtoupper(substr(md5(uniqid(microtime(true))), 0, 8));

        $rawBranch = $request->input('branch_id');
        $resolvedBranchId = null;
        if (!empty($rawBranch) && $rawBranch !== 'global' && $rawBranch !== 'null') {
            $resolvedBranchId = intval($rawBranch);
        } else if ($request->has('branch_name') && !empty($request->branch_name) && strtolower($request->branch_name) !== 'global') {
            $b = \App\Models\Branch::where('name', 'like', "%{$request->branch_name}%")
                ->orWhere('abbreviation', 'like', "%{$request->branch_name}%")
                ->first();
            if ($b) $resolvedBranchId = $b->id;
        }

        DB::beginTransaction();
        try {
            $vehicle = Vehicle::create([
                'id' => $id,
                'customer_id' => $request->customer_id ?: null,
                'branch_id' => $resolvedBranchId,
                'make' => $request->make ?: 'Generic',
                'model' => $request->model ?: 'EV',
                'vehicle_type' => $request->vehicle_type,
                'color' => $request->color,
                'year' => $request->year ?? date('Y'),
                'license_plate' => $request->license_plate,
                'vin' => $request->vin,
                'engine_number' => $request->engine_number,
                'controller_number' => $request->controller_number,
                'activate_date' => $request->activate_date ?: null,
            ]);

            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'CREATE',
                'target_table' => 'vehicles',
                'record_id' => $vehicle->id,
                'new_value' => $vehicle->toArray(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Vehicle created successfully.', 'vehicle_id' => $vehicle->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create vehicle: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|string|exists:vehicles,id',
            'customer_id' => 'nullable|string|exists:customers,id',
            'branch_id' => 'nullable',
            'make' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'vehicle_type' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:30',
            'year' => 'nullable|integer',
            'license_plate' => 'required|string|max:20',
            'vin' => 'nullable|string|max:50',
            'engine_number' => 'nullable|string|max:50',
            'controller_number' => 'nullable|string|max:50',
            'activate_date' => 'nullable|date',
        ]);

        $vehicle = Vehicle::find($request->vehicle_id);

        if ($vehicle->license_plate !== $request->license_plate) {
            $exists = Vehicle::where('license_plate', $request->license_plate)->first();
            if ($exists) {
                return response()->json(['error' => 'License plate already exists on another vehicle.'], 400);
            }
        }

        $oldValue = $vehicle->toArray();

        DB::beginTransaction();
        try {
            $dataToUpdate = [
                'customer_id' => $request->customer_id,
                'make' => $request->make,
                'model' => $request->model,
                'vehicle_type' => $request->vehicle_type,
                'color' => $request->color,
                'year' => $request->year ?? date('Y'),
                'license_plate' => $request->license_plate,
                'vin' => $request->vin,
                'engine_number' => $request->engine_number,
                'controller_number' => $request->controller_number,
                'activate_date' => $request->activate_date ?: null,
            ];
            if ($request->has('branch_id')) {
                $rawBranch = $request->input('branch_id');
                $dataToUpdate['branch_id'] = (!empty($rawBranch) && $rawBranch !== 'global' && $rawBranch !== 'null') ? intval($rawBranch) : null;
            } else if ($request->has('branch_name')) {
                $bName = $request->input('branch_name');
                if (empty($bName) || strtolower($bName) === 'global') {
                    $dataToUpdate['branch_id'] = null;
                } else {
                    $b = \App\Models\Branch::where('name', 'like', "%{$bName}%")
                        ->orWhere('abbreviation', 'like', "%{$bName}%")
                        ->first();
                    if ($b) $dataToUpdate['branch_id'] = $b->id;
                }
            }
            $vehicle->update($dataToUpdate);

            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'UPDATE',
                'target_table' => 'vehicles',
                'record_id' => $vehicle->id,
                'old_value' => $oldValue,
                'new_value' => $vehicle->toArray(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Vehicle updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update vehicle: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|string|exists:vehicles,id'
        ]);

        $vehicle = Vehicle::find($request->vehicle_id);
        $oldValue = $vehicle->toArray();

        DB::beginTransaction();
        try {
            $vehicle->delete();

            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'DELETE',
                'target_table' => 'vehicles',
                'record_id' => $vehicle->id,
                'old_value' => $oldValue,
            ]);

            DB::commit();
            return response()->json(['message' => 'Vehicle deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete vehicle.'], 500);
        }
    }

    public function importVehiclesBatch(Request $request)
    {
        $rows = $request->input('rows', []);
        if (empty($rows)) {
            return response()->json(['error' => 'No rows provided.'], 400);
        }

        $inserted = 0;
        $updated = 0;

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $vehId = trim($row['Vehicle ID'] ?? $row['Veh ID'] ?? $row['ID'] ?? '');
                $plate = trim($row['License Plate'] ?? '');
                $frame = trim($row['Frame Number / VIN'] ?? '');
                $controller = trim($row['Controller Number'] ?? '');
                $custId = trim($row['Customer ID'] ?? $row['Cust ID'] ?? '');
                $custIdCard = trim($row['ID Card Number'] ?? $row['Customer ID Card'] ?? $row['KTP'] ?? '');
                $custName = trim($row['Customer Name'] ?? '');

                if (!$vehId && !$plate && !$frame && !$controller) continue;

                // Lookup customer if ID, ID Card (KTP), or Name provided
                $customerId = null;
                if ($custId) {
                    $c = \App\Models\Customer::find($custId);
                    if ($c) $customerId = $c->id;
                }
                if (!$customerId && $custIdCard) {
                    $c = \App\Models\Customer::where('id_card_number', $custIdCard)->first();
                    if ($c) $customerId = $c->id;
                }
                if (!$customerId && $custName) {
                    $c = \App\Models\Customer::where('name', 'like', "%{$custName}%")->first();
                    if ($c) $customerId = $c->id;
                }

                $vehicle = null;
                if ($vehId) {
                    $vehicle = Vehicle::find($vehId);
                }
                if (!$vehicle && $plate) {
                    $vehicle = Vehicle::where('license_plate', $plate)->first();
                }
                if (!$vehicle && $frame) {
                    $vehicle = Vehicle::where('vin', $frame)->first();
                }

                $branchName = trim($row['Branch'] ?? $row['Branch Name'] ?? $row['Branch Abbreviation'] ?? '');
                $branchId = null;
                if ($branchName) {
                    $b = \App\Models\Branch::where('name', 'like', "%{$branchName}%")
                        ->orWhere('abbreviation', 'like', "%{$branchName}%")
                        ->first();
                    if ($b) $branchId = $b->id;
                }

                $data = [
                    'customer_id' => $customerId ?: ($vehicle ? $vehicle->customer_id : null),
                    'branch_id' => $branchId ?: ($vehicle ? $vehicle->branch_id : (auth()->user()->branch_id ?? 1)),
                    'license_plate' => $plate ?: ($vehicle ? $vehicle->license_plate : 'TEMP-' . strtoupper(substr(md5(uniqid()), 0, 6))),
                    'vehicle_type' => $row['Vehicle Type'] ?? 'Motorcycle',
                    'make' => $row['Make'] ?? 'Generic',
                    'model' => $row['Model'] ?? 'Standard',
                    'color' => $row['Color'] ?? null,
                    'year' => $row['Year'] ?? date('Y'),
                    'vin' => $frame ?: null,
                    'engine_number' => $row['Engine Number'] ?? null,
                    'controller_number' => $controller ?: null,
                ];

                if ($vehicle) {
                    $vehicle->update($data);
                    $updated++;
                } else {
                    $data['id'] = $vehId ?: ('VEH-' . strtoupper(substr(md5(uniqid(microtime(true))), 0, 8)));
                    Vehicle::create($data);
                    $inserted++;
                }
            }

            DB::commit();
            return response()->json([
                'message' => "Vehicles batch import complete! {$inserted} created, {$updated} updated."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Import vehicles batch failed: ' . $e->getMessage()], 500);
        }
    }

    public function bindCustomer(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|string|exists:vehicles,id',
            'customer_id' => 'required|string|exists:customers,id',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $oldValue = $vehicle->toArray();

        $vehicle->update(['customer_id' => $request->customer_id]);

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'action_type' => 'UPDATE',
            'target_table' => 'vehicles',
            'record_id' => $vehicle->id,
            'old_value' => $oldValue,
            'new_value' => $vehicle->toArray(),
        ]);

        return response()->json(['message' => 'Vehicle successfully bound to customer.']);
    }

    public function rebindVehicles(Request $request)
    {
        $unboundCount = Vehicle::enforceSingleCustomerBinding();
        return response()->json([
            'message' => "Existing vehicle bindings successfully cleaned up! {$unboundCount} older vehicle(s) unbound to ensure 1 person = max 1 vehicle.",
            'unbound_count' => $unboundCount
        ]);
    }
}
