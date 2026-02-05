<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\ActivityLogger;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test with admin user
$admin = User::where('role', 'admin')->first();
Auth::login($admin);

echo "=== Testing with Admin Account ===\n";
echo "Logged in as: " . $admin->name . " (role: " . $admin->role . ")\n";

// Test logging with admin
ActivityLogger::logCreate('TestModel', 1, ['test' => 'admin data']);

// Test with secretary user
$secretary = User::where('role', 'secretaire')->first();
if ($secretary) {
    Auth::login($secretary);
    echo "\n=== Testing with Secretary Account ===\n";
    echo "Logged in as: " . $secretary->name . " (role: " . $secretary->role . ")\n";
    
    // Test logging with secretary
    ActivityLogger::logCreate('TestMesse', 999, ['demandeur' => 'Test Secrétaire', 'type_messe' => 'QUOTIDIEN']);
}

// Check final results
$logCount = DB::table('activity_logs')->count();
echo "\n=== FINAL RESULTS ===\n";
echo "Total number of logs in database: " . $logCount . "\n";

$logsByRole = DB::table('activity_logs')
    ->select('user_role', DB::raw('count(*) as count'))
    ->groupBy('user_role')
    ->get();

echo "Logs by role:\n";
foreach ($logsByRole as $log) {
    echo "- " . $log->user_role . ": " . $log->count . " logs\n";
}

echo "\nAll logs:\n";
$allLogs = DB::table('activity_logs')->orderBy('created_at', 'desc')->get();
foreach ($allLogs as $log) {
    echo "- " . $log->user_name . " (" . $log->user_role . ") - " . $log->action . " " . $log->model . " at " . $log->created_at . "\n";
}