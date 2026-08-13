<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user')->orderBy('timestamp', 'desc')->get()->map(function ($log) {
            $user = $log->user;
            $usernameStr = 'System';
            if ($user) {
                $usernameStr = $user->display_name ?: ($user->username ?: $user->email);
                if ($user->role) {
                    $usernameStr .= ' (' . str_replace('_', ' ', strtoupper($user->role)) . ')';
                }
            }

            $logArr = $log->toArray();
            $logArr['username'] = $usernameStr;
            $logArr['user_display_name'] = $user ? ($user->display_name ?? $user->username) : 'System';
            $logArr['user_role'] = $user ? $user->role : null;
            return $logArr;
        });

        return response()->json($logs);
    }

    public function update(Request $request)
    {
        return response()->json([
            'error' => 'Audit log records are immutable and cannot be modified under compliance policy.'
        ], 400);
    }
}
