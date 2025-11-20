# Script para iniciar el proyecto Nomadia correctamente
Write-Host "Iniciando Nomadia..." -ForegroundColor Cyan

# 1. Verificar que MySQL este corriendo
Write-Host "`nVerificando MySQL..." -ForegroundColor Yellow
$mysqlProcess = Get-Process mysqld -ErrorAction SilentlyContinue
if (-not $mysqlProcess) {
    Write-Host "ERROR MySQL no esta corriendo. Por favor inicia MySQL desde el Panel de Control de XAMPP." -ForegroundColor Red
    Write-Host "Presiona Enter para continuar cuando MySQL este corriendo..."
    Read-Host
}

# 2. Verificar que la base de datos existe
Write-Host "`nVerificando base de datos 'nomadia'..." -ForegroundColor Yellow
$mysqlPath = "C:\xampp\mysql\bin\mysql.exe"
$checkDb = & $mysqlPath -u root -e "SHOW DATABASES LIKE 'nomadia';" 2>&1
if ($checkDb -notmatch "nomadia") {
    Write-Host "Base de datos 'nomadia' no encontrada. Creandola..." -ForegroundColor Red
    & $mysqlPath -u root -e "CREATE DATABASE IF NOT EXISTS nomadia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    
    Write-Host "Importando schema..." -ForegroundColor Yellow
    $schemaPath = "C:\xampp\htdocs\NomadiaOficial\NomadiaOficial\backend\database\schema_and_seed.sql"
    Get-Content $schemaPath | & $mysqlPath -u root nomadia
    
    Write-Host "Hasheando contrasenias..." -ForegroundColor Yellow
    cd C:\xampp\htdocs\NomadiaOficial\NomadiaOficial\backend
    php hash_plain_passwords.php
} else {
    Write-Host "OK Base de datos 'nomadia' encontrada" -ForegroundColor Green
}

# 3. Verificar configuracion del backend
Write-Host "`nVerificando configuracion del backend..." -ForegroundColor Yellow
$backendEnv = "C:\xampp\htdocs\NomadiaOficial\NomadiaOficial\backend\.env"
if (Test-Path $backendEnv) {
    $envContent = Get-Content $backendEnv -Raw
    if ($envContent -match "DB_DATABASE=nomadia") {
        Write-Host "OK Backend configurado correctamente" -ForegroundColor Green
    } else {
        Write-Host "ADVERTENCIA: Verifica que DB_DATABASE=nomadia en backend/.env" -ForegroundColor Yellow
    }
}

# 4. Detener procesos PHP anteriores
Write-Host "`nDeteniendo procesos PHP anteriores..." -ForegroundColor Yellow
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force
Start-Sleep -Seconds 1

# 5. Iniciar backend en puerto 8000
Write-Host "`nIniciando backend en http://localhost:8000 ..." -ForegroundColor Yellow
cd C:\xampp\htdocs\NomadiaOficial\NomadiaOficial\backend\public
Start-Process powershell -ArgumentList "-NoExit", "-Command", "Write-Host 'BACKEND - No cierres esta ventana' -ForegroundColor Green; php -S localhost:8000 router.php"

Start-Sleep -Seconds 3

# 6. Verificar que el backend este corriendo
Write-Host "`nVerificando backend..." -ForegroundColor Yellow
$backendRunning = $false
for ($i = 0; $i -lt 5; $i++) {
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8000/api/v1/experiencias" -TimeoutSec 2 -ErrorAction SilentlyContinue
        if ($response.StatusCode -eq 200) {
            $backendRunning = $true
            break
        }
    } catch {
        Start-Sleep -Seconds 1
    }
}

if ($backendRunning) {
    Write-Host "OK Backend corriendo correctamente" -ForegroundColor Green
} else {
    Write-Host "ADVERTENCIA Backend no responde. Verifica la ventana de PHP." -ForegroundColor Yellow
}

# 7. Actualizar configuracion del frontend
Write-Host "`nActualizando configuracion del frontend..." -ForegroundColor Yellow
$frontendEnv = "C:\xampp\htdocs\NomadiaOficial\NomadiaOficial\frontend\.env"
$newFrontendConfig = "VITE_API_BASE=http://localhost:8000/api/v1"
Set-Content -Path $frontendEnv -Value $newFrontendConfig -Encoding UTF8
Write-Host "OK Frontend configurado para usar http://localhost:8000" -ForegroundColor Green

# 8. Iniciar frontend
Write-Host "`nIniciando frontend..." -ForegroundColor Yellow
cd C:\xampp\htdocs\NomadiaOficial\NomadiaOficial\frontend
Start-Process powershell -ArgumentList "-NoExit", "-Command", "Write-Host 'FRONTEND - No cierres esta ventana' -ForegroundColor Cyan; npm run dev"

Write-Host "`nProyecto iniciado!" -ForegroundColor Green
Write-Host "`nInformacion:" -ForegroundColor Cyan
Write-Host "   Backend:  http://localhost:8000" -ForegroundColor White
Write-Host "   Frontend: http://localhost:5173" -ForegroundColor White
Write-Host "   API:      http://localhost:8000/api/v1/experiencias" -ForegroundColor White
Write-Host "`nPara detener el proyecto, cierra las ventanas de PowerShell del backend y frontend." -ForegroundColor Yellow
Write-Host "`nPresiona Enter para cerrar esta ventana..."
Read-Host
