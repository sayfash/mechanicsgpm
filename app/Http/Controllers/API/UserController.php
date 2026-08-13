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
        $users = User::with('branch')->orderBy('username', 'asc')->get()->map(function ($u) {
            $u->branch_name = $u->branch ? $u->branch->name : null;
            return $u;
        });
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,manager,shop_admin,inventory_admin,mechanic',
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
                'user_id'         => auth()->id() ?? 1,
                'action_type'     => 'CREATE',
                'target_table'    => 'users',
                'module_location' => 'Data Management > User Management',
                'action_summary'  => "Created user '{$user->username}' with role " . strtoupper($user->role),
                'record_id'       => $user->id,
                'new_value'       => $user->toArray(),
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
            'username' => 'nullable|string|max:50|unique:users,username,' . $request->user_id,
            'display_name' => 'nullable|string|max:100',
            'role' => 'nullable|in:super_admin,manager,shop_admin,inventory_admin,mechanic',
            'branch_id' => 'nullable',
            'password' => 'nullable|string|min:6',
        ]);

        $user = User::findOrFail($request->user_id);
        $oldValue = $user->toArray();

        DB::beginTransaction();
        try {
            $data = [];

            if ($request->has('username') && !empty($request->username)) {
                $data['username'] = $request->username;
            }
            if ($request->has('display_name')) {
                $data['display_name'] = $request->display_name ?? $request->username ?? $user->display_name;
            }
            if ($request->has('role') && !empty($request->role)) {
                $data['role'] = $request->role;
            }
            if ($request->exists('branch_id')) {
                $data['branch_id'] = $request->branch_id ? (int)$request->branch_id : null;
            }

            if ($request->filled('password')) {
                $data['password_hash'] = Hash::make($request->password);
            }

            $user->update($data);

            AuditLog::create([
                'user_id'         => auth()->id() ?? 1,
                'action_type'     => 'UPDATE',
                'target_table'    => 'users',
                'module_location' => 'Data Management > User Management',
                'action_summary'  => "Updated profile/role for user '{$user->username}'",
                'record_id'       => $user->id,
                'old_value'       => $oldValue,
                'new_value'       => $user->fresh()->toArray(),
            ]);

            DB::commit();
            return response()->json(['message' => 'User updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update user: ' . $e->getMessage()], 500);
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
                'user_id'         => auth()->id() ?? 1,
                'action_type'     => 'DELETE',
                'target_table'    => 'users',
                'module_location' => 'Data Management > User Management',
                'action_summary'  => "Deleted user account '{$user->username}'",
                'record_id'       => $user->id,
                'old_value'       => $oldValue,
            ]);

            DB::commit();
            return response()->json(['message' => 'User deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete user.'], 500);
        }
    }
}
