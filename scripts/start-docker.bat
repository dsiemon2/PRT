@echo off
REM =====================================================
REM Pecos River Traders - Docker Startup Script
REM =====================================================

echo.
echo =====================================================
echo   Pecos River Traders - Starting Docker Services
echo =====================================================
echo.

REM Navigate to project root
cd /d "%~dp0.."

echo Step 1: Copying Docker environment files...
copy /Y "PRT5\.env.docker" "PRT5\.env" > nul
copy /Y "pecos-backendadmin-api\.env.docker" "pecos-backendadmin-api\.env" > nul
copy /Y "pecos-backend-admin-site\.env.docker" "pecos-backend-admin-site\.env" > nul
echo Done.
echo.

echo Step 2: Building Docker images...
docker-compose build
echo.

echo Step 3: Starting containers...
docker-compose up -d
echo.

echo Step 4: Waiting for MySQL to be ready...
timeout /t 10 /nobreak > nul

echo Step 5: Running Laravel migrations...
echo.
echo Running PRT5 migrations...
docker-compose exec -T prt php artisan migrate --force

echo Running API migrations...
docker-compose exec -T api php artisan migrate --force

echo Running Admin migrations...
docker-compose exec -T admin php artisan migrate --force

echo.
echo =====================================================
echo   All Services Started Successfully!
echo =====================================================
echo.
echo Access your apps at:
echo   - Storefront: http://localhost:8300/
echo   - API:        http://localhost:8300/api/
echo   - Admin:      http://localhost:8300/admin/
echo   - phpMyAdmin: http://localhost:8380/
echo.
echo Docker MySQL is on port 3307 (to avoid XAMPP conflict)
echo.
echo Commands:
echo   docker-compose logs -f     View logs
echo   docker-compose down        Stop all services
echo   docker-compose restart     Restart all services
echo.
pause
