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
        $request->validate([
            'vehicle_id' => 'required|string',
            'branch_id' => 'required|integer',
            'repair_category' => 'nullable|string',
            'description' => 'required|string',
            'km_reached' => 'nullable|numeric',
            'common_issues' => 'nullable|array',
            'other_issues' => 'nullable|string',
            'parts_used' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Logic to create/update vehicle or customer can be placed here if the payload includes it.
            // For submit_mechanic_job, we assume vehicle exists.

            $nextId = MaintenanceRecord::max('id') ?? 0;
            $nextId += 1;
            $jobId = sprintf("JOB-%s-%04d", date('Ymd'), $nextId);

            $job = MaintenanceRecord::create([
                'job_id' => $jobId,
                'vehicle_id' => $request->vehicle_id,
                'branch_id' => $request->branch_id,
                'mechanic_id' => auth()->id() ?? 1,
                'repair_category' => $request->repair_category ?? 'Repair',
                'description' => $request->description,
                'km_reached' => $request->km_reached,
                'common_issues' => $request->common_issues ? json_encode($request->common_issues) : null,
                'other_issues' => $request->other_issues,
                'notes' => $request->notes,
                'status' => 'pending',
                'check_in_time' => Carbon::now()->format('H:i:s'),
                'repair_date' => Carbon::today(),
            ]);

            // Handle Parts Used
            $partsUsed = $request->parts_used ?? [];
            foreach ($partsUsed as $part) {
                $inventory = Inventory::find($part['inventory_id']);
                if ($inventory && $inventory->available_qty >= $part['qty']) {
                    RecordPartUsed::create([
                        'maintenance_record_id' => $job->id,
                        'inventory_id' => $inventory->id,
                        'quantity_used' => $part['qty'],
                        'price_at_use' => $inventory->price,
                    ]);
                    
                    // Deduct inventory
                    $inventory->decrement('available_qty', $part['qty']);
                }
            }

            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'CREATE',
                'target_table' => 'maintenance_records',
                'record_id' => $job->id,
                'new_value' => $job->toArray(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Job created successfully.', 'job_id' => $job->job_id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create job: ' . $e->getMessage()], 500);
        }
    }

    public function getCustomerMaintenanceRecords(Request $request)
    {
        $query = $request->query('query');
        $records = MaintenanceRecord::with(['vehicle', 'mechanic', 'branch', 'parts.inventory'])
            ->whereHas('vehicle', function ($q) use ($query) {
                $q->where('license_plate', 'like', "%{$query}%")
                  ->orWhere('id', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($records);
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

    public function importRecordsBatch(Request $request)
    {
        $rows = $request->input('rows', []);
        if (empty($rows)) {
            return response()->json(['error' => 'No rows provided for maintenance records import.'], 400);
        }

        $inserted = 0;
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $plate = trim($row['License Plate'] ?? $row['Vehicle Plate'] ?? '');
                $desc = trim($row['Description'] ?? $row['Work Done'] ?? '');
                $branchName = trim($row['Branch'] ?? '');

                if (!$plate && !$desc) continue;

                $branchId = auth()->user()->branch_id ?? 1;
                if ($branchName) {
                    $b = \App\Models\Branch::where('name', 'like', "%{$branchName}%")->first();
                    if ($b) $branchId = $b->id;
                }

                $vehicle = Vehicle::where('license_plate', $plate)->first();
                if (!$vehicle) {
                    $vehId = 'VEH-' . strtoupper(substr(md5(uniqid(microtime(true))), 0, 8));
                    $vehicle = Vehicle::create([
                        'id' => $vehId,
                        'license_plate' => $plate ?: 'TEMP-' . strtoupper(substr(md5(uniqid()), 0, 6)),
                        'make' => 'Generic',
                        'model' => 'EV',
                        'year' => date('Y'),
                    ]);
                }

                $nextId = MaintenanceRecord::max('id') ?? 0;
                $nextId += 1;
                $jobId = sprintf("JOB-%s-%04d", date('Ymd'), $nextId);

                MaintenanceRecord::create([
                    'job_id' => $jobId,
                    'vehicle_id' => $vehicle->id,
                    'branch_id' => $branchId,
                    'mechanic_id' => auth()->id() ?? 1,
                    'repair_category' => $row['Category'] ?? 'Repair',
                    'description' => $desc ?: 'General Maintenance',
                    'status' => 'completed',
                    'repair_date' => $row['Date'] ?? date('Y-m-d'),
                ]);

                $inserted++;
            }

            DB::commit();
            return response()->json([
                'message' => "Successfully imported {$inserted} maintenance records."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Import maintenance records failed: ' . $e->getMessage()], 500);
        }
    }
}
