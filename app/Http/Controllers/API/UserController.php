<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('branch')->orderBy('username', 'asc')->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,shop_admin,mechanic',
            'branch_id' => 'nullable|exists:branches,id',
            'display_name' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $request->username,
                'display_name' => $request->display_name ?? $request->username,
                'password_hash' => Hash::make($request->password),
                'role' => $request->role,
                'branch_id' => $request->branch_id,
            ]);

            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'CREATE',
                'target_table' => 'users',
                'record_id' => $user->id,
                'new_value' => $user->toArray(),
            ]);

            DB::commit();
            return response()->json(['message' => 'User registered successfully.', 'user_id' => $user->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create user: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'display_name' => 'nullable|string|max:100',
            'role' => 'nullable|in:super_admin,shop_admin,mechanic',
            'branch_id' => 'nullable|exists:branches,id',
            'password' => 'nullable|string|min:6',
        ]);

        $user = User::findOrFail($request->user_id);
        $oldValue = $user->toArray();

        DB::beginTransaction();
        try {
            $data = [
                'display_name' => $request->display_name ?? $user->display_name,
                'role' => $request->role ?? $user->role,
                'branch_id' => $request->branch_id ?? $user->branch_id,
            ];

            if ($request->password) {
                $data['password_hash'] = Hash::make($request->password);
            }

            $user->update($data);

            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'UPDATE',
                'target_table' => 'users',
                'record_id' => $user->id,
                'old_value' => $oldValue,
                'new_value' => $user->toArray(),
            ]);

            DB::commit();
            return response()->json(['message' => 'User updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update user.'], 500);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);
        $oldValue = $user->toArray();

        DB::beginTransaction();
        try {
            $user->delete();

            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'DELETE',
                'target_table' => 'users',
                'record_id' => $user->id,
                'old_value' => $oldValue,
            ]);

            DB::commit();
            return response()->json(['message' => 'User deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete user.'], 500);
        }
    }
}
