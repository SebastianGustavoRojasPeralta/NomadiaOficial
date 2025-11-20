# 📋 DOCUMENTACIÓN DEL PROYECTO NOMADIA
## Documentación Técnica para Defensa Previa

**Fecha:** Noviembre 2025  
**Versión:** 1.0  
**Desarrollador:** [Tu Nombre]

---

## 📖 ÍNDICE

1. [Descripción General del Proyecto](#1-descripción-general-del-proyecto)
2. [Arquitectura del Sistema](#2-arquitectura-del-sistema)
3. [Stack Tecnológico](#3-stack-tecnológico)
4. [Estructura del Proyecto](#4-estructura-del-proyecto)
5. [Base de Datos](#5-base-de-datos)
6. [Módulos y Funcionalidades](#6-módulos-y-funcionalidades)
7. [APIs y Endpoints](#7-apis-y-endpoints)
8. [Vistas y Paneles](#8-vistas-y-paneles)
9. [Seguridad y Autenticación](#9-seguridad-y-autenticación)
10. [Flujos de Usuario](#10-flujos-de-usuario)

---

## 1. DESCRIPCIÓN GENERAL DEL PROYECTO

### 1.1 ¿Qué es Nomadia?
**Nomadia** es una plataforma web para la gestión y reserva de experiencias turísticas en Bolivia. Conecta a **viajeros** con **guías turísticos locales** para ofrecer experiencias auténticas y personalizadas.

### 1.2 Objetivo Principal
Crear un marketplace digital donde:
- Los guías pueden publicar y gestionar sus experiencias turísticas
- Los viajeros pueden explorar, reservar y pagar experiencias
- Los administradores supervisan y aprueban el contenido

### 1.3 Tipos de Usuarios
1. **Viajero (Traveler)** - Usuario estándar que reserva experiencias
2. **Guía (Guide)** - Usuario que crea y gestiona experiencias turísticas
3. **Administrador (Admin)** - Usuario con permisos completos de gestión

---

## 2. ARQUITECTURA DEL SISTEMA

### 2.1 Patrón Arquitectónico
- **Arquitectura:** Cliente-Servidor (Client-Server)
- **Patrón:** MVC (Model-View-Controller) adaptado
- **Tipo:** Aplicación Web SPA (Single Page Application)

### 2.2 Capas del Sistema

```
┌─────────────────────────────────────┐
│         CAPA DE PRESENTACIÓN        │
│   (Vue.js + Bootstrap Frontend)     │
└─────────────────────────────────────┘
                 ↕ HTTP/AJAX
┌─────────────────────────────────────┐
│         CAPA DE APLICACIÓN          │
│      (API REST - PHP Backend)       │
└─────────────────────────────────────┘
                 ↕ SQL
┌─────────────────────────────────────┐
│          CAPA DE DATOS              │
│      (MySQL/MariaDB Database)       │
└─────────────────────────────────────┘
```

### 2.3 Comunicación
- **Protocolo:** HTTP/HTTPS
- **Formato de datos:** JSON
- **Método:** RESTful API
- **CORS:** Habilitado para localhost (desarrollo)

---

## 3. STACK TECNOLÓGICO

### 3.1 FRONTEND

#### Framework Principal
- **Vue.js 3.3.0** - Framework JavaScript progresivo
  - Composition API
  - Reactive State Management
  - Component-based architecture

#### Gestión de Estado
- **Pinia 2.0** - Store para manejo de estado global
  - User Store (sesión de usuario)
  - Experiencia Store (catálogo)

#### Enrutamiento
- **Vue Router 4.2** - Manejo de rutas SPA
  - Guards de navegación
  - Lazy loading de componentes
  - Rutas protegidas por rol

#### UI Framework
- **Bootstrap 5.3.0** - Framework CSS
  - Sistema de grid responsive
  - Componentes pre-diseñados
  - Utility classes

#### Iconografía
- **Bootstrap Icons** - Librería de iconos

#### HTTP Client
- **Axios 1.4.0** - Cliente HTTP para APIs
  - Interceptores de request/response
  - Manejo centralizado de errores
  - Configuración de baseURL

#### Build Tool
- **Vite 5.0** - Herramienta de desarrollo
  - Hot Module Replacement (HMR)
  - Build optimizado para producción
  - Dev server ultrarrápido

### 3.2 BACKEND

#### Lenguaje
- **PHP 8.0+** - Lenguaje del lado del servidor
  - Programación orientada a objetos
  - Type declarations
  - Arrow functions

#### Servidor Web
- **PHP Built-in Server** (desarrollo)
- Compatible con Apache/Nginx (producción)

#### Base de Datos
- **MySQL/MariaDB** - Sistema de gestión de base de datos relacional
  - InnoDB engine
  - Transacciones ACID
  - Foreign Keys

#### Autoloading
- **Composer** - Dependency manager
  - PSR-4 autoloading
  - Gestión de dependencias

#### Arquitectura Backend
- **Patrón Repository** - Separación de lógica de datos
- **Patrón Service** - Lógica de negocio encapsulada
- **API REST** - Endpoints organizados por versión

### 3.3 HERRAMIENTAS DE DESARROLLO

- **XAMPP** - Entorno de desarrollo local
  - Apache Server
  - MySQL Database
  - PHP Runtime
- **Visual Studio Code** - Editor de código
- **Git** - Control de versiones
- **Postman/cURL** - Testing de APIs
- **MySQL Workbench** - Gestión de base de datos

---

## 4. ESTRUCTURA DEL PROYECTO

### 4.1 Estructura del Frontend

```
frontend/
├── public/                    # Archivos estáticos
│   └── favicon.ico
├── src/
│   ├── api/                   # Repositorios de API
│   │   ├── axiosConfig.js     # Configuración de Axios
│   │   ├── authRepository.js  # Auth endpoints
│   │   ├── experienciaRepository.js
│   │   ├── reservaRepository.js
│   │   ├── pagoRepository.js
│   │   ├── calificacionRepository.js
│   │   ├── disponibilidadRepository.js
│   │   └── adminRepository.js
│   ├── pages/                 # Páginas/Vistas principales
│   │   ├── Home.vue
│   │   ├── Login.vue
│   │   ├── ExploreExperiences.vue
│   │   ├── ExperienciaShow.vue
│   │   ├── CreateExperience.vue
│   │   ├── GuideDashboard.vue
│   │   ├── AdminDashboard.vue
│   │   ├── AdminCategorias.vue
│   │   ├── MyReservations.vue
│   │   └── CheckoutPage.vue
│   ├── router/                # Configuración de rutas
│   │   └── index.js
│   ├── stores/                # Pinia stores
│   │   ├── experienciaStore.js
│   │   └── userStore.js
│   ├── App.vue                # Componente raíz
│   └── main.js                # Punto de entrada
├── package.json               # Dependencias NPM
└── vite.config.js             # Configuración de Vite
```

### 4.2 Estructura del Backend

```
backend/
├── app/
│   ├── Database/
│   │   └── DB.php             # Clase de conexión a BD
│   ├── Models/                # Modelos de datos
│   │   ├── Experiencia.php
│   │   ├── Reserva.php
│   │   ├── Pago.php
│   │   ├── Calificacion.php
│   │   └── Disponibilidad.php
│   ├── Repositories/          # Capa de acceso a datos
│   │   ├── ExperienciaRepository.php
│   │   ├── ReservaRepository.php
│   │   ├── PagoRepository.php
│   │   ├── CalificacionRepository.php
│   │   └── DisponibilidadRepository.php
│   └── Services/              # Lógica de negocio
│       ├── ExperienciaService.php
│       ├── ReservaService.php
│       ├── PagoService.php
│       ├── CalificacionService.php
│       └── DisponibilidadService.php
├── database/
│   ├── migrations/            # Migraciones de BD
│   ├── seeders/               # Datos de prueba
│   └── schema_and_seed.sql    # Schema completo
├── public/
│   ├── api/
│   │   └── v1/                # Endpoints API v1
│   │       ├── login.php
│   │       ├── register.php
│   │       ├── experiencias.php
│   │       ├── experiencias_create.php
│   │       ├── experiencias_update.php
│   │       ├── experiencias_mine.php
│   │       ├── reservas.php
│   │       ├── reservas_guia.php
│   │       ├── pagos.php
│   │       ├── calificaciones.php
│   │       ├── disponibilidades.php
│   │       ├── categorias.php
│   │       ├── categorias_create.php
│   │       ├── categorias_update.php
│   │       ├── categorias_delete.php
│   │       ├── admin_users.php
│   │       ├── admin_approve_experiencia.php
│   │       ├── admin_reports.php
│   │       └── admin_audit.php
│   ├── uploads/               # Archivos subidos
│   │   └── experiencias/      # Imágenes de experiencias
│   └── router.php             # Router para PHP server
├── composer.json              # Dependencias PHP
└── run-local.ps1             # Script de inicio
```

---

## 5. BASE DE DATOS

### 5.1 Diagrama de Entidades

```
┌─────────────┐         ┌──────────────────┐         ┌──────────────┐
│    users    │────────▶│   experiencias   │◀────────│  categorias  │
└─────────────┘   1:N   └──────────────────┘   N:1   └──────────────┘
      │                          │
      │ 1:N                      │ 1:N
      ▼                          ▼
┌─────────────┐         ┌──────────────────┐
│   reservas  │◀────────│disponibilidades  │
└─────────────┘         └──────────────────┘
      │
      │ 1:1
      ▼
┌─────────────┐
│    pagos    │
└─────────────┘
      │
      │ 1:N
      ▼
┌──────────────┐
│calificaciones│
└──────────────┘
```

### 5.2 Tablas Principales

#### **users** - Usuarios del sistema
- `id` (PK) - Identificador único
- `name` - Nombre completo
- `email` - Email (único)
- `password` - Contraseña hasheada
- `role` - Rol: traveler, guia, admin
- `created_at`, `updated_at` - Timestamps

#### **experiencias** - Experiencias turísticas
- `id` (PK)
- `title` - Título de la experiencia
- `description` - Descripción detallada
- `price` - Precio en Bs.
- `categoria` - Categoría
- `guia_id` (FK → users) - Guía propietario
- `location` - Ubicación
- `duration_minutes` - Duración en minutos
- `imagen` - Ruta de imagen
- `cantidad` - Capacidad máxima
- `published` - Estado: 0=Pendiente, 1=Aprobado, -1=Rechazado
- `created_at`, `updated_at`

#### **categorias** - Categorías de experiencias
- `id` (PK)
- `nombre` - Nombre de categoría
- `descripcion` - Descripción
- `created_at`, `updated_at`

#### **reservas** - Reservas de usuarios
- `id` (PK)
- `usuario_id` (FK → users)
- `experiencia_id` (FK → experiencias)
- `fecha_reserva` - Fecha de la reserva
- `cantidad` - Número de personas
- `total` - Monto total
- `status` - Estado: pendiente, confirmada, cancelada
- `created_at`, `updated_at`

#### **pagos** - Pagos realizados
- `id` (PK)
- `reserva_id` (FK → reservas)
- `amount` - Monto pagado
- `method` - Método: tarjeta, efectivo, qr
- `status` - Estado: pending, completed, failed
- `created_at`, `updated_at`

#### **calificaciones** - Reseñas y ratings
- `id` (PK)
- `usuario_id` (FK → users)
- `experiencia_id` (FK → experiencias)
- `rating` - Calificación (1-5 estrellas)
- `comentario` - Comentario opcional
- `created_at`, `updated_at`

#### **disponibilidades** - Horarios disponibles
- `id` (PK)
- `experiencia_id` (FK → experiencias)
- `fecha` - Fecha disponible
- `hora_inicio` - Hora de inicio
- `hora_fin` - Hora de fin
- `cupos_disponibles` - Espacios disponibles
- `created_at`, `updated_at`

#### **admin_audit_logs** - Logs de auditoría
- `id` (PK)
- `admin_id` (FK → users)
- `action` - Acción realizada
- `target_type` - Tipo de entidad afectada
- `target_id` - ID de entidad afectada
- `details` - Detalles en JSON
- `created_at`

---

## 6. MÓDULOS Y FUNCIONALIDADES

### 6.1 Módulo de Autenticación
**Funcionalidades:**
- ✅ Registro de usuarios (traveler/guia)
- ✅ Login con email y password
- ✅ Sesiones con PHP Sessions
- ✅ Logout
- ✅ Gestión de roles y permisos

**Endpoints:**
- `POST /api/v1/register.php`
- `POST /api/v1/login.php`
- `POST /api/v1/logout.php`

### 6.2 Módulo de Experiencias (Catálogo)
**Funcionalidades:**
- ✅ Listado público de experiencias
- ✅ Búsqueda y filtrado por categoría
- ✅ Vista detallada de experiencia
- ✅ Galería de imágenes (AVIF, WebP, JPG, PNG, GIF)
- ✅ Información de guía
- ✅ Disponibilidad y horarios

**Endpoints:**
- `GET /api/v1/experiencias.php`
- `GET /api/v1/experiencias.php?id={id}`

### 6.3 Módulo de Guías (Gestión de Experiencias)
**Funcionalidades:**
- ✅ Dashboard del guía con estadísticas
- ✅ Crear nueva experiencia con imágenes
- ✅ Editar experiencias existentes
- ✅ Ver mis experiencias
- ✅ Gestionar disponibilidad
- ✅ Ver reservas recibidas con estados de pago
- ✅ Sistema de tabs organizado

**Endpoints:**
- `POST /api/v1/experiencias_create.php`
- `POST /api/v1/experiencias_update.php`
- `GET /api/v1/experiencias_mine.php`
- `GET /api/v1/reservas_guia.php`
- `GET /api/v1/disponibilidades.php`

### 6.4 Módulo de Reservas
**Funcionalidades:**
- ✅ Selección de fecha y cantidad de personas
- ✅ Cálculo automático de total
- ✅ Creación de reserva
- ✅ Ver mis reservas como viajero
- ✅ Estados: pendiente, confirmada, cancelada

**Endpoints:**
- `POST /api/v1/reservas.php` (crear)
- `GET /api/v1/reservas.php` (listar mis reservas)

### 6.5 Módulo de Pagos
**Funcionalidades:**
- ✅ Checkout page
- ✅ Métodos de pago: Tarjeta, Efectivo, QR
- ✅ Confirmación de pago
- ✅ Estados de pago: pending, completed, failed
- ✅ Asociación pago-reserva

**Endpoints:**
- `POST /api/v1/pagos.php` (crear pago)
- `GET /api/v1/pagos.php` (consultar)

### 6.6 Módulo de Calificaciones
**Funcionalidades:**
- ✅ Sistema de estrellas (1-5)
- ✅ Comentarios opcionales
- ✅ Listado de reseñas por experiencia
- ✅ Validación: solo usuarios con reserva confirmada

**Endpoints:**
- `POST /api/v1/calificaciones.php` (crear)
- `GET /api/v1/calificaciones.php?experiencia_id={id}`

### 6.7 Módulo de Administración
**Funcionalidades:**
- ✅ Dashboard con estadísticas
- ✅ Gestión de usuarios (CRUD)
- ✅ Cambio de roles de usuario
- ✅ Aprobación/Rechazo de experiencias
- ✅ Estados: Pendiente, Aprobado, Rechazado
- ✅ Filtros por estado de experiencia
- ✅ Gestión de categorías (CRUD)
- ✅ Reportes y métricas
- ✅ Logs de auditoría
- ✅ Top experiencias más reservadas

**Endpoints:**
- `GET /api/v1/admin_users.php` (listar)
- `POST /api/v1/admin_users.php` (crear/actualizar/eliminar)
- `POST /api/v1/admin_approve_experiencia.php` (aprobar/rechazar)
- `GET /api/v1/admin_reports.php` (reportes)
- `GET /api/v1/admin_audit.php` (auditoría)
- `GET /api/v1/categorias.php`
- `POST /api/v1/categorias_create.php`
- `POST /api/v1/categorias_update.php`
- `POST /api/v1/categorias_delete.php`

### 6.8 Módulo de Categorías
**Funcionalidades:**
- ✅ Listado de categorías
- ✅ Crear categoría (admin)
- ✅ Editar categoría (admin)
- ✅ Eliminar categoría (admin)
- ✅ Filtrado de experiencias por categoría

---

## 7. APIS Y ENDPOINTS

### 7.1 Autenticación

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | `/api/v1/register.php` | Registrar usuario | No |
| POST | `/api/v1/login.php` | Iniciar sesión | No |
| POST | `/api/v1/logout.php` | Cerrar sesión | Sí |

### 7.2 Experiencias

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/experiencias.php` | Listar experiencias | No |
| GET | `/api/v1/experiencias.php?id={id}` | Detalle de experiencia | No |
| POST | `/api/v1/experiencias_create.php` | Crear experiencia | Guía |
| POST | `/api/v1/experiencias_update.php` | Actualizar experiencia | Guía |
| GET | `/api/v1/experiencias_mine.php` | Mis experiencias | Guía |

### 7.3 Reservas

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/reservas.php` | Mis reservas | User |
| POST | `/api/v1/reservas.php` | Crear reserva | User |
| GET | `/api/v1/reservas_guia.php` | Reservas recibidas | Guía |

### 7.4 Pagos

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | `/api/v1/pagos.php` | Crear pago | User |
| GET | `/api/v1/pagos.php?reserva_id={id}` | Consultar pago | User |

### 7.5 Calificaciones

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/calificaciones.php` | Listar calificaciones | No |
| POST | `/api/v1/calificaciones.php` | Crear calificación | User |

### 7.6 Disponibilidades

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/disponibilidades.php` | Listar disponibilidades | No |
| POST | `/api/v1/disponibilidades.php` | Crear disponibilidad | Guía |

### 7.7 Categorías

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/categorias.php` | Listar categorías | No |
| POST | `/api/v1/categorias_create.php` | Crear categoría | Admin |
| POST | `/api/v1/categorias_update.php` | Actualizar categoría | Admin |
| POST | `/api/v1/categorias_delete.php` | Eliminar categoría | Admin |

### 7.8 Administración

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/admin_users.php` | Listar usuarios | Admin |
| POST | `/api/v1/admin_users.php` | Gestionar usuarios | Admin |
| POST | `/api/v1/admin_approve_experiencia.php` | Aprobar/Rechazar | Admin |
| GET | `/api/v1/admin_reports.php` | Reportes | Admin |
| GET | `/api/v1/admin_audit.php` | Logs de auditoría | Admin |

---

## 8. VISTAS Y PANELES

### 8.1 Vistas Públicas (Sin autenticación)

#### **Home** (`/`)
- **Descripción:** Página principal de aterrizaje
- **Componentes:**
  - Hero section
  - Catálogo destacado de experiencias
  - Llamadas a la acción

#### **Explore Experiences** (`/explore`)
- **Descripción:** Exploración del catálogo completo
- **Funcionalidades:**
  - Grid de experiencias con cards
  - Filtros por categoría
  - Búsqueda por texto
  - Paginación

#### **Experience Detail** (`/experiencia/:id`)
- **Descripción:** Vista detallada de una experiencia
- **Componentes:**
  - Galería de imágenes
  - Información completa
  - Perfil del guía
  - Reseñas y calificaciones
  - Selector de fecha y cantidad
  - Botón de reserva

#### **Login** (`/login`)
- **Descripción:** Autenticación de usuarios
- **Diseño:** Split-screen moderno
- **Campos:** Email, Password

### 8.2 Panel del Viajero (Traveler)

#### **My Reservations** (`/my-reservations`)
- **Descripción:** Gestión de reservas personales
- **Funcionalidades:**
  - Lista de reservas con estados
  - Detalles de reserva
  - Estado de pago
  - Opción de cancelar
  - Dejar calificación

#### **Checkout** (`/checkout`)
- **Descripción:** Proceso de pago
- **Funcionalidades:**
  - Resumen de reserva
  - Selección de método de pago
  - Confirmación

### 8.3 Panel del Guía (Guide Dashboard)

#### **Guide Dashboard** (`/guide-dashboard`)
- **Descripción:** Panel completo del guía
- **Estructura:** Sistema de TABS

**TAB 1: Mis Experiencias**
- Cards con imágenes de experiencias
- Botones: Editar, Ver detalles
- Estado de publicación
- Estadísticas: Total experiencias

**TAB 2: Reservas Recibidas**
- Tabla con reservas
- Columnas: Usuario, Fecha, Cantidad, Total, Estado de pago
- Filtros: Todas, Pendientes, Confirmadas, Pagadas
- Estadísticas: Total reservas, Pendientes, Confirmadas

**TAB 3: Disponibilidad**
- Selector de experiencia
- Agregar horarios disponibles
- Gestión de cupos

**Estadísticas Generales:**
- Número de experiencias
- Total de reservas
- Reservas pendientes
- Reservas confirmadas

#### **Create Experience** (`/create-experience`)
- **Descripción:** Formulario de creación de experiencia
- **Diseño:** Compacto y centrado
- **Campos:**
  - Título
  - Descripción
  - Precio
  - Categoría
  - Ubicación
  - Duración
  - Capacidad
  - Imagen (AVIF, WebP, JPG, PNG, GIF)

### 8.4 Panel del Administrador (Admin Dashboard)

#### **Admin Dashboard** (`/admin-dashboard`)
- **Descripción:** Panel de administración completo
- **Diseño:** Sidebar + Contenido principal

**ESTRUCTURA DEL SIDEBAR:**
- Logo
- Admin Dashboard (Overview)
- User Management
- Experience Management
- Business Reports

**TAB 1: Admin Dashboard (Overview)**
- **Cards de Estadísticas:**
  - Total Users (con icono)
  - Total Experiences
  - Total Reservations
  - Payments Completed
- **Top Experiences:**
  - Tabla con experiencias más reservadas
  - Ordenadas por número de reservas

**TAB 2: User Management**
- **Formulario de Creación:**
  - Nombre, Email, Password, Role
- **Tabla de Usuarios:**
  - Columnas: ID, Name, Email, Current Role
  - Cambio de rol inline
  - Botón eliminar
  - Badges para roles

**TAB 3: Experience Management**
- **Filtros de Estado:**
  - All (todos)
  - Pending (pendientes)
  - Approved (aprobados)
  - Rejected (rechazados)
  - Con contadores dinámicos
- **Tabla de Experiencias:**
  - Columnas: Name, Guide, Date, Status, Actions
  - Badges de estado:
    - 🟡 Pending (amarillo)
    - 🟢 Approved (verde)
    - 🔴 Rejected (rojo)
  - Botones:
    - Review (ver detalles)
    - Approve (solo pendientes)
    - Reject (solo pendientes)
- **Botón:** Manage Categories → AdminCategorias

**TAB 4: Business Reports**
- **Audit Log:**
  - Lista de acciones administrativas
  - Información: Admin, Action, Target, Timestamp
  - Detalles de cada operación

#### **Admin Categorias** (`/admin/categorias`)
- **Descripción:** Gestión CRUD de categorías
- **Funcionalidades:**
  - Listar categorías
  - Crear nueva categoría
  - Editar categoría existente
  - Eliminar categoría

---

## 9. SEGURIDAD Y AUTENTICACIÓN

### 9.1 Sistema de Autenticación
- **Tipo:** Session-based authentication
- **Storage:** PHP Sessions en servidor
- **Persistencia:** Cookie de sesión (PHPSESSID)

### 9.2 Control de Acceso (Authorization)

#### Guards de Rutas (Frontend)
```javascript
// Ejemplo de guard en router
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth) {
    // Verificar sesión
  }
  if (to.meta.requiresRole) {
    // Verificar rol específico
  }
})
```

#### Verificación Backend
- Cada endpoint protegido verifica `$_SESSION['user_id']`
- Endpoints de admin verifican `role === 'admin'`
- Endpoints de guía verifican `role === 'guia'`

### 9.3 Seguridad de Contraseñas
- **Hashing:** `password_hash()` con bcrypt
- **Verificación:** `password_verify()`
- **Salt:** Generado automáticamente

### 9.4 Seguridad de Datos
- **SQL Injection:** Prepared statements con MySQLi
- **XSS:** Escapado de salida con `htmlspecialchars()`
- **CSRF:** (Por implementar: tokens CSRF)

### 9.5 CORS (Cross-Origin Resource Sharing)
- Habilitado para localhost:5173 y localhost:5174
- Headers configurados en router.php
- Manejo de preflight OPTIONS

### 9.6 Validación de Archivos
- **Tipos permitidos:** image/jpeg, image/png, image/gif, image/webp, image/avif
- **Validación:** MIME type y extensión
- **Ubicación:** `/uploads/experiencias/`
- **Nombres sanitizados:** timestamp + nombre limpio

---

## 10. FLUJOS DE USUARIO

### 10.1 Flujo de Registro y Login

```
┌─────────────┐
│   Inicio    │
└──────┬──────┘
       │
       v
┌─────────────┐
│   /login    │
└──────┬──────┘
       │
       ├─→ Registro (/register)
       │   └─→ Crear cuenta
       │       └─→ Login automático
       │
       └─→ Login
           └─→ Verificar credenciales
               └─→ Crear sesión
                   └─→ Redirigir según rol:
                       ├─→ Admin → /admin-dashboard
                       ├─→ Guía → /guide-dashboard
                       └─→ Traveler → /explore
```

### 10.2 Flujo de Creación de Experiencia (Guía)

```
┌──────────────────┐
│ Guide Dashboard  │
└────────┬─────────┘
         │
         v
┌──────────────────┐
│Create Experience │
└────────┬─────────┘
         │
         v
┌──────────────────┐
│ Llenar formulario│
│ - Título         │
│ - Descripción    │
│ - Precio         │
│ - Categoría      │
│ - Imagen         │
└────────┬─────────┘
         │
         v
┌──────────────────┐
│ Subir imagen     │
└────────┬─────────┘
         │
         v
┌──────────────────┐
│Guardar en BD     │
│(estado: pending) │
└────────┬─────────┘
         │
         v
┌──────────────────┐
│Esperar aprobación│
│     del Admin    │
└──────────────────┘
```

### 10.3 Flujo de Reserva (Viajero)

```
┌──────────────────┐
│ Explore          │
│ Experiences      │
└────────┬─────────┘
         │
         v
┌──────────────────┐
│ Seleccionar      │
│ Experiencia      │
└────────┬─────────┘
         │
         v
┌──────────────────┐
│ Experience Show  │
│ - Ver detalles   │
│ - Ver reseñas    │
└────────┬─────────┘
         │
         v
┌──────────────────┐
│ Seleccionar:     │
│ - Fecha          │
│ - Nº Personas    │
└────────┬─────────┘
         │
         v
┌──────────────────┐
│ Crear Reserva    │
│ (status:pending) │
└────────┬─────────┘
         │
         v
┌──────────────────┐
│    Checkout      │
│ - Ver resumen    │
│ - Elegir método  │
└────────┬─────────┘
         │
         v
┌──────────────────┐
│ Crear Pago       │
│(status:completed)│
└────────┬─────────┘
         │
         v
┌──────────────────┐
│Actualizar Reserva│
│(status:confirmed)│
└────────┬─────────┘
         │
         v
┌──────────────────┐
│ My Reservations  │
│ - Ver estado     │
│ - Calificar      │
└──────────────────┘
```

### 10.4 Flujo de Aprobación (Admin)

```
┌──────────────────┐
│ Admin Dashboard  │
└────────┬─────────┘
         │
         v
┌──────────────────┐
│Experience Mgmt   │
│ (tab)            │
└────────┬─────────┘
         │
         v
┌──────────────────┐
│ Ver experiencias │
│ pendientes       │
└────────┬─────────┘
         │
         v
┌──────────────────┐
│ Revisar detalles │
└────────┬─────────┘
         │
         ├─→ Aprobar
         │   └─→ published = 1
         │       └─→ Visible públicamente
         │
         └─→ Rechazar
             └─→ published = -1
                 └─→ No visible
```

---

## 11. CARACTERÍSTICAS DESTACADAS

### 11.1 Diseño Responsive
- Mobile-first approach
- Bootstrap grid system
- Breakpoints optimizados
- Sidebar colapsable en móviles

### 11.2 Experiencia de Usuario (UX)
- SPA sin recargas de página
- Transiciones suaves
- Feedback visual inmediato
- Loading states
- Confirmaciones de acciones

### 11.3 Gestión de Imágenes
- Soporte multi-formato (AVIF, WebP, JPG, PNG, GIF)
- Cache busting
- Error handling con placeholders
- Preview en modales de edición

### 11.4 Sistema de Estados
- Experiencias: Pendiente, Aprobado, Rechazado
- Reservas: Pendiente, Confirmada, Cancelada
- Pagos: Pending, Completed, Failed
- Badges de color por estado

### 11.5 Auditoría y Logs
- Registro de acciones administrativas
- Timestamp automático
- Detalles en JSON
- Trazabilidad completa

---

## 12. MÉTRICAS Y ESTADÍSTICAS

### 12.1 Líneas de Código Aproximadas
- **Frontend:** ~3,500 líneas
  - Vue components: ~2,000
  - JavaScript: ~1,000
  - CSS/Styles: ~500

- **Backend:** ~4,000 líneas
  - PHP endpoints: ~3,000
  - Models/Services: ~1,000

- **SQL:** ~500 líneas
  - Schema: ~200
  - Seeders: ~300

**Total:** ~8,000 líneas de código

### 12.2 Componentes del Sistema
- **Vistas Vue:** 10 páginas principales
- **API Endpoints:** 22 archivos PHP
- **Tablas de BD:** 18 tablas
- **Repositorios:** 7 repositorios
- **Stores Pinia:** 2 stores

---

## 13. INSTALACIÓN Y EJECUCIÓN

### 13.1 Requisitos Previos
- XAMPP (Apache, MySQL, PHP 8.0+)
- Node.js 16+ y npm
- Composer

### 13.2 Instalación Backend

```bash
# 1. Clonar proyecto
cd C:\xampp\htdocs\NomadiaOficial\backend

# 2. Instalar dependencias
composer install

# 3. Configurar base de datos
# Importar database/schema_and_seed.sql en MySQL

# 4. Iniciar servidor
php -S localhost:8000 -t public
```

### 13.3 Instalación Frontend

```bash
# 1. Ir a directorio frontend
cd C:\xampp\htdocs\NomadiaOficial\frontend

# 2. Instalar dependencias
npm install

# 3. Iniciar dev server
npm run dev
```

### 13.4 Acceso al Sistema
- **Frontend:** http://localhost:5173
- **Backend API:** http://localhost:8000/api/v1/
- **MySQL:** localhost:3306

### 13.5 Usuarios de Prueba

```
Admin:
- Email: testadmin@example.com
- Password: password

Guía:
- Email: testguia@example.com
- Password: password

Viajero:
- Email: testtraveler@example.com
- Password: password
```

---

## 14. VENTAJAS DEL PROYECTO

### 14.1 Técnicas
✅ Arquitectura escalable y mantenible  
✅ Separación clara de responsabilidades  
✅ API RESTful bien estructurada  
✅ Base de datos normalizada  
✅ Código reutilizable y modular  
✅ Sistema de roles flexible  

### 14.2 Funcionales
✅ Flujo completo de negocio implementado  
✅ Múltiples roles de usuario  
✅ Sistema de aprobación de contenido  
✅ Gestión completa de reservas y pagos  
✅ Auditoría de acciones administrativas  
✅ Dashboard con métricas en tiempo real  

### 14.3 De Experiencia
✅ Interfaz moderna y profesional  
✅ Responsive design  
✅ Feedback visual inmediato  
✅ Navegación intuitiva  
✅ Sistema de filtros y búsqueda  

---

## 15. TRABAJO FUTURO

### 15.1 Funcionalidades Pendientes
- [ ] Sistema de mensajería entre guías y viajeros
- [ ] Notificaciones push/email
- [ ] Integración con pasarelas de pago reales
- [ ] Sistema de cupones y descuentos
- [ ] Geolocalización y mapas
- [ ] Chat en tiempo real
- [ ] App móvil nativa

### 15.2 Mejoras Técnicas
- [ ] Implementar tokens CSRF
- [ ] Optimización de imágenes automática
- [ ] CDN para assets estáticos
- [ ] Caché de consultas frecuentes
- [ ] Tests unitarios y de integración
- [ ] CI/CD pipeline
- [ ] Containerización con Docker

### 15.3 Optimizaciones
- [ ] Lazy loading de imágenes
- [ ] Paginación en backend
- [ ] Compresión de respuestas API
- [ ] Service Workers (PWA)
- [ ] Optimización SEO

---

## 16. CONCLUSIONES

### 16.1 Logros del Proyecto
El proyecto **Nomadia** cumple exitosamente con los objetivos planteados:

1. ✅ **Sistema completo de marketplace turístico** con roles diferenciados
2. ✅ **Backend robusto** con API RESTful bien estructurada
3. ✅ **Frontend moderno** con Vue.js y diseño responsive
4. ✅ **Base de datos normalizada** con integridad referencial
5. ✅ **Sistema de autenticación y autorización** funcional
6. ✅ **Flujos de negocio completos** (reserva, pago, calificación)
7. ✅ **Panel administrativo** con control total del sistema

### 16.2 Aprendizajes Técnicos
- Implementación de arquitectura MVC en PHP
- Desarrollo de SPA con Vue.js 3 Composition API
- Diseño de APIs RESTful
- Gestión de sesiones y autenticación
- Manejo de archivos y uploads
- Sistema de roles y permisos

### 16.3 Impacto Potencial
El sistema tiene potencial real para:
- Conectar guías locales con turistas
- Promover turismo sostenible
- Generar ingresos para comunidades locales
- Ofrecer experiencias auténticas

---

## 📞 CONTACTO Y SOPORTE

**Desarrollador:** [Tu Nombre]  
**Email:** [tu-email@ejemplo.com]  
**GitHub:** [tu-usuario]  
**Fecha de última actualización:** Noviembre 2025

---

## 📚 REFERENCIAS

### Documentación Oficial
- Vue.js: https://vuejs.org/
- Vite: https://vitejs.dev/
- Bootstrap: https://getbootstrap.com/
- PHP: https://www.php.net/
- MySQL: https://dev.mysql.com/doc/

### Recursos Utilizados
- Bootstrap Icons: https://icons.getbootstrap.com/
- Axios: https://axios-http.com/
- Pinia: https://pinia.vuejs.org/
- Vue Router: https://router.vuejs.org/

---

**FIN DE LA DOCUMENTACIÓN**

*Documento generado para defensa previa del proyecto Nomadia*  
*Versión 1.0 - Noviembre 2025*
