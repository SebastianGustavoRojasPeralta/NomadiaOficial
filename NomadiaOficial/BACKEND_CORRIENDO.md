# ✅ EL BACKEND YA ESTÁ CORRIENDO

## 🎉 Estado Actual

✅ **Backend:** Corriendo en `http://localhost:8000` (puerto correcto)  
✅ **Contraseñas:** Hasheadas correctamente  
⚠️ **Problema:** No sabemos las contraseñas de los usuarios existentes (fueron registrados con hash)

---

## 🔐 SOLUCIÓN: Crear un Usuario Nuevo

### Opción 1: Usar el Formulario de Registro (RECOMENDADO)

1. Ve a tu aplicación en el navegador
2. Busca el botón/link de **"Registrarse"** o **"Register"**
3. Crea un nuevo usuario con estos datos:
   - **Nombre:** Tu Nombre
   - **Email:** tunombre@example.com
   - **Password:** password123 (o la que prefieras)

4. Una vez registrado, usa esas mismas credenciales para hacer **Login**

---

### Opción 2: Crear Usuario desde la Terminal (Alternativa)

Si no encuentras el formulario de registro, abre PowerShell y ejecuta:

```powershell
curl -X POST "http://localhost:8000/api/v1/register.php" `
  -H "Content-Type: application/json" `
  -d '{\"name\":\"Mi Usuario\",\"email\":\"miusuario@example.com\",\"password\":\"mipassword123\",\"role\":\"traveler\"}'
```

Luego haz login con:
- **Email:** miusuario@example.com
- **Password:** mipassword123

---

### Opción 3: Resetear un Usuario Existente (SQL)

Si prefieres usar uno de los usuarios que ya existen (como `testadmin@example.com`), puedes cambiar su contraseña manualmente:

1. Abre PowerShell y ejecuta:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root nomadia -e "UPDATE users SET password = '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE email = 'testadmin@example.com';"
```

2. Ahora puedes hacer login con:
   - **Email:** testadmin@example.com
   - **Password:** password

---

## 🧪 Prueba el Login Ahora

1. Ve a **http://localhost:5173/login** (tu frontend)
2. Usa las credenciales del usuario que acabas de crear
3. ¡Debería funcionar! ✅

---

## 📊 Usuarios en la Base de Datos

Encontré estos usuarios en tu DB (pero no sabemos sus contraseñas porque fueron hasheadas):

| Email | Rol | ID |
|-------|-----|-----|
| test@example.com | traveler | 1 |
| testadmin@example.com | admin | 14 |
| seb@gmail.com | admin | 7 |
| sebas777@gmail.com | admin | 10 |

---

## 🔧 Mantener el Backend Corriendo

**IMPORTANTE:** El backend debe seguir corriendo en la terminal. Si cierras la terminal o presionas Ctrl+C, el backend se detendrá y volverás a tener el error "Network Error".

Si accidentalmente lo detienes, vuelve a ejecutar:

```powershell
C:\xampp\php\php.exe -S localhost:8000 -t C:\xampp\htdocs\NomadiaOficial\backend\public
```

---

## 🎯 Resumen de lo que Funcionó

✅ Backend arrancado correctamente en puerto 8000  
✅ Todas las contraseñas hasheadas  
✅ MySQL corriendo  
✅ Frontend en puerto 5173  

**Lo único que necesitas ahora es crear un nuevo usuario o resetear la contraseña de uno existente.**
