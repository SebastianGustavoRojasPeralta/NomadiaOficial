<template>
  <div class="guide-profile bg-light min-vh-100 py-5">
    <div class="container" v-if="guia">
      <!-- Header del Perfil -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-md-5 text-center">
          <img 
            :src="guiaPhoto" 
            class="rounded-circle mb-3 border border-4 border-primary"
            style="width: 150px; height: 150px; object-fit: cover;"
            :alt="guia.name"
          />
          <h2 class="fw-bold mb-2">{{ guia.name }}</h2>
          <p class="text-muted mb-3">
            <i class="bi bi-geo-alt-fill text-danger"></i> {{ guia.ubicacion || 'Bolivia' }}
          </p>
          
          <!-- Rating -->
          <div class="mb-3">
            <span class="h4 text-warning me-2">
              <i class="bi bi-star-fill"></i> {{ guia.rating_promedio || '5.0' }}
            </span>
            <span class="text-muted">({{ guia.total_reviews || 0 }} {{ guia.total_reviews === 1 ? 'review' : 'reviews' }})</span>
          </div>

          <!-- Bio -->
          <p class="text-start mb-4" v-if="guia.bio">{{ guia.bio }}</p>
        </div>
      </div>

      <!-- Información del Guía -->
      <div class="row g-4 mb-4">
        <!-- Languages Spoken -->
        <div class="col-md-6" v-if="guia.idiomas_hablados">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title mb-3">
                <i class="bi bi-translate text-primary"></i> Languages Spoken
              </h5>
              <div class="d-flex flex-wrap gap-2">
                <span 
                  v-for="(idioma, index) in idiomas" 
                  :key="index" 
                  class="badge bg-light text-dark border px-3 py-2"
                >
                  {{ idioma }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Certifications -->
        <div class="col-md-6" v-if="guia.certificaciones">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title mb-3">
                <i class="bi bi-award text-warning"></i> Certifications & Achievements
              </h5>
              <div class="d-flex flex-wrap gap-2">
                <span 
                  v-for="(cert, index) in certificaciones" 
                  :key="index" 
                  class="badge bg-light text-dark border px-3 py-2"
                >
                  {{ cert }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Experiencias Ofrecidas -->
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <h3 class="mb-4">Experiences Offered by {{ guia.name }}</h3>
          
          <div v-if="guia.experiencias && guia.experiencias.length > 0" class="row g-4">
            <div 
              v-for="exp in guia.experiencias" 
              :key="exp.id" 
              class="col-md-6 col-lg-4"
            >
              <div class="card h-100 border-0 shadow-sm cursor-pointer" @click="goToExperience(exp.id)">
                <img 
                  :src="getImageUrl(exp.imagen)" 
                  class="card-img-top" 
                  style="height: 200px; object-fit: cover;"
                  :alt="exp.title"
                />
                <div class="card-body">
                  <span class="badge bg-danger mb-2">{{ exp.categoria }}</span>
                  <h5 class="card-title">{{ exp.title }}</h5>
                  <p class="card-text text-muted small">{{ truncate(exp.description, 100) }}</p>
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="h5 text-danger mb-0">{{ formatPrice(exp.price) }}</span>
                    <span class="text-muted small">{{ formatDuration(exp.duration_minutes) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
            <p>Este guía aún no tiene experiencias publicadas</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-else class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const apiBase = 'http://localhost/NomadiaOficial/backend/public/api/v1'

const guia = ref(null)

const guiaPhoto = computed(() => {
  if (guia.value?.foto) {
    return `${apiBase.replace('/api/v1', '')}${guia.value.foto}`
  }
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(guia.value?.name || 'Guia')}&size=150&background=dc3545&color=fff`
})

const idiomas = computed(() => {
  if (!guia.value?.idiomas_hablados) return []
  return guia.value.idiomas_hablados.split(',').map(i => i.trim()).filter(i => i.length > 0)
})

const certificaciones = computed(() => {
  if (!guia.value?.certificaciones) return []
  return guia.value.certificaciones.split(',').map(c => c.trim()).filter(c => c.length > 0)
})

onMounted(async () => {
  const guiaId = route.params.id
  try {
    const res = await fetch(`${apiBase}/perfil_guia_publico.php?id=${guiaId}`)
    if (!res.ok) throw new Error('Guía no encontrado')
    guia.value = await res.json()
  } catch (error) {
    console.error('Error:', error)
    alert('Error al cargar el perfil del guía')
    router.push('/')
  }
})

function getImageUrl(imagen) {
  if (!imagen) return 'https://via.placeholder.com/400x300?text=Sin+Imagen'
  if (imagen.startsWith('http')) return imagen
  return `${apiBase.replace('/api/v1', '')}${imagen}`
}

function formatPrice(v) {
  const n = Number(v || 0)
  return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(n)
}

function formatDuration(minutes) {
  if (!minutes) return 'No especificado'
  if (minutes < 60) return `${minutes} min`
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  return mins > 0 ? `${hours}h ${mins}min` : `${hours}h`
}

function truncate(text, length) {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

function goToExperience(id) {
  router.push(`/experiencia/${id}`)
}
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
  transition: transform 0.2s;
}

.cursor-pointer:hover {
  transform: translateY(-5px);
}

.badge {
  font-size: 0.9rem;
  font-weight: 500;
}
</style>
