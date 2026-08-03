<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user')->orderBy('timestamp', 'desc')->get();
        return response()->json($logs);
    }

    public function update(Request $request)
    {
        return response()->json([
            'error' => 'Audit log records are immutable and cannot be modified under compliance policy.'
        ], 400);
    }
}
