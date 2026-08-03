<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventoryController extends Controller
{
    /**
     * Pair input category with Data Management sparepart_categories.
     * If matched (case-insensitive), returns the registered category name;
     * otherwise defaults to 'General'.
     */
    private function pairCategory(?string $inputCategory): string
    {
        $inputCategory = trim((string)$inputCategory);
        if (!$inputCategory) {
            return 'General';
        }

        if (Schema::hasTable('sparepart_categories')) {
            $matched = DB::table('sparepart_categories')
                ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($inputCategory)])
                ->value('name');

            if ($matched) {
                return $matched;
            }
        }

        return 'General';
    }

    public function index(Request $request)
    {
        $branchId = $request->query('branch_id');
        $query = Inventory::with('branch')->orderBy('part_name', 'asc');
        
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|integer',
            'sku' => 'required|string|max:50',
            'part_name' => 'required|string|max:100',
            'unit' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:100',
            'connected_service' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'available_qty' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $category = $this->pairCategory($request->category);

        // Check if SKU or Part Name exists for this branch (Case-insensitive & whitespace trimmed)
        $exists = Inventory::where('branch_id', $request->branch_id)
            ->where(function ($q) use ($request) {
                $q->whereRaw('LOWER(TRIM(sku)) = ?', [strtolower(trim($request->sku))])
                  ->orWhereRaw('LOWER(TRIM(part_name)) = ?', [strtolower(trim($request->part_name))]);
            })
            ->first();

        DB::beginTransaction();
        try {
            if ($exists) {
                $oldValue = $exists->toArray();
                $exists->update([
                    'sku'               => trim($request->sku) ?: $exists->sku,
                    'part_name'         => trim($request->part_name) ?: $exists->part_name,
                    'unit'              => $request->unit ?? $exists->unit,
                    'category'          => $category,
                    'connected_service' => $request->connected_service ?? $exists->connected_service,
                    'description'       => $request->description ?? $exists->description,
                    'available_qty'     => $request->available_qty,
                    'price'             => $request->price,
                ]);

                AuditLog::create([
                    'user_id'      => auth()->id() ?? 1,
                    'action_type'  => 'UPDATE',
                    'target_table' => 'inventory',
                    'record_id'    => $exists->id,
                    'old_value'    => $oldValue,
                    'new_value'    => $exists->toArray(),
                ]);

                DB::commit();
                return response()->json(['message' => 'Inventory item updated successfully.']);
            } else {
                $inventory = Inventory::create([
                    'branch_id'         => $request->branch_id,
                    'sku'               => trim($request->sku),
                    'part_name'         => trim($request->part_name),
                    'unit'              => $request->unit ?? 'Pcs',
                    'category'          => $category,
                    'connected_service' => $request->connected_service,
                    'description'       => $request->description,
                    'available_qty'     => $request->available_qty,
                    'price'             => $request->price,
                ]);

                AuditLog::create([
                    'user_id' => auth()->id() ?? 1,
                    'action_type' => 'CREATE',
                    'target_table' => 'inventory',
                    'record_id' => $inventory->id,
                    'new_value' => $inventory->toArray(),
                ]);

                DB::commit();
                return response()->json(['message' => 'Inventory item added successfully.']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to add inventory: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $id = $request->id ?? $request->inventory_id;
        if (!$id) {
            return response()->json(['error' => 'The inventory ID is required.'], 422);
        }

        $inventory = Inventory::find($id);
        if (!$inventory) {
            return response()->json(['error' => 'Inventory item not found.'], 404);
        }

        $category = $this->pairCategory($request->category ?? $inventory->category);

        $request->merge([
            'id' => $id,
            'sku' => $request->sku ?? $inventory->sku,
            'part_name' => $request->part_name ?? $inventory->part_name,
            'category' => $category,
            'unit' => $request->unit ?? $inventory->unit ?? 'Pcs',
            'available_qty' => $request->has('available_qty') ? $request->available_qty : $inventory->available_qty,
            'price' => $request->has('price') ? $request->price : $inventory->price,
        ]);

        $request->validate([
            'id' => 'required|integer|exists:inventory,id',
            'sku' => 'required|string|max:50',
            'part_name' => 'required|string|max:100',
            'unit' => 'nullable|string|max:20',
            'category' => 'required|string|max:100',
            'connected_service' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'available_qty' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);
        
        // Ensure SKU uniqueness within the branch if SKU changed
        if ($inventory->sku !== $request->sku) {
            $exists = Inventory::where('branch_id', $inventory->branch_id)
                ->where('sku', $request->sku)
                ->first();
                
            if ($exists) {
                return response()->json(['error' => 'This SKU is already used by another item in this branch.'], 400);
            }
        }

        $oldValue = $inventory->toArray();

        DB::beginTransaction();
        try {
            $inventory->update([
                'sku' => $request->sku,
                'part_name' => $request->part_name,
                'unit' => $request->unit ?? 'Pcs',
                'category' => $category,
                'connected_service' => $request->connected_service,
                'description' => $request->description,
                'available_qty' => $request->available_qty,
                'price' => $request->price,
            ]);

            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'UPDATE',
                'target_table' => 'inventory',
                'record_id' => $inventory->id,
                'old_value' => $oldValue,
                'new_value' => $inventory->toArray(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Inventory item updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update inventory.'], 500);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:inventory,id'
        ]);

        $inventory = Inventory::find($request->id);
        
        $isUsed = \App\Models\RecordPartUsed::where('inventory_id', $inventory->id)->exists();
        if ($isUsed) {
            return response()->json(['error' => 'Cannot delete inventory item because it has been used in past maintenance records.'], 400);
        }

        $oldValue = $inventory->toArray();

        DB::beginTransaction();
        try {
            $inventory->delete();

            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'DELETE',
                'target_table' => 'inventory',
                'record_id' => $inventory->id,
                'old_value' => $oldValue,
            ]);

            DB::commit();
            return response()->json(['message' => 'Inventory item deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete inventory.'], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        $branchId = $request->input('branch_id');

        if ($branchId && empty($ids)) {
            $items = Inventory::where('branch_id', $branchId)->get();
        } else if (!empty($ids)) {
            $items = Inventory::whereIn('id', $ids)->get();
        } else {
            return response()->json(['error' => 'No items selected or branch specified for bulk delete.'], 400);
        }

        if ($items->isEmpty()) {
            return response()->json(['message' => 'No inventory items found to delete.']);
        }

        $deletedCount = 0;
        $skippedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $isUsed = \App\Models\RecordPartUsed::where('inventory_id', $item->id)->exists();
                if ($isUsed) {
                    $skippedCount++;
                    continue;
                }

                $oldValue = $item->toArray();
                $itemId = $item->id;
                $item->delete();

                AuditLog::create([
                    'user_id'      => auth()->id() ?? 1,
                    'action_type'  => 'BULK_DELETE',
                    'target_table' => 'inventory',
                    'record_id'    => $itemId,
                    'old_value'    => $oldValue,
                ]);

                $deletedCount++;
            }

            DB::commit();
            $msg = "Bulk delete complete. {$deletedCount} inventory items deleted.";
            if ($skippedCount > 0) {
                $msg .= " ({$skippedCount} items skipped because they are used in maintenance records).";
            }
            return response()->json(['message' => $msg, 'deleted_count' => $deletedCount, 'skipped_count' => $skippedCount]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Bulk delete failed: ' . $e->getMessage()], 500);
        }
    }

    public function getCategories()
    {
        if (Schema::hasTable('sparepart_categories')) {
            $defaultMainCategories = ['General', 'Bearing', 'Brake Pad', 'Shock Breaker', 'Tire'];
            foreach ($defaultMainCategories as $mainCat) {
                $exists = DB::table('sparepart_categories')->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($mainCat)])->exists();
                if (!$exists) {
                    DB::table('sparepart_categories')->insert([
                        'name' => $mainCat,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            $categories = DB::table('sparepart_categories')->select('id', 'name')->orderBy('id', 'asc')->get();
        } else {
            $categories = Inventory::select('category as name')->distinct()->get()->map(function ($c, $idx) {
                return ['id' => $idx + 1, 'name' => $c->name];
            });
        }
        return response()->json($categories);
    }

    public function getHistory(Request $request)
    {
        $history = \App\Models\RecordPartUsed::with(['inventory', 'maintenanceRecord.vehicle'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($history);
    }

    public function importBatch(Request $request)
    {
        $rows = $request->input('rows', []);
        if (empty($rows)) {
            return response()->json(['error' => 'No rows provided for import.'], 400);
        }

        $inserted = 0;
        $updated = 0;

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $sku = trim($row['SKU'] ?? $row['sku'] ?? '');
                $partName = trim($row['Sparepart Name'] ?? $row['part_name'] ?? $row['Name'] ?? '');
                $branchName = trim($row['Branch'] ?? $row['branch'] ?? '');

                if (!$sku && !$partName && !isset($row['Price']) && !isset($row['price']) && !isset($row['Available Qty']) && !isset($row['available_qty'])) {
                    continue;
                }

                $branch = null;
                if ($branchName) {
                    $branch = \App\Models\Branch::where('name', 'like', "%{$branchName}%")->first();
                    if (!$branch) {
                        $branch = \App\Models\Branch::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($branchName)])->first();
                    }
                }
                $branchId = $branch ? $branch->id : (auth()->user()->branch_id ?? 1);

                $connService = trim($row['Connected Service'] ?? $row['connected_service'] ?? '');
                $servicePrice = floatval($row['Service Price'] ?? $row['service_price'] ?? 0);
                if ($connService) {
                    $existingService = DB::table('service_options')->where('name', $connService)->first();
                    $soData = ['fee' => $servicePrice > 0 ? $servicePrice : ($existingService ? $existingService->fee : 0)];
                    if (Schema::hasColumn('service_options', 'updated_at')) {
                        $soData['updated_at'] = now();
                    }

                    if ($existingService) {
                        DB::table('service_options')->where('id', $existingService->id)->update($soData);
                    } else {
                        $soData['name'] = $connService;
                        if (Schema::hasColumn('service_options', 'created_at')) {
                            $soData['created_at'] = now();
                        }
                        DB::table('service_options')->insert($soData);
                    }
                }

                if (!$sku) {
                    $sku = 'SKU-' . strtoupper(substr(md5($partName . '_' . $branchId . '_' . $index), 0, 8));
                }

                $inventory = Inventory::where('branch_id', $branchId)
                    ->whereRaw('LOWER(TRIM(sku)) = ?', [strtolower($sku)])
                    ->first();

                $rawCategory = $row['Category'] ?? $row['category'] ?? ($inventory ? $inventory->category : '');
                $category = $this->pairCategory($rawCategory);

                $data = [
                    'branch_id'         => $branchId,
                    'sku'               => $sku,
                    'part_name'         => $partName ?: ($inventory ? $inventory->part_name : 'Unnamed Part'),
                    'unit'              => $row['Unit'] ?? $row['unit'] ?? ($inventory ? $inventory->unit : 'Pcs'),
                    'category'          => $category,
                    'connected_service' => $connService ?: ($inventory ? $inventory->connected_service : null),
                    'description'       => $row['Description'] ?? $row['description'] ?? ($inventory ? $inventory->description : null),
                    'available_qty'     => intval($row['Available Qty'] ?? $row['available_qty'] ?? $row['Qty'] ?? $row['qty'] ?? 0),
                    'price'             => floatval($row['Price'] ?? $row['price'] ?? 0),
                ];

                if ($inventory) {
                    $inventory->update($data);
                    $updated++;
                } else {
                    Inventory::create($data);
                    $inserted++;
                }
            }

            DB::commit();
            return response()->json([
                'message' => "Successfully processed batch! {$inserted} new inventory items created, {$updated} existing items updated."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Import batch failed: ' . $e->getMessage()], 500);
        }
    }

    public function addCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $name = trim($request->name);

        if (Schema::hasTable('sparepart_categories')) {
            $existing = DB::table('sparepart_categories')->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($name)])->first();
            if ($existing) {
                return response()->json(['error' => 'Category already exists.'], 400);
            }
            $id = DB::table('sparepart_categories')->insertGetId([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return response()->json(['message' => 'Category created successfully.', 'id' => $id]);
        }

        return response()->json(['message' => 'Category created successfully.']);
    }

    public function editCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required|string|max:100'
        ]);

        $name = trim($request->name);
        $catId = $request->category_id;

        if (Schema::hasTable('sparepart_categories')) {
            $oldCategory = DB::table('sparepart_categories')->where('id', $catId)->first();
            if ($oldCategory && $oldCategory->name !== $name) {
                DB::table('sparepart_categories')->where('id', $catId)->update([
                    'name' => $name,
                    'updated_at' => now(),
                ]);

                // Update inventory items with old category name
                Inventory::where('category', $oldCategory->name)->update(['category' => $name]);
            }
        }

        return response()->json(['message' => 'Category updated successfully.']);
    }

    public function deleteCategory(Request $request)
    {
        $request->validate(['category_id' => 'required']);
        $catId = $request->category_id;

        if (Schema::hasTable('sparepart_categories')) {
            $oldCategory = DB::table('sparepart_categories')->where('id', $catId)->first();
            if ($oldCategory) {
                if (strtolower(trim($oldCategory->name)) === 'general') {
                    return response()->json(['error' => 'The default General category cannot be deleted.'], 400);
                }

                DB::table('sparepart_categories')->where('id', $catId)->delete();

                // Reassign affected inventory items to 'General'
                Inventory::where('category', $oldCategory->name)->update(['category' => 'General']);
            }
        }

        return response()->json(['message' => 'Category deleted successfully.']);
    }
}
