@echo off
echo [%date% %time%] Starting scheduler task... >> E:\WORKSPACE\ntu-eval\storage\logs\scheduler-windows.log

rem Chuyển đến thư mục của dự án
cd /d E:\WORKSPACE\ntu-eval
echo [%date% %time%] Changed directory to %cd% >> E:\WORKSPACE\ntu-eval\storage\logs\scheduler-windows.log

rem Đặt đường dẫn đầy đủ tới PHP
set PHP_PATH=D:\app\xampp\php\php.exe
rem Thay đổi đường dẫn PHP ở trên nếu khác trên máy bạn

rem Kiểm tra xem file PHP có tồn tại
if not exist "%PHP_PATH%" (
    echo [%date% %time%] ERROR: PHP not found at %PHP_PATH% >> E:\WORKSPACE\ntu-eval\storage\logs\scheduler-windows.log
    exit /b 1
)

rem Chạy command Laravel
echo [%date% %time%] Running command: %PHP_PATH% artisan periods:close-expired >> E:\WORKSPACE\ntu-eval\storage\logs\scheduler-windows.log
%PHP_PATH% artisan periods:close-expired >> E:\WORKSPACE\ntu-eval\storage\logs\scheduler-windows.log 2>&1

rem Lưu kết quả
if %ERRORLEVEL% EQU 0 (
    echo [%date% %time%] SUCCESS: Command executed successfully with code %ERRORLEVEL% >> E:\WORKSPACE\ntu-eval\storage\logs\scheduler-windows.log
    exit /b 0
) else (
    echo [%date% %time%] ERROR: Command failed with code %ERRORLEVEL% >> E:\WORKSPACE\ntu-eval\storage\logs\scheduler-windows.log
    exit /b %ERRORLEVEL%
)