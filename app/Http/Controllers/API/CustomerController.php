<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::withCount('vehicles')->orderBy('name', 'asc')->get();
        return response()->json($customers);
    }

    public function store(Request $request)
    {
        if ($request->branch_id === '') {
            $request->merge(['branch_id' => null]);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'customer_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:30',
            'id_card_number' => 'nullable|string|max:50',
            'customer_status' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'branch_id' => 'nullable|integer',
        ]);

        $customId = $request->customer_id;
        
        if ($customId) {
            $existing = Customer::find($customId);
            if ($existing) {
                return response()->json(['error' => 'Customer ID already exists.'], 400);
            }
        }

        if ($request->id_card_number) {
            $existing = Customer::where('id_card_number', $request->id_card_number)->first();
            if ($existing) {
                return response()->json(['error' => 'ID Card number already exists.'], 400);
            }
        }

        $id = $customId ?: 'CUST-' . strtoupper(substr(md5(uniqid(microtime(true))), 0, 8));

        DB::beginTransaction();
        try {
            $customer = Customer::create([
                'id' => $id,
                'name' => $request->name,
                'phone' => $request->phone,
                'id_card_number' => $request->id_card_number,
                'customer_status' => $request->customer_status ?? 'Retail',
                'address' => $request->address,
            ]);

            AuditLog::create([
                'user_id' => auth()->id() ?? 1, // fallback to 1 if auth not setup
                'action_type' => 'CREATE',
                'target_table' => 'customers',
                'record_id' => $customer->id,
                'new_value' => $customer->toArray(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Customer created successfully.', 'customer_id' => $customer->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create customer: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|string',
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'id_card_number' => 'nullable|string|max:50',
            'customer_status' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        $customer = Customer::find($request->customer_id);
        if (!$customer) {
            return response()->json(['error' => 'Customer not found.'], 404);
        }

        $oldValue = $customer->toArray();

        DB::beginTransaction();
        try {
            $customer->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'id_card_number' => $request->id_card_number,
                'customer_status' => $request->customer_status ?? 'Retail',
                'address' => $request->address,
            ]);

            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'UPDATE',
                'target_table' => 'customers',
                'record_id' => $customer->id,
                'old_value' => $oldValue,
                'new_value' => $customer->toArray(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Customer updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update customer.'], 500);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|string'
        ]);

        $customer = Customer::find($request->customer_id);
        if (!$customer) {
            return response()->json(['error' => 'Customer not found.'], 404);
        }

        $oldValue = $customer->toArray();

        DB::beginTransaction();
        try {
            // Unbind vehicles
            \App\Models\Vehicle::where('customer_id', $customer->id)->update(['customer_id' => null]);
            
            $customer->delete();

            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'DELETE',
                'target_table' => 'customers',
                'record_id' => $customer->id,
                'old_value' => $oldValue,
            ]);

            DB::commit();
            return response()->json(['message' => 'Customer deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete customer.'], 500);
        }
    }

    public function importCustomersBatch(Request $request)
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
                $name = trim($row['Customer Name'] ?? '');
                $phone = trim($row['Phone'] ?? '');
                $idCard = trim($row['ID Card Number'] ?? '');
                $branchName = trim($row['Branch'] ?? '');

                if (!$name && !$phone && !$idCard) continue;

                $branchId = auth()->user()->branch_id ?? 1;
                if ($branchName) {
                    $b = \App\Models\Branch::where('name', 'like', "%{$branchName}%")->first();
                    if ($b) $branchId = $b->id;
                }

                $customer = null;
                if ($idCard) {
                    $customer = Customer::where('id_card_number', $idCard)->first();
                }
                if (!$customer && $phone) {
                    $customer = Customer::where('phone', $phone)->first();
                }

                $data = [
                    'branch_id' => $branchId,
                    'name' => $name ?: ($customer ? $customer->name : 'Unknown'),
                    'phone' => $phone ?: ($customer ? $customer->phone : '0000000000'),
                    'id_card_number' => $idCard ?: null,
                    'address' => $row['Address'] ?? null,
                    'customer_status' => $row['Customer Status'] ?? 'Retail',
                ];

                if ($customer) {
                    $customer->update($data);
                    $updated++;
                } else {
                    Customer::create($data);
                    $inserted++;
                }
            }

            DB::commit();
            return response()->json([
                'message' => "Customers batch import complete! {$inserted} created, {$updated} updated."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Import customers batch failed: ' . $e->getMessage()], 500);
        }
    }

    public function registerWithVehicle(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'id_card_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'license_plate' => 'required|string|max:20',
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'vehicle_type' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:30',
            'vin' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            $custId = 'CUST-' . strtoupper(substr(md5(uniqid(microtime(true))), 0, 8));
            $customer = Customer::create([
                'id' => $custId,
                'name' => $request->name,
                'phone' => $request->phone,
                'id_card_number' => $request->id_card_number,
                'customer_status' => $request->customer_status ?? 'Retail',
                'address' => $request->address,
            ]);

            $vehId = 'VEH-' . strtoupper(substr(md5(uniqid(microtime(true))), 0, 8));
            $vehicle = \App\Models\Vehicle::create([
                'id' => $vehId,
                'customer_id' => $customer->id,
                'license_plate' => $request->license_plate,
                'make' => $request->make ?: 'Generic',
                'model' => $request->model ?: 'EV',
                'vehicle_type' => $request->vehicle_type,
                'color' => $request->color,
                'year' => date('Y'),
                'vin' => $request->vin,
            ]);

            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'CREATE',
                'target_table' => 'customers',
                'record_id' => $customer->id,
                'new_value' => $customer->toArray(),
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Customer and vehicle registered successfully.',
                'customer_id' => $customer->id,
                'vehicle_id' => $vehicle->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to register customer and vehicle: ' . $e->getMessage()], 500);
        }
    }
}
