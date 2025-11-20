# Integración de Idiomas y Múltiples Imágenes en Experiencias

## 📋 Resumen de Cambios

Se ha integrado la funcionalidad para que al crear una experiencia se puedan:
1. **Especificar idioma principal** (requerido)
2. **Agregar idiomas adicionales** (opcional)
3. **Subir múltiples fotos** de la experiencia

---

## 🗄️ Cambios en Base de Datos

### Nuevas Columnas en `experiencias`

```sql
-- Migración aplicada: 2025_11_16_000000_add_idiomas_to_experiencias.sql

ALTER TABLE `experiencias` 
ADD COLUMN `idioma_principal` VARCHAR(50) DEFAULT NULL AFTER `cantidad`,
ADD COLUMN `idiomas_adicionales` VARCHAR(255) DEFAULT NULL AFTER `idioma_principal`,
ADD COLUMN `imagenes` TEXT DEFAULT NULL COMMENT 'JSON array de URLs de imágenes adicionales' AFTER `imagen`;
```

### Estructura Resultante

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `idioma_principal` | VARCHAR(50) | Idioma principal de la experiencia (Español, Inglés, etc.) |
| `idiomas_adicionales` | VARCHAR(255) | Lista de idiomas adicionales separados por comas |
| `imagenes` | TEXT | JSON array con URLs de imágenes adicionales |

---

## 🎨 Cambios en Frontend

### `CreateExperience.vue`

#### 1. **Nuevo campo: Idioma Principal** (requerido)
```vue
<select v-model="form.idioma_principal" class="form-select" required>
  <option value="">-- Selecciona --</option>
  <option value="Español">Español</option>
  <option value="Inglés">Inglés</option>
  <option value="Quechua">Quechua</option>
  <option value="Aymara">Aymara</option>
  <option value="Francés">Francés</option>
  <option value="Alemán">Alemán</option>
  <option value="Portugués">Portugués</option>
</select>
```

#### 2. **Nuevo campo: Idiomas Adicionales** (opcional)
```vue
<input 
  v-model="form.idiomas_adicionales" 
  type="text" 
  class="form-control" 
  placeholder="Ej: Inglés, Quechua (separados por comas)"
>
```

#### 3. **Mejoras en Subida de Imágenes**

**Características:**
- ✅ Contador de fotos subidas: `3 fotos`
- ✅ Badge "Principal" en la primera imagen
- ✅ Numeración de imágenes (1, 2, 3...)
- ✅ Preview mejorado con mayor altura (140px)
- ✅ Mensaje clarificado: "Puedes subir múltiples fotos"

**Envío al Backend:**
```javascript
// Primera imagen como 'image'
formData.append('image', uploadedImages.value[0].file)

// Imágenes adicionales como 'images[]'
for (let i = 1; i < uploadedImages.value.length; i++) {
  formData.append('images[]', uploadedImages.value[i].file)
}
```

### `ExperienciaShow.vue`

#### 1. **Sección de Idiomas**
Muestra los idiomas disponibles con badges:
- 🌟 **Badge Primary** para idioma principal
- 🔹 **Badge Secondary** para idiomas adicionales

```vue
<div v-if="exp.idioma_principal || exp.idiomas_adicionales">
  <h5><i class="bi bi-translate me-2"></i>Idiomas</h5>
  <span class="badge bg-primary">
    <i class="bi bi-star-fill me-1"></i>{{ exp.idioma_principal }}
  </span>
  <span v-for="idioma in idiomasAdicionalesArray" class="badge bg-secondary">
    {{ idioma }}
  </span>
</div>
```

#### 2. **Galería de Imágenes Adicionales**
Grid responsive 4x4 con imágenes clickeables:

```vue
<div v-if="imagenesAdicionales.length > 0">
  <h5><i class="bi bi-images me-2"></i>Galería de Fotos</h5>
  <div class="row g-2">
    <div v-for="(img, index) in imagenesAdicionales" class="col-md-3 col-6">
      <img 
        :src="getFullImageUrl(img)" 
        @click="openImageModal(img)"
        style="height: 150px; object-fit: cover; cursor: pointer;"
      >
    </div>
  </div>
</div>
```

**Funcionalidades:**
- ✅ Click en imagen abre en nueva pestaña (fullscreen)
- ✅ Efecto hover con scale
- ✅ Responsive (4 columnas desktop, 2 columnas móvil)

---

## ⚙️ Cambios en Backend

### `experiencias_create.php`

#### 1. **Manejo de Imagen Principal**
```php
// Imagen principal (campo 'image')
if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    // Upload a /uploads/experiencias/
    $uploadedImagePath = '/uploads/experiencias/' . $filename;
}
```

#### 2. **Manejo de Imágenes Adicionales**
```php
// Múltiples imágenes adicionales (campo 'images[]')
$additionalImages = [];
if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
    for ($i = 0; $i < $fileCount; $i++) {
        if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
            // Upload cada imagen
            $additionalImages[] = '/uploads/experiencias/' . $filename;
        }
    }
}

// Convertir a JSON para almacenar en BD
$imagenesJson = !empty($additionalImages) ? json_encode($additionalImages) : null;
```

#### 3. **Extracción de Campos de Idiomas**
```php
$idiomaPrincipal = $mysqli->real_escape_string($input['idioma_principal'] ?? '');
$idiomasAdicionales = $mysqli->real_escape_string($input['idiomas_adicionales'] ?? '');
```

#### 4. **INSERT Dinámico**
El backend ahora construye el INSERT dinámicamente según las columnas disponibles:

```php
// Detectar columnas disponibles
$colIdiomaPrincipal = in_array('idioma_principal', $columns);
$colIdiomasAdicionales = in_array('idiomas_adicionales', $columns);
$colImagenes = in_array('imagenes', $columns);

// Agregar condicionalmente
if ($colIdiomaPrincipal) {
    $cols[] = 'idioma_principal';
    $vals[] = '?';
    $bindVars[] = $idiomaPrincipal;
}

if ($colImagenes && $imagenesJsonEsc) {
    $cols[] = 'imagenes';
    $vals[] = '?';
    $bindVars[] = $imagenesJsonEsc;
}
```

---

## 📊 Estructura de Datos

### Ejemplo de Experiencia con Idiomas y Múltiples Fotos

```json
{
  "id": 11,
  "title": "Traditional Bolivian Cooking Class",
  "description": "Learn to cook traditional dishes...",
  "price": 55.00,
  "categoria": "Gastronómico",
  "location": "Sucre, Bolivia",
  "duration_minutes": 180,
  "cantidad": 12,
  "idioma_principal": "Español",
  "idiomas_adicionales": "Inglés, Quechua",
  "imagen": "/uploads/experiencias/cooking-class-1731776400.jpg",
  "imagenes": "[
    \"/uploads/experiencias/cooking-class-1731776400-0.jpg\",
    \"/uploads/experiencias/ingredients-1731776400-1.jpg\",
    \"/uploads/experiencias/final-dish-1731776400-2.jpg\"
  ]",
  "published": 0,
  "guia_id": 15
}
```

### Parseo en Frontend

```javascript
// Idiomas adicionales
const idiomasAdicionalesArray = computed(() => {
  if (!exp.value.idiomas_adicionales) return []
  return exp.value.idiomas_adicionales.split(',').map(i => i.trim())
})

// Imágenes adicionales
const imagenesAdicionales = computed(() => {
  if (!exp.value.imagenes) return []
  try {
    return JSON.parse(exp.value.imagenes)
  } catch {
    return []
  }
})
```

---

## 🧪 Pruebas

### Casos de Prueba

1. **✅ Crear experiencia con idioma principal únicamente**
   - Campo `idioma_principal`: "Español"
   - Campo `idiomas_adicionales`: vacío

2. **✅ Crear experiencia con múltiples idiomas**
   - Campo `idioma_principal`: "Español"
   - Campo `idiomas_adicionales`: "Inglés, Quechua"

3. **✅ Subir 1 sola imagen**
   - Se guarda en `imagen`
   - Campo `imagenes` queda NULL

4. **✅ Subir múltiples imágenes (ej: 4 fotos)**
   - Primera foto → `imagen`
   - Fotos 2, 3, 4 → `imagenes` (JSON array)

5. **✅ Visualizar idiomas en detalle**
   - Badge azul para idioma principal
   - Badges grises para idiomas adicionales

6. **✅ Visualizar galería de fotos**
   - Grid 4x4 responsive
   - Click abre imagen en nueva pestaña

---

## 📁 Archivos Modificados

### Frontend
- ✅ `frontend/src/pages/CreateExperience.vue`
- ✅ `frontend/src/pages/ExperienciaShow.vue`

### Backend
- ✅ `backend/public/api/v1/experiencias_create.php`
- ✅ `backend/database/migrations/2025_11_16_000000_add_idiomas_to_experiencias.sql`

### Documentación
- ✅ `CAMBIOS_IDIOMAS_IMAGENES.md` (este archivo)

---

## 🚀 Cómo Usar

### Para Guías (Crear Experiencia)

1. Ir a **Panel de Guía** → **Crear Nueva Experiencia**
2. Completar formulario básico (título, descripción, precio, etc.)
3. **Idioma Principal**: Seleccionar del dropdown (requerido)
4. **Idiomas Adicionales**: Escribir separados por comas (opcional)
   - Ejemplo: `Inglés, Quechua`
5. **Fotos**: Click en "Subir Fotos" y seleccionar múltiples archivos
   - Primera foto = imagen principal
   - Se muestra badge "Principal" y numeración
6. Click en **"Publicar Experiencia"**

### Para Turistas (Ver Experiencia)

1. Navegar a cualquier experiencia desde **Home** o **Explorar**
2. Visualizar:
   - **Sección "Idiomas"**: Badges con idiomas disponibles
   - **Sección "Galería de Fotos"**: Grid con todas las fotos
   - Click en cualquier foto para ver en tamaño completo

---

## 🎯 Beneficios

1. **Mejor información para turistas**: Saben qué idiomas habla el guía
2. **Experiencia visual mejorada**: Múltiples fotos muestran mejor la experiencia
3. **Flexibilidad**: Campos opcionales no bloquean la creación
4. **Escalable**: JSON permite agregar más imágenes sin cambios en BD
5. **Responsive**: Funciona perfecto en móviles y tablets

---

## 🔧 Mantenimiento Futuro

### Posibles Mejoras

1. **Lightbox/Carousel** para galería de imágenes
2. **Drag & Drop** para reordenar fotos
3. **Crop/Resize** automático de imágenes
4. **Límite de imágenes** (ej: máx 10 fotos)
5. **Validación de tamaño** en backend (actualmente sin límite)
6. **Compresión automática** de imágenes grandes
7. **Idiomas desde tabla separada** (normalización BD)

---

## 📞 Soporte

Para dudas o problemas:
- Revisar logs en `backend/logs/`
- Verificar permisos de carpeta `uploads/experiencias/`
- Confirmar que columnas existen: `DESCRIBE experiencias;`

**Estado**: ✅ **IMPLEMENTADO Y FUNCIONANDO**
