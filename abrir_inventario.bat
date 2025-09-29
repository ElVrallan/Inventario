@echo off
:: Nombre del proceso de WAMP
set "process=wampmanager.exe"

:: Verifica si WAMP ya está corriendo
tasklist /FI "IMAGENAME eq %process%" 2>NUL | find /I "%process%" >NUL
if "%ERRORLEVEL%"=="0" (
    echo WAMP ya está ejecutándose.
) else (
    echo Iniciando WAMP...
    start "" "C:\wamp64\wampmanager.exe"
    timeout /t 5 /nobreak >nul
)

:: Abre el navegador en el proyecto
start "" "http://inventario.local"
exit
