<template>
  <div>
    <section class="mb-4">
      <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <template v-if="(experiencias || []).length > 0">
          <div class="carousel-indicators">
            <button v-for="(e, idx) in experiencias.slice(0,3)" :key="idx" type="button" data-bs-target="#heroCarousel" :data-bs-slide-to="idx" :class="{ active: idx === 0 }" :aria-current="idx === 0 ? 'true' : null" :aria-label="`Slide ${idx+1}`"></button>
          </div>
          <div class="carousel-inner">
            <div v-for="(e, idx) in experiencias.slice(0,3)" :key="e.id || idx" class="carousel-item" :class="{ active: idx === 0 }">
              <div class="hero-slide" :style="{ backgroundImage: `linear-gradient(rgba(10,10,10,0.35), rgba(10,10,10,0.15)), url('${ imageSrc(e) || '/api/hero.jpg' }')` }"></div>
            </div>
          </div>
        </template>
        <template v-else>
          <div class="carousel-inner">
            <div class="carousel-item active">
              <div class="hero-slide" style="background-image: linear-gradient(rgba(10,10,10,0.35), rgba(10,10,10,0.15)), url('/api/hero.jpg')"></div>
            </div>
          </div>
        </template>
        <div class="carousel-caption d-flex justify-content-center align-items-center flex-column text-center">
          <h1 style="font-size:36px;margin-bottom:8px">Conecta con guías locales y vive experiencias auténticas</h1>
          <p class="lead">Busca experiencias, destinos y descubre actividades diseñadas por anfitriones locales.</p>
          <div class="search-box mt-3 w-100">
            <div class="input-group" style="max-width:760px;margin:0 auto;">
              <input v-model="query" class="form-control form-control-lg" placeholder="Buscar experiencias, destinos..." />
              <button class="btn btn-lg" :style="{ background: 'var(--brand-primary)', color:'#fff', borderColor: 'var(--brand-primary)' }" type="button" @click="onSearch">Buscar</button>
            </div>
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    </section>

    <div class="container">
      <h3 class="popular-title">Experiencias Populares</h3>
      <div v-if="filteredExperiencias.length === 0" class="alert alert-secondary">No hay experiencias con esos filtros.</div>
      <div class="row">
        <div class="col-md-4" v-for="e in filteredExperiencias" :key="e.id">
          <div class="card mb-4 experience-card">
            <div class="card-img-top" :style="{ backgroundImage: `url(${ imageSrc(e) || '/api/default.jpg' })` }"></div>
            <div class="card-body">
              <!-- Info del Guía -->
              <div class="d-flex align-items-center mb-2" v-if="e.guia_nombre">
                <img 
                  :src="getGuiaPhoto(e)" 
                  class="rounded-circle me-2"
                  style="width: 32px; height: 32px; object-fit: cover; cursor: pointer;"
                  :alt="e.guia_nombre"
                  @click.stop="goToGuia(e.guia_id)"
                />
                <small class="text-muted" style="cursor: pointer;" @click.stop="goToGuia(e.guia_id)">
                  {{ e.guia_nombre }}
                </small>
              </div>
              
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h5 class="card-title">{{ e.title }}</h5>
                  <p class="text-muted small mb-1">{{ e.description ? (e.description.length>100? e.description.slice(0,100)+'...': e.description) : '' }}</p>
                </div>
                <div class="text-end">
                  <div class="price-badge">${{ e.price }}</div>
                </div>
              </div>
              <div class="mt-3 d-flex justify-content-between align-items-center">
                <div>
                  <button class="btn btn-outline-primary btn-sm me-2" @click="goToExperience(e)">Reservar</button>
                  <button class="btn btn-outline-secondary btn-sm" @click="showRateForm(e)">Calificar</button>
                </div>
                <div class="text-muted small">
                  <div>Duración: {{ e.duration_minutes || e.duracion || '-' }} min</div>
                  <div>Capacidad: {{ e.cantidad || e.capacity || '-' }} personas</div>
                </div>
              </div>
              <div v-if="actionStatus[e.id]" class="mt-2">
                <small class="text-success">{{ actionStatus[e.id] }}</small>
              </div>
              <div v-if="showRatingFor === e.id" class="mt-3 border-top pt-3">
                <h6 class="mb-2">Califica esta experiencia</h6>
                <div class="mb-2">
                  <label class="form-label small">Estrellas (1-5):</label>
                  <div class="star-rating">
                    <i 
                      v-for="star in 5" 
                      :key="star"
                      class="bi fs-4 cursor-pointer"
                      :class="star <= rating ? 'bi-star-fill text-warning' : 'bi-star text-muted'"
                      @click="rating = star"
                    ></i>
                  </div>
                </div>
                <div class="mb-2">
                  <label class="form-label small">Comentario (opcional):</label>
                  <textarea 
                    v-model="comentario" 
                    class="form-control form-control-sm" 
                    placeholder="Cuéntanos tu experiencia..."
                    rows="2"
                  ></textarea>
                </div>
                <div class="d-flex gap-2">
                  <button class="btn btn-primary btn-sm" @click="submitRating(e)">
                    <i class="bi bi-send me-1"></i>Enviar calificación
                  </button>
                  <button class="btn btn-outline-secondary btn-sm" @click="showRatingFor = null">
                    Cancelar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import experienciaRepository from '../api/experienciaRepository'
