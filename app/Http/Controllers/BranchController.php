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
        $branches = Branch::orderBy('id', 'asc')->get();
        return response()->json($branches);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:branches,name']);

        $branch = Branch::create(['name' => $request->name]);
        return response()->json(['message' => 'Branch created successfully.', 'branch' => $branch]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|integer|exists:branches,id',
            'name'      => 'required|string|max:100|unique:branches,name,' . $request->branch_id
        ]);

        $branch = Branch::findOrFail($request->branch_id);
        $branch->update(['name' => $request->name]);

        return response()->json(['message' => 'Branch renamed successfully.', 'branch' => $branch]);
    }

    public function destroy(Request $request)
    {
        $request->validate(['branch_id' => 'required|integer|exists:branches,id']);

        $branch = Branch::findOrFail($request->branch_id);
        $branch->delete();

        return response()->json(['message' => 'Branch deleted successfully.']);
    }
}
