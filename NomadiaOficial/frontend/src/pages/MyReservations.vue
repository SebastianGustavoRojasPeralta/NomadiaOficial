<template>
  <div class="reservations-page bg-light min-vh-100 py-5">
    <div class="container">
      <h2 class="fw-bold mb-4">Mis Reservas</h2>
      
      <!-- Modal de Detalles -->
      <ModalDetallesReserva :reservaId="selectedReservaId" ref="modalRef" />
      
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
      </div>
      
      <div v-else-if="reservas.length === 0" class="card shadow-sm border-0">
        <div class="card-body text-center py-5">
          <i class="bi bi-calendar-x fs-1 text-muted mb-3 d-block"></i>
          <h5 class="text-muted">No tienes reservas todavía</h5>
          <p class="text-muted">Explora nuestras experiencias y reserva tu próxima aventura</p>
          <router-link to="/experiencias" class="btn btn-primary mt-3">
            Explorar Experiencias
          </router-link>
        </div>
      </div>

      <div v-else class="row g-4">
        <div class="col-md-6 col-lg-4" v-for="reserva in reservas" :key="reserva.id">
          <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <h5 class="card-title mb-0">{{ reserva.experiencia_title || 'Experiencia' }}</h5>
                <span 
                  class="badge" 
                  :class="getStatusBadgeClass(reserva.status)"
                >
                  {{ getStatusLabel(reserva.status) }}
                </span>
              </div>

              <div class="mb-3">
                <div class="d-flex align-items-center mb-2">
                  <i class="bi bi-calendar3 me-2 text-muted"></i>
                  <span>{{ formatDate(reserva.fecha_reserva) }}</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                  <i class="bi bi-people me-2 text-muted"></i>
                  <span>{{ reserva.cantidad }} persona(s)</span>
                </div>
                <div class="d-flex align-items-center">
                  <i class="bi bi-cash me-2 text-muted"></i>
                  <span class="fw-bold">Bs. {{ parseFloat(reserva.total).toFixed(2) }}</span>
                </div>
              </div>

              <!-- Botones de acción según el estado -->
              <div class="d-grid gap-2">
                <!-- Botón para pagar si está pendiente -->
                <button 
                  v-if="reserva.status === 'pending_payment' || reserva.status === 'pending'"
                  class="btn btn-danger"
                  @click="goToPayment(reserva)"
                >
                  <i class="bi bi-credit-card me-2"></i>Pagar Ahora
                </button>

                <!-- Botón para calificar si está completada -->
                <button 
                  v-if="reserva.status === 'completed'"
                  class="btn btn-outline-primary"
                  @click="rateExperience(reserva)"
                >
                  <i class="bi bi-star me-2"></i>Calificar Experiencia
                </button>

                <!-- Botón para ver detalles -->
                <button 
                  class="btn btn-outline-secondary btn-sm"
                  @click="viewDetails(reserva)"
                >
                  Ver Detalles
                </button>

                <!-- Botón para cancelar si está pendiente o confirmada -->
                <button 
                  v-if="['pending', 'pending_payment', 'confirmed'].includes(reserva.status)"
                  class="btn btn-outline-danger btn-sm"
                  @click="cancelReservation(reserva)"
                >
                  Cancelar Reserva
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api/axiosConfig'
import reservaRepository from '../api/reservaRepository'
import ModalDetallesReserva from '../components/ModalDetallesReserva.vue'
import { Modal } from 'bootstrap'

const router = useRouter()
const reservas = ref([])
const loading = ref(false)
const selectedReservaId = ref(null)
const modalRef = ref(null)

const load = async () => {
  loading.value = true
  try {
    const res = await api.get('/reservas.php')
    reservas.value = res.data || []
  } catch (e) {
    console.error('Error cargando reservas', e)
    reservas.value = []
  } finally {
    loading.value = false
  }
}

const getStatusLabel = (status) => {
  const labels = {
    'pending': 'Pendiente',
    'pending_payment': 'Pendiente de Pago',
    'confirmed': 'Confirmada',
    'completed': 'Completada',
    'cancelled': 'Cancelada'
  }
  return labels[status] || status
}

const getStatusBadgeClass = (status) => {
  const classes = {
    'pending': 'bg-warning',
    'pending_payment': 'bg-warning',
    'confirmed': 'bg-success',
    'completed': 'bg-info',
    'cancelled': 'bg-danger'
  }
  return classes[status] || 'bg-secondary'
}

const formatDate = (dateStr) => {
  try {
    const date = new Date(dateStr)
    return date.toLocaleDateString('es-ES', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch {
    return dateStr
  }
}

const goToPayment = (reserva) => {
  router.push({
    name: 'checkout',
    query: {
      experienceId: reserva.experiencia_id,
      reservaId: reserva.id,
      title: reserva.experiencia_title || 'Experiencia',
      date: formatDate(reserva.fecha_reserva),
      participants: reserva.cantidad,
      price: (parseFloat(reserva.total) / reserva.cantidad).toFixed(2),
      total: reserva.total
    }
  })
}

const rateExperience = (reserva) => {
  router.push({
    name: 'experiencia-show',
    params: { id: reserva.experiencia_id },
    query: { rate: true }
  })
}

const viewDetails = (reserva) => {
  selectedReservaId.value = reserva.id
  
  // Esperar un tick para asegurarnos de que el DOM se ha actualizado
  setTimeout(() => {
    const modalElement = document.getElementById('detallesReservaModal')
    if (modalElement) {
      const modal = new Modal(modalElement)
      modal.show()
    }
  }, 100)
}

const cancelReservation = async (reserva) => {
  if (!confirm('¿Estás seguro de que deseas cancelar esta reserva?')) {
    return
  }

  try {
    // Aquí deberías llamar a un endpoint para cancelar
    // Por ahora solo mostramos un mensaje
    alert('Función de cancelación en desarrollo')
    // await reservaRepository.cancel(reserva.id)
    // await load()
  } catch (error) {
    console.error('Error al cancelar reserva:', error)
    alert('Error al cancelar la reserva')
  }
}

onMounted(() => {
  load()
})
</script>

<style scoped>
.reservations-page {
  background: #f8f9fa;
}

.card {
  border-radius: 12px;
  transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.badge {
  font-size: 0.75rem;
  padding: 0.375rem 0.75rem;
  border-radius: 20px;
}
</style>
