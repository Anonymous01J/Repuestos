@echo off
setlocal

set "MYSQLDUMP=C:\xampp\mysql\bin\mysqldump.exe"
set "DB_HOST=localhost"
set "DB_USER=root"
set "DB_NAME=repuestos"
set "BACKUP_DIR=%~dp0backups"

if not exist "%MYSQLDUMP%" (
    echo ERROR: No se encontro mysqldump en "%MYSQLDUMP%".
    exit /b 1
)

for /f %%I in ('powershell -NoProfile -Command "Get-Date -Format yyyyMMdd_HHmmss"') do set "TIMESTAMP=%%I"
if not defined TIMESTAMP (
    echo ERROR: No se pudo generar la fecha para el nombre del respaldo.
    exit /b 1
)

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"
if errorlevel 1 (
    echo ERROR: No se pudo crear la carpeta "%BACKUP_DIR%".
    exit /b 1
)

set "BACKUP_FILE=%BACKUP_DIR%\%DB_NAME%_%TIMESTAMP%.sql"

"%MYSQLDUMP%" --host="%DB_HOST%" --user="%DB_USER%" --single-transaction --routines --triggers --events --default-character-set=utf8mb4 --databases "%DB_NAME%" > "%BACKUP_FILE%"
if errorlevel 1 goto backup_failed
for %%A in ("%BACKUP_FILE%") do if %%~zA EQU 0 goto backup_failed

echo Respaldo creado: "%BACKUP_FILE%"
exit /b 0

:backup_failed
if exist "%BACKUP_FILE%" del "%BACKUP_FILE%"
echo ERROR: mysqldump no pudo generar el respaldo de "%DB_NAME%".
exit /b 1