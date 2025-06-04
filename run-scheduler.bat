@echo off
cd /d E:\WORKSPACE\ntu-eval
php artisan schedule:run >> storage/logs/scheduler.log 2>&1