# ⚡ SOLUCIÓN RÁPIDA - Error "Network Error" en Login

## El Problema
El error "Login failed: Network Error" significa que el frontend no puede conectarse al backend.

## La Solución (3 pasos - 2 minutos)

### 1️⃣ Arranca el Backend
Abre PowerShell y ejecuta:

```powershell
cd C:\xampp\htdocs\NomadiaOficial\backend
php -S localhost:8000 -t public
```

✅ **DEJA ESTA TERMINAL ABIERTA** (el servidor debe seguir corriendo)

Deberías ver:
```
[Thu Nov 14 ...] PHP 8.x.x Development Server (http://localhost:8000) started
```

---

### 2️⃣ Hashea las Contraseñas (solo una vez)
Abre **OTRA** PowerShell y ejecuta:

```powershell
cd C:\xampp\htdocs\NomadiaOficial\backend
php .\hash_plain_passwords.php
```

✅ Deberías ver:
```
Updated user test@example.com (id=1)
Done. Updated 1 users.
```

---

### 3️⃣ Reinicia el Frontend
En la terminal donde está corriendo el frontend (npm run dev):

1. Presiona `Ctrl + C` para detenerlo
2. Ejecuta de nuevo:

```powershell
cd C:\xampp\htdocs\NomadiaOficial\frontend
npm run dev
```

✅ Deberías ver algo como:
```
VITE v5.x.x  ready in xxx ms

➜  Local:   http://localhost:5173/
```

---

## 🧪 Prueba el Login

1. Ve a **http://localhost:5173** en tu navegador
2. Haz clic en "Login" o "Iniciar sesión"
3. Usa estas credenciales:
   - **Email:** `test@example.com`
   - **Password:** `secret123`

✅ Deberías entrar sin errores y ver la página principal.

---

## 🔍 Verificación Rápida (si aún falla)

### Verifica que el backend responde:
Abre otra PowerShell y ejecuta:

```powershell
curl http://localhost:8000/api/v1/experiencias.php
```

✅ Deberías ver JSON con experiencias (o `[]` si está vacío)

❌ Si ves error de conexión → El backend NO está corriendo en puerto 8000

### Verifica que MySQL está corriendo:
1. Abre el panel de control de XAMPP
2. Verifica que "MySQL" tenga el estado "Running" (verde)
3. Si no → presiona "Start" en MySQL

---

## 📝 Archivos Creados/Actualizados

He creado estos archivos para que todo funcione automáticamente:

- ✅ `frontend/.env` → Configuración de URL del backend
- ✅ `.env` (raíz) → Configuración de base de datos (copia de backend/.env)
- ✅ `README.md` → Actualizado con instrucciones completas

**IMPORTANTE:** Los archivos `.env` ya están configurados. Si el error persiste, ejecuta los 3 pasos de arriba.

---

## 🆘 Si Sigue Fallando

Abre las DevTools del navegador (F12) y ve a la pestaña **Network**:

1. Intenta hacer login
2. Busca la petición a `login.php`
3. Copia el error y pégalo aquí (yo lo analizo)

También puedes abrir la terminal donde está corriendo el backend y ver si hay algún error PHP.
