<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$publicCssDir = __DIR__ . '/assets/css';
$publicJsDir = __DIR__ . '/assets/js';

if (!is_dir($publicCssDir)) {
    mkdir($publicCssDir, 0777, true);
}
if (!is_dir($publicJsDir)) {
    mkdir($publicJsDir, 0777, true);
}

// Copy style.css
copy(__DIR__ . '/../sources/assets/css/style.css', $publicCssDir . '/style.css');

// Read and update index.html -> welcome.blade.php
$htmlContent = file_get_contents(__DIR__ . '/../sources/index.html');
$htmlContent = str_replace('href="assets/css/style.css"', 'href="{{ asset(\'assets/css/style.css\') }}"', $htmlContent);
$htmlContent = str_replace('src="assets/js/app.js"', 'src="{{ asset(\'assets/js/app.js\') }}"', $htmlContent);
file_put_contents(__DIR__ . '/../resources/views/welcome.blade.php', $htmlContent);

// Read and update app.js -> public/assets/js/app.js
$jsContent = file_get_contents(__DIR__ . '/../sources/assets/js/app.js');
$jsContent = str_replace('api.php', '/api/legacy', $jsContent);
file_put_contents($publicJsDir . '/app.js', $jsContent);

// Update api.php to not require db.php if running under Laravel
$apiContent = file_get_contents(__DIR__ . '/../sources/api.php');
$apiContent = str_replace("require_once 'db.php';", "if (!defined('LARAVEL_MIGRATION')) { require_once 'db.php'; }", $apiContent);
file_put_contents(__DIR__ . '/../sources/api.php', $apiContent);

echo "Migration script completed successfully! You can delete this file now.";
