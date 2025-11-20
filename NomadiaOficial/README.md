# NomadiaOficial — Plataforma de Experiencias Turísticas

Este repositorio contiene la aplicación completa "Nomadia":

- `backend/` - API REST en PHP con arquitectura MVC (Repositories/Services)
- `frontend/` - SPA en Vue 3 (Vite) con Pinia, Vue Router y Axios

## 🚀 Inicio Rápido (Windows + XAMPP)

### Prerrequisitos
- XAMPP instalado con PHP 8.x y MySQL
- Node.js 18+ y npm
- Composer (opcional, para dependencias adicionales)

### Instalación Automática (Recomendada)

**Paso 1:** Abre PowerShell **como Administrador** en la carpeta del proyecto y ejecuta:

```powershell
cd C:\xampp\htdocs\NomadiaOficial\backend
powershell -ExecutionPolicy Bypass -File .\run-local.ps1
```

El script automáticamente:
- ✅ Crea la base de datos `nomadia`
- ✅ Importa el esquema y datos de prueba
- ✅ Hashea las contraseñas (necesario para login)
- ✅ Arranca el backend en `http://localhost:8000`
- ✅ Instala dependencias del frontend y lo arranca en `http://localhost:5173`

**Paso 2:** Abre tu navegador en **http://localhost:5173**

---

### Instalación Manual (Si prefieres control paso a paso)

### Instalación Manual (Si prefieres control paso a paso)

#### 1. Configurar Base de Datos

```powershell
# Crear base de datos (abre MySQL desde XAMPP o usa la consola)
& 'C:\xampp\mysql\bin\mysql.exe' -u root -e "CREATE DATABASE IF NOT EXISTS nomadia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importar esquema y datos de prueba
& 'C:\xampp\mysql\bin\mysql.exe' -u root nomadia < 'C:\xampp\htdocs\NomadiaOficial\backend\database\schema_and_seed.sql'

# IMPORTANTE: Hashear contraseñas del seed (necesario para que funcione el login)
cd C:\xampp\htdocs\NomadiaOficial\backend
php .\hash_plain_passwords.php
```

#### 2. Arrancar Backend (Puerto 8000)

```powershell
cd C:\xampp\htdocs\NomadiaOficial\backend
php -S localhost:8000 -t public
```

Deja esta terminal **abierta y corriendo**.

#### 3. Arrancar Frontend (Puerto 5173)

Abre **otra terminal** PowerShell:

```powershell
cd C:\xampp\htdocs\NomadiaOficial\frontend
npm install
npm run dev
```

#### 4. Abrir en el Navegador

Ve a **http://localhost:5173** — deberías ver la página de inicio de Nomadia.

---

## 🔐 Credenciales de Prueba

Después de ejecutar `hash_plain_passwords.php`, usa estas credenciales para hacer login:

- **Email:** `test@example.com`
- **Password:** `secret123`

---

## 📁 Estructura del Proyecto

```
NomadiaOficial/
├── backend/
│   ├── .env                    # Configuración de base de datos
│   ├── public/
│   │   ├── router.php          # Router con CORS para desarrollo
│   │   └── api/v1/             # Endpoints PHP (login.php, experiencias.php, etc.)
│   ├── database/
│   │   └── schema_and_seed.sql # Esquema de tablas + datos de prueba
│   ├── app/
│   │   ├── Models/             # Modelos de dominio
│   │   ├── Repositories/       # Capa de acceso a datos
│   │   └── Services/           # Lógica de negocio
│   └── hash_plain_passwords.php # Script para hashear contraseñas
│
└── frontend/
    ├── .env                    # VITE_API_BASE (URL del backend)
    ├── src/
    │   ├── pages/              # Componentes de páginas (Login, Home, etc.)
    │   ├── api/                # Repositorios Axios (authRepository, etc.)
    │   ├── stores/             # Pinia stores (state management)
    │   └── router/             # Vue Router
    └── vite.config.js
```

---

## 🔧 Solución de Problemas Comunes

### ❌ Error: "Login failed: Network Error"

**Causa:** El backend NO está corriendo en `http://localhost:8000`

**Solución:**
1. Verifica que el backend esté corriendo:
   ```powershell
   cd C:\xampp\htdocs\NomadiaOficial\backend
   php -S localhost:8000 -t public
   ```
2. Verifica que `frontend/.env` tenga:
   ```
   VITE_API_BASE=http://localhost:8000/api/v1
   ```
3. **Reinicia el frontend** después de crear/editar `.env`:
   ```powershell
   # Presiona Ctrl+C en la terminal del frontend y vuelve a ejecutar:
   npm run dev
   ```

### ❌ Error: "invalid_credentials" (401)

**Causa:** Las contraseñas en la base de datos están en texto plano (sin hashear)

**Solución:** Ejecuta el script de hash:
```powershell
cd C:\xampp\htdocs\NomadiaOficial\backend
php .\hash_plain_passwords.php
```

### ❌ Error: "db_connection_failed"

**Causa:** MySQL no está corriendo o las credenciales en `.env` son incorrectas

**Solución:**
1. Inicia MySQL desde el panel de XAMPP
2. Verifica las credenciales en `backend/.env`:
   ```
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nomadia
   DB_USERNAME=root
   DB_PASSWORD=
   ```

---

## 🌐 Endpoints de la API

Base URL: `http://localhost:8000/api/v1/`

### Autenticación
- `POST /login.php` - Login (JSON: `{email, password}`)
- `POST /register.php` - Registro (JSON: `{name, email, password, role?}`)

### Experiencias
- `GET /experiencias.php` - Listar experiencias
- `POST /experiencias_create.php` - Crear experiencia (requiere sesión de guía)
- `GET /experiencias_mine.php` - Mis experiencias (requiere sesión de guía)

### Reservas
- `GET /reservas.php` - Listar mis reservas (requiere sesión)
- `POST /reservas.php` - Crear reserva (requiere sesión)

### Admin
- `GET /admin_users.php` - Gestión de usuarios (requiere rol admin)
- `POST /admin_approve_experiencia.php` - Aprobar experiencia (requiere rol admin)

---

## 📋 Próximos Pasos

1. **Revisar el documento de requerimientos** (`ModeloDeRequerimientos.pdf`) para verificar casos de uso
2. **Crear más datos de prueba** editando `backend/database/schema_and_seed.sql`
3. **Configurar roles** usando los endpoints `/make_me_guide.php` o `/make_me_admin.php`
4. **Desplegar en producción** configurando Apache/Nginx con CORS apropiado

---
