<template>
  <div class="guide-dashboard">
    <div class="container-fluid py-4">
      <!-- Header con estadísticas -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold mb-0">Panel de Guía</h2>
            <div>
              <router-link to="/guia/perfil/editar" class="btn btn-outline-primary me-2">
                <i class="bi bi-person-gear me-2"></i>Editar Perfil
              </router-link>
              <router-link to="/guia/experiencias/nueva" class="btn btn-danger">
                <i class="bi bi-plus-circle me-2"></i>Crear Nueva Experiencia
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                    <i class="bi bi-compass fs-3 text-primary"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="text-muted mb-1">Experiencias</h6>
                  <h3 class="mb-0 fw-bold">{{ experiencias.length }}</h3>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="rounded-circle bg-success bg-opacity-10 p-3">
                    <i class="bi bi-calendar-check fs-3 text-success"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="text-muted mb-1">Reservas Totales</h6>
                  <h3 class="mb-0 fw-bold">{{ reservas.length }}</h3>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                    <i class="bi bi-clock-history fs-3 text-warning"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="text-muted mb-1">Pendientes</h6>
                  <h3 class="mb-0 fw-bold">{{ reservasPendientes }}</h3>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="rounded-circle bg-info bg-opacity-10 p-3">
                    <i class="bi bi-cash-coin fs-3 text-info"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="text-muted mb-1">Confirmadas</h6>
                  <h3 class="mb-0 fw-bold">{{ reservasConfirmadas }}</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#experiencias-tab" type="button">
            <i class="bi bi-compass me-2"></i>Mis Experiencias
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reservas-tab" type="button" @click="loadReservas">
            <i class="bi bi-bookmark me-2"></i>Reservas Recibidas
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#calendario-tab" type="button">
            <i class="bi bi-calendar3 me-2"></i>Disponibilidad
          </button>
        </li>
      </ul>

      <!-- Tab Content -->
      <div class="tab-content">
        <!-- Experiencias Tab -->
        <div class="tab-pane fade show active" id="experiencias-tab">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div v-if="loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Cargando...</span>
                </div>
              </div>
              
              <div v-else-if="experiencias.length === 0" class="text-center py-5">
                <i class="bi bi-compass fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">No tienes experiencias publicadas</h5>
                <p class="text-muted">Crea tu primera experiencia para comenzar a recibir reservas</p>
                <router-link to="/guia/experiencias/nueva" class="btn btn-danger">
                  <i class="bi bi-plus-circle me-2"></i>Crear Experiencia
                </router-link>
              </div>

              <div v-else class="row g-3">
                <div class="col-md-6 col-lg-4" v-for="e in experiencias" :key="e.id">
                  <div class="card h-100 border-0 shadow-sm hover-card">
                    <img 
                      :src="getImageUrl(e.imagen)" 
                      class="card-img-top" 
                      style="height: 200px; object-fit: cover;"
                      alt="Experiencia"
                      @error="handleImageError($event, e.imagen)"
                    >
                    <div class="card-body">
                      <h5 class="card-title fw-bold">{{ e.titulo || e.title }}</h5>
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-primary">{{ e.categoria }}</span>
                        <span class="fw-bold text-danger">Bs. {{ e.precio ?? e.price }}</span>
                      </div>
                      <div class="text-muted small mb-3">
                        <i class="bi bi-people me-1"></i>Capacidad: {{ e.cantidad ?? e.capacity ?? '-' }}
                      </div>
                      <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary flex-fill" @click="editExperience(e)">
                          <i class="bi bi-pencil me-1"></i>Editar
                        </button>
                        <button class="btn btn-sm btn-outline-secondary flex-fill" @click="selectExperience(e)">
                          <i class="bi bi-calendar3 me-1"></i>Calendario
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Reservas Tab -->
        <div class="tab-pane fade" id="reservas-tab">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0 fw-bold">Reservas Recibidas</h5>
                <div class="btn-group" role="group">
                  <button 
                    type="button" 
                    class="btn btn-sm"
                    :class="filtroReservas === 'todas' ? 'btn-danger' : 'btn-outline-secondary'"
                    @click="filtroReservas = 'todas'"
                  >
                    Todas
                  </button>
                  <button 
                    type="button" 
                    class="btn btn-sm"
                    :class="filtroReservas === 'pendientes' ? 'btn-danger' : 'btn-outline-secondary'"
                    @click="filtroReservas = 'pendientes'"
                  >
                    Pendientes
                  </button>
                  <button 
                    type="button" 
                    class="btn btn-sm"
                    :class="filtroReservas === 'confirmadas' ? 'btn-danger' : 'btn-outline-secondary'"
                    @click="filtroReservas = 'confirmadas'"
                  >
                    Confirmadas/Pagadas
                  </button>
                </div>
              </div>

              <div v-if="loadingReservas" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Cargando...</span>
                </div>
              </div>

              <div v-else-if="reservasFiltradas.length === 0" class="text-center py-5">
                <i class="bi bi-bookmark fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">No hay reservas {{ filtroReservas === 'todas' ? '' : filtroReservas }}</h5>
              </div>

              <div v-else class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Cliente</th>
                      <th>Experiencia</th>
                      <th>Fecha</th>
                      <th>Personas</th>
                      <th>Total</th>
                      <th>Estado</th>
                      <th>Pago</th>
                      <th>Fecha Reserva</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="r in reservasFiltradas" :key="r.id">
                      <td>
                        <div class="fw-semibold">{{ r.cliente_nombre }}</div>
                        <small class="text-muted">{{ r.cliente_email }}</small>
                      </td>
                      <td>{{ r.experiencia_titulo }}</td>
                      <td>
                        <small>{{ formatFecha(r.fecha_experiencia) }}</small>
                      </td>
                      <td>
                        <span class="badge bg-secondary">{{ r.num_personas }} <i class="bi bi-people"></i></span>
                      </td>
                      <td class="fw-bold">Bs. {{ r.total }}</td>
                      <td>
                        <span 
                          class="badge"
                          :class="getStatusBadgeClass(r.status)"
                        >
                          {{ getStatusText(r.status) }}
                        </span>
                      </td>
                      <td>
                        <span v-if="r.pago" class="text-success">
                          <i class="bi bi-check-circle-fill me-1"></i>
                          {{ r.pago.metodo }}
                        </span>
                        <span v-else class="text-warning">
                          <i class="bi bi-clock me-1"></i>Sin pagar
                        </span>
                      </td>
                      <td>
                        <small>{{ formatFecha(r.created_at) }}</small>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Calendario Tab -->
        <div class="tab-pane fade" id="calendario-tab">
          <div class="row">
            <div class="col-md-8">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <h5 class="fw-bold mb-4">Selecciona una Experiencia</h5>
                  <div class="list-group">
                    <button 
                      v-for="e in experiencias" 
                      :key="e.id"
                      class="list-group-item list-group-item-action"
                      :class="{ 'active': selected && selected.id === e.id }"
                      @click="selectExperience(e)"
                    >
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-0">{{ e.titulo || e.title }}</h6>
                          <small>{{ e.categoria }}</small>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                      </div>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div v-if="selected" class="card border-0 shadow-sm">
                <div class="card-body">
                  <h5 class="fw-bold mb-3">Agregar Disponibilidad</h5>
                  <p class="text-muted small">{{ selected.titulo || selected.title }}</p>
                  
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Fecha y Hora</label>
                    <input 
                      v-model="disDate" 
                      type="datetime-local" 
                      class="form-control"
                    />
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Cupos Disponibles</label>
                    <input 
                      v-model.number="disCupos" 
                      type="number" 
                      min="1" 
                      class="form-control"
                    />
                  </div>
                  
                  <button class="btn btn-success w-100" @click="addDisponibilidad">
                    <i class="bi bi-plus-circle me-2"></i>Agregar
                  </button>
                  
                  <small v-if="disStatus" class="text-muted d-block mt-2 text-center">
                    {{ disStatus }}
                  </small>

                  <hr class="my-4" />

                  <h6 class="fw-bold mb-3">Disponibilidades</h6>
                  <div v-if="loadingDisp" class="text-center py-3">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                  </div>
                  
                  <div v-else-if="disponibilidades.length === 0" class="text-muted small text-center py-3">
                    No hay disponibilidades
                  </div>
                  
                  <div v-else class="list-group list-group-flush">
                    <div 
                      v-for="d in disponibilidades" 
                      :key="d.id"
                      class="list-group-item px-0"
                    >
                      <div class="d-flex justify-content-between align-items-start">
                        <div>
                          <div class="fw-semibold small">{{ formatFecha(d.fecha) }}</div>
                          <small class="text-muted">Cupos: {{ d.cupos }}</small>
                        </div>
                        <button 
                          class="btn btn-sm btn-outline-danger"
                          @click="deleteDisponibilidad(d.id)"
                        >
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-else class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-5">
                  <i class="bi bi-calendar3 fs-1 mb-3 d-block"></i>
                  <p>Selecciona una experiencia para gestionar su disponibilidad</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Edición -->
    <div 
      class="modal fade" 
      id="editModal" 
      tabindex="-1" 
      ref="editModal"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">Editar Experiencia</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveExperience">
              <div class="mb-3">
                <label class="form-label fw-semibold">Título</label>
                <input 
                  v-model="editForm.title" 
                  type="text" 
                  class="form-control"
                  required
                >
              </div>
              
              <div class="mb-3">
                <label class="form-label fw-semibold">Descripción</label>
                <textarea 
                  v-model="editForm.description" 
                  class="form-control" 
                  rows="4"
                  required
                ></textarea>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-semibold">Precio (Bs)</label>
                  <input 
                    v-model.number="editForm.price" 
                    type="number" 
                    class="form-control"
                    min="0"
                    step="0.01"
                    required
                  >
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label fw-semibold">Capacidad</label>
                  <input 
                    v-model.number="editForm.capacity" 
                    type="number" 
                    class="form-control"
                    min="1"
                    required
                  >
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-semibold">Categoría</label>
                  <select v-model="editForm.categoria" class="form-select" required>
                    <option value="">-- Selecciona --</option>
                    <option v-for="cat in categorias" :key="cat.id" :value="cat.nombre">
                      {{ cat.nombre }}
                    </option>
                  </select>
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label fw-semibold">Ubicación</label>
                  <input 
                    v-model="editForm.location" 
                    type="text" 
                    class="form-control"
                    required
                  >
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Duración (minutos)</label>
                <input 
                  v-model.number="editForm.duration_minutes" 
                  type="number" 
                  class="form-control"
                  min="1"
                  required
                >
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-semibold">Idioma Principal</label>
                  <select v-model="editForm.idioma_principal" class="form-select" required>
                    <option value="">-- Selecciona --</option>
                    <option value="Español">Español</option>
                    <option value="Inglés">Inglés</option>
                    <option value="Quechua">Quechua</option>
                    <option value="Francés">Francés</option>
                    <option value="Alemán">Alemán</option>
                    <option value="Portugués">Portugués</option>
                    <option value="Italiano">Italiano</option>
                    <option value="Chino">Chino</option>
                  </select>
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label fw-semibold">Idiomas Adicionales</label>
                  <input 
                    v-model="editForm.idiomas_adicionales" 
                    type="text" 
                    class="form-control"
                    placeholder="Ej: Inglés, Quechua (separados por comas)"
                  >
                  <small class="text-muted">Opcional, separados por comas</small>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Imagen Actual</label>
                <div v-if="editForm.currentImage" class="mb-2">
                  <img 
                    :src="getImageUrl(editForm.currentImage)" 
                    class="img-thumbnail" 
                    style="max-height: 150px;"
                    alt="Imagen actual"
                    @error="handleImageError($event, editForm.currentImage)"
                  >
                </div>
                <label class="form-label fw-semibold mt-2">Cambiar Imagen</label>
                <input 
                  type="file" 
                  class="form-control"
                  accept="image/jpeg,image/png,image/gif,image/webp,image/avif"
                  @change="onEditImageChange"
                >
                <small class="text-muted">Formatos soportados: JPG, PNG, GIF, WebP, AVIF</small>
              </div>

              <div class="d-flex justify-content-between gap-2">
                <button 
                  type="button" 
                  class="btn btn-danger"
                  @click="deleteExperience(editForm.id)"
                  :disabled="savingEdit"
                >
                  <i class="bi bi-trash me-1"></i>
                  Eliminar
                </button>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                  </button>
                  <button 
                    type="submit" 
                    class="btn btn-primary"
                    :disabled="savingEdit"
                  >
                    <span v-if="savingEdit" class="spinner-border spinner-border-sm me-2"></span>
                    {{ savingEdit ? 'Guardando...' : 'Guardar Cambios' }}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Modal } from 'bootstrap'
import api from '../api/axiosConfig'
import adminRepo from '../api/adminRepository'
import { useUserStore } from '../stores/user'

const experiencias = ref([])
const loading = ref(false)
const categorias = ref([])
const selected = ref(null)
const disponibilidades = ref([])
const loadingDisp = ref(false)
const disDate = ref('')
const disCupos = ref(1)
const disStatus = ref('')
const reservas = ref([])
const loadingReservas = ref(false)
const filtroReservas = ref('todas')
const editModal = ref(null)
const editForm = ref({
  id: null,
  title: '',
  description: '',
  price: 0,
  capacity: 1,
  categoria: '',
  location: '',
  duration_minutes: 60,
  idioma_principal: '',
  idiomas_adicionales: '',
  currentImage: null
})
const editImageFile = ref(null)
const savingEdit = ref(false)

const store = useUserStore()
store.loadFromSession()

const reservasPendientes = computed(() => {
  return reservas.value.filter(r => 
    r.status === 'pending' || r.status === 'pending_payment'
  ).length
})

const reservasConfirmadas = computed(() => {
  return reservas.value.filter(r => 
    r.status === 'confirmed' || r.status === 'paid'
  ).length
})

const reservasFiltradas = computed(() => {
  if (filtroReservas.value === 'pendientes') {
    return reservas.value.filter(r => 
      r.status === 'pending' || r.status === 'pending_payment'
    )
  } else if (filtroReservas.value === 'confirmadas') {
    return reservas.value.filter(r => 
      r.status === 'confirmed' || r.status === 'paid'
    )
  }
  return reservas.value
})

const loadExperiencias = async () => {
  loading.value = true
  try {
    const res = await api.get('/experiencias_mine.php')
    experiencias.value = res.data || []
  } catch (e) {
    console.error('loadExperiencias', e)
    experiencias.value = []
  } finally {
    loading.value = false
  }
}

const loadCategorias = async () => {
  try {
    const res = await adminRepo.listCategorias()
    categorias.value = res.data || []
  } catch (e) { 
    console.error('loadCategorias', e)
    categorias.value = []
  }
}

const loadReservas = async () => {
  loadingReservas.value = true
  try {
    console.log('Cargando reservas del guía...')
    const res = await api.get('/reservas_guia.php')
    console.log('Respuesta de reservas:', res.data)
    
    if (res.data && res.data.reservas) {
      reservas.value = res.data.reservas
      console.log('Reservas cargadas:', reservas.value.length)
    } else {
      console.log('No se recibieron reservas o estructura incorrecta')
      reservas.value = []
    }
  } catch (e) {
    console.error('Error al cargar reservas:', e)
    console.error('Detalle del error:', e.response?.data)
    reservas.value = []
  } finally {
    loadingReservas.value = false
  }
}

const getImageUrl = (imagePath) => {
  if (!imagePath) {
    console.log('No image path provided')
    return '/placeholder.jpg'
  }
  if (imagePath.startsWith('http')) {
    console.log('Image URL:', imagePath)
    return imagePath
  }
  // Apuntar al backend en puerto 8000 con cache busting
  const fullUrl = `http://localhost:8000${imagePath}?t=${Date.now()}`
  console.log('Constructed image URL:', fullUrl)
  return fullUrl
}

const handleImageError = (event, imagePath) => {
  console.error('Failed to load image:', imagePath)
  console.error('Attempted URL:', event.target.src)
  // Use a placeholder from placeholder.com
  event.target.src = 'https://via.placeholder.com/400x200/6366f1/ffffff?text=Imagen+No+Disponible'
}

const selectExperience = async (e) => {
  selected.value = e
  await loadDisponibilidades(e.id)
  
  // Cambiar al tab de calendario
  const calendarTab = document.querySelector('[data-bs-target="#calendario-tab"]')
  if (calendarTab) {
    const tab = new bootstrap.Tab(calendarTab)
    tab.show()
  }
}

const loadDisponibilidades = async (experienciaId) => {
  loadingDisp.value = true
  try {
    const res = await api.get('/disponibilidades.php', { params: { experiencia_id: experienciaId } })
    disponibilidades.value = res.data || []
  } catch (e) {
    console.error('loadDisponibilidades', e)
    disponibilidades.value = []
  } finally {
    loadingDisp.value = false
  }
}

const addDisponibilidad = async () => {
  if (!selected.value) return
  if (!disDate.value) { 
    disStatus.value = 'Selecciona fecha'
    return 
  }
  disStatus.value = 'Agregando...'
  try {
    const dt = new Date(disDate.value)
    const y = dt.getFullYear()
    const m = String(dt.getMonth()+1).padStart(2,'0')
    const d = String(dt.getDate()).padStart(2,'0')
    const hh = String(dt.getHours()).padStart(2,'0')
    const mm = String(dt.getMinutes()).padStart(2,'0')
    const formatted = `${y}-${m}-${d} ${hh}:${mm}:00`
    
    const payload = { 
      experiencia_id: selected.value.id, 
      fecha: formatted, 
      cupos: disCupos.value 
    }
    const res = await api.post('/disponibilidades.php', payload)
    
    if (res.data && res.data.disponibilidad) {
      disStatus.value = 'Agregado exitosamente'
      await loadDisponibilidades(selected.value.id)
      disDate.value = ''
      disCupos.value = 1
      setTimeout(() => disStatus.value = '', 3000)
    } else {
      disStatus.value = 'Error al agregar'
    }
  } catch (e) {
    console.error('addDisponibilidad', e)
    disStatus.value = 'Error al agregar'
  }
}

