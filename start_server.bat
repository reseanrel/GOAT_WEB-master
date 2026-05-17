@echo off
echo Starting PHP Development Server...
echo Access your application at: http://localhost:8000
echo Press Ctrl+C to stop the server
echo.
cd /d "php_version"
php -S localhost:8000 -t .
pause
