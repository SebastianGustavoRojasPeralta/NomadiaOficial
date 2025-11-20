<template>
  <div class="card h-100 experience-card shadow-sm">
    <div 
      class="card-img-top position-relative" 
      :style="{ backgroundImage: `url(${imageSrc})` }"
    >
      <div class="position-absolute top-0 end-0 m-2">
        <span class="badge bg-danger price-badge">${{ experience.price }}</span>
      </div>
      <div v-if="experience.estado === 'pending'" class="position-absolute top-0 start-0 m-2">
        <span class="badge bg-warning text-dark">Pendiente</span>
      </div>
    </div>
    
    <div class="card-body d-flex flex-column">
      <h5 class="card-title text-truncate" :title="experience.title || experience.titulo">
        {{ experience.title || experience.titulo || 'Sin título' }}
      </h5>
      
      <p class="card-text text-muted small flex-grow-1 description">
        {{ truncatedDescription }}
      </p>

      <div class="d-flex justify-content-between align-items-center text-muted small mb-3">
        <div>
          <i class="bi bi-clock me-1"></i>
          {{ experience.duration_minutes || experience.duracion || '-' }} min
        </div>
        <div>
          <i class="bi bi-people me-1"></i>
          {{ experience.cantidad || experience.capacity || '-' }} personas
        </div>
      </div>

      <div class="d-flex gap-2 mt-auto">
        <button 
          v-if="showReserveButton" 
          class="btn btn-primary btn-sm flex-grow-1" 
          @click="$emit('reserve', experience)"
        >
          <i class="bi bi-calendar-check me-1"></i>Reservar
        </button>
        <button 
          v-if="showDetailsButton" 
          class="btn btn-outline-primary btn-sm flex-grow-1" 
          @click="$emit('details', experience)"
        >
          <i class="bi bi-eye me-1"></i>Ver más
        </button>
        <button 
          v-if="showRateButton" 
          class="btn btn-outline-secondary btn-sm" 
          @click="$emit('rate', experience)"
          title="Calificar experiencia"
        >
          <i class="bi bi-star"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  experience: {
    type: Object,
    required: true
  },
  showReserveButton: {
    type: Boolean,
    default: true
  },
  showDetailsButton: {
    type: Boolean,
    default: true
  },
  showRateButton: {
    type: Boolean,
    default: false
  }
})

defineEmits(['reserve', 'details', 'rate'])

const imageSrc = computed(() => {
  const img = props.experience.imagen || props.experience.image
  if (img) {
    // Si la imagen ya tiene una URL completa o empieza con /uploads, usarla directamente
    if (img.startsWith('http') || img.startsWith('/uploads')) {
      return `http://localhost:8000${img.startsWith('/') ? img : '/' + img}`
    }
    return img
  }
  // Imagen por defecto
  return 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=800&h=400&fit=crop'
})

const truncatedDescription = computed(() => {
  const desc = props.experience.description || props.experience.descripcion || ''
  return desc.length > 100 ? desc.substring(0, 100) + '...' : desc
})
</script>

<style scoped>
.experience-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border: none;
  border-radius: 12px;
  overflow: hidden;
}

.experience-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
}

.card-img-top {
  height: 200px;
  background-size: cover;
  background-position: center;
  background-color: #f0f0f0;
}

.price-badge {
  font-size: 1rem;
  font-weight: 700;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
}

.card-title {
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 0.75rem;
}

.description {
  min-height: 3rem;
  line-height: 1.5;
}

.btn-sm {
  font-size: 0.875rem;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
}

.bi {
  font-size: 0.9rem;
}
</style>
