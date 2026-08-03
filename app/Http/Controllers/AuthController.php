<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();

            $user = Auth::user();
            return response()->json([
                'message' => 'Login successful.',
                'user' => [
                    'username' => $user->username,
                    'display_name' => $user->display_name ?? $user->username,
                    'role' => $user->role,
                    'branch_id' => $user->branch_id,
                    'profile_picture' => $user->profile_picture,
                ]
            ]);
        }

        return response()->json([
            'error' => 'Invalid username or password.'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Not authenticated.'], 401);
        }

        return response()->json([
            'logged_in' => true,
            'user' => [
                'username' => $user->username,
                'display_name' => $user->display_name ?? $user->username,
                'role' => $user->role,
                'branch_id' => $user->branch_id,
                'profile_picture' => $user->profile_picture,
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Not authenticated.'], 401);
        }

        $request->validate([
            'new_display_name' => 'required|string|max:100',
            'old_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6',
        ]);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->old_password, $user->password_hash)) {
                return response()->json(['error' => 'Incorrect old password.'], 400);
            }
            $user->password_hash = Hash::make($request->new_password);
        }

        $user->display_name = $request->new_display_name;
        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'display_name' => $user->display_name,
            'profile_picture' => $user->profile_picture
        ]);
    }

    public function forgotPassword(Request $request)
    {
        return response()->json([
            'message' => 'For security compliance, password resets must be authorized by a Super Admin. Please contact your system administrator.'
        ]);
    }
}
