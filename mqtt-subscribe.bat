@echo off
setlocal enabledelayedexpansion

:START
echo [%DATE% %TIME%] Starting MQTT Subscriber...

REM Pindah ke direktori skrip ini
cd /d %~dp0

REM Jalankan command, log stdout dan stderr
php artisan mqtt:subscribe > mqtt.log 2> error.log

REM Cek exit code Laravel (exit(1) kalau error)
if %errorlevel% neq 0 (
    echo [%DATE% %TIME%] MQTT Subscriber exited with error code %errorlevel%.
    echo Restarting in 5 seconds...
    timeout /t 5 >nul
    goto START
)

REM Kalau keluar normal (exit(0)), tetap restart (misal untuk kasus update manual)
echo [%DATE% %TIME%] MQTT Subscriber exited normally. Restarting...
timeout /t 5 >nul
goto START
