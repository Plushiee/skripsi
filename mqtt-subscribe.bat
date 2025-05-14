@echo off
setlocal enabledelayedexpansion

:START
echo [%DATE% %TIME%] Starting MQTT Subscriber...

REM Pindah ke direktori skrip ini
cd /d %~dp0

:loop
php artisan mqtt:subscribe
