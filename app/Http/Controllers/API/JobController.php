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

            $customer = null;
            if (!empty($idCardNumber)) {
                $customer = Customer::where('id_card_number', $idCardNumber)->first();
            }
            if (!$customer && !empty($customerPhone)) {
                $customer = Customer::where('phone', $customerPhone)->first();
            }
            if (!$customer && $vehicle && $vehicle->customer_id) {
                $customer = Customer::find($vehicle->customer_id);
            }

            if (!$customer && !empty($customerName)) {
                $custId = 'CUST-' . strtoupper(substr(md5(uniqid(microtime(true))), 0, 8));
                $customer = Customer::create([
                    'id' => $custId,
                    'name' => $customerName,
                    'phone' => $customerPhone ?: '0000000000',
                    'id_card_number' => $idCardNumber ?: null,
                    'address' => $customerAddress ?: 'N/A',
                    'customer_status' => $customerStatus,
                ]);
            } else if ($customer) {
                $customer->update([
                    'name' => $customerName ?: $customer->name,
                    'phone' => $customerPhone ?: $customer->phone,
                    'address' => $customerAddress ?: $customer->address,
                    'customer_status' => $customerStatus ?: $customer->customer_status,
                ]);
            }

            if (!$vehicle) {
                $vPlate = !empty($licensePlate) ? $licensePlate : ('PLATE-' . strtoupper(Str::random(6)));
                $vehId = 'VEH-' . strtoupper(substr(md5(uniqid(microtime(true))), 0, 8));
                $vehicle = Vehicle::create([
                    'id' => $vehId,
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
                if ($customer && $vehicle->customer_id != $customer->id) {
                    $vehicle->customer_id = $customer->id;
                }
                if (!empty($vehicleType)) $vehicle->vehicle_type = $vehicleType;
                if (!empty($frameNumber)) $vehicle->vin = $frameNumber;
                if (!empty($controllerNumber)) $vehicle->controller_number = $controllerNumber;
                $vehicle->save();
            }

            // 2. Build Job ID & Description
            $branchObj = \App\Models\Branch::find($branchId);
            $abbrStr = ($branchObj && !empty($branchObj->abbreviation)) ? strtoupper($branchObj->abbreviation) : 'JOB';
            $branchJobCount = MaintenanceRecord::where('branch_id', $branchId)->count() + 1;
            
            $jobIdStr = $request->input('job_id');
            if (empty($jobIdStr)) {
                $jobIdStr = sprintf("%s-%s-%04d", $abbrStr, date('dmY'), $branchJobCount);
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
            if (!empty($otherIssues)) $descParts[] = "Notes: " . $otherIssues;
            if (!empty($commonIssues)) $descParts[] = "Issues: " . $commonIssues;
            if (!empty($mechanicFormItems)) $descParts[] = "Checks: " . $mechanicFormItems;
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
            $otherSkus = [];
            foreach ($otherServices as $o) {
                if (!empty($o['is_charged']) || isset($o['fee'])) {
                    $fee = floatval($o['fee'] ?? 0);
                    $totalOtherFee += $fee;
                    if (!empty($o['category'])) $otherCategories[] = $o['category'];

                    $skuVal = $o['sku'] ?? $o['service_sku'] ?? null;
                    if (empty($skuVal) && !empty($o['id'])) {
                        $skuVal = \Illuminate\Support\Facades\DB::table('other_services')->where('id', $o['id'])->value('sku');
                    }
                    if (empty($skuVal) && !empty($o['category'])) {
                        $skuVal = \Illuminate\Support\Facades\DB::table('other_services')->where('name', $o['category'])->value('sku');
                    }
                    $otherSkus[] = !empty($skuVal) ? $skuVal : ('JS' . sprintf('%03d', count($otherSkus) + 1));
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
                    'notes' => $otherIssues,
                    'service_sku' => !empty($serviceSkus) ? implode(', ', $serviceSkus) : $job->service_sku,
                    'service_name' => !empty($serviceNames) ? implode(', ', $serviceNames) : $job->service_name,
                    'labor_fee' => $totalLaborFee,
                    'other_expenses_sku' => !empty($otherSkus) ? implode(', ', $otherSkus) : $job->other_expenses_sku,
                    'other_expenses_category' => !empty($otherCategories) ? implode(', ', $otherCategories) : $job->other_expenses_category,
                    'other_expenses_fee' => $totalOtherFee,
                    'end_time' => ($request->input('end_time') ?? $request->input('stopped_at')) 
                        ? Carbon::parse($request->input('end_time') ?? $request->input('stopped_at')) 
                        : Carbon::now(),
                    'status' => 'completed',
                ]);
            } else {
                $todayDate = Carbon::now()->toDateString();
                $maxQueueToday = MaintenanceRecord::where('branch_id', $branchId)
                    ->whereDate('created_at', $todayDate)
                    ->max('daily_queue_number');
                $dailyQueueNum = ($maxQueueToday ? intval($maxQueueToday) : 0) + 1;

                $job = MaintenanceRecord::create([
                    'job_id' => $jobIdStr,
                    'vehicle_id' => $vehicle->id,
                    'branch_id' => $branchId,
                    'daily_queue_number' => $dailyQueueNum,
                    'mechanic_id' => $request->input('mechanic_id') ?? ($user ? $user->id : 1) ?? 1,
                    'repair_category' => $request->input('repair_category', 'Repair'),
                    'description' => $fullDescription,
                    'km_reached' => $request->input('km_reached') ? intval($request->input('km_reached')) : null,
                    'common_issues' => $commonIssues,
                    'other_issues' => $otherIssues,
                    'notes' => $otherIssues,
                    'service_sku' => !empty($serviceSkus) ? implode(', ', $serviceSkus) : null,
                    'service_name' => !empty($serviceNames) ? implode(', ', $serviceNames) : null,
                    'labor_fee' => $totalLaborFee,
                    'other_expenses_sku' => !empty($otherSkus) ? implode(', ', $otherSkus) : null,
                    'other_expenses_category' => !empty($otherCategories) ? implode(', ', $otherCategories) : null,
                    'other_expenses_fee' => $totalOtherFee,
                    'repair_date' => Carbon::now()->format('Y-m-d'),
                    'check_in_time' => Carbon::now()->format('H:i:s'),
                    'start_time' => $request->input('start_time') ? Carbon::parse($request->input('start_time'))->format('Y-m-d H:i:s') : Carbon::now()->format('Y-m-d H:i:s'),
                    'end_time' => $request->input('end_time') ? Carbon::parse($request->input('end_time'))->format('Y-m-d H:i:s') : Carbon::now()->format('Y-m-d H:i:s'),
                    'status' => 'completed',
                ]);
            }

            // 5. Parts Used & Inventory Deduction
            $oldParts = RecordPartUsed::where('maintenance_record_id', $job->id)->get();
            foreach ($oldParts as $op) {
                $inv = Inventory::find($op->inventory_id);
                if ($inv) {
                    $inv->increment('available_qty', $op->quantity_used);
                }
            }
            RecordPartUsed::where('maintenance_record_id', $job->id)->delete();

            $partsUsed = $request->input('parts_used', []);
            $totalPartsCost = 0;
            foreach ($partsUsed as $part) {
                $invId = $part['inventory_id'] ?? null;
                $qty = intval($part['qty'] ?? $part['quantity_used'] ?? 1);
                if ($invId) {
                    $inventory = Inventory::find($invId);
                    if ($inventory) {
                        $priceAtUse = floatval($part['price_at_use'] ?? $part['price'] ?? ($inventory->price ?? 0));
                        $isPartCharged = isset($part['is_charged']) ? (bool)$part['is_charged'] : ($priceAtUse > 0);
                        $warrantyCategory = $part['warranty_category'] ?? ($inventory->warranty_category ?? 'Unclaimable / No Warranty');
                        RecordPartUsed::create([
                            'maintenance_record_id' => $job->id,
                            'inventory_id' => $inventory->id,
                            'quantity_used' => $qty,
                            'price_at_use' => $priceAtUse,
                            'is_charged' => $isPartCharged ? 1 : 0,
                            'is_claimed' => $isPartClaimed ? 1 : 0,
                            'warranty_category' => $warrantyCategory,
                        ]);
                        $inventory->decrement('available_qty', $qty);
                        $effectiveCost = $isPartClaimed ? 0 : ($priceAtUse * $qty);
                        $totalPartsCost += $effectiveCost;
                    }
                }
            }

            // Derive repair category automatically
            $partsRecords = RecordPartUsed::where('maintenance_record_id', $job->id)->get();
            if ($partsRecords->isEmpty()) {
                $autoCategory = 'Repair';
            } elseif ($partsRecords->contains('is_claimed', true)) {
                $autoCategory = 'Claim';
            } else {
                $autoCategory = 'Stock';
            }
            $job->repair_category = $autoCategory;

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
            if (strtolower($status) === 'active' || strtolower($status) === 'in_progress,queue' || strtolower($status) === 'queue,in_progress') {
                $builder->whereIn('status', ['in_progress', 'queue']);
            } else if (str_contains($status, ',')) {
                $statuses = array_map('trim', explode(',', strtolower($status)));
                $builder->whereIn('status', $statuses);
            } else {
                $builder->where('status', strtolower($status));
            }
        }

        $records = $builder->orderBy('daily_queue_number', 'asc')->orderBy('created_at', 'asc')->get();

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

            $qNum = $r->daily_queue_number;
            if (!$qNum) {
                $qDate = $r->created_at ? Carbon::parse($r->created_at)->toDateString() : Carbon::now()->toDateString();
                $qNum = MaintenanceRecord::where('branch_id', $r->branch_id)
                    ->whereDate('created_at', $qDate)
                    ->where('id', '<=', $r->id)
                    ->count() ?: 1;
            }

            return [
                'id' => $r->id,
                'job_id' => $r->job_id,
                'daily_queue_number' => $qNum,
                'daily_queue_formatted' => sprintf('#%02d', $qNum),
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
                'customer_status' => $r->customer_status ?? $r->vehicle->customer->customer_status ?? 'Retail',
                'customer_type' => $r->customer_type ?? 'external',
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

        $record->update($request->only(['customer_type', 'km_reached', 'status', 'other_issues', 'notes']));

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
        $id = $request->input('record_id') ?? $request->input('id');
        if (!$id) {
            return response()->json(['error' => 'Record ID is required.'], 422);
        }

        $record = MaintenanceRecord::find($id);
        if (!$record) {
            return response()->json(['error' => 'Maintenance record not found.'], 404);
        }

        $oldValue = $record->toArray();
        $record->delete();

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'action_type' => 'DELETE',
            'target_table' => 'maintenance_records',
            'record_id' => $id,
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
            return response("<div style='font-family:sans-serif;padding:40px;text-align:center;'><h2>Record ID Required</h2><p>Please specify a valid record ID or job ID.</p></div>", 400);
        }

        $record = MaintenanceRecord::with([
            'vehicle.customer',
            'mechanic',
            'branch',
            'parts.inventory'
        ])
        ->where('id', $id)
        ->orWhere('job_id', $id)
        ->orWhere('job_id', 'like', "%{$id}")
        ->first();

        if (!$record && is_numeric($id)) {
            $record = MaintenanceRecord::with([
                'vehicle.customer',
                'mechanic',
                'branch',
                'parts.inventory'
            ])->find((int)$id);
        }

        if (!$record) {
            return response("<div style='font-family:sans-serif;padding:40px;text-align:center;'><h2>Maintenance Record Not Found</h2><p>No record found for ID: <strong>" . e($id) . "</strong>.</p></div>", 404);
        }

        // Branch Isolation Check: Shop Admins can only view records from their own branch
        $user = auth()->user();
        if ($user && $user->role === 'shop_admin' && $user->branch_id && $record->branch_id != $user->branch_id) {
            return response("<div style='font-family:sans-serif;padding:40px;text-align:center;'><h2>Unauthorized Access</h2><p>You do not have permission to view records from another branch outlet.</p></div>", 403);
        }

        // Map similar helper attributes as used in front‑end
        $recordData = $record->toArray();
        $qNum = $record->daily_queue_number;
        if (!$qNum) {
            $qDate = $record->created_at ? Carbon::parse($record->created_at)->toDateString() : Carbon::now()->toDateString();
            $qNum = MaintenanceRecord::where('branch_id', $record->branch_id)
                ->whereDate('created_at', $qDate)
                ->where('id', '<=', $record->id)
                ->count() ?: 1;
        }
        $recordData['daily_queue_number'] = $qNum;
        $recordData['daily_queue_formatted'] = sprintf('#%02d', $qNum);

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
        $recordData['customer_address'] = $record->vehicle->customer->address ?? 'N/A';
        $recordData['customer_status'] = $record->vehicle->customer->customer_status ?? 'Retail';
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

    /**
     * Show printable customer invoice view formatted for A5 Landscape.
     * Accessible only to authenticated users.
     */
    public function printCustomerInvoice(Request $request)
    {
        $id = $request->query('record_id') ?? $request->query('id') ?? $request->query('job_id');

        if (!$id) {
            return response("<div style='font-family:sans-serif;padding:40px;text-align:center;'><h2>Record ID Required</h2><p>Please specify a valid record ID or job ID.</p></div>", 400);
        }

        $record = MaintenanceRecord::with([
            'vehicle.customer',
            'mechanic',
            'branch',
            'parts.inventory'
        ])
        ->where('id', $id)
        ->orWhere('job_id', $id)
        ->orWhere('job_id', 'like', "%{$id}")
        ->first();

        if (!$record && is_numeric($id)) {
            $record = MaintenanceRecord::with([
                'vehicle.customer',
                'mechanic',
                'branch',
                'parts.inventory'
            ])->find((int)$id);
        }

        if (!$record) {
            return response("<div style='font-family:sans-serif;padding:40px;text-align:center;'><h2>Maintenance Record Not Found</h2><p>No record found for ID: <strong>" . e($id) . "</strong>.</p></div>", 404);
        }

        // Branch Isolation Check: Shop Admins can only view/print invoices from their own branch
        $user = auth()->user();
        if ($user && $user->role === 'shop_admin' && $user->branch_id && $record->branch_id != $user->branch_id) {
            return response("<div style='font-family:sans-serif;padding:40px;text-align:center;'><h2>Unauthorized Access</h2><p>You do not have permission to view invoices from another branch outlet.</p></div>", 403);
        }

        // Map helper attributes
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
        $recordData['customer_address'] = $record->vehicle->customer->address ?? 'N/A';
        $recordData['customer_status'] = $record->vehicle->customer->customer_status ?? 'Retail';
        $recordData['branch_name'] = $record->branch->name ?? 'Main Branch';
        $recordData['mechanic_name'] = $record->mechanic->display_name ?? $record->mechanic->username ?? $record->mechanic->name ?? 'Mechanic Specialist';
        $recordData['cashier_name'] = $user->display_name ?? $user->username ?? $user->name ?? 'Authorized Cashier';

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

        return view('pages.mechanic.print_customer_invoice', ['record' => $recordData]);
    }

    public function downloadCustomerInvoicePdf(Request $request)
    {
        $id = $request->input('record_id') ?? $request->input('id');
        if (!$id) {
            return response("Record ID Required", 400);
        }

        $record = MaintenanceRecord::with([
            'vehicle.customer',
            'mechanic',
            'branch',
            'parts.inventory'
        ])
        ->where('id', $id)
        ->orWhere('job_id', $id)
        ->orWhere('job_id', 'like', "%{$id}")
        ->first();

        if (!$record && is_numeric($id)) {
            $record = MaintenanceRecord::with([
                'vehicle.customer',
                'mechanic',
                'branch',
                'parts.inventory'
            ])->find((int)$id);
        }

        if (!$record) {
            return response("Maintenance Record Not Found", 404);
        }

        $user = auth()->user();

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
        $recordData['customer_address'] = $record->vehicle->customer->address ?? 'N/A';
        $recordData['customer_status'] = $record->vehicle->customer->customer_status ?? 'Retail';
        $recordData['branch_name'] = $record->branch->name ?? 'Main Branch';
        $recordData['mechanic_name'] = $record->mechanic->display_name ?? $record->mechanic->username ?? $record->mechanic->name ?? 'Mechanic Specialist';
        $recordData['cashier_name'] = $user ? ($user->display_name ?? $user->username ?? $user->name ?? 'Authorized Cashier') : 'Authorized Cashier';

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

        $pdfBuilder = new \App\Services\SimplePdfBuilder();
        $pdfBinary = $pdfBuilder->generateInvoicePdf($recordData);

        $filename = 'Faktur_SGPM_' . ($record->job_id ?: $record->id) . '.pdf';

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, private',
        ]);
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

            $customer = null;
            if (!empty($idCardNumber)) {
                $customer = Customer::where('id_card_number', $idCardNumber)->first();
            }
            if (!$customer && !empty($customerPhone)) {
                $customer = Customer::where('phone', $customerPhone)->first();
            }
            if (!$customer && $vehicle && $vehicle->customer_id) {
                $customer = Customer::find($vehicle->customer_id);
            }

            if (!$customer && !empty($customerName)) {
                $custId = 'CUST-' . strtoupper(substr(md5(uniqid(microtime(true))), 0, 8));
                $customer = Customer::create([
                    'id' => $custId,
                    'name' => $customerName,
                    'phone' => $customerPhone ?: '0000000000',
                    'id_card_number' => $idCardNumber ?: null,
                    'address' => $customerAddress ?: 'N/A',
                    'customer_status' => $customerStatus,
                ]);
            } else if ($customer) {
                $customer->update([
                    'name' => $customerName ?: $customer->name,
                    'phone' => $customerPhone ?: $customer->phone,
                    'address' => $customerAddress ?: $customer->address,
                    'customer_status' => $customerStatus ?: $customer->customer_status,
                ]);
            }

            if (!$vehicle) {
                $vPlate = !empty($licensePlate) ? $licensePlate : ('PLATE-' . strtoupper(Str::random(6)));
                $vehId = 'VEH-' . strtoupper(substr(md5(uniqid(microtime(true))), 0, 8));
                $vehicle = Vehicle::create([
                    'id' => $vehId,
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
                if ($customer && $vehicle->customer_id != $customer->id) {
                    $vehicle->customer_id = $customer->id;
                }
                if (!empty($vehicleType)) $vehicle->vehicle_type = $vehicleType;
                if (!empty($frameNumber)) $vehicle->vin = $frameNumber;
                if (!empty($controllerNumber)) $vehicle->controller_number = $controllerNumber;
                $vehicle->save();
            }

            // 2. Service Options & Labor Fee
            $serviceOptions = $request->input('service_options', []);
            $totalLaborFee = 0;
            $serviceNames = [];
            $serviceSkus = [];
            foreach ($serviceOptions as $s) {
                if (!empty($s['is_charged']) || isset($s['fee'])) {
                    $fee = floatval($s['fee'] ?? 0);
                    $totalLaborFee += $fee;
                    $sName = $s['service_name'] ?? $s['name'] ?? null;
                    if (!empty($sName)) $serviceNames[] = $sName;
                    if (!empty($s['sku'])) $serviceSkus[] = $s['sku'];
                }
            }

            // 3. Other Services
            $otherServices = $request->input('other_services', []);
            $totalOtherFee = 0;
            $otherCategories = [];
            $otherSkus = [];
            foreach ($otherServices as $o) {
                if (!empty($o['is_charged']) || isset($o['fee'])) {
                    $fee = floatval($o['fee'] ?? 0);
                    $totalOtherFee += $fee;
                    if (!empty($o['category'])) $otherCategories[] = $o['category'];

                    $skuVal = $o['sku'] ?? $o['service_sku'] ?? null;
                    if (empty($skuVal) && !empty($o['id'])) {
                        $skuVal = \Illuminate\Support\Facades\DB::table('other_services')->where('id', $o['id'])->value('sku');
                    }
                    if (empty($skuVal) && !empty($o['category'])) {
                        $skuVal = \Illuminate\Support\Facades\DB::table('other_services')->where('name', $o['category'])->value('sku');
                    }
                    $otherSkus[] = !empty($skuVal) ? $skuVal : ('JS' . sprintf('%03d', count($otherSkus) + 1));
                }
            }

            $recordId = $request->input('record_id');
            $rawMechId = $request->input('mechanic_id');
            $mechanicId = (!empty($rawMechId) && $rawMechId !== 'null') ? $rawMechId : null;
            
            // Validate mechanic availability
            if ($mechanicId) {
                $busyJob = MaintenanceRecord::where('mechanic_id', $mechanicId)
                    ->where('status', 'in_progress')
                    ->when($recordId, function($q) use ($recordId) {
                        return $q->where('id', '!=', $recordId);
                    })
                    ->first();
                if ($busyJob) {
                    return response()->json([
                        'error' => sprintf("Mechanic is currently assigned to active job (%s) and cannot be assigned a new job.", $busyJob->job_id)
                    ], 422);
                }
            }

            $job = $recordId ? MaintenanceRecord::find($recordId) : null;
            
            $customerStatus = $request->input('customer_status', 'Retail');
            $customerType = $request->input('customer_type', 'external');
            $endTimeVal = $request->input('end_time') ?? $request->input('stopped_at');

            $otherIssues = $request->input('other_issues');
            $commonIssues = $request->input('common_issues');
            $mechanicFormItems = $request->input('mechanic_form_items');

            $descParts = [];
            if (!empty($otherIssues)) $descParts[] = "Notes: " . $otherIssues;
            if (!empty($commonIssues)) $descParts[] = "Issues: " . $commonIssues;
            if (!empty($mechanicFormItems)) $descParts[] = "Checks: " . $mechanicFormItems;
            $jobDescription = implode(' | ', $descParts);
            if (empty($jobDescription)) {
                $jobDescription = $request->input('description') ?? ($job ? $job->description : 'General Intake Repair Service');
            }

            $targetStatus = $mechanicId ? 'in_progress' : ($job ? $job->status : 'queue');
            $startTimeVal = $mechanicId ? ($job && $job->start_time ? $job->start_time : Carbon::now()->format('Y-m-d H:i:s')) : ($job ? $job->start_time : null);

            if ($job) {
                $job->update([
                    'vehicle_id' => $vehicle->id,
                    'branch_id' => $branchId,
                    'mechanic_id' => $mechanicId ?: $job->mechanic_id,
                    'status' => $mechanicId ? 'in_progress' : $job->status,
                    'start_time' => ($mechanicId && !$job->start_time) ? Carbon::now()->format('Y-m-d H:i:s') : $job->start_time,
                    'repair_category' => $request->input('repair_category', 'Repair'),
                    'description' => $jobDescription,
                    'km_reached' => $request->input('km_reached') ? intval($request->input('km_reached')) : $job->km_reached,
                    'common_issues' => $request->input('common_issues'),
                    'mechanic_form_items' => $request->input('mechanic_form_items'),
                    'other_issues' => $otherIssues,
                    'notes' => $otherIssues,
                    'service_sku' => !empty($serviceSkus) ? implode(', ', $serviceSkus) : null,
                    'service_name' => !empty($serviceNames) ? implode(', ', $serviceNames) : null,
                    'labor_fee' => $totalLaborFee,
                    'other_expenses_sku' => !empty($otherSkus) ? implode(', ', $otherSkus) : $job->other_expenses_sku,
                    'other_expenses_category' => !empty($otherCategories) ? implode(', ', $otherCategories) : null,
                    'other_expenses_fee' => $totalOtherFee,
                    'end_time' => $endTimeVal ? Carbon::parse($endTimeVal) : $job->end_time,
                ]);
            } else {
                $branchObj = \App\Models\Branch::find($branchId);
                $abbrStr = ($branchObj && !empty($branchObj->abbreviation)) ? strtoupper($branchObj->abbreviation) : 'JOB';
                $branchJobCount = MaintenanceRecord::where('branch_id', $branchId)->count() + 1;
                $jobIdStr = sprintf("%s-%s-%04d", $abbrStr, date('dmY'), $branchJobCount);

                $todayDate = Carbon::now()->toDateString();
                $maxQueueToday = MaintenanceRecord::where('branch_id', $branchId)
                    ->whereDate('created_at', $todayDate)
                    ->max('daily_queue_number');
                $dailyQueueNum = ($maxQueueToday ? intval($maxQueueToday) : 0) + 1;

                $job = MaintenanceRecord::create([
                    'job_id' => $jobIdStr,
                    'vehicle_id' => $vehicle->id,
                    'branch_id' => $branchId,
                    'daily_queue_number' => $dailyQueueNum,
                    'mechanic_id' => $mechanicId,
                    'repair_category' => $request->input('repair_category', 'Repair'),
                    'description' => $jobDescription,
                    'km_reached' => $request->input('km_reached') ? intval($request->input('km_reached')) : null,
                    'common_issues' => $request->input('common_issues'),
                    'other_issues' => $otherIssues,
                    'notes' => $otherIssues,
                    'service_sku' => !empty($serviceSkus) ? implode(', ', $serviceSkus) : null,
                    'service_name' => !empty($serviceNames) ? implode(', ', $serviceNames) : null,
                    'labor_fee' => $totalLaborFee,
                    'other_expenses_sku' => !empty($otherSkus) ? implode(', ', $otherSkus) : null,
                    'other_expenses_category' => !empty($otherCategories) ? implode(', ', $otherCategories) : null,
                    'other_expenses_fee' => $totalOtherFee,
                    'repair_date' => Carbon::now()->format('Y-m-d'),
                    'check_in_time' => Carbon::now()->format('H:i:s'),
                    'start_time' => $startTimeVal,
                    'status' => $targetStatus,
                ]);
            }

            // 4. Parts Used & Inventory Deduction
            $oldParts = RecordPartUsed::where('maintenance_record_id', $job->id)->get();
            foreach ($oldParts as $op) {
                $inv = Inventory::find($op->inventory_id);
                if ($inv) {
                    $inv->increment('available_qty', $op->quantity_used);
                }
            }
            RecordPartUsed::where('maintenance_record_id', $job->id)->delete();

            $partsUsed = $request->input('parts_used', []);
            $totalPartsCost = 0;
            foreach ($partsUsed as $part) {
                $invId = $part['inventory_id'] ?? null;
                $qty = intval($part['qty'] ?? $part['quantity_used'] ?? 1);
                if ($invId) {
                    $inventory = Inventory::find($invId);
                    if ($inventory) {
                        $priceAtUse = floatval($part['price_at_use'] ?? $part['price'] ?? ($inventory->price ?? 0));
                        $isPartCharged = isset($part['is_charged']) ? (bool)$part['is_charged'] : ($priceAtUse > 0);
                        $warrantyCategory = $part['warranty_category'] ?? ($inventory->warranty_category ?? 'Unclaimable / No Warranty');
                        RecordPartUsed::create([
                            'maintenance_record_id' => $job->id,
                            'inventory_id' => $inventory->id,
                            'quantity_used' => $qty,
                            'price_at_use' => $priceAtUse,
                            'is_charged' => $isPartCharged ? 1 : 0,
                            'is_claimed' => $isPartClaimed ? 1 : 0,
                            'warranty_category' => $warrantyCategory,
                        ]);
                        $inventory->decrement('available_qty', $qty);
                        $effectiveCost = $isPartClaimed ? 0 : ($priceAtUse * $qty);
                        $totalPartsCost += $effectiveCost;
                    }
                }
            }

            // Derive repair category automatically
            $partsRecords = RecordPartUsed::where('maintenance_record_id', $job->id)->get();
            if ($partsRecords->isEmpty()) {
                $autoCategory = 'Repair';
            } elseif ($partsRecords->contains('is_claimed', true)) {
                $autoCategory = 'Claim';
            } else {
                $autoCategory = 'Stock';
            }
            $job->repair_category = $autoCategory;

            $job->parts_labor_paid = $totalPartsCost + $totalLaborFee + $totalOtherFee;
            $job->grand_total = $totalPartsCost + $totalLaborFee + $totalOtherFee;
            $job->save();

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

    public function cancelActiveJob(Request $request)
    {
        $id = $request->input('record_id') ?? $request->input('id');
        if (!$id) {
            return response()->json(['error' => 'Record ID is required.'], 422);
        }

        $record = MaintenanceRecord::find($id);
        if (!$record) {
            return response()->json(['error' => 'Maintenance record not found.'], 404);
        }

        if (!in_array(strtolower($record->status), ['in_progress', 'queue', 'pending'])) {
            return response()->json(['error' => 'Only active jobs in progress or queue can be cancelled.'], 422);
        }

        DB::beginTransaction();
        try {
            // Restore inventory stock for allocated parts
            $partsUsed = RecordPartUsed::where('maintenance_record_id', $record->id)->get();
            foreach ($partsUsed as $pu) {
                $inv = Inventory::find($pu->inventory_id);
                if ($inv) {
                    $inv->increment('available_qty', $pu->quantity_used);
                }
            }
            RecordPartUsed::where('maintenance_record_id', $record->id)->delete();

            $record->delete();
            DB::commit();

            return response()->json(['message' => 'Job record cancelled and inventory stock returned successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to cancel job: ' . $e->getMessage()], 500);
        }
    }

    public function exportMaintenanceRecords(Request $request)
    {
        $user = auth()->user();
        $filters = $request->all();

        // If Shop Admin or Inventory Admin, enforce their branch_id
        if ($user && in_array($user->role, ['shop_admin', 'inventory_admin']) && $user->branch_id) {
            $filters['branch_id'] = $user->branch_id;
        }

        $exporter = new \App\Exports\BranchMaintenanceExport($filters);

        if ($request->query('format') === 'json') {
            return response()->json($exporter->getJsonData());
        }

        return $exporter->downloadCsv();
    }
}
