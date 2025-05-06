@echo off
:START
echo Starting MQTT Publish...

REM Pindah ke direktori tempat .bat ini berada
cd /d %~dp0

REM Jalankan perintah dan tangkap output error ke error.log
php artisan mqtt:publish 2> error_publish.log

REM Periksa apakah file error.log mengandung "MQTT error"
findstr /c:"MQTT error" error_publish.log >nul

if %errorlevel% equ 0 (
    echo Detected "MQTT error". Restarting MQTT Publish...
    timeout /t 5 >nul
    goto START
)

REM Jika perintah keluar tanpa pesan error tertentu
if %errorlevel% neq 0 (
    echo An unexpected error occurred. Restarting MQTT Publish...
    timeout /t 5 >nul
    goto START
)

echo MQTT Publish stopped.
pause
