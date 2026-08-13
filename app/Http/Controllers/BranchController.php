<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;

class BranchController extends Controller
{
    /**
     * Get all branches
     */
    public function index(Request $request)
    {
        $branches = \Illuminate\Support\Facades\Cache::remember('branches_list', 3600, function () {
            return Branch::select('id', 'name', 'abbreviation', 'created_at', 'updated_at')->orderBy('id', 'asc')->get()->toArray();
        });

        // Guard against corrupted cache (e.g. __PHP_Incomplete_Class)
        if (!is_array($branches) || (isset($branches['__PHP_Incomplete_Class_Name']))) {
            \Illuminate\Support\Facades\Cache::forget('branches_list');
            $branches = Branch::select('id', 'name', 'abbreviation', 'created_at', 'updated_at')->orderBy('id', 'asc')->get()->toArray();
            \Illuminate\Support\Facades\Cache::put('branches_list', $branches, 3600);
        }

        return response()->json($branches);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:branches,name',
            'abbreviation' => 'nullable|string|max:10'
        ]);

        $abbreviation = $request->abbreviation ? strtoupper(trim($request->abbreviation)) : null;

        $branch = Branch::create([
            'name' => $request->name,
            'abbreviation' => $abbreviation
        ]);
        \Illuminate\Support\Facades\Cache::forget('branches_list');
        return response()->json(['message' => 'Branch created successfully.', 'branch' => $branch]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|integer|exists:branches,id',
            'name'      => 'required|string|max:100|unique:branches,name,' . $request->branch_id,
            'abbreviation' => 'nullable|string|max:10'
        ]);

        $branch = Branch::findOrFail($request->branch_id);
        $abbreviation = $request->has('abbreviation') 
            ? ($request->abbreviation ? strtoupper(trim($request->abbreviation)) : null)
            : $branch->abbreviation;

        $branch->update([
            'name' => $request->name,
            'abbreviation' => $abbreviation
        ]);

        \Illuminate\Support\Facades\Cache::forget('branches_list');
        return response()->json(['message' => 'Branch updated successfully.', 'branch' => $branch]);
    }

    public function destroy(Request $request)
    {
        $request->validate(['branch_id' => 'required|integer|exists:branches,id']);

        $branch = Branch::findOrFail($request->branch_id);
        $branch->delete();
        \Illuminate\Support\Facades\Cache::forget('branches_list');
        return response()->json(['message' => 'Branch deleted successfully.']);
    }
}
