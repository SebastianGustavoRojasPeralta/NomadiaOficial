<template>
  <div class="experience-show bg-light min-vh-100 py-5">
    <!-- Modal de Éxito -->
    <div class="modal fade" id="reservaExitosaModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title">
              <i class="bi bi-check-circle me-2"></i>¡Reserva Creada Exitosamente!
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center py-4">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
            <h4 class="mt-3 mb-3">Tu reserva ha sido registrada</h4>
            <p class="text-muted mb-4">Puedes completar el pago desde "Mis Reservas" para confirmar tu experiencia.</p>
            <div class="d-grid gap-2">
              <button class="btn btn-primary" @click="irAMisReservas">
                <i class="bi bi-calendar-check me-2"></i>Ver Mis Reservas
              </button>
              <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                Continuar Explorando
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row g-4">
        <div class="col-lg-8">
          <!-- Galería de Imágenes -->
          <div class="card border-0 shadow-sm mb-4">
            <div class="gallery-container">
              <!-- Imagen Principal Grande -->
              <div class="main-image-wrapper">
                <img 
                  :src="currentImage || imageSrc || 'https://via.placeholder.com/800x400?text=Sin+Imagen'" 
                  class="main-gallery-image" 
                  :alt="exp.titulo || exp.title"
                />
              </div>
              
              <!-- Thumbnails Horizontales -->
              <div v-if="allImages.length > 1" class="thumbnails-row">
                <div 
                  v-for="(img, index) in allImages" 
                  :key="index"
                  class="thumbnail-item"
                  :class="{ 'active': currentImage === img }"
                  @click="currentImage = img"
                >
                  <img :src="img" :alt="`Foto ${index + 1}`" />
                </div>
              </div>
            </div>
          </div>

          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <span class="badge bg-danger mb-2">{{ exp.categoria || 'Categoría' }}</span>
                  <h2 class="fw-bold mb-0">{{ exp.titulo || exp.title }}</h2>
                </div>
                <div class="text-end">
                  <div class="h3 text-danger mb-0">{{ formatPrice(exp.precio || exp.price) }}</div>
                  <small class="text-muted">por persona</small>
                </div>
              </div>

              <div class="row g-3 mb-4 pb-4 border-bottom">
                <div class="col-6 col-md-3">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-clock fs-4 text-danger me-2"></i>
                    <div>
                      <small class="text-muted d-block">Duración</small>
                      <strong>{{ formatDuration(exp.duration_minutes) }}</strong>
                    </div>
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-people fs-4 text-danger me-2"></i>
                    <div>
                      <small class="text-muted d-block">Capacidad</small>
                      <strong>{{ exp.cantidad || exp.capacity || '10' }} personas</strong>
                    </div>
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-geo-alt fs-4 text-danger me-2"></i>
                    <div>
                      <small class="text-muted d-block">Ubicación</small>
                      <strong>{{ exp.location || 'La Paz' }}</strong>
                    </div>
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-star-fill fs-4 text-warning me-2"></i>
                    <div>
                      <small class="text-muted d-block">Calificación</small>
                      <strong v-if="calificaciones.length > 0">{{ promedioCalificacion }} ({{ calificaciones.length }})</strong>
                      <strong v-else class="text-muted">Sin calificaciones</strong>
                    </div>
                  </div>
                </div>
              </div>

              <div class="mb-4">
                <h5 class="fw-bold mb-3">Descripción</h5>
                <p class="text-muted" style="white-space: pre-wrap;">{{ exp.descripcion || exp.description }}</p>
              </div>

              <!-- Información del Guía -->
              <div v-if="exp.guia_nombre" class="mb-4 pb-4 border-bottom">
                <h5 class="fw-bold mb-3"><i class="bi bi-person-badge me-2"></i>Tu Guía</h5>
                <div class="d-flex align-items-center">
                  <img 
                    :src="guiaPhoto" 
                    class="rounded-circle me-3 border border-2 border-primary"
                    style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                    :alt="exp.guia_nombre"
                    @click="goToGuiaProfile"
                  />
                  <div class="flex-grow-1">
                    <h6 class="mb-1 fw-bold" style="cursor: pointer;" @click="goToGuiaProfile">{{ exp.guia_nombre }}</h6>
                    <p class="text-muted small mb-2">
                      <i class="bi bi-geo-alt"></i> {{ exp.guia_ubicacion || 'Bolivia' }}
                    </p>
                    <p class="small mb-2" v-if="exp.guia_anos_experiencia">
                      <i class="bi bi-award text-warning"></i> {{ exp.guia_anos_experiencia }} años de experiencia
                    </p>
                    <p class="small text-muted mb-0" v-if="exp.guia_idiomas">
                      <i class="bi bi-translate"></i> {{ exp.guia_idiomas }}
                    </p>
                  </div>
                  <button @click="goToGuiaProfile" class="btn btn-outline-primary btn-sm">
                    Ver Perfil
                  </button>
                </div>
              </div>

              <!-- Idiomas -->
              <div v-if="exp.idioma_principal || exp.idiomas_adicionales" class="mb-4 pb-4 border-bottom">
                <h5 class="fw-bold mb-3"><i class="bi bi-translate me-2"></i>Idiomas</h5>
                <div class="d-flex flex-wrap gap-2">
                  <span v-if="exp.idioma_principal" class="badge bg-primary px-3 py-2">
                    <i class="bi bi-star-fill me-1"></i>{{ exp.idioma_principal }}
                  </span>
                  <span 
                    v-for="(idioma, index) in idiomasAdicionalesArray" 
                    :key="index" 
                    class="badge bg-secondary px-3 py-2"
                  >
                    {{ idioma }}
                  </span>
                </div>
              </div>

              <div class="mb-4">
                <h5 class="fw-bold mb-3">Qué Incluye</h5>
                <ul class="list-unstyled">
                  <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Guía profesional certificado</li>
                  <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Equipo necesario</li>
                  <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Seguro de accidentes</li>
                  <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Fotografías de la experiencia</li>
                </ul>
              </div>

              <div class="alert alert-info mb-0">
                <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Requisitos</h6>
                <ul class="mb-0 ps-3">
                  <li>Edad mínima: 12 años</li>
                  <li>Ropa cómoda y zapatos deportivos</li>
                  <li>Llegar 15 minutos antes</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Sección de Calificaciones y Reseñas -->
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-4">
                <i class="bi bi-star-fill text-warning me-2"></i>Calificaciones y Reseñas
                <span class="badge bg-secondary ms-2">{{ calificaciones.length }}</span>
              </h5>
              
              <div v-if="calificaciones.length === 0" class="text-center py-4 text-muted">
                <i class="bi bi-chat-quote fs-1 d-block mb-3"></i>
                <p>Aún no hay calificaciones para esta experiencia.</p>
                <p class="small">¡Sé el primero en compartir tu opinión!</p>
              </div>

              <div v-else>
                <!-- Resumen de calificaciones -->
                <div class="bg-light rounded p-4 mb-4">
                  <div class="row align-items-center">
                    <div class="col-md-4 text-center border-end">
                      <div class="display-3 fw-bold text-warning">{{ promedioCalificacion }}</div>
                      <div class="star-rating-display mb-2">
                        <i v-for="star in 5" :key="star" 
                           :class="star <= Math.round(promedioCalificacion) ? 'bi-star-fill' : 'bi-star'" 
                           class="bi text-warning fs-5"></i>
                      </div>
                      <p class="text-muted mb-0">{{ calificaciones.length }} {{ calificaciones.length === 1 ? 'calificación' : 'calificaciones' }}</p>
                    </div>
                    <div class="col-md-8 ps-4">
                      <div v-for="stars in [5, 4, 3, 2, 1]" :key="stars" class="d-flex align-items-center mb-2">
                        <span class="me-2 small">{{ stars }} <i class="bi bi-star-fill text-warning"></i></span>
                        <div class="progress flex-grow-1 me-3" style="height: 8px;">
                          <div class="progress-bar bg-warning" 
                               :style="{ width: porcentajeEstrellas(stars) + '%' }"></div>
                        </div>
                        <span class="small text-muted">{{ contarEstrellas(stars) }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Lista de reseñas -->
                <div class="reviews-list">
                  <div v-for="cal in calificaciones" :key="cal.id" class="review-item border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <div>
                        <strong class="d-block">{{ cal.usuario_name || 'Usuario' }}</strong>
                        <div class="star-rating-small">
                          <i v-for="star in 5" :key="star" 
                             :class="star <= cal.rating ? 'bi-star-fill' : 'bi-star'" 
                             class="bi text-warning small"></i>
                        </div>
                      </div>
                      <small class="text-muted">{{ formatFechaReview(cal.created_at || cal.fecha) }}</small>
                    </div>
                    <p class="text-muted mb-0" v-if="cal.comentario">{{ cal.comentario }}</p>
                    <p class="text-muted fst-italic small mb-0" v-else>Sin comentario</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-4">Reserva tu Experiencia</h5>
              <form @submit.prevent="handleReservation">
                <div class="mb-3">
                  <label class="form-label fw-semibold">
                    <i class="bi bi-calendar3 me-2"></i>Fecha y Hora
                  </label>
                  <input 
                    v-model="form.fecha_reserva" 
                    type="datetime-local" 
                    class="form-control form-control-lg" 
                    required 
                  />
                </div>
                
                <div class="mb-4">
                  <label class="form-label fw-semibold">
                    <i class="bi bi-people me-2"></i>Cantidad de Personas
                  </label>
                  <input 
                    v-model.number="form.cantidad" 
                    type="number" 
                    min="1" 
                    :max="exp.cantidad || exp.capacity || 10"
                    class="form-control form-control-lg" 
                    required 
                  />
                  <small class="text-muted">Máximo {{ exp.cantidad || exp.capacity || 10 }} personas</small>
                </div>

                <div class="bg-light rounded p-3 mb-4">
                  <div class="d-flex justify-content-between mb-2">
                    <span>{{ formatPrice(exp.precio || exp.price) }} x {{ form.cantidad }}</span>
                    <strong>Bs. {{ calculateTotal() }}</strong>
                  </div>
                  <hr class="my-2">
                  <div class="d-flex justify-content-between">
                    <strong>Total</strong>
                    <strong class="text-danger fs-5">Bs. {{ calculateTotal() }}</strong>
                  </div>
                </div>

                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-danger btn-lg">
                    <i class="bi bi-credit-card me-2"></i>Pagar Ahora
                  </button>
                  <button 
                    type="button" 
                    class="btn btn-outline-danger btn-lg" 
                    @click="reserveWithoutPayment"
                  >
                    <i class="bi bi-bookmark me-2"></i>Reservar y Pagar Después
                  </button>
                </div>

                <p class="text-center text-muted small mt-3 mb-0">
                  <i class="bi bi-shield-check me-1"></i>Pago 100% seguro
                </p>
              </form>
            </div>
          </div>

          <div class="card border-0 shadow-sm mt-4" v-if="disponibilidades.length > 0">
            <div class="card-body p-4">
              <h6 class="fw-bold mb-3">
                <i class="bi bi-calendar-check me-2"></i>Disponibilidad
              </h6>
              <ul class="list-unstyled mb-0">
                <li v-for="d in disponibilidades" :key="d.id" class="mb-2">
                  <i class="bi bi-dot"></i>
                  <span class="text-muted">{{ formatDate(d.fecha) }}</span>
                  <span class="badge bg-success ms-2">{{ d.cupos }} cupos</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import useExperienciaStore from '../stores/experienciaStore'
import disponibilidadRepository from '../api/disponibilidadRepository'
import reservaRepository from '../api/reservaRepository'
import calificacionRepository from '../api/calificacionRepository'

const route = useRoute()
const router = useRouter()
const store = useExperienciaStore()
const exp = ref({})
const disponibilidades = ref([])
const calificaciones = ref([])
const currentImage = ref(null)

const apiBase = (import.meta.env.VITE_API_BASE || 'http://localhost:8000/api/v1').replace(/\/api\/v1\/?$/i, '')
const imageSrc = computed(() => {
  if (!exp.value) return null
  if (exp.value.image_url) return exp.value.image_url
  const img = exp.value.image || exp.value.imagen
  if (!img) return null
  if (/^https?:\/\//i.test(img)) return img
  return apiBase.replace(/\/$/, '') + '/' + img.replace(/^\//, '')
})

// Computed para idiomas adicionales
const idiomasAdicionalesArray = computed(() => {
  if (!exp.value.idiomas_adicionales) return []
  return exp.value.idiomas_adicionales.split(',').map(i => i.trim()).filter(i => i.length > 0)
})

// Computed para imágenes adicionales
const imagenesAdicionales = computed(() => {
  if (!exp.value.imagenes) return []
  
  // Si ya es un array, devolverlo directamente
  if (Array.isArray(exp.value.imagenes)) {
    return exp.value.imagenes
  }
  
  // Si es string, intentar parsear
  if (typeof exp.value.imagenes === 'string') {
    try {
      const parsed = JSON.parse(exp.value.imagenes)
      return Array.isArray(parsed) ? parsed : []
    } catch (e) {
      console.error('Error parseando imagenes:', e)
      return []
    }
  }
  
  return []
})

// Computed para todas las imágenes (principal + adicionales)
const allImages = computed(() => {
  const images = []
  if (imageSrc.value) images.push(imageSrc.value)
  imagenesAdicionales.value.forEach(img => {
    images.push(getFullImageUrl(img))
  })
  return images
})

// Computed para foto del guía
const guiaPhoto = computed(() => {
  if (exp.value.guia_foto) {
    return getFullImageUrl(exp.value.guia_foto)
  }
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(exp.value.guia_nombre || 'Guia')}&size=80&background=dc3545&color=fff`
})

const form = ref({ fecha_reserva: '', cantidad: 1 })

// Función para obtener URL completa de imagen
function getFullImageUrl(img) {
  if (!img) return null
  if (/^https?:\/\//i.test(img)) return img
  return apiBase.replace(/\/$/, '') + '/' + img.replace(/^\//, '')
}

// Función para abrir modal de imagen (opcional)
function openImageModal(img) {
  window.open(getFullImageUrl(img), '_blank')
}

onMounted(async () => {
  const id = route.params.id
  await store.fetchExperienciaById(id)
  exp.value = store.experienciaActual || {}
  const d = await disponibilidadRepository.list({ experiencia_id: id })
  disponibilidades.value = d || []
  
  // Cargar calificaciones de la experiencia
  try {
    const cals = await calificacionRepository.list({ experiencia_id: id })
    calificaciones.value = cals || []
  } catch (error) {
    console.error('Error cargando calificaciones:', error)
    calificaciones.value = []
  }
})

function formatPrice(v) { 
  const n = Number(v || 0)
  return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(n)
}

function formatDuration(minutes) {
  if (!minutes) return 'No especificado'
  if (minutes < 60) return `${minutes} minutos`
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  if (mins === 0) return `${hours} ${hours === 1 ? 'hora' : 'horas'}`
  return `${hours}h ${mins}min`
}

function goToGuiaProfile() {
  if (exp.value.guia_id) {
    router.push(`/guia/${exp.value.guia_id}`)
  }
}

function formatDate(dateStr) {
  try {
    const date = new Date(dateStr)
    return date.toLocaleDateString('es-ES', {
      weekday: 'short',
      day: 'numeric',
      month: 'short',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch {
    return dateStr
  }
}

function calculateTotal() {
  const price = Number(exp.value.precio || exp.value.price || 0)
  return (price * form.value.cantidad).toFixed(2)
}

function handleReservation() {
  const user = JSON.parse(sessionStorage.getItem('user') || 'null')
  if (!user) { 
    alert('Por favor inicia sesión primero')
    router.push({ name: 'login' })
    return 
  }
  
  router.push({
    name: 'checkout',
    query: {
      experienceId: exp.value.id,
      title: exp.value.titulo || exp.value.title,
      guide: exp.value.guia_name || 'Guía Local',
      date: new Date(form.value.fecha_reserva).toLocaleDateString('es-ES', { 
        weekday: 'long', 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
      }),
      time: new Date(form.value.fecha_reserva).toLocaleTimeString('es-ES', { 
        hour: '2-digit', 
        minute: '2-digit' 
      }),
      participants: form.value.cantidad,
      price: exp.value.precio || exp.value.price,
      fecha_reserva: form.value.fecha_reserva.replace('T', ' '),
      image: imageSrc.value
    }
  })
}

async function reserveWithoutPayment() {
  const user = JSON.parse(sessionStorage.getItem('user') || 'null')
  if (!user) { 
    alert('Por favor inicia sesión primero')
    router.push({ name: 'login' })
    return 
  }
  
  try {
    const payload = {
      experiencia_id: exp.value.id,
      usuario_id: user.id,
      fecha_reserva: form.value.fecha_reserva.replace('T', ' '),
      cantidad: form.value.cantidad,
      total: calculateTotal(),
      status: 'pending_payment'
    }
    
    const res = await reservaRepository.create(payload)
    
    if (res && res.reserva) {
      // Mostrar modal de éxito
      const modalElement = document.getElementById('reservaExitosaModal')
      if (modalElement) {
        const modal = new bootstrap.Modal(modalElement)
        modal.show()
      }
    } else {
      throw new Error('Error al crear la reserva')
    }
  } catch (e) { 
    console.error(e)
    alert('Error al crear la reserva: ' + (e.message || 'Error desconocido'))
  }
}

const irAMisReservas = () => {
  const modalElement = document.getElementById('reservaExitosaModal')
  if (modalElement) {
    const modal = bootstrap.Modal.getInstance(modalElement)
    if (modal) modal.hide()
  }
  router.push({ name: 'mis-reservas' })
}

// Funciones para calificaciones
const promedioCalificacion = computed(() => {
  if (calificaciones.value.length === 0) return 0
  const sum = calificaciones.value.reduce((acc, cal) => acc + Number(cal.rating), 0)
  return (sum / calificaciones.value.length).toFixed(1)
})

function contarEstrellas(stars) {
  return calificaciones.value.filter(cal => Number(cal.rating) === stars).length
}

function porcentajeEstrellas(stars) {
  if (calificaciones.value.length === 0) return 0
  return ((contarEstrellas(stars) / calificaciones.value.length) * 100).toFixed(0)
}

function formatFechaReview(fecha) {
  if (!fecha) return 'Fecha no disponible'
  try {
    const date = new Date(fecha)
    const now = new Date()
    const diffTime = Math.abs(now - date)
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))
    
    if (diffDays === 0) return 'Hoy'
    if (diffDays === 1) return 'Ayer'
    if (diffDays < 7) return `Hace ${diffDays} días`
    if (diffDays < 30) return `Hace ${Math.floor(diffDays / 7)} semanas`
    if (diffDays < 365) return `Hace ${Math.floor(diffDays / 30)} meses`
    return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' })
  } catch {
    return fecha
  }
}
</script>

<style scoped>
.experience-show {
  background: linear-gradient(135deg, #f5f7fa 0%, #fff 100%);
}

.gallery-container {
  padding: 0;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
}

.main-image-wrapper {
  width: 100%;
  height: 450px;
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
}

.main-gallery-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.thumbnails-row {
  display: flex;
  gap: 8px;
  padding: 12px;
  background: #f8f9fa;
  overflow-x: auto;
  scrollbar-width: thin;
}

.thumbnails-row::-webkit-scrollbar {
  height: 6px;
}

.thumbnails-row::-webkit-scrollbar-track {
  background: #e9ecef;
  border-radius: 10px;
}

.thumbnails-row::-webkit-scrollbar-thumb {
  background: #adb5bd;
  border-radius: 10px;
}

.thumbnail-item {
  flex-shrink: 0;
  width: 120px;
  height: 80px;
  cursor: pointer;
  border-radius: 8px;
  overflow: hidden;
  border: 3px solid transparent;
  transition: all 0.3s ease;
}

.thumbnail-item:hover {
  border-color: #dc3545;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.thumbnail-item.active {
  border-color: #dc3545;
  box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.thumbnail-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.experience-image {
  height: 400px;
  object-fit: cover;
  border-radius: 12px;
}

.card {
  border-radius: 12px;
  transition: transform 0.2s;
}

.sticky-top {
  z-index: 1020;
}

.cursor-pointer {
  cursor: pointer;
  transition: transform 0.2s;
}

.cursor-pointer:hover {
  transform: scale(1.05);
}

.review-item:last-child {
  border-bottom: none !important;
  padding-bottom: 0 !important;
  margin-bottom: 0 !important;
}

.star-rating-display {
  display: flex;
  gap: 0.25rem;
  justify-content: center;
}

.star-rating-small {
  display: inline-flex;
  gap: 0.15rem;
}

.reviews-list {
  max-height: 600px;
  overflow-y: auto;
}

.reviews-list::-webkit-scrollbar {
  width: 6px;
}

.reviews-list::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.reviews-list::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

.reviews-list::-webkit-scrollbar-thumb:hover {
  background: #555;
}

@media (max-width: 991px) {
  .experience-image {
    height: 300px;
  }
}
</style>
