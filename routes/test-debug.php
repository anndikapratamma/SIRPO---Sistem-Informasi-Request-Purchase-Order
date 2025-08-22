<?php
// Test direct database access
use Illuminate\Support\Facades\Route;

Route::get('/test-pb-data', function () {
    $pbs = \App\Models\Pbs::all();
    $counter = \App\Models\PbCounter::all();

    return response()->json([
        'pbs_count' => $pbs->count(),
        'pbs_data' => $pbs->toArray(),
        'counter_data' => $counter->toArray(),
        'db_connection' => \DB::connection()->getDatabaseName()
    ]);
});
