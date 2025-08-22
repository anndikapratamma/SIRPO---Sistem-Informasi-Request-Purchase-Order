@echo off
title Reset PB Counter
cd /d C:\project\SIRPO

echo ========================================
echo    RESET PB COUNTER TO 4590
echo ========================================
echo.

echo Running PHP script...
php direct_update.php

echo.
echo ========================================
echo    RESET COMPLETED
echo ========================================
echo.
echo Your next PB will be numbered 4590!
echo.

pause
