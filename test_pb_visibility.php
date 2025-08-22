<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing PB Visibility Fix ===\n";

// Get admin user
$admin = App\Models\User::where('role', 'admin')->first();
if (!$admin) {
    echo "No admin user found!\n";
    exit;
}

// Get regular user
$user = App\Models\User::where('role', '!=', 'admin')->first();
if (!$user) {
    echo "No regular user found!\n";
    exit;
}

echo "Admin: {$admin->name} (ID: {$admin->id})\n";
echo "User: {$user->name} (ID: {$user->id})\n\n";

// Create PB by admin
$pbCounter = new App\Models\PbCounter();
$nomorPb = $pbCounter->getNextNumber(now());

$pb = App\Models\Pbs::create([
    'nomor_pb' => $nomorPb,
    'tanggal' => now(),
    'penginput' => $admin->name,
    'divisi' => 'E-CHANNEL',
    'keterangan' => 'Test PB dibuat oleh Admin',
    'nominal' => 1000000,
    'user_id' => $admin->id,
    'status' => 'active'
]);

echo "✓ PB created by admin: {$pb->nomor_pb}\n";
echo "  Created by: {$pb->penginput} (User ID: {$pb->user_id})\n\n";

// Test visibility for regular user
echo "=== Testing Visibility for Regular User ===\n";

// Simulate what regular user sees
$userPbs = App\Models\Pbs::with(['user', 'cancelledBy'])
    ->where(function($q) use ($user) {
        $q->where('user_id', $user->id) // PB milik user sendiri
          ->orWhereHas('user', function($subQuery) { // PB yang dibuat oleh admin
              $subQuery->where('role', 'admin');
          });
    })
    ->get();

echo "PBs visible to regular user ({$user->name}):\n";
foreach ($userPbs as $pb) {
    echo "- {$pb->nomor_pb}: {$pb->keterangan} (by {$pb->penginput})\n";
}

echo "\nTotal PBs visible to user: " . $userPbs->count() . "\n";

echo "\n=== Test Completed ===\n";
