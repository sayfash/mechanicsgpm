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
        $partsBillingSum = (float) DB::table('record_parts_used')->sum(DB::raw('price_at_use * quantity_used'));
        $serviceOptionsSum = (float) DB::table('maintenance_records')->sum(DB::raw('labor_fee + other_expenses_fee'));

        $counters = [
            'total_branches'  => Branch::count(),
            'service_revenue' => $serviceOptionsSum,
            'active_jobs'     => DB::table('maintenance_records')->where('status', '!=', 'completed')->count(),
            'completed_jobs'  => DB::table('maintenance_records')->where('status', 'completed')->count(),
            'parts_revenue'   => $partsBillingSum + $serviceOptionsSum,
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

        // Spare parts vs dates / periods
        $period = $request->query('period', 'daily'); // 'daily', 'weekly', 'monthly'
        $now = \Carbon\Carbon::now();

        if ($period === 'weekly') {
            // Weekly for current month (Week 1, Week 2, etc.)
            $startOfMonth = $now->copy()->startOfMonth();
            $endOfMonth = $now->copy()->endOfMonth();

            $rawRecords = DB::table('record_parts_used')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->get();

            $weeksData = [];
            // Divide month into 4-5 weeks
            $currentPointer = $startOfMonth->copy();
            $weekNum = 1;
            while ($currentPointer->lte($endOfMonth)) {
                $weekStart = $currentPointer->copy();
                $weekEnd = $currentPointer->copy()->endOfWeek()->min($endOfMonth);
                $label = "W{$weekNum}";

                $sum = $rawRecords->filter(function ($r) use ($weekStart, $weekEnd) {
                    $c = \Carbon\Carbon::parse($r->created_at);
                    return $c->between($weekStart, $weekEnd->copy()->endOfDay());
                })->sum('quantity_used');

                $weeksData[] = [
                    'date' => $label,
                    'date_range' => $weekStart->format('d M') . " - " . $weekEnd->format('d M'),
                    'total_parts' => (int) $sum
                ];

                $currentPointer = $weekEnd->copy()->addDay()->startOfDay();
                $weekNum++;
            }
            $sparePartsVsDates = collect($weeksData);
        } elseif ($period === 'monthly') {
            // Monthly for current year (Jan - Dec)
            $startOfYear = $now->copy()->startOfYear();
            $endOfYear = $now->copy()->endOfYear();

            $monthlySums = DB::table('record_parts_used')
                ->select(DB::raw('MONTH(created_at) as month_num'), DB::raw('SUM(quantity_used) as total_parts'))
                ->whereBetween('created_at', [$startOfYear, $endOfYear])
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->pluck('total_parts', 'month_num');

            $monthsData = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthLabel = \Carbon\Carbon::create($now->year, $m, 1)->format('M y');
                $monthsData[] = [
                    'date' => $monthLabel,
                    'total_parts' => (int) ($monthlySums[$m] ?? 0)
                ];
            }
            $sparePartsVsDates = collect($monthsData);
        } else {
            // Daily for current week (Mon - Sun)
            $startOfWeek = $now->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
            $endOfWeek = $now->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

            $dailySums = DB::table('record_parts_used')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(quantity_used) as total_parts'))
                ->whereBetween('created_at', [$startOfWeek->copy()->startOfDay(), $endOfWeek->copy()->endOfDay()])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->pluck('total_parts', 'date');

            $dailyData = [];
            $cur = $startOfWeek->copy();
            while ($cur->lte($endOfWeek)) {
                $dateStr = $cur->format('Y-m-d');
                $labelStr = $cur->format('D, d M');
                $dailyData[] = [
                    'date' => $labelStr,
                    'total_parts' => (int) ($dailySums[$dateStr] ?? 0)
                ];
                $cur->addDay();
            }
            $sparePartsVsDates = collect($dailyData);
        }

        // Mechanic record duration data
        $mechanicsList = User::where('role', 'mechanic')->get();
        $mechanicRecordTime = [];

        foreach ($mechanicsList as $index => $mech) {
            $aliasName = "Mechanic " . chr(65 + ($index % 26)) . ($index >= 26 ? (floor($index / 26) + 1) : '');
            
            // Get last 5 completed records with duration
            $completedJobs = DB::table('maintenance_records')
                ->where(function ($query) use ($mech) {
                    $query->where('mechanic_id', $mech->id);
                    if (Schema::hasColumn('maintenance_records', 'mechanic_user_id')) {
                        $query->orWhere('mechanic_user_id', $mech->id);
                    }
                })
                ->where('status', 'completed')
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->reverse()
                ->values();

            $jobsData = [];
            $totalMinutes = 0;
            $jobCount = 0;

            foreach ($completedJobs as $jIdx => $jobRec) {
                $start = \Carbon\Carbon::parse($jobRec->start_time);
                $end = \Carbon\Carbon::parse($jobRec->end_time);
                $mins = max(1, round($start->diffInMinutes($end)));
                $totalMinutes += $mins;
                $jobCount++;

                $jobsData[] = [
                    'job_label' => "JOB " . ($jIdx + 1),
                    'record_id' => $jobRec->id,
                    'duration_minutes' => $mins
                ];
            }

            $avgMinutes = $jobCount > 0 ? round($totalMinutes / $jobCount) : 0;

            $mechanicRecordTime[] = [
                'mechanic_id'   => $mech->id,
                'real_name'     => $mech->display_name ?? $mech->username,
                'alias_name'    => $aliasName,
                'avg_minutes'   => $avgMinutes,
                'total_jobs'    => $jobCount,
                'last_jobs'     => $jobsData
            ];
        }

        // Mechanic scoreboard - strictly mechanics only
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
        $items = Schema::hasTable('common_issues')
            ? \Illuminate\Support\Facades\Cache::remember('common_issues_list', 3600, function() {
                return DB::table('common_issues')->select('id', 'name', 'created_at', 'updated_at')->get()->toArray();
              })
            : [];
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
        \Illuminate\Support\Facades\Cache::forget('common_issues_list');
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
        $items = Schema::hasTable('mechanic_form_items')
            ? \Illuminate\Support\Facades\Cache::remember('mechanic_form_items_list', 3600, function() {
                return DB::table('mechanic_form_items')->select('id', 'label', 'created_at', 'updated_at')->get()->toArray();
              })
            : [];
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
        \Illuminate\Support\Facades\Cache::forget('mechanic_form_items_list');
        return response()->json(['message' => 'Mechanic checklist item added successfully.', 'id' => $id]);
    }

    public function editMechanicFormItem(Request $request)
    {
        $request->validate(['id' => 'required', 'label' => 'required|string']);
        DB::table('mechanic_form_items')->where('id', $request->id)->update([
            'label' => $request->label,
            'updated_at' => now()
        ]);
        \Illuminate\Support\Facades\Cache::forget('mechanic_form_items_list');
        return response()->json(['message' => 'Mechanic checklist item updated successfully.']);
    }

    public function deleteMechanicFormItem(Request $request)
    {
        $request->validate(['id' => 'required']);
        DB::table('mechanic_form_items')->where('id', $request->id)->delete();
        \Illuminate\Support\Facades\Cache::forget('mechanic_form_items_list');
        return response()->json(['message' => 'Mechanic checklist item deleted successfully.']);
    }

    public function getOtherServices()
    {
        $items = Schema::hasTable('other_services')
            ? \Illuminate\Support\Facades\Cache::remember('other_services_list', 3600, function() {
                return DB::table('other_services')->select('id', 'sku', 'name', 'fee', 'created_at', 'updated_at')->get()->toArray();
              })
            : [];
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
        \Illuminate\Support\Facades\Cache::forget('other_services_list');
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
        \Illuminate\Support\Facades\Cache::forget('other_services_list');
        return response()->json(['message' => 'Other service updated successfully.']);
    }

    public function deleteOtherService(Request $request)
    {
        $request->validate(['id' => 'required']);
        DB::table('other_services')->where('id', $request->id)->delete();
        \Illuminate\Support\Facades\Cache::forget('other_services_list');
        return response()->json(['message' => 'Other service deleted successfully.']);
    }

    // Service Options CRUD
    public function getServiceOptions()
    {
        $items = Schema::hasTable('service_options')
            ? \Illuminate\Support\Facades\Cache::remember('service_options_list', 3600, function() {
                $cols = ['id', 'name', 'fee'];
                if (Schema::hasColumn('service_options', 'sku')) $cols[] = 'sku';
                if (Schema::hasColumn('service_options', 'created_at')) $cols[] = 'created_at';
                if (Schema::hasColumn('service_options', 'updated_at')) $cols[] = 'updated_at';
                return DB::table('service_options')->select($cols)->get()->toArray();
              })
            : [];
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
            \Illuminate\Support\Facades\Cache::forget('service_options_list');
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
        \Illuminate\Support\Facades\Cache::forget('service_options_list');
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
        \Illuminate\Support\Facades\Cache::forget('service_options_list');
        return response()->json(['message' => 'Service option updated successfully.']);
    }

    public function deleteServiceOption(Request $request)
    {
        $request->validate(['id' => 'required']);
        DB::table('service_options')->where('id', $request->id)->delete();
        \Illuminate\Support\Facades\Cache::forget('service_options_list');
        return response()->json(['message' => 'Service option deleted successfully.']);
    }
}
