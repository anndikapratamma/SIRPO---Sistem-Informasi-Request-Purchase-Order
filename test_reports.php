<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Controllers\PbsController;
use App\Models\Pbs;
use App\Models\User;
use Carbon\Carbon;

class ReportTestScript
{
    public function testReportMethods()
    {
        echo "Testing Report Methods...\n";

        // Test the controller can be instantiated
        try {
            $controller = app(PbsController::class);
            echo "✓ PbsController instantiated successfully\n";
        } catch (Exception $e) {
            echo "✗ Error instantiating PbsController: " . $e->getMessage() . "\n";
            return;
        }

        // Test if the methods exist
        if (method_exists($controller, 'laporanBulanan')) {
            echo "✓ laporanBulanan method exists\n";
        } else {
            echo "✗ laporanBulanan method does not exist\n";
        }

        if (method_exists($controller, 'laporanMingguan')) {
            echo "✓ laporanMingguan method exists\n";
        } else {
            echo "✗ laporanMingguan method does not exist\n";
        }

        // Test if Pbs model can be loaded
        try {
            $pbCount = Pbs::count();
            echo "✓ Pbs model accessible, found $pbCount records\n";
        } catch (Exception $e) {
            echo "✗ Error accessing Pbs model: " . $e->getMessage() . "\n";
        }

        echo "Report methods test completed!\n";
    }
}

// Run the test
$test = new ReportTestScript();
$test->testReportMethods();
