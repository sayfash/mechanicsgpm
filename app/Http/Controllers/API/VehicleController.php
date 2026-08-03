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
        $vehicles = Vehicle::with('customer')->orderBy('license_plate', 'asc')->get();
        $customers = \App\Models\Customer::withCount('vehicles')->orderBy('name', 'asc')->get();
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
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'vehicle_type' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:30',
            'year' => 'nullable|integer',
            'vin' => 'nullable|string|max:50|unique:vehicles,vin',
            'engine_number' => 'nullable|string|max:50',
            'controller_number' => 'nullable|string|max:50',
        ]);

        $customId = $request->vehicle_id;
        
        if ($customId) {
            $existing = Vehicle::find($customId);
            if ($existing) {
                return response()->json(['error' => 'Vehicle ID already exists.'], 400);
            }
        }

        $id = $customId ?: 'VEH-' . strtoupper(substr(md5(uniqid(microtime(true))), 0, 8));

        DB::beginTransaction();
        try {
            $vehicle = Vehicle::create([
                'id' => $id,
                'customer_id' => $request->customer_id ?: null,
                'make' => $request->make ?: 'Generic',
                'model' => $request->model ?: 'EV',
                'vehicle_type' => $request->vehicle_type,
                'color' => $request->color,
                'year' => $request->year ?? date('Y'),
                'license_plate' => $request->license_plate,
                'vin' => $request->vin,
                'engine_number' => $request->engine_number,
                'controller_number' => $request->controller_number,
            ]);

            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'CREATE',
                'target_table' => 'vehicles',
                'record_id' => $vehicle->id,
                'new_value' => $vehicle->toArray(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Vehicle added successfully.', 'vehicle_id' => $vehicle->id]);
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
            'make' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'vehicle_type' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:30',
            'year' => 'nullable|integer',
            'license_plate' => 'required|string|max:20',
            'vin' => 'nullable|string|max:50',
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
            $vehicle->update([
                'customer_id' => $request->customer_id,
                'make' => $request->make,
                'model' => $request->model,
                'vehicle_type' => $request->vehicle_type,
                'color' => $request->color,
                'year' => $request->year ?? date('Y'),
                'license_plate' => $request->license_plate,
                'vin' => $request->vin,
            ]);

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
                $plate = trim($row['License Plate'] ?? '');
                $frame = trim($row['Frame Number / VIN'] ?? '');
                $controller = trim($row['Controller Number'] ?? '');
                $custName = trim($row['Customer Name'] ?? '');

                if (!$plate && !$frame && !$controller) continue;

                // Lookup customer if name provided
                $customerId = null;
                if ($custName) {
                    $c = \App\Models\Customer::where('name', 'like', "%{$custName}%")->first();
                    if ($c) $customerId = $c->id;
                }

                $vehicle = null;
                if ($plate) {
                    $vehicle = Vehicle::where('license_plate', $plate)->first();
                }
                if (!$vehicle && $frame) {
                    $vehicle = Vehicle::where('vin', $frame)->first();
                }

                $data = [
                    'customer_id' => $customerId ?: ($vehicle ? $vehicle->customer_id : null),
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
}
