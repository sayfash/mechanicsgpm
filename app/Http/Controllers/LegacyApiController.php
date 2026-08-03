<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LegacyApiController extends Controller
{
    public function handle(Request $request)
    {
        // Provide the PDO instance expected by the legacy API
        $pdo = DB::connection()->getPdo();
        
        // Define a constant so api.php knows it's running inside Laravel and skips db.php
        if (!defined('LARAVEL_MIGRATION')) {
            define('LARAVEL_MIGRATION', true);
        }
        
        // Require the modified legacy script if it exists
        $path = base_path('sources/api.php');
        if (file_exists($path)) {
            require $path;
        } else {
            return response()->json(['error' => 'Action not supported or route not found.'], 404);
        }
    }
}