const deleteDisponibilidad = async (id) => {
  if (!confirm('¿Eliminar esta disponibilidad?')) return
  try {
    const res = await api.post('/disponibilidades.php', { action: 'delete', id })
    if (res.data && res.data.deleted) {
      await loadDisponibilidades(selected.value.id)
    }
  } catch (e) {
    console.error('deleteDisponibilidad', e)
  }
}

const editExperience = (experiencia) => {
  editForm.value = {
    id: experiencia.id,
    title: experiencia.titulo || experiencia.title || '',
    description: experiencia.descripcion || experiencia.description || '',
    price: experiencia.precio ?? experiencia.price ?? 0,
    capacity: experiencia.cantidad ?? experiencia.capacity ?? 1,
    categoria: experiencia.categoria || '',
    location: experiencia.location || '',
    duration_minutes: experiencia.duration_minutes ?? experiencia.duracion ?? 60,
    idioma_principal: experiencia.idioma_principal || '',
    idiomas_adicionales: experiencia.idiomas_adicionales || '',
    currentImage: experiencia.imagen || null
  }
  editImageFile.value = null
  
  const modalElement = document.getElementById('editModal')
  const modal = new Modal(modalElement)
  modal.show()
}

const onEditImageChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    console.log('Selected image file:', {
      name: file.name,
      type: file.type,
      size: file.size,
      extension: file.name.split('.').pop()
    })
  }
  editImageFile.value = file || null
}

