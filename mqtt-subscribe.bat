@echo off
:START
echo Starting MQTT Subscriber...

REM Jalankan perintah dan tangkap output error ke error.log
php artisan mqtt:subscribe 2> error.log

REM Periksa apakah file error.log mengandung "MQTT error"
findstr /c:"MQTT error" error.log >nul

if %errorlevel% equ 0 (
    echo Detected "MQTT error". Restarting MQTT Subscriber...
    timeout /t 5 >nul
    goto START
)

REM Jika perintah keluar tanpa pesan error tertentu
if %errorlevel% neq 0 (
    echo An unexpected error occurred. Restarting MQTT Subscriber...
    timeout /t 5 >nul
    goto START
)

echo MQTT Subscriber stopped.
pause
