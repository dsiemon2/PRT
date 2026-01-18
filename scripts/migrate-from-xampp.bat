@echo off
REM =====================================================
REM Pecos River Traders - Migrate Data from XAMPP MySQL
REM =====================================================
REM
REM This script exports data from your XAMPP MySQL and imports
REM it into the Docker MySQL container.
REM
REM Prerequisites:
REM   1. XAMPP MySQL is running on port 3306
REM   2. Docker containers are running (docker-compose up -d)
REM   3. MySQL is in your PATH (usually C:\xampp\mysql\bin)
REM
REM =====================================================

echo.
echo =====================================================
echo   Pecos River Traders - Database Migration
echo =====================================================
echo.

REM Set variables
set XAMPP_MYSQL_PATH=C:\xampp\mysql\bin
set DB_NAME=pecosriver
set BACKUP_FILE=pecosriver_backup.sql

REM Check if XAMPP MySQL exists
if not exist "%XAMPP_MYSQL_PATH%\mysqldump.exe" (
    echo ERROR: mysqldump not found at %XAMPP_MYSQL_PATH%
    echo Please update XAMPP_MYSQL_PATH in this script
    pause
    exit /b 1
)

echo Step 1: Exporting database from XAMPP MySQL...
echo.
"%XAMPP_MYSQL_PATH%\mysqldump" -u root %DB_NAME% > %BACKUP_FILE%

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to export database from XAMPP
    pause
    exit /b 1
)

echo Export successful! File: %BACKUP_FILE%
echo.

echo Step 2: Importing database into Docker MySQL...
echo.

REM Wait a moment for file to be fully written
timeout /t 2 /nobreak > nul

REM Import using docker exec
docker exec -i prt_mysql mysql -u root -psecret %DB_NAME% < %BACKUP_FILE%

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to import database into Docker
    echo Make sure Docker containers are running: docker-compose up -d
    pause
    exit /b 1
)

echo.
echo =====================================================
echo   Migration Complete!
echo =====================================================
echo.
echo Your XAMPP database has been copied to Docker MySQL.
echo.
echo Backup file saved at: %BACKUP_FILE%
echo.
echo You can now access your apps at:
echo   - Storefront: http://localhost:8300/
echo   - API:        http://localhost:8300/api/
echo   - Admin:      http://localhost:8300/admin/
echo   - phpMyAdmin: http://localhost:8380/
echo.
pause
