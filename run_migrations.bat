@echo off
REM Crear DB y ejecutar migraciones (Windows)

set /p DB_USER=DB user (default: root): 
set /p DB_PASS=DB password (leave blank for none): 
if "%DB_USER%"=="" set DB_USER=root
set DB_NAME=inventarioDB

echo Creando base de datos %DB_NAME%...
REM Si la contraseña está vacía, mysql -p%DB_PASS% quedará como -p y pedirá entrada; si desea pasar vacío, borre -p%DB_PASS% y use -u%DB_USER%
if "%DB_PASS%"=="" (
  mysql -u%DB_USER% -e "CREATE DATABASE IF NOT EXISTS \`%DB_NAME%\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
) else (
  mysql -u%DB_USER% -p%DB_PASS% -e "CREATE DATABASE IF NOT EXISTS \`%DB_NAME%\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
)

echo Ejecutando php artisan session:table (solo si usa SESSION_DRIVER=database)...
php artisan session:table

echo Ejecutando migraciones...
php artisan migrate --force

echo Migraciones completadas.
pause
