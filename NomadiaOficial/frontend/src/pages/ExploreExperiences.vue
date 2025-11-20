<template>
  <div class="explore-page">
    <div class="container-fluid py-4">
      <div class="row">
        <!-- Sidebar de Filtros -->
        <div class="col-lg-3 col-md-4 mb-4">
          <div class="filters-sidebar">
            <h5 class="fw-bold mb-4">Filtros</h5>

            <!-- Tipo de Experiencia -->
            <div class="filter-section mb-4">
              <button 
                class="filter-header w-100 d-flex justify-content-between align-items-center"
                @click="toggleSection('type')"
              >
                <span class="fw-semibold">Tipo de Experiencia</span>
                <i class="bi" :class="expanded.type ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
              </button>
              <div v-show="expanded.type" class="filter-content mt-3">
                <div v-if="categorias.length === 0" class="text-muted small">
                  Cargando categorías...
                </div>
                <div class="form-check mb-2" v-for="cat in categorias" :key="cat.id">
                  <input 
                    class="form-check-input" 
                    type="checkbox" 
                    :id="'cat-' + cat.id"
                    :value="cat.nombre"
                    v-model="filters.categorias"
                  >
                  <label class="form-check-label" :for="'cat-' + cat.id">
                    {{ cat.nombre }}
                  </label>
                </div>
              </div>
            </div>

            <!-- Rango de Precio -->
            <div class="filter-section mb-4">
              <button 
                class="filter-header w-100 d-flex justify-content-between align-items-center"
                @click="toggleSection('price')"
              >
                <span class="fw-semibold">Rango de Precio</span>
                <i class="bi" :class="expanded.price ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
              </button>
              <div v-show="expanded.price" class="filter-content mt-3">
                <input 
                  type="range" 
                  class="form-range" 
                  min="0" 
                  max="500" 
                  step="10"
                  v-model="filters.maxPrice"
                >
                <div class="d-flex justify-content-between small text-muted mt-2">
                  <span>Bs. 0</span>
                  <span>Bs. {{ filters.maxPrice }}</span>
                </div>
              </div>
            </div>

            <!-- Idioma -->
            <div class="filter-section mb-4">
              <button 
                class="filter-header w-100 d-flex justify-content-between align-items-center"
                @click="toggleSection('language')"
              >
                <span class="fw-semibold">Idioma</span>
                <i class="bi" :class="expanded.language ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
              </button>
              <div v-show="expanded.language" class="filter-content mt-3">
                <div class="form-check mb-2" v-for="lang in languageOptions" :key="lang.value">
                  <input 
                    class="form-check-input" 
                    type="checkbox" 
                    :id="'lang-' + lang.value"
                    :value="lang.value"
                    v-model="filters.languages"
                  >
                  <label class="form-check-label" :for="'lang-' + lang.value">
                    {{ lang.label }}
                  </label>
                </div>
              </div>
            </div>

            <!-- Duración -->
            <div class="filter-section mb-4">
              <button 
                class="filter-header w-100 d-flex justify-content-between align-items-center"
                @click="toggleSection('duration')"
              >
                <span class="fw-semibold">Duración</span>
                <i class="bi" :class="expanded.duration ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
              </button>
              <div v-show="expanded.duration" class="filter-content mt-3">
                <div class="form-check mb-2" v-for="dur in durationOptions" :key="dur.value">
                  <input 
                    class="form-check-input" 
                    type="radio" 
                    name="duration"
                    :id="'dur-' + dur.value"
                    :value="dur.value"
                    v-model="filters.duration"
                  >
                  <label class="form-check-label" :for="'dur-' + dur.value">
                    {{ dur.label }}
                  </label>
                </div>
              </div>
            </div>

            <!-- Botón Limpiar Filtros -->
            <button class="btn btn-outline-primary w-100" @click="clearFilters">
              <i class="bi bi-x-circle me-2"></i>Limpiar Filtros
            </button>
          </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9 col-md-8">
          <!-- Header -->
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
              <h2 class="fw-bold mb-1">Explorar Experiencias en {{ location }}</h2>
              <p class="text-muted mb-0">{{ filteredExperiences.length }} experiencias encontradas</p>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Cargando...</span>
            </div>
          </div>

          <!-- No Results -->
          <div v-else-if="filteredExperiences.length === 0" class="text-center py-5">
            <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
            <h4 class="text-muted">No se encontraron experiencias</h4>
            <p class="text-muted">Intenta ajustar los filtros para ver más resultados</p>
            <button class="btn btn-primary" @click="clearFilters">Limpiar Filtros</button>
          </div>

          <!-- Experiences Grid -->
          <div v-else class="row g-4">
            <div 
              v-for="exp in filteredExperiences" 
              :key="exp.id"
              class="col-lg-4 col-md-6 col-sm-12"
            >
              <div class="experience-card h-100">
                <div class="card-img-wrapper">
                  <img 
                    :src="getImageUrl(exp)" 
                    :alt="exp.title"
                    class="card-img-top"
                    @error="handleImageError"
                  >
                </div>
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title mb-0">{{ exp.title }}</h5>
                    <span class="badge bg-primary">{{ exp.categoria || 'General' }}</span>
                  </div>
                  
                  <div class="rating mb-2">
                    <i class="bi bi-star-fill text-warning"></i>
                    <span class="ms-1 fw-semibold">{{ exp.rating || '4.5' }}</span>
                    <span class="text-muted small">({{ exp.reviews || '0' }} reseñas)</span>
                  </div>

                  <p class="card-text text-muted small mb-3">
                    {{ truncate(exp.description, 100) }}
                  </p>

                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted small">
                      <i class="bi bi-clock me-1"></i>
                      {{ formatDuration(exp.duration_minutes) }}
                    </div>
                    <div class="text-primary fw-bold fs-5">
                      Bs. {{ parseFloat(exp.price).toFixed(2) }}
                    </div>
                  </div>

                  <button 
                    class="btn btn-primary w-100"
                    @click="viewDetails(exp)"
                  >
                    Ver Detalles
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
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import experienciaRepository from '@/api/experienciaRepository'
import categoriaRepository from '@/api/categoriaRepository'

