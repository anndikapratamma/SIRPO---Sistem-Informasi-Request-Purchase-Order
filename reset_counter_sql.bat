@echo off
echo Resetting PB Counter...
cd /d C:\project\SIRPO

echo Executing SQL update...
mysql -u root -p sirpo -e "INSERT INTO pb_counters (counter_date, counter_value, created_at, updated_at) VALUES ('2025-08-15', 4589, NOW(), NOW()) ON DUPLICATE KEY UPDATE counter_value = 4589, updated_at = NOW();"

echo.
echo Counter reset complete!
echo Next PB will be 4590

pause
