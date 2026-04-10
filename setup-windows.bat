@echo off
setlocal enabledelayedexpansion

echo ========================================================
echo     Dr Shawarma POS System - Windows Auto Setup
echo ========================================================
echo.

:: Check prerequisites
echo [1/6] Checking required software...
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: PHP is not installed or not in PATH! Please install XAMPP and add PHP to your environment variables.
    pause
    exit /b
)
where composer >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Composer is not installed! Please install Composer.
    pause
    exit /b
)
where npm >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Node.js (npm) is not installed! Please install Node.js.
    pause
    exit /b
)
echo All required software is installed.
echo.

echo [2/6] Setting up environment configuration...
if not exist .env (
    copy .env.example .env >nul
    echo .env file created.
    php artisan key:generate
) else (
    echo .env already exists. Skipping.
)
echo.

echo [3/6] Configuring Database (SQLite)...
if not exist database mkdir database
if not exist database\database.sqlite type nul > database\database.sqlite
:: Simple powershell script to replace DB vars in .env to use SQLite
powershell -Command "(Get-Content .env) -replace '^DB_CONNECTION=.*', 'DB_CONNECTION=sqlite' -replace '^DB_DATABASE=.*', 'DB_DATABASE=%CD%\database\database.sqlite' | Set-Content .env"
echo SQLite database configured.
echo.

echo [4/6] Installing PHP packages (Composer)...
call composer install
echo.

echo [5/6] Installing UI Assets (NPM)...
call npm install
call npm run build
echo.

echo [6/6] Building Database and Default Accounts...
call php artisan migrate --force --seed
call php artisan storage:link
echo.

echo ========================================================
echo SETUP COMPLETE!
echo ========================================================
echo The POS system is ready to run.
echo.
echo Press any key to start the server now...
pause >nul

echo Starting server on your network IP...
php artisan serve --host=0.0.0.0 --port=8000
