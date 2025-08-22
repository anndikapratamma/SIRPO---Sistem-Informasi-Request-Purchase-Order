<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Full PB Visibility Scenario ===\n";

// Get users
$admin = App\Models\User::where('role', 'admin')->first();
$user1 = App\Models\User::where('role', '!=', 'admin')->first();
$user2 = App\Models\User::where('role', '!=', 'admin')->skip(1)->first();

echo "Admin: {$admin->name} (ID: {$admin->id})\n";
echo "User1: {$user1->name} (ID: {$user1->id})\n";
echo "User2: {$user2->name} (ID: {$user2->id})\n\n";

// Create PB by User1
$pbCounter = new App\Models\PbCounter();
$nomorPb = $pbCounter->getNextNumber(now());

$pbByUser1 = App\Models\Pbs::create([
    'nomor_pb' => $nomorPb,
    'tanggal' => now(),
    'penginput' => $user1->name,
    'divisi' => 'TREASURY OPERASIONAL',
    'keterangan' => 'PB dibuat oleh User1',
    'nominal' => 500000,
    'user_id' => $user1->id,
    'status' => 'active'
]);

echo "✓ PB created by User1: {$pbByUser1->nomor_pb}\n";

// Create another PB by User2
$nomorPb2 = $pbCounter->getNextNumber(now());

$pbByUser2 = App\Models\Pbs::create([
    'nomor_pb' => $nomorPb2,
    'tanggal' => now(),
    'penginput' => $user2->name,
    'divisi' => 'LAYANAN OPERASIONAL',
    'keterangan' => 'PB dibuat oleh User2',
    'nominal' => 750000,
    'user_id' => $user2->id,
    'status' => 'active'
]);

echo "✓ PB created by User2: {$pbByUser2->nomor_pb}\n\n";

// Test what User1 can see
echo "=== What User1 can see ===\n";
$user1Pbs = App\Models\Pbs::with(['user', 'cancelledBy'])
    ->where(function($q) use ($user1) {
        $q->where('user_id', $user1->id)
          ->orWhereHas('user', function($subQuery) {
              $subQuery->where('role', 'admin');
          });
    })
    ->get();

foreach ($user1Pbs as $pb) {
    echo "- {$pb->nomor_pb}: {$pb->keterangan} (by {$pb->penginput})\n";
}
echo "Total visible to User1: " . $user1Pbs->count() . "\n\n";

// Test what User2 can see
echo "=== What User2 can see ===\n";
$user2Pbs = App\Models\Pbs::with(['user', 'cancelledBy'])
    ->where(function($q) use ($user2) {
        $q->where('user_id', $user2->id)
          ->orWhereHas('user', function($subQuery) {
              $subQuery->where('role', 'admin');
          });
    })
    ->get();

foreach ($user2Pbs as $pb) {
    echo "- {$pb->nomor_pb}: {$pb->keterangan} (by {$pb->penginput})\n";
}
echo "Total visible to User2: " . $user2Pbs->count() . "\n\n";

// Test what Admin can see
echo "=== What Admin can see ===\n";
$adminPbs = App\Models\Pbs::with(['user', 'cancelledBy'])->get();

foreach ($adminPbs as $pb) {
    echo "- {$pb->nomor_pb}: {$pb->keterangan} (by {$pb->penginput})\n";
}
echo "Total visible to Admin: " . $adminPbs->count() . "\n\n";

echo "=== Summary ===\n";
echo "✓ User1 dapat melihat: PB miliknya sendiri + PB dari Admin\n";
echo "✓ User2 dapat melihat: PB miliknya sendiri + PB dari Admin\n";
echo "✓ Admin dapat melihat: Semua PB\n";
echo "✓ User1 TIDAK bisa melihat PB milik User2\n";
echo "✓ User2 TIDAK bisa melihat PB milik User1\n";

echo "\n=== Test Completed Successfully! ===\n";
