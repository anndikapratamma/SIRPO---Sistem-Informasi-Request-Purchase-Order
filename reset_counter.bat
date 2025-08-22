@echo off
cd /d C:\project\SIRPO
echo Resetting PB counter to 4589...
php artisan tinker --execute="use Illuminate\Support\Facades\DB; DB::table('pb_counters')->where('counter_date', date('Y-m-d'))->update(['counter_value' => 4589]); echo 'Counter reset to 4589. Next PB will be 4590';"
pause
