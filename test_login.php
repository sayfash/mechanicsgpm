<?php

use Illuminate\Support\Facades\Auth;

$attempt = Auth::attempt(['username' => 'superadmin', 'password' => 'password123']);
if ($attempt) {
    echo "SUCCESS\n";
} else {
    echo "FAILED\n";
    $user = \App\Models\User::where('username', 'superadmin')->first();
    if (!$user) {
        echo "User superadmin not found in DB\n";
    } else {
        echo "User found, password hash in DB: " . $user->password_hash . "\n";
        echo "Hash Check: " . (\Illuminate\Support\Facades\Hash::check('password123', $user->password_hash) ? 'true' : 'false') . "\n";
    }
}
