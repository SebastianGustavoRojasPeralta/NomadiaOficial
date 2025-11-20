# Script para verificar la base de datos
Write-Host "Verificando Base de Datos Nomadia..." -ForegroundColor Cyan

$mysqlPath = "C:\xampp\mysql\bin\mysql.exe"

# 1. Verificar que MySQL este corriendo
Write-Host "`n1. Estado de MySQL:" -ForegroundColor Yellow
$mysqlProcess = Get-Process mysqld -ErrorAction SilentlyContinue
if ($mysqlProcess) {
    Write-Host "   OK MySQL esta corriendo (PID: $($mysqlProcess.Id))" -ForegroundColor Green
} else {
    Write-Host "   ERROR MySQL NO esta corriendo" -ForegroundColor Red
    Write-Host "   Por favor inicia MySQL desde el Panel de Control de XAMPP" -ForegroundColor Yellow
    Read-Host "Presiona Enter para salir"
    exit
}

# 2. Verificar conexion
Write-Host "`n2. Verificando conexion..." -ForegroundColor Yellow
try {
    $testConnection = & $mysqlPath -u root -e "SELECT 1;" 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "   OK Conexion exitosa" -ForegroundColor Green
    } else {
        Write-Host "   ERROR de conexion: $testConnection" -ForegroundColor Red
        Read-Host "Presiona Enter para salir"
        exit
    }
} catch {
    Write-Host "   ERROR: $_" -ForegroundColor Red
    Read-Host "Presiona Enter para salir"
    exit
}

# 3. Verificar base de datos
Write-Host "`n3. Base de datos 'nomadia':" -ForegroundColor Yellow
$checkDb = & $mysqlPath -u root -e "SHOW DATABASES LIKE 'nomadia';" 2>&1
if ($checkDb -match "nomadia") {
    Write-Host "   OK Base de datos 'nomadia' existe" -ForegroundColor Green
} else {
    Write-Host "   ERROR Base de datos 'nomadia' NO existe" -ForegroundColor Red
    Write-Host "   Creando base de datos..." -ForegroundColor Yellow
    & $mysqlPath -u root -e "CREATE DATABASE nomadia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    Write-Host "   OK Base de datos creada" -ForegroundColor Green
}

# 4. Verificar tablas
Write-Host "`n4. Tablas en la base de datos:" -ForegroundColor Yellow
$tables = & $mysqlPath -u root nomadia -e "SHOW TABLES;" 2>&1
if ($tables -and $tables.Count -gt 1) {
    Write-Host "   OK Tablas encontradas:" -ForegroundColor Green
    $tables | Select-Object -Skip 1 | ForEach-Object {
        if ($_.Trim()) {
            Write-Host "      - $_" -ForegroundColor White
        }
    }
} else {
    Write-Host "   ADVERTENCIA No hay tablas en la base de datos" -ForegroundColor Yellow
    Write-Host "   Necesitas importar el schema" -ForegroundColor Yellow
}

# 5. Verificar datos de experiencias
Write-Host "`n5. Datos de prueba:" -ForegroundColor Yellow
$expCount = & $mysqlPath -u root nomadia -e "SELECT COUNT(*) FROM experiencias;" 2>&1 | Select-Object -Last 1
if ($expCount -match '^\d+$') {
    Write-Host "   Experiencias: $expCount" -ForegroundColor Green
} else {
    Write-Host "   No se pudo contar experiencias" -ForegroundColor Yellow
}

$userCount = & $mysqlPath -u root nomadia -e "SELECT COUNT(*) FROM users;" 2>&1 | Select-Object -Last 1
if ($userCount -match '^\d+$') {
    Write-Host "   Usuarios: $userCount" -ForegroundColor Green
}

# 6. Probar conexion desde PHP
Write-Host "`n6. Prueba de conexion PHP:" -ForegroundColor Yellow
$testPhpScript = @'
<?php
$host = '127.0.0.1';
$dbname = 'nomadia';
$user = 'root';
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM experiencias");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "OK PHP puede conectarse. Experiencias: " . $result['total'];
} catch (PDOException $e) {
    echo "ERROR PHP: " . $e->getMessage();
}
'@

$tempFile = [System.IO.Path]::GetTempFileName() + ".php"
Set-Content -Path $tempFile -Value $testPhpScript -Encoding UTF8
$phpResult = php $tempFile 2>&1
Write-Host "   $phpResult" -ForegroundColor $(if ($phpResult -match "OK") { "Green" } else { "Red" })
Remove-Item $tempFile -ErrorAction SilentlyContinue

Write-Host "`nVerificacion completada" -ForegroundColor Green
Write-Host "`nPresiona Enter para cerrar..."
Read-Host