import api from '../api/axiosConfig'
import { useUserStore } from '../stores/user'
import { useRouter } from 'vue-router'

const experiencias = ref([])
const loading = ref(false)
const lastAction = ref('idle')
const actionStatus = ref({})
const query = ref('')
const minPrice = ref(null)
const maxPrice = ref(null)
const sortBy = ref('newest')
const showRatingFor = ref(null)
const rating = ref(5)
const comentario = ref('')

const clearFilters = () => {
  query.value = ''
  minPrice.value = null
  maxPrice.value = null
  sortBy.value = 'newest'
}

// search button handler: filtering is reactive, so this is a no-op placeholder
const onSearch = () => {
  // Intentionally empty — `filteredExperiencias` updates reactively from `query`
}

const filteredExperiencias = computed(() => {
  let list = (experiencias.value || []).slice()
  const q = (query.value || '').toString().toLowerCase().trim()
  if (q) {
    list = list.filter(item => {
      return (item.title && item.title.toLowerCase().includes(q)) || (item.description && item.description.toLowerCase().includes(q))
    })
  }
  if (minPrice.value != null && minPrice.value !== '') {
    const min = parseFloat(minPrice.value) || 0
    list = list.filter(i => parseFloat(i.price) >= min)
  }
  if (maxPrice.value != null && maxPrice.value !== '') {
    const max = parseFloat(maxPrice.value) || 0
    list = list.filter(i => parseFloat(i.price) <= max)
  }
  if (sortBy.value === 'priceAsc') list.sort((a,b) => parseFloat(a.price) - parseFloat(b.price))
  else if (sortBy.value === 'priceDesc') list.sort((a,b) => parseFloat(b.price) - parseFloat(a.price))
  else list.sort((a,b) => new Date(b.created_at) - new Date(a.created_at))
  return list
})

const load = async () => {
  try {
    const data = await experienciaRepository.getAll()
    experiencias.value = data || []
  } catch (e) {
    console.error('Error cargando experiencias', e)
    experiencias.value = []
  }
}

const router = useRouter()

const showRateForm = (exp) => {
  if (showRatingFor.value === exp.id) {
    showRatingFor.value = null
  } else {
    showRatingFor.value = exp.id
    rating.value = 5
    comentario.value = ''
  }
}

const submitRating = async (exp) => {
  try {
    const store = useUserStore()
    if (!store.user) {
      alert('Debes iniciar sesión para calificar')
      return
    }
    
    const payload = {
      experiencia_id: exp.id,
      rating: rating.value,
      comentario: comentario.value || null
    }
    
    await api.post('/calificaciones.php', payload)
    actionStatus.value[exp.id] = '✅ Calificación enviada exitosamente'
    showRatingFor.value = null
    rating.value = 5
    comentario.value = ''
    
    setTimeout(() => {
      actionStatus.value[exp.id] = null
    }, 3000)
  } catch (e) {
    console.error('Error al enviar calificación:', e)
    const msg = e?.response?.data?.error || 'Error al calificar'
    actionStatus.value[exp.id] = '❌ ' + msg
  }
}

onMounted(async () => {
  console.log('[Home.vue] mounted')
  // no dev globals exposed
  const store = useUserStore()
  store.loadFromSession()
  await load()
})

// helper to compute absolute image url
const apiBase = (import.meta.env.VITE_API_BASE || 'http://localhost:8000/api/v1').replace(/\/api\/v1\/?$/i, '')
function imageSrc(exp) {
  if (!exp) return null
  if (exp.image_url) return exp.image_url
  const img = exp.image || exp.imagen || exp.image_url
  if (!img) return null
  if (/^https?:\/\//i.test(img)) return img
  return apiBase.replace(/\/$/, '') + '/' + img.replace(/^\//, '')
}

const goToExperience = (experiencia) => {
  router.push(`/experiencia/${experiencia.id}`)
}

function getGuiaPhoto(exp) {
  if (exp.guia_foto) {
    const foto = exp.guia_foto
    if (/^https?:\/\//i.test(foto)) return foto
    return apiBase.replace(/\/$/, '') + '/' + foto.replace(/^\//, '')
  }
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(exp.guia_nombre || 'Guia')}&size=32&background=dc3545&color=fff`
}

function goToGuia(guiaId) {
  if (guiaId) {
    router.push(`/guia/${guiaId}`)
  }
}
</script>

<style scoped>
.hero-slide{ height:300px; background-size:cover; background-position:center; border-radius:8px }
.carousel-caption{ top:20%; transform:translateY(0); }
.search-box .form-control{ border-top-left-radius: .375rem; border-bottom-left-radius: .375rem }
.search-box .btn{ border-top-right-radius: .375rem; border-bottom-right-radius: .375rem }
.cursor-pointer { cursor: pointer; }
.star-rating { display: flex; gap: 0.25rem; }
@media(min-width:992px){ .hero-slide{ height:420px } }
</style>
