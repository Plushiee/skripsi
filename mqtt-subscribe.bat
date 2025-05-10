@echo off
setlocal enabledelayedexpansion

:START
echo [%DATE% %TIME%] Starting MQTT Subscriber...

REM Pindah ke direktori skrip ini
cd /d %~dp0

:loop
echo [%DATE% %TIME%] Starting MQTT Subscriber...
echo Menjalankan php artisan mqtt:subscribe...
php artisan mqtt:subscribe
echo Menunggu 30 menit sebelum restart...
timeout /t 1800
goto loop