const route = useRoute()
const router = useRouter()

// State
const experiences = ref([])
const categorias = ref([])
const loading = ref(true)
const location = ref('Chuquisaca')
const searchQuery = ref(route.query.search || '')

const filters = ref({
  categorias: [],
  maxPrice: 500,
  languages: [],
  duration: ''
})

const expanded = ref({
  type: true,
  price: true,
  language: true,
  duration: true
})

// Duration options (in minutes)
const durationOptions = [
  { value: '30-60', label: '30-60 minutos', min: 30, max: 60 },
  { value: '60-120', label: '1-2 horas', min: 60, max: 120 },
  { value: '120-240', label: '2-4 horas (Medio día)', min: 120, max: 240 },
  { value: '240-480', label: '4-8 horas (Día completo)', min: 240, max: 480 },
  { value: '480+', label: 'Más de 8 horas (Multi-día)', min: 480, max: 999999 }
]

const languageOptions = [
  { value: 'español', label: 'Español' },
  { value: 'ingles', label: 'Inglés' },
  { value: 'quechua', label: 'Quechua' },
  { value: 'aymara', label: 'Aymara' }
]

// Computed
const filteredExperiences = computed(() => {
  let result = experiences.value

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(exp => 
      (exp.title || '').toLowerCase().includes(query) ||
      (exp.description || '').toLowerCase().includes(query)
    )
  }

  // Filter by categories
  if (filters.value.categorias.length > 0) {
    result = result.filter(exp => 
      filters.value.categorias.includes(exp.categoria)
    )
  }

  // Filter by price
  result = result.filter(exp => parseFloat(exp.price) <= filters.value.maxPrice)

  // Filter by languages
  if (filters.value.languages.length > 0) {
    result = result.filter(exp => {
      // Obtener todos los idiomas de la experiencia
      const expLanguages = []
      if (exp.idioma_principal) {
        expLanguages.push(exp.idioma_principal.toLowerCase().trim())
      }
      if (exp.idiomas_adicionales) {
        const adicionales = exp.idiomas_adicionales.split(',').map(i => i.toLowerCase().trim())
        expLanguages.push(...adicionales)
      }
      
      // Verificar si al menos uno de los idiomas seleccionados está en la experiencia
      return filters.value.languages.some(selectedLang => 
        expLanguages.some(expLang => expLang.includes(selectedLang.toLowerCase()))
      )
    })
  }

  // Filter by duration
  if (filters.value.duration) {
    const durOpt = durationOptions.find(d => d.value === filters.value.duration)
    if (durOpt) {
      result = result.filter(exp => {
        const minutes = parseInt(exp.duration_minutes || 0)
        return minutes >= durOpt.min && minutes <= durOpt.max
      })
    }
  }

  return result
})

