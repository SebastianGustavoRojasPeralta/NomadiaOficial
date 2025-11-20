<#
run-local.ps1
Script en PowerShell para preparar y arrancar el proyecto Nomadia localmente.
Coloca este archivo en `backend` y ejecútalo con permisos adecuados.

Funciones que realiza:
- Comprueba rutas comunes de XAMPP (`mysql.exe`, `php.exe`).
- Crea la base de datos `nomadia` si no existe.
- Importa `database/schema_and_seed.sql` dentro de la DB.
- Ejecuta `composer install` si Composer está disponible.
- Abre una ventana PowerShell para levantar el backend con `php -S localhost:8000 -t public`.
- Abre otra ventana PowerShell para instalar y arrancar el frontend (`npm install` + `npm run dev`).

USO (PowerShell como Administrador recomendado):
powershell -ExecutionPolicy Bypass -File 'C:\xampp\htdocs\Nomadia\NomadiaOficial\backend\run-local.ps1'
#>

param(
    [switch]$AutoConfirm
)

Set-StrictMode -Version Latest

function Prompt-Continue($message) {
    if ($AutoConfirm) { return $true }
    Write-Host $message -ForegroundColor Yellow
    $ans = Read-Host "Continuar? (s/N)"
    return ($ans -match '^[sS]')
}

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$projectRoot = Split-Path -Parent $scriptDir

Write-Host "Script ejecutándose desde: $scriptDir`n" -ForegroundColor Cyan

# Detectar mysql.exe y php.exe en rutas comunes de XAMPP
$possibleMysql = 'C:\xampp\mysql\bin\mysql.exe'
$possiblePhp = 'C:\xampp\php\php.exe'

if (-Not (Test-Path $possibleMysql)) {
    Write-Warning "No se detectó mysql.exe en $possibleMysql. Especifica la ruta manualmente.";
    $possibleMysql = Read-Host "Ruta completa a mysql.exe (o deja vacío para abortar)"
    if (-Not $possibleMysql) { Write-Error "mysql.exe no proporcionado. Abortando."; exit 1 }
}
if (-Not (Test-Path $possiblePhp)) {
    Write-Warning "No se detectó php.exe en $possiblePhp. Especifica la ruta manualmente.";
    $possiblePhp = Read-Host "Ruta completa a php.exe (o deja vacío para abortar)"
    if (-Not $possiblePhp) { Write-Error "php.exe no proporcionado. Abortando."; exit 1 }
}

$mysql = $possibleMysql
$php = $possiblePhp

# Variables de la BD (coinciden con backend/.env creado)
$dbName = 'nomadia'
$dbUser = 'root'
$dbPass = ''
$sqlFile = Join-Path $scriptDir 'database\schema_and_seed.sql'

if (-Not (Test-Path $sqlFile)) {
    Write-Error "No se encontró el archivo SQL en: $sqlFile. Abortando."; exit 1
}

Write-Host "Se importará el SQL en la base de datos '$dbName' usando: $mysql" -ForegroundColor Green
if (-Not (Prompt-Continue "Proceder con la creación/importación de la base de datos?")) { Write-Host "Cancelado por usuario."; exit 0 }

try {
    # Crear base de datos si no existe
    & $mysql -u $dbUser -e "CREATE DATABASE IF NOT EXISTS `$dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1 | Write-Host
} catch {
    Write-Error "Error al crear la base de datos: $_"; exit 1
}

try {
    # Importar el SQL usando cmd.exe para soportar redirección (<)
    $cmd = "`"$mysql`" -u $dbUser $dbName < `"$sqlFile`""
    Write-Host "Importando SQL (esto puede tardar unos segundos)..." -ForegroundColor Green
    Start-Process -FilePath "cmd.exe" -ArgumentList "/c", $cmd -NoNewWindow -Wait
    Write-Host "Importación completada." -ForegroundColor Green
} catch {
    Write-Error "Error al importar SQL: $_"; exit 1
}

# Composer install (opcional)
$composerFound = Get-Command composer -ErrorAction SilentlyContinue
if ($composerFound) {
    Write-Host "Composer detectado. Ejecutando 'composer install' en backend..." -ForegroundColor Green
    Push-Location $scriptDir
    & composer install
    Pop-Location
} else {
    Write-Host "Composer no encontrado en PATH. Saltando 'composer install'." -ForegroundColor Yellow
}

if (-Not (Prompt-Continue "Arrancar backend (PHP built-in server) en http://localhost:8000 ? Se abrirá una nueva ventana de PowerShell.")) { Write-Host "Omitiendo arranque del backend." } else {
    $backendCmd = "& '$php' -S localhost:8000 -t `"$scriptDir\public`""
    Start-Process -FilePath "powershell.exe" -ArgumentList "-NoExit","-Command", $backendCmd -WorkingDirectory $scriptDir
    Write-Host "Servidor PHP arrancado en nueva ventana (http://localhost:8000)." -ForegroundColor Green
}

if (-Not (Prompt-Continue "Arrancar frontend (npm install + npm run dev) en una nueva ventana?")) { Write-Host "Omitiendo arranque del frontend." } else {
    $frontendDir = Join-Path $projectRoot 'frontend'
    if (-Not (Test-Path $frontendDir)) { Write-Warning "No se encontró la carpeta frontend en $frontendDir; omitiendo." }
    else {
        $frontCmd = "cd `"$frontendDir`"; npm install; npm run dev"
        Start-Process -FilePath "powershell.exe" -ArgumentList "-NoExit","-Command", $frontCmd -WorkingDirectory $frontendDir
        Write-Host "Frontend iniciado en nueva ventana. Vite suele exponer http://localhost:5173" -ForegroundColor Green
    }
}

Write-Host "Listo. Abre tu navegador en http://localhost:5173 (frontend) o http://localhost:8000/api/v1/experiencias.php (API)" -ForegroundColor Cyan
