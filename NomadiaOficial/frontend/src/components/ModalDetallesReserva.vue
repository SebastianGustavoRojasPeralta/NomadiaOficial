<template>
  <div 
    class="modal fade" 
    id="detallesReservaModal" 
    tabindex="-1" 
    aria-labelledby="detallesReservaModalLabel" 
    aria-hidden="true"
  >
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="detallesReservaModalLabel">
            <i class="bi bi-receipt me-2"></i>Detalles de la Reserva
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <div class="modal-body">
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Cargando...</span>
            </div>
          </div>

          <div v-else-if="detalles" class="details-container">
            <!-- Estado de la Reserva -->
            <div class="alert" :class="getAlertClass(detalles.reserva.status)" role="alert">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="alert-heading mb-1">
                    <i class="bi bi-info-circle me-2"></i>Estado de la Reserva
                  </h6>
                  <span class="badge" :class="getStatusBadgeClass(detalles.reserva.status)">
                    {{ getStatusLabel(detalles.reserva.status) }}
                  </span>
                </div>
                <div class="text-end">
                  <small class="text-muted">Reserva #{{ detalles.reserva.id }}</small>
                </div>
              </div>
            </div>

            <!-- Información de la Experiencia -->
            <div class="card mb-3 border-0 shadow-sm">
              <div class="card-body">
                <h6 class="card-subtitle mb-3 text-primary">
                  <i class="bi bi-compass me-2"></i>Experiencia
                </h6>
                <div class="row g-3">
                  <div class="col-md-4" v-if="detalles.experiencia.imagen">
                    <img 
                      :src="getImageUrl(detalles.experiencia.imagen)" 
                      class="img-fluid rounded" 
                      :alt="detalles.experiencia.title"
                      style="width: 100%; height: 150px; object-fit: cover;"
                    />
                  </div>
                  <div :class="detalles.experiencia.imagen ? 'col-md-8' : 'col-12'">
                    <h5 class="mb-2">{{ detalles.experiencia.title }}</h5>
                    <p class="text-muted mb-2">{{ detalles.experiencia.description }}</p>
                    <div class="d-flex flex-wrap gap-3">
                      <small class="text-muted">
                        <i class="bi bi-geo-alt me-1"></i>{{ detalles.experiencia.location }}
                      </small>
                      <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>{{ detalles.experiencia.duration_minutes }} min
                      </small>
                      <small class="text-muted">
                        <i class="bi bi-tag me-1"></i>{{ detalles.experiencia.categoria }}
                      </small>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Detalles de la Reserva -->
            <div class="card mb-3 border-0 shadow-sm">
              <div class="card-body">
                <h6 class="card-subtitle mb-3 text-primary">
                  <i class="bi bi-calendar-check me-2"></i>Detalles de la Reserva
                </h6>
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-calendar3 text-muted me-2"></i>
                      <div>
                        <small class="text-muted d-block">Fecha de la experiencia</small>
                        <strong>{{ formatDate(detalles.reserva.fecha_reserva) }}</strong>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-people text-muted me-2"></i>
                      <div>
                        <small class="text-muted d-block">Participantes</small>
                        <strong>{{ detalles.reserva.cantidad }} persona(s)</strong>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-clock-history text-muted me-2"></i>
                      <div>
                        <small class="text-muted d-block">Fecha de reserva</small>
                        <strong>{{ formatDate(detalles.reserva.created_at) }}</strong>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-cash text-muted me-2"></i>
                      <div>
                        <small class="text-muted d-block">Total</small>
                        <strong class="text-primary fs-5">Bs. {{ parseFloat(detalles.reserva.total).toFixed(2) }}</strong>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Información del Pago -->
            <div v-if="detalles.pago" class="card mb-3 border-0 shadow-sm">
              <div class="card-body">
                <h6 class="card-subtitle mb-3 text-primary">
                  <i class="bi bi-credit-card me-2"></i>Información del Pago
                </h6>
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-credit-card-2-front text-muted me-2"></i>
                      <div>
                        <small class="text-muted d-block">Método de pago</small>
                        <strong>{{ getPaymentMethodLabel(detalles.pago.method) }}</strong>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-cash-stack text-muted me-2"></i>
                      <div>
                        <small class="text-muted d-block">Monto pagado</small>
                        <strong class="text-success">Bs. {{ parseFloat(detalles.pago.amount).toFixed(2) }}</strong>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-check-circle text-muted me-2"></i>
                      <div>
                        <small class="text-muted d-block">Estado del pago</small>
                        <span class="badge" :class="detalles.pago.status === 'completed' ? 'bg-success' : 'bg-warning'">
                          {{ detalles.pago.status === 'completed' ? 'Completado' : 'Pendiente' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-calendar-check text-muted me-2"></i>
                      <div>
                        <small class="text-muted d-block">Fecha de pago</small>
                        <strong>{{ formatDate(detalles.pago.created_at) }}</strong>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <!-- Aviso para pagos pendientes -->
            <div v-else class="alert alert-warning" role="alert">
              <i class="bi bi-exclamation-triangle me-2"></i>
              Esta reserva aún no tiene un pago registrado. Por favor completa el pago para confirmar tu reserva.
            </div>
          </div>

          <div v-else class="alert alert-danger">
            <i class="bi bi-exclamation-circle me-2"></i>
            No se pudieron cargar los detalles de la reserva. Por favor, intenta nuevamente.
            <div class="mt-2">
              <button class="btn btn-sm btn-danger" @click="cargarDetalles">
                <i class="bi bi-arrow-clockwise me-1"></i>Reintentar
              </button>
            </div>
          </div>
        </div>
        
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-2"></i>Cerrar
          </button>
          <button 
            v-if="detalles && !detalles.pago" 
            type="button" 
            class="btn btn-primary"
            @click="irAPagar"
          >
            <i class="bi bi-credit-card me-2"></i>Ir a Pagar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api/axiosConfig'

const props = defineProps({
  reservaId: {
    type: Number,
    default: null
  }
})

const router = useRouter()
const detalles = ref(null)
const loading = ref(false)

const apiBase = (import.meta.env.VITE_API_BASE || 'http://localhost:8000/api/v1').replace(/\/api\/v1\/?$/i, '')

const cargarDetalles = async () => {
  if (!props.reservaId) {
    console.log('No hay reservaId')
    return
  }
  
  loading.value = true
  detalles.value = null
  
  try {
    console.log('Cargando detalles de reserva ID:', props.reservaId)
    const res = await api.get(`/reserva_detalles.php?id=${props.reservaId}`)
    console.log('Respuesta del servidor:', res.data)
    detalles.value = res.data
  } catch (e) {
    console.error('Error cargando detalles de reserva:', e)
    console.error('Detalles del error:', e.response?.data)
    detalles.value = null
  } finally {
    loading.value = false
  }
}

watch(() => props.reservaId, (newVal) => {
  if (newVal) {
    cargarDetalles()
  }
}, { immediate: true })

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
    'pending': 'bg-warning text-dark',
    'pending_payment': 'bg-warning text-dark',
    'confirmed': 'bg-success',
    'completed': 'bg-info',
    'cancelled': 'bg-danger'
  }
  return classes[status] || 'bg-secondary'
}

const getAlertClass = (status) => {
  const classes = {
    'pending': 'alert-warning',
    'pending_payment': 'alert-warning',
    'confirmed': 'alert-success',
    'completed': 'alert-info',
    'cancelled': 'alert-danger'
  }
  return classes[status] || 'alert-secondary'
}

const getPaymentMethodLabel = (method) => {
  const labels = {
    'credit_card': 'Tarjeta de Crédito',
    'debit_card': 'Tarjeta de Débito',
    'paypal': 'PayPal',
    'cash': 'Efectivo',
    'bank_transfer': 'Transferencia Bancaria',
    'mock': 'Pago de Prueba'
  }
  return labels[method] || method
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

const getImageUrl = (imagen) => {
  if (!imagen) return null
  if (/^https?:\/\//i.test(imagen)) return imagen
  return apiBase.replace(/\/$/, '') + '/' + imagen.replace(/^\//, '')
}

const irAPagar = () => {
  if (!detalles.value) return
  
  const modal = document.getElementById('detallesReservaModal')
  const bsModal = bootstrap.Modal.getInstance(modal)
  if (bsModal) bsModal.hide()
  
  router.push({
    name: 'checkout',
    query: {
      experienceId: detalles.value.experiencia.id,
      reservaId: detalles.value.reserva.id,
      title: detalles.value.experiencia.title,
      date: formatDate(detalles.value.reserva.fecha_reserva),
      participants: detalles.value.reserva.cantidad,
      price: (parseFloat(detalles.value.reserva.total) / detalles.value.reserva.cantidad).toFixed(2),
      total: detalles.value.reserva.total
    }
  })
}

defineExpose({
  cargarDetalles
})
</script>

<style scoped>
.details-container {
  font-size: 0.95rem;
}

.modal-body {
  max-height: 70vh;
  overflow-y: auto;
}

.card {
  border-radius: 12px;
}

.badge {
  font-size: 0.85rem;
  padding: 0.5rem 1rem;
  border-radius: 20px;
}

.img-fluid {
  border: 2px solid #e9ecef;
}
</style>
