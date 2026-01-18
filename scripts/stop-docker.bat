@echo off
REM =====================================================
REM Pecos River Traders - Docker Stop Script
REM =====================================================

echo.
echo =====================================================
echo   Pecos River Traders - Stopping Docker Services
echo =====================================================
echo.

cd /d "%~dp0.."

docker-compose down

echo.
echo All services stopped.
echo.
echo To restart: run scripts\start-docker.bat
echo.
pause