const saveExperience = async () => {
  savingEdit.value = true
  try {
    const formData = new FormData()
    formData.append('id', editForm.value.id)
    formData.append('title', editForm.value.title)
    formData.append('description', editForm.value.description)
    formData.append('price', editForm.value.price)
    formData.append('capacity', editForm.value.capacity)
    formData.append('categoria', editForm.value.categoria)
    formData.append('location', editForm.value.location)
    formData.append('duration_minutes', editForm.value.duration_minutes)
    formData.append('idioma_principal', editForm.value.idioma_principal)
    formData.append('idiomas_adicionales', editForm.value.idiomas_adicionales || '')
    
    if (editImageFile.value) {
      console.log('Uploading image:', {
        name: editImageFile.value.name,
        type: editImageFile.value.type,
        size: editImageFile.value.size
      })
      formData.append('image', editImageFile.value)
    } else {
      console.log('No image file to upload')
    }

    console.log('Enviando actualización de experiencia:', {
      id: editForm.value.id,
      title: editForm.value.title,
      hasImage: !!editImageFile.value
    })

    const res = await api.post('/experiencias_update.php', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    console.log('Respuesta del servidor:', res.data)

    if (res.data && res.data.success) {
      alert('✅ Experiencia actualizada exitosamente')
      await loadExperiencias()
      
      const modalElement = document.getElementById('editModal')
      const modal = Modal.getInstance(modalElement)
      if (modal) {
        modal.hide()
      }
    } else {
      const errorMsg = res.data?.error || res.data?.message || 'Error desconocido'
      console.error('Error en respuesta:', res.data)
      alert('❌ Error al actualizar: ' + errorMsg)
    }
  } catch (e) {
    console.error('Error completo:', e)
    console.error('Respuesta del servidor:', e.response?.data)
    const errorMsg = e.response?.data?.error || e.response?.data?.message || e.message || 'Error desconocido'
    alert('❌ Error al actualizar la experiencia: ' + errorMsg)
  } finally {
    savingEdit.value = false
  }
}

const deleteExperience = async (experienciaId) => {
  if (!experienciaId) {
    alert('❌ Error: ID de experiencia no válido')
    return
  }
  
  if (!confirm('¿Estás seguro de eliminar esta experiencia? Esta acción no se puede deshacer.')) {
    return
  }
  
  try {
    const res = await api.delete(`/experiencias_delete.php`, {
      data: { id: experienciaId }
    })
    
    if (res.data && res.data.success) {
      alert('✅ Experiencia eliminada exitosamente')
      await loadExperiencias()
      
      const modalElement = document.getElementById('editModal')
      const modal = Modal.getInstance(modalElement)
      if (modal) {
        modal.hide()
      }
    } else {
      const errorMsg = res.data?.error || 'Error al eliminar'
      alert('❌ ' + errorMsg)
    }
  } catch (e) {
    console.error('Error al eliminar:', e)
    const errorMsg = e.response?.data?.error || e.message || 'Error desconocido'
    alert('❌ Error al eliminar la experiencia: ' + errorMsg)
  }
}

const formatFecha = (fecha) => {
  if (!fecha) return '-'
  try {
    const d = new Date(fecha)
    return d.toLocaleString('es-ES', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch {
    return fecha
  }
}

const getStatusBadgeClass = (status) => {
  switch(status) {
    case 'pending': return 'bg-warning'
    case 'pending_payment': return 'bg-info'
    case 'confirmed': return 'bg-primary'
    case 'paid': return 'bg-success'
    case 'cancelled': return 'bg-danger'
    default: return 'bg-secondary'
  }
}

const getStatusText = (status) => {
  switch(status) {
    case 'pending': return 'Pendiente'
    case 'pending_payment': return 'Pago Pendiente'
    case 'confirmed': return 'Confirmada'
    case 'paid': return 'Pagada'
    case 'cancelled': return 'Cancelada'
    default: return status
  }
}

onMounted(() => {
  loadExperiencias()
  loadCategorias()
})
</script>

<style scoped>
.guide-dashboard {
  background: #f8f9fa;
  min-height: 100vh;
}

.hover-card {
  transition: transform 0.2s, box-shadow 0.2s;
}

.hover-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.nav-tabs .nav-link {
  color: #6c757d;
  border: none;
  border-bottom: 2px solid transparent;
}

.nav-tabs .nav-link:hover {
  border-color: #dee2e6;
  color: #495057;
}

.nav-tabs .nav-link.active {
  color: #dc3545;
  border-color: #dc3545;
  background: transparent;
}

.list-group-item.active {
  background-color: #dc3545;
  border-color: #dc3545;
}
</style>
