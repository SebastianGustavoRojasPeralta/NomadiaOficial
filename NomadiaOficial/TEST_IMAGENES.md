# 🔍 Guía de Depuración: Múltiples Imágenes

## ✅ Cambios Implementados

### Backend (`experiencias_create.php`)
1. ✅ Mejorado manejo de arrays de imágenes desde `$_FILES`
2. ✅ Agregados logs de debug extensivos
3. ✅ Verificación de columna `imagenes` en BD
4. ✅ Conversión correcta a JSON antes de guardar

### Backend (`experiencias_update.php`)
1. ✅ Soporte para actualizar idiomas
2. ✅ Manejo de múltiples imágenes en edición
3. ✅ Preservación de imágenes existentes

### Frontend (`CreateExperience.vue`)
1. ✅ Validación de idioma principal (requerido)
2. ✅ Logs en consola para cada imagen agregada
3. ✅ Envío correcto con `images[]` para múltiples archivos

---

## 🧪 Cómo Probar

### 1. Limpiar Logs Anteriores
```powershell
# Limpiar el archivo de logs de PHP
Clear-Content "C:\xampp\php\logs\php_error_log"
```

### 2. Crear una Experiencia con Múltiples Fotos

**Pasos:**
1. Ir a Panel de Guía → Crear Nueva Experiencia
2. Llenar formulario:
   - Título: "Prueba Múltiples Imágenes"
   - Descripción: "Test"
   - Categoría: Cualquiera
   - Precio: 100
   - Duración: 2 horas
   - Capacidad: 10
   - **Idioma Principal: Español** ⚠️ OBLIGATORIO
   - Idiomas Adicionales: "Inglés, Quechua"
3. Subir **4-5 fotos diferentes**
4. Verificar que se vean todas las previews con numeración
5. Click en "Publicar Experiencia"

### 3. Verificar en Consola del Navegador

Deberías ver:
```
✅ Imagen principal agregada: foto1.jpg
✅ Imagen adicional 1 agregada: foto2.jpg
✅ Imagen adicional 2 agregada: foto3.jpg
✅ Imagen adicional 3 agregada: foto4.jpg
📸 Total de imágenes a subir: 4
```

### 4. Verificar Logs de PHP

```powershell
# Ver últimas líneas del log
Get-Content "C:\xampp\php\logs\php_error_log" -Tail 50
```

Deberías ver:
```
=== FILES RECIBIDOS ===
Array ( [0] => image [1] => images )
Processing key: image
Processing key: images
Array de archivos encontrado en key: images
Total de archivos en array: 3
✅ Imagen adicional guardada: foto2-1234567890-0.jpg
✅ Imagen adicional guardada: foto3-1234567890-1.jpg
✅ Imagen adicional guardada: foto4-1234567890-2.jpg
Total imágenes adicionales guardadas: 3
📸 JSON de imágenes adicionales que se guardará: ["/uploads/experiencias/..."]
```

### 5. Verificar en Base de Datos

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root nomadia -e "SELECT id, title, idioma_principal, idiomas_adicionales, imagen, imagenes FROM experiencias ORDER BY id DESC LIMIT 1\G"
```

Deberías ver:
- `idioma_principal`: Español
- `idiomas_adicionales`: Inglés, Quechua
- `imagen`: /uploads/experiencias/foto1-xxx.jpg
- `imagenes`: ["\/uploads\/experiencias\/foto2-xxx.jpg","\/uploads\/..."]

### 6. Verificar en la Página de Detalle

1. Ir a Home
2. Click en la experiencia recién creada
3. Deberías ver:
   - ✅ Imagen grande arriba
   - ✅ Fila de thumbnails abajo (todas las fotos)
   - ✅ Sección "Idiomas" con badges
   - ✅ Click en thumbnails cambia la imagen principal

---

## 🐛 Problemas Comunes y Soluciones

### ❌ "Por favor selecciona el idioma principal"
**Causa:** No seleccionaste idioma principal  
**Solución:** Es campo obligatorio, selecciona uno del dropdown

### ❌ No se guardan las imágenes adicionales
**Causa:** Columna `imagenes` no existe en BD  
**Verificar:**
```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root nomadia -e "SHOW COLUMNS FROM experiencias LIKE 'imagenes';"
```
**Si está vacío, ejecutar:**
```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root nomadia -e "ALTER TABLE experiencias ADD COLUMN imagenes TEXT DEFAULT NULL AFTER imagen;"
```

### ❌ Solo se guarda 1 imagen
**Revisar logs:**
```powershell
Get-Content "C:\xampp\php\logs\php_error_log" -Tail 30
```
Buscar:
- "Total de archivos en array: X" (debería ser > 1)
- "✅ Imagen adicional guardada" (debería aparecer múltiples veces)

### ❌ Error al editar experiencia
**Causa:** Faltan campos de idiomas en formulario de edición  
**Solución:** Por implementar - por ahora solo crea nuevas experiencias

### ❌ Thumbnails no se ven
**Causa:** La columna `imagenes` está NULL o vacía  
**Verificar:** El computed `allImages` solo muestra la imagen principal

---

## 📊 Estructura de Datos

### Columna `imagenes` en BD
```json
[
  "/uploads/experiencias/foto2-1731776400-0.jpg",
  "/uploads/experiencias/foto3-1731776400-1.jpg",
  "/uploads/experiencias/foto4-1731776400-2.jpg"
]
```

### Objeto `uploadedImages` en Frontend
```javascript
[
  { file: File, preview: "data:image/jpeg;base64,..." },
  { file: File, preview: "data:image/jpeg;base64,..." },
  { file: File, preview: "data:image/jpeg;base64,..." }
]
```

### FormData enviado al Backend
```
image: File (primera imagen)
images[]: File (segunda imagen)
images[]: File (tercera imagen)
images[]: File (cuarta imagen)
title: "Prueba..."
idioma_principal: "Español"
idiomas_adicionales: "Inglés, Quechua"
...
```

---

## 🎯 Checklist de Verificación

Antes de reportar un bug, verifica:

- [ ] Columnas `idioma_principal`, `idiomas_adicionales`, `imagenes` existen en BD
- [ ] Idioma principal está seleccionado (obligatorio)
- [ ] Se subieron 2+ imágenes
- [ ] Consola del navegador muestra logs de imágenes
- [ ] Logs de PHP muestran "Total imágenes adicionales guardadas: X"
- [ ] BD muestra JSON en columna `imagenes`
- [ ] Thumbnails aparecen en página de detalle

---

## 🔧 Comandos Útiles

```powershell
# Ver estructura de tabla
& "C:\xampp\mysql\bin\mysql.exe" -u root nomadia -e "DESCRIBE experiencias;"

# Ver última experiencia creada
& "C:\xampp\mysql\bin\mysql.exe" -u root nomadia -e "SELECT * FROM experiencias ORDER BY id DESC LIMIT 1\G"

# Limpiar logs
Clear-Content "C:\xampp\php\logs\php_error_log"

# Ver logs en tiempo real
Get-Content "C:\xampp\php\logs\php_error_log" -Wait -Tail 10

# Verificar permisos de carpeta uploads
Get-Acl "C:\xampp\htdocs\NomadiaOficial\backend\public\uploads\experiencias" | Format-List
```

---

## 📝 Notas Finales

- **Imagen Principal**: Siempre se guarda en columna `imagen`
- **Imágenes Adicionales**: Se guardan como JSON en columna `imagenes`
- **Idioma Principal**: Campo obligatorio desde ahora
- **Edición**: Implementación pendiente para actualizar idiomas e imágenes

**Estado:** ✅ Creación funcionando | ⏳ Edición en progreso