// Methods
const toggleSection = (section) => {
  expanded.value[section] = !expanded.value[section]
}

const clearFilters = () => {
  filters.value = {
    categorias: [],
    maxPrice: 500,
    languages: [],
    duration: ''
  }
  searchQuery.value = ''
}

const getImageUrl = (exp) => {
  const img = exp.imagen || exp.image
  if (img) {
    if (img.startsWith('http') || img.startsWith('/uploads')) {
      return `http://localhost:8000${img.startsWith('/') ? img : '/' + img}`
    }
    return img
  }
  return 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=800&h=400&fit=crop'
}

const handleImageError = (e) => {
  e.target.src = 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=800&h=400&fit=crop'
}

const viewDetails = (exp) => {
  router.push(`/experiencia/${exp.id}`)
}

const truncate = (text, length) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

const formatDuration = (minutes) => {
  if (!minutes) return 'No especificado'
  const mins = parseInt(minutes)
  if (mins < 60) return `${mins} minutos`
  const hours = Math.floor(mins / 60)
  const remainingMins = mins % 60
  if (remainingMins === 0) return `${hours} ${hours === 1 ? 'hora' : 'horas'}`
  return `${hours}h ${remainingMins}min`
}

const loadExperiences = async () => {
  try {
    loading.value = true
    const data = await experienciaRepository.getAll()
    experiences.value = data || []
  } catch (e) {
    console.error('Error loading experiences', e)
    experiences.value = []
  } finally {
    loading.value = false
  }
}

const loadCategorias = async () => {
  try {
    const data = await categoriaRepository.getAll()
    categorias.value = data || []
  } catch (e) {
    console.error('Error loading categories', e)
    categorias.value = []
  }
}

// Watch for route query changes
watch(
  () => route.query.search,
  (newSearch) => {
    searchQuery.value = newSearch || ''
  }
)

onMounted(() => {
  loadExperiences()
  loadCategorias()
})
</script>

<style scoped>
.explore-page {
  background-color: #f8f9fa;
  min-height: 100vh;
}

.filters-sidebar {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  position: sticky;
  top: 20px;
}

.filter-header {
  background: none;
  border: none;
  padding: 0;
  text-align: left;
  cursor: pointer;
  transition: color 0.2s;
}

.filter-header:hover {
  color: var(--bs-primary);
}

.filter-section {
  border-bottom: 1px solid #e9ecef;
  padding-bottom: 1rem;
}

.filter-section:last-child {
  border-bottom: none;
}

.experience-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.experience-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.card-img-wrapper {
  height: 200px;
  overflow: hidden;
}

.card-img-top {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.experience-card:hover .card-img-top {
  transform: scale(1.05);
}

.card-body {
  padding: 1.25rem;
}

.card-title {
  font-size: 1.1rem;
  color: #2c3e50;
}

.rating i {
  font-size: 0.9rem;
}

.form-range::-webkit-slider-thumb {
  background: var(--bs-primary);
}

.form-range::-moz-range-thumb {
  background: var(--bs-primary);
}
</style>
