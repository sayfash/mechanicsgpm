<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRecord;
use App\Models\RecordPartUsed;
use App\Models\Inventory;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JobController extends Controller
{
    public function submitMechanicJob(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $branchId = $request->input('branch_id') ?? ($user ? $user->branch_id : 1) ?? 1;

            // 1. Resolve Customer & Vehicle
            $vehicleId = $request->input('vehicle_id');
            $licensePlate = trim($request->input('license_plate', ''));
            $idCardNumber = trim($request->input('customer_idcard', ''));
            $customerPhone = trim($request->input('customer_phone', ''));
            $customerName = trim($request->input('customer_name', ''));
            $customerAddress = trim($request->input('customer_address', ''));
            $customerStatus = $request->input('customer_status', 'Retail');

            $vehicleType = trim($request->input('vehicle_type', ''));
            $frameNumber = trim($request->input('frame_number', ''));
            $controllerNumber = trim($request->input('controller_number', ''));

            $customer = null;
            if (!empty($idCardNumber)) {
                $customer = Customer::where('id_card_number', $idCardNumber)->first();
            }
            if (!$customer && !empty($customerPhone)) {
                $customer = Customer::where('phone', $customerPhone)->first();
            }
            if (!$customer && !empty($customerName)) {
                $customer = Customer::create([
                    'name' => $customerName,
                    'phone' => $customerPhone ?: '0000000000',
                    'id_card_number' => $idCardNumber ?: null,
                    'address' => $customerAddress ?: 'N/A',
                    'customer_status' => $customerStatus,
                ]);
            } else if ($customer && !empty($customerName)) {
                $customer->update([
                    'name' => $customerName ?: $customer->name,
                    'phone' => $customerPhone ?: $customer->phone,
                    'address' => $customerAddress ?: $customer->address,
                    'customer_status' => $customerStatus ?: $customer->customer_status,
                ]);
            }

            $vehicle = null;
            if (!empty($vehicleId) && $vehicleId !== 'new') {
                $vehicle = Vehicle::find($vehicleId);
            }
            if (!$vehicle && !empty($licensePlate)) {
                $vehicle = Vehicle::where('license_plate', $licensePlate)->first();
            }
            if (!$vehicle && !empty($frameNumber)) {
                $vehicle = Vehicle::where('vin', $frameNumber)->first();
            }

            if (!$vehicle) {
                $vPlate = !empty($licensePlate) ? $licensePlate : ('PLATE-' . strtoupper(Str::random(6)));
                $vehicle = Vehicle::create([
                    'license_plate' => $vPlate,
                    'customer_id' => $customer ? $customer->id : null,
                    'make' => $vehicleType ?: 'Generic',
                    'model' => 'EV',
                    'vehicle_type' => $vehicleType ?: 'EV',
                    'vin' => $frameNumber ?: null,
                    'controller_number' => $controllerNumber ?: null,
                    'branch_id' => $branchId,
                ]);
            } else {
                if ($customer && !$vehicle->customer_id) {
                    $vehicle->customer_id = $customer->id;
                }
                if (!empty($vehicleType)) $vehicle->vehicle_type = $vehicleType;
                if (!empty($frameNumber)) $vehicle->vin = $frameNumber;
                if (!empty($controllerNumber)) $vehicle->controller_number = $controllerNumber;
                $vehicle->save();
            }

            // 2. Build Job ID & Description
            $nextId = MaintenanceRecord::max('id') ?? 0;
            $nextId += 1;
            $jobIdStr = $request->input('job_id');
            if (empty($jobIdStr) || !str_contains($jobIdStr, 'JOB-')) {
                $jobIdStr = sprintf("JOB-%s-%04d", date('Ymd'), $nextId);
            }

            $commonIssues = $request->input('common_issues');
            if (is_array($commonIssues)) {
                $commonIssues = implode(', ', $commonIssues);
            }

            $mechanicFormItems = $request->input('mechanic_form_items');
            if (is_array($mechanicFormItems)) {
                $mechanicFormItems = implode(', ', $mechanicFormItems);
            }

            $otherIssues = $request->input('other_issues');

            $descParts = [];
            if (!empty($commonIssues)) $descParts[] = "Issues: " . $commonIssues;
            if (!empty($mechanicFormItems)) $descParts[] = "Checks: " . $mechanicFormItems;
            if (!empty($otherIssues)) $descParts[] = "Notes: " . $otherIssues;
            $fullDescription = implode(' | ', $descParts);
            if (empty($fullDescription)) {
                $fullDescription = "General Intake Repair Service";
            }

            // 3. Service Options & Labor Fees
            $serviceOptions = $request->input('service_options', []);
            $otherServices = $request->input('other_services', []);

            $totalLaborFee = 0;
            $serviceNames = [];
            $serviceSkus = [];
            foreach ($serviceOptions as $s) {
                if (!empty($s['is_charged']) || isset($s['fee'])) {
                    $fee = floatval($s['fee'] ?? 0);
                    $totalLaborFee += $fee;
                    if (!empty($s['name'])) $serviceNames[] = $s['name'];
                    if (!empty($s['sku'])) $serviceSkus[] = $s['sku'];
                }
            }

            $totalOtherFee = 0;
            $otherCategories = [];
            foreach ($otherServices as $o) {
                if (!empty($o['is_charged']) || isset($o['fee'])) {
                    $fee = floatval($o['fee'] ?? 0);
                    $totalOtherFee += $fee;
                    if (!empty($o['category'])) $otherCategories[] = $o['category'];
                }
            }

            // 4. Create or Update Maintenance Record
            $recordId = $request->input('record_id');
            if ($recordId) {
                $job = MaintenanceRecord::findOrFail($recordId);
                $job->update([
                    'mechanic_id' => $request->input('mechanic_id') ?? $job->mechanic_id ?? 1,
                    'repair_category' => $request->input('repair_category', 'Repair'),
                    'description' => $fullDescription,
                    'km_reached' => $request->input('km_reached') ? intval($request->input('km_reached')) : $job->km_reached,
                    'common_issues' => $commonIssues,
                    'other_issues' => $otherIssues,
                    'service_sku' => !empty($serviceSkus) ? implode(', ', $serviceSkus) : $job->service_sku,
                    'service_name' => !empty($serviceNames) ? implode(', ', $serviceNames) : $job->service_name,
                    'labor_fee' => $totalLaborFee,
                    'other_expenses_category' => !empty($otherCategories) ? implode(', ', $otherCategories) : $job->other_expenses_category,
                    'other_expenses_fee' => $totalOtherFee,
                    'end_time' => $request->input('end_time') ? Carbon::parse($request->input('end_time')) : Carbon::now(),
                    'status' => 'completed',
                ]);
            } else {
                $job = MaintenanceRecord::create([
                    'job_id' => $jobIdStr,
                    'vehicle_id' => $vehicle->id,
                    'branch_id' => $branchId,
                    'mechanic_id' => $request->input('mechanic_id') ?? ($user ? $user->id : 1) ?? 1,
                    'repair_category' => $request->input('repair_category', 'Repair'),
                    'description' => $fullDescription,
                    'km_reached' => $request->input('km_reached') ? intval($request->input('km_reached')) : null,
                    'common_issues' => $commonIssues,
                    'other_issues' => $otherIssues,
                    'service_sku' => !empty($serviceSkus) ? implode(', ', $serviceSkus) : null,
                    'service_name' => !empty($serviceNames) ? implode(', ', $serviceNames) : null,
                    'labor_fee' => $totalLaborFee,
                    'other_expenses_category' => !empty($otherCategories) ? implode(', ', $otherCategories) : null,
                    'other_expenses_fee' => $totalOtherFee,
                    'repair_date' => Carbon::today(),
                    'check_in_time' => Carbon::now()->format('H:i:s'),
                    'start_time' => $request->input('start_time') ? Carbon::parse($request->input('start_time')) : Carbon::now(),
                    'end_time' => $request->input('end_time') ? Carbon::parse($request->input('end_time')) : Carbon::now(),
                    'status' => 'completed',
                ]);
            }

            // 5. Parts Used & Inventory Deduction
            $partsUsed = $request->input('parts_used', []);
            $totalPartsCost = 0;
            foreach ($partsUsed as $part) {
$invId = $part['inventory_id'] ?? null;
                $qty = intval($part['qty'] ?? 1);
                if ($invId) {
                    $inventory = Inventory::find($invId);
                    if ($inventory) {
                        $priceAtUse = floatval($inventory->price ?? 0);
                        RecordPartUsed::create([
                            'maintenance_record_id' => $job->id,
                            'inventory_id' => $inventory->id,
                            'quantity_used' => $qty,
                            'price_at_use' => $priceAtUse,
                        ]);
                        $inventory->decrement('available_qty', $qty);
                        $totalPartsCost += ($priceAtUse * $qty);
                    }
                }
            }

            $job->parts_labor_paid = $totalPartsCost + $totalLaborFee + $totalOtherFee;
            $job->grand_total = $totalPartsCost + $totalLaborFee + $totalOtherFee;
            $job->save();

            AuditLog::create([
                'user_id' => $user ? $user->id : 1,
                'action_type' => 'CREATE',
                'target_table' => 'maintenance_records',
                'record_id' => $job->id,
                'new_value' => $job->toArray(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Job intake submitted successfully.',
                'job_id' => $job->job_id,
                'record' => $job
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create job: ' . $e->getMessage()], 500);
        }
    }

    public function getCustomerMaintenanceRecords(Request $request)
    {
        $query = trim($request->query('query', ''));
        $branchId = $request->query('branch_id');
        $status = $request->query('status');

        $builder = MaintenanceRecord::with(['vehicle.customer', 'mechanic', 'branch', 'parts.inventory']);

        if (!empty($query)) {
            $builder->where(function ($q) use ($query) {
                $q->where('job_id', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('common_issues', 'like', "%{$query}%")
                  ->orWhere('other_issues', 'like', "%{$query}%")
                  ->orWhereHas('vehicle', function ($vq) use ($query) {
                      $vq->where('license_plate', 'like', "%{$query}%")
                        ->orWhere('make', 'like', "%{$query}%")
                        ->orWhere('vin', 'like', "%{$query}%")
                        ->orWhere('controller_number', 'like', "%{$query}%")
                        ->orWhereHas('customer', function ($cq) use ($query) {
                            $cq->where('name', 'like', "%{$query}%")
                              ->orWhere('phone', 'like', "%{$query}%")
                              ->orWhere('id_card_number', 'like', "%{$query}%");
                        });
                  });
            });
        }

        if (!empty($branchId)) {
            $builder->where('branch_id', $branchId);
        }

        if (!empty($status)) {
            $builder->where('status', strtolower($status));
        }

        $records = $builder->orderBy('created_at', 'desc')->get();

        $result = $records->map(function ($r) {
            $parts = $r->parts->map(function ($p) {
                return [
                    'id' => $p->id,
                    'inventory_id' => $p->inventory_id,
                    'part_name' => $p->inventory->part_name ?? $p->inventory->sku ?? 'Spare Part',
                    'sku' => $p->inventory->sku ?? '',
                    'branch_name' => $p->inventory->branch->name ?? '',
                    'quantity_used' => $p->quantity_used,
                    'price_at_use' => $p->price_at_use,
                ];
            });

            return [
                'id' => $r->id,
                'job_id' => $r->job_id,
                'vehicle_id' => $r->vehicle_id,
                'branch_id' => $r->branch_id,
                'mechanic_id' => $r->mechanic_id,
                'repair_category' => $r->repair_category,
                'description' => $r->description,
                'km_reached' => $r->km_reached,
                'common_issues' => $r->common_issues,
                'other_issues' => $r->other_issues,
                'service_sku' => $r->service_sku,
                'service_name' => $r->service_name,
                'labor_fee' => $r->labor_fee,
                'other_expenses_category' => $r->other_expenses_category,
                'other_expenses_fee' => $r->other_expenses_fee,
                'repair_date' => $r->repair_date,
                'check_in_time' => $r->check_in_time,
                'check_out_time' => $r->check_out_time,
                'notes' => $r->notes,
                'payment_method' => $r->payment_method,
                'parts_labor_paid' => $r->parts_labor_paid,
                'grand_total' => $r->grand_total,
                'photo_path' => $r->photo_path,
                'status' => $r->status,
                'start_time' => $r->start_time,
                'end_time' => $r->end_time,
                'created_at' => $r->created_at,
                // Mapped helper attributes for frontend display
                'customer_id' => $r->vehicle->customer_id ?? null,
                'license_plate' => $r->vehicle->license_plate ?? 'N/A',
                'make' => $r->vehicle->make ?? '',
                'model' => $r->vehicle->model ?? '',
                'vehicle_type' => $r->vehicle->vehicle_type ?? 'EV',
                'vin' => $r->vehicle->vin ?? 'N/A',
                'frame_number' => $r->vehicle->vin ?? 'N/A',
                'controller_number' => $r->vehicle->controller_number ?? 'N/A',
                'customer_name' => $r->vehicle->customer->name ?? 'Unbound Customer',
                'customer_phone' => $r->vehicle->customer->phone ?? 'N/A',
                'customer_idcard' => $r->vehicle->customer->id_card_number ?? 'N/A',
                'branch_name' => $r->branch->name ?? 'Main Branch',
                'mechanic_name' => $r->mechanic->display_name ?? $r->mechanic->username ?? $r->mechanic->name ?? 'Mechanic',
                'parts' => $parts,
            ];
        });

        return response()->json($result);
    }

    public function editMaintenanceRecord(Request $request)
    {
        $request->validate([
            'record_id' => 'required|exists:maintenance_records,id',
            'description' => 'required|string',
            'status' => 'nullable|string',
        ]);

        $record = MaintenanceRecord::findOrFail($request->record_id);
        $oldValue = $record->toArray();

        $record->update($request->only(['description', 'km_reached', 'status', 'other_issues', 'notes']));

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'action_type' => 'UPDATE',
            'target_table' => 'maintenance_records',
            'record_id' => $record->id,
            'old_value' => $oldValue,
            'new_value' => $record->toArray(),
        ]);

        return response()->json(['message' => 'Maintenance record updated successfully.']);
    }

    public function deleteMaintenanceRecord(Request $request)
    {
        $request->validate([
            'record_id' => 'required|exists:maintenance_records,id'
        ]);

        $record = MaintenanceRecord::findOrFail($request->record_id);
        $oldValue = $record->toArray();

        $record->delete();

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'action_type' => 'DELETE',
            'target_table' => 'maintenance_records',
            'record_id' => $record->id,
            'old_value' => $oldValue,
        ]);

        return response()->json(['message' => 'Maintenance record deleted successfully.']);
    }

    /**
     * Show printable maintenance record view.
     * Accessible only to authenticated users.
     */
    public function printMaintenanceRecord(Request $request)
    {
        $id = $request->query('record_id') ?? $request->query('id') ?? $request->query('job_id');

        if (!$id) {
            return response()->json(['error' => 'Record ID or Job ID is required.'], 400);
        }

        $record = MaintenanceRecord::with([
            'vehicle.customer',
            'mechanic',
            'branch',
            'parts.inventory'
        ])
        ->where('id', $id)
        ->orWhere('job_id', $id)
        ->firstOrFail();

        // Map similar helper attributes as used in front‑end
        $recordData = $record->toArray();
        $recordData['customer_id'] = $record->vehicle->customer_id ?? null;
        $recordData['license_plate'] = $record->vehicle->license_plate ?? 'N/A';
        $recordData['make'] = $record->vehicle->make ?? '';
        $recordData['model'] = $record->vehicle->model ?? '';
        $recordData['vehicle_type'] = $record->vehicle->vehicle_type ?? 'EV';
        $recordData['vin'] = $record->vehicle->vin ?? 'N/A';
        $recordData['frame_number'] = $record->vehicle->vin ?? 'N/A';
        $recordData['controller_number'] = $record->vehicle->controller_number ?? 'N/A';
        $recordData['customer_name'] = $record->vehicle->customer->name ?? 'Unbound Customer';
        $recordData['customer_phone'] = $record->vehicle->customer->phone ?? 'N/A';
        $recordData['customer_idcard'] = $record->vehicle->customer->id_card_number ?? 'N/A';
        $recordData['branch_name'] = $record->branch->name ?? 'Main Branch';
        $recordData['mechanic_name'] = $record->mechanic->display_name ?? $record->mechanic->username ?? $record->mechanic->name ?? 'Mechanic';

        // Build parts array for view
        $recordData['parts'] = $record->parts->map(function ($p) {
            return [
                'id' => $p->id,
                'inventory_id' => $p->inventory_id,
                'part_name' => $p->inventory->part_name ?? $p->inventory->sku ?? 'Spare Part',
                'sku' => $p->inventory->sku ?? '',
                'branch_name' => $p->inventory->branch->name ?? '',
                'quantity_used' => $p->quantity_used,
                'price_at_use' => $p->price_at_use,
            ];
        })->toArray();

        return view('pages.mechanic.print_maintenance_record', ['record' => $recordData]);
    }

    public function startJob(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $branchId = $request->input('branch_id') ?? ($user ? $user->branch_id : 1) ?? 1;

            // 1. Resolve Customer & Vehicle
            $vehicleId = $request->input('vehicle_id');
            $licensePlate = trim($request->input('license_plate', ''));
            $idCardNumber = trim($request->input('customer_idcard', ''));
            $customerPhone = trim($request->input('customer_phone', ''));
            $customerName = trim($request->input('customer_name', ''));
            $customerAddress = trim($request->input('customer_address', ''));
            $customerStatus = $request->input('customer_status', 'Retail');

            $vehicleType = trim($request->input('vehicle_type', ''));
            $frameNumber = trim($request->input('frame_number', ''));
            $controllerNumber = trim($request->input('controller_number', ''));

            $customer = null;
            if (!empty($idCardNumber)) {
                $customer = Customer::where('id_card_number', $idCardNumber)->first();
            }
            if (!$customer && !empty($customerPhone)) {
                $customer = Customer::where('phone', $customerPhone)->first();
            }
            if (!$customer && !empty($customerName)) {
                $customer = Customer::create([
                    'name' => $customerName,
                    'phone' => $customerPhone ?: '0000000000',
                    'id_card_number' => $idCardNumber ?: null,
                    'address' => $customerAddress ?: 'N/A',
                    'customer_status' => $customerStatus,
                ]);
            } else if ($customer && !empty($customerName)) {
                $customer->update([
                    'name' => $customerName ?: $customer->name,
                    'phone' => $customerPhone ?: $customer->phone,
                    'address' => $customerAddress ?: $customer->address,
                    'customer_status' => $customerStatus ?: $customer->customer_status,
                ]);
            }

            $vehicle = null;
            if (!empty($vehicleId) && $vehicleId !== 'new') {
                $vehicle = Vehicle::find($vehicleId);
            }
            if (!$vehicle && !empty($licensePlate)) {
                $vehicle = Vehicle::where('license_plate', $licensePlate)->first();
            }
            if (!$vehicle && !empty($frameNumber)) {
                $vehicle = Vehicle::where('vin', $frameNumber)->first();
            }

            if (!$vehicle) {
                $vPlate = !empty($licensePlate) ? $licensePlate : ('PLATE-' . strtoupper(Str::random(6)));
                $vehicle = Vehicle::create([
                    'license_plate' => $vPlate,
                    'customer_id' => $customer ? $customer->id : null,
                    'make' => $vehicleType ?: 'Generic',
                    'model' => 'EV',
                    'vehicle_type' => $vehicleType ?: 'EV',
                    'vin' => $frameNumber ?: null,
                    'controller_number' => $controllerNumber ?: null,
                    'branch_id' => $branchId,
                ]);
            } else {
                if ($customer && !$vehicle->customer_id) {
                    $vehicle->customer_id = $customer->id;
                }
                if (!empty($vehicleType)) $vehicle->vehicle_type = $vehicleType;
                if (!empty($frameNumber)) $vehicle->vin = $frameNumber;
                if (!empty($controllerNumber)) $vehicle->controller_number = $controllerNumber;
                $vehicle->save();
            }

            $nextId = MaintenanceRecord::max('id') ?? 0;
            $nextId += 1;
            $jobIdStr = sprintf("JOB-%s-%04d", date('Ymd'), $nextId);

            $job = MaintenanceRecord::create([
                'job_id' => $jobIdStr,
                'vehicle_id' => $vehicle->id,
                'branch_id' => $branchId,
                'mechanic_id' => $request->input('mechanic_id') ?? 1,
                'repair_category' => $request->input('repair_category', 'Repair'),
                'description' => $request->input('description') ?? 'General Intake Repair Service',
                'km_reached' => $request->input('km_reached') ? intval($request->input('km_reached')) : null,
                'common_issues' => $request->input('common_issues'),
                'other_issues' => $request->input('other_issues'),
                'repair_date' => Carbon::today(),
                'check_in_time' => Carbon::now()->format('H:i:s'),
                'start_time' => Carbon::now(),
                'status' => 'in_progress',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Job started successfully.',
                'job_id' => $job->job_id,
                'record' => $job
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to start job: ' . $e->getMessage()], 500);
        }
    }
}
