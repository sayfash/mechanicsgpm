<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function getManagementCounts(Request $request)
    {
        $counts = [
            'branches' => Branch::count(),
            'users' => User::count(),
            'customers' => Customer::count(),
            'vehicles' => Vehicle::count(),
            'common-issues' => Schema::hasTable('common_issues') ? DB::table('common_issues')->count() : 0,
            'form-items' => Schema::hasTable('mechanic_form_items') ? DB::table('mechanic_form_items')->count() : 0,
            'categories' => Schema::hasTable('sparepart_categories') ? DB::table('sparepart_categories')->count() : 0,
            'service-options' => Schema::hasTable('service_options') ? DB::table('service_options')->count() : 0,
            'other-services' => Schema::hasTable('other_services') ? DB::table('other_services')->count() : 0,
        ];

        return response()->json($counts);
    }

    public function getDashboardStats(Request $request)
    {
        $counters = [
            'total_branches' => Branch::count(),
            'total_users'    => User::count(),
            'active_jobs'    => DB::table('maintenance_records')->where('status', '!=', 'completed')->count(),
            'completed_jobs' => DB::table('maintenance_records')->where('status', 'completed')->count(),
            'parts_revenue'  => (float) DB::table('record_parts_used')->sum(DB::raw('price_at_use * quantity_used')),
        ];

        $lowStockAlerts = DB::table('inventory')
            ->join('branches', 'inventory.branch_id', '=', 'branches.id')
            ->where('inventory.available_qty', '<=', 5)
            ->select('inventory.id', 'inventory.part_name', 'inventory.available_qty', 'branches.name as branch_name')
            ->get();

        $branchPerformance = Branch::withCount(['maintenanceRecords as total_jobs'])
            ->get()
            ->map(function ($branch) {
                $revenue = (float) DB::table('record_parts_used')
                    ->join('maintenance_records', 'record_parts_used.maintenance_record_id', '=', 'maintenance_records.id')
                    ->where('maintenance_records.branch_id', $branch->id)
                    ->sum(DB::raw('record_parts_used.price_at_use * record_parts_used.quantity_used'));

                return [
                    'branch_name'   => $branch->name,
                    'total_jobs'    => $branch->total_jobs,
                    'parts_billing' => $revenue
                ];
            });

        // Spare parts vs dates (last 7 days)
        $sparePartsVsDates = DB::table('record_parts_used')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(quantity_used) as total_parts'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        // Mechanic record time
        $mechanicRecordTime = DB::table('maintenance_records')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total_records'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        // Mechanic scoreboard
        $mechanicScoreboard = User::where('role', 'mechanic')
            ->get()
            ->map(function ($mechanic) {
                $completedRecords = DB::table('maintenance_records')
                    ->where(function ($query) use ($mechanic) {
                        $query->where('mechanic_id', $mechanic->id);
                        if (Schema::hasColumn('maintenance_records', 'mechanic_user_id')) {
                            $query->orWhere('mechanic_user_id', $mechanic->id);
                        }
                    })
                    ->where('status', 'completed')
                    ->get();

                $totalJobs = $completedRecords->count();

                $recordIds = $completedRecords->pluck('id');
                $totalPartsUsed = (int) DB::table('record_parts_used')
                    ->whereIn('maintenance_record_id', $recordIds)
                    ->sum('quantity_used');

                // Average duration in minutes
                $totalMinutes = 0;
                $durationCount = 0;
                foreach ($completedRecords as $rec) {
                    if ($rec->start_time && $rec->end_time) {
                        $start = \Carbon\Carbon::parse($rec->start_time);
                        $end = \Carbon\Carbon::parse($rec->end_time);
                        $totalMinutes += max(0, $start->diffInMinutes($end));
                        $durationCount++;
                    }
                }
                $avgDuration = $durationCount > 0 ? round($totalMinutes / $durationCount) : 0;

                // Overall performance score calculation
                $score = ($totalJobs * 10) + ($totalPartsUsed * 2) + ($avgDuration > 0 ? max(0, 50 - round($avgDuration / 2)) : 0);

                return [
                    'mechanic_name'        => $mechanic->display_name ?? $mechanic->username,
                    'total_jobs'           => $totalJobs,
                    'total_parts_used'     => $totalPartsUsed,
                    'avg_duration_minutes' => $avgDuration,
                    'score'                => $score
                ];
            })
            ->sortByDesc('score')
            ->values();

        return response()->json([
            'counters'               => $counters,
            'low_stock_alerts'       => $lowStockAlerts,
            'branch_performance'     => $branchPerformance,
            'spare_parts_vs_dates'   => $sparePartsVsDates,
            'mechanic_record_time'   => $mechanicRecordTime,
            'mechanic_scoreboard'    => $mechanicScoreboard,
        ]);
    }

    public function getCommonIssues()
    {
        $items = Schema::hasTable('common_issues') ? DB::table('common_issues')->get() : [];
        return response()->json($items);
    }

    public function addCommonIssue(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        $id = DB::table('common_issues')->insertGetId([
            'name' => $request->name,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return response()->json(['message' => 'Common issue added successfully.', 'id' => $id]);
    }

    public function editCommonIssue(Request $request)
    {
        $request->validate(['id' => 'required', 'name' => 'required|string']);
        DB::table('common_issues')->where('id', $request->id)->update([
            'name' => $request->name,
            'updated_at' => now()
        ]);
        return response()->json(['message' => 'Common issue updated successfully.']);
    }

    public function deleteCommonIssue(Request $request)
    {
        $request->validate(['id' => 'required']);
        DB::table('common_issues')->where('id', $request->id)->delete();
        return response()->json(['message' => 'Common issue deleted successfully.']);
    }

    public function getMechanicFormItems()
    {
        $items = Schema::hasTable('mechanic_form_items') ? DB::table('mechanic_form_items')->get() : [];
        return response()->json($items);
    }

    public function addMechanicFormItem(Request $request)
    {
        $request->validate(['label' => 'required|string']);
        $id = DB::table('mechanic_form_items')->insertGetId([
            'label' => $request->label,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return response()->json(['message' => 'Mechanic checklist item added successfully.', 'id' => $id]);
    }

    public function editMechanicFormItem(Request $request)
    {
        $request->validate(['id' => 'required', 'label' => 'required|string']);
        DB::table('mechanic_form_items')->where('id', $request->id)->update([
            'label' => $request->label,
            'updated_at' => now()
        ]);
        return response()->json(['message' => 'Mechanic checklist item updated successfully.']);
    }

    public function deleteMechanicFormItem(Request $request)
    {
        $request->validate(['id' => 'required']);
        DB::table('mechanic_form_items')->where('id', $request->id)->delete();
        return response()->json(['message' => 'Mechanic checklist item deleted successfully.']);
    }

    public function getOtherServices()
    {
        $items = Schema::hasTable('other_services') ? DB::table('other_services')->get() : [];
        return response()->json($items);
    }

    public function addOtherService(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'sku'  => 'nullable|string',
            'fee'  => 'nullable|numeric'
        ]);

        $data = [
            'name'       => $request->name,
            'fee'        => $request->fee ?? 0.00,
            'created_at' => now(),
            'updated_at' => now()
        ];

        if (Schema::hasColumn('other_services', 'sku')) {
            $data['sku'] = $request->sku ?? null;
        }

        $id = DB::table('other_services')->insertGetId($data);
        return response()->json(['message' => 'Other service added successfully.', 'id' => $id]);
    }

    public function editOtherService(Request $request)
    {
        $request->validate([
            'id'   => 'required',
            'name' => 'required|string',
            'sku'  => 'nullable|string',
            'fee'  => 'nullable|numeric'
        ]);

        $data = [
            'name'       => $request->name,
            'fee'        => $request->fee ?? 0.00,
            'updated_at' => now()
        ];

        if (Schema::hasColumn('other_services', 'sku')) {
            $data['sku'] = $request->sku ?? null;
        }

        DB::table('other_services')->where('id', $request->id)->update($data);
        return response()->json(['message' => 'Other service updated successfully.']);
    }

    public function deleteOtherService(Request $request)
    {
        $request->validate(['id' => 'required']);
        DB::table('other_services')->where('id', $request->id)->delete();
        return response()->json(['message' => 'Other service deleted successfully.']);
    }

    // Service Options CRUD
    public function getServiceOptions()
    {
        $items = Schema::hasTable('service_options') ? DB::table('service_options')->get() : [];
        return response()->json($items);
    }

    public function addServiceOption(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'sku'  => 'nullable|string',
            'fee'  => 'nullable|numeric'
        ]);

        $existing = DB::table('service_options')->where('name', $request->name)->first();

        if ($existing) {
            $data = ['fee' => $request->fee ?? $existing->fee];
            if (Schema::hasColumn('service_options', 'updated_at')) {
                $data['updated_at'] = now();
            }
            if (Schema::hasColumn('service_options', 'sku') && $request->filled('sku')) {
                $data['sku'] = $request->sku;
            }
            DB::table('service_options')->where('id', $existing->id)->update($data);
            return response()->json(['message' => 'Service option price/details updated successfully.', 'id' => $existing->id]);
        }

        $data = [
            'name' => $request->name,
            'fee'  => $request->fee ?? 0.00,
        ];

        if (Schema::hasColumn('service_options', 'created_at')) {
            $data['created_at'] = now();
        }
        if (Schema::hasColumn('service_options', 'updated_at')) {
            $data['updated_at'] = now();
        }

        if (Schema::hasColumn('service_options', 'sku')) {
            $data['sku'] = $request->sku ?? null;
        }

        $id = DB::table('service_options')->insertGetId($data);
        return response()->json(['message' => 'Service option added successfully.', 'id' => $id]);
    }

    public function editServiceOption(Request $request)
    {
        $request->validate([
            'id'   => 'required',
            'name' => 'required|string',
            'sku'  => 'nullable|string',
            'fee'  => 'nullable|numeric'
        ]);

        $data = [
            'name' => $request->name,
            'fee'  => $request->fee ?? 0.00,
        ];

        if (Schema::hasColumn('service_options', 'updated_at')) {
            $data['updated_at'] = now();
        }

        if (Schema::hasColumn('service_options', 'sku')) {
            $data['sku'] = $request->sku ?? null;
        }

        DB::table('service_options')->where('id', $request->id)->update($data);
        return response()->json(['message' => 'Service option updated successfully.']);
    }

    public function deleteServiceOption(Request $request)
    {
        $request->validate(['id' => 'required']);
        DB::table('service_options')->where('id', $request->id)->delete();
        return response()->json(['message' => 'Service option deleted successfully.']);
    }
}
