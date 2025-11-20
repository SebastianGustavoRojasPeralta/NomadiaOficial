<template>
  <div class="checkout-page bg-light min-vh-100 py-5">
    <div class="container">
      <div class="row">
        <!-- Left Column - Payment Form -->
        <div class="col-lg-7 mb-4">
          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <h4 class="fw-bold mb-4">Confirmar y Pagar</h4>

              <!-- Payment Method Tabs -->
              <ul class="nav nav-pills mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                  <button 
                    class="nav-link active" 
                    data-bs-toggle="pill" 
                    data-bs-target="#credit-card" 
                    type="button"
                    role="tab"
                  >
                    Tarjeta Crédito/Débito
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button 
                    class="nav-link" 
                    data-bs-toggle="pill" 
                    data-bs-target="#qr-payment" 
                    type="button"
                    role="tab"
                  >
                    Pago QR
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button 
                    class="nav-link" 
                    data-bs-toggle="pill" 
                    data-bs-target="#mobile-wallet" 
                    type="button"
                    role="tab"
                  >
                    Billeteras Móviles
                  </button>
                </li>
              </ul>

              <!-- Tab Content -->
              <div class="tab-content">
                <!-- Credit/Debit Card Tab -->
                <div class="tab-pane fade show active" id="credit-card" role="tabpanel">
                  <form @submit.prevent="processPayment">
                    <div class="mb-3">
                      <label class="form-label fw-semibold">Nombre en la tarjeta</label>
                      <input 
                        v-model="paymentForm.cardName" 
                        type="text" 
                        class="form-control" 
                        placeholder="Nombre completo del titular"
                        required
                      >
                    </div>

                    <div class="mb-3">
                      <label class="form-label fw-semibold">Número de tarjeta</label>
                      <input 
                        v-model="paymentForm.cardNumber" 
                        type="text" 
                        class="form-control" 
                        placeholder="Número de tarjeta de crédito o débito"
                        maxlength="19"
                        @input="formatCardNumber"
                        required
                      >
                    </div>

                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Fecha de expiración</label>
                        <input 
                          v-model="paymentForm.expiryDate" 
                          type="text" 
                          class="form-control" 
                          placeholder="MM/YY"
                          maxlength="5"
                          @input="formatExpiry"
                          required
                        >
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Código de seguridad</label>
                        <input 
                          v-model="paymentForm.cvv" 
                          type="text" 
                          class="form-control" 
                          placeholder="123"
                          maxlength="4"
                          required
                        >
                      </div>
                    </div>

                    <div class="mb-3">
                      <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Paga de forma segura con</span>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa" style="height: 20px;">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" style="height: 20px;">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/American_Express_logo_%282018%29.svg" alt="Amex" style="height: 20px;">
                      </div>
                    </div>

                    <div class="mb-4">
                      <label class="form-label fw-semibold">Código de descuento</label>
                      <div class="input-group">
                        <input 
                          v-model="discountCode" 
                          type="text" 
                          class="form-control" 
                          placeholder="Ingresa código de descuento"
                        >
                        <button class="btn btn-outline-primary" type="button" @click="applyDiscount">
                          Aplicar
                        </button>
                      </div>
                    </div>

                    <button 
                      type="submit" 
                      class="btn btn-danger w-100 py-3 fw-bold"
                      :disabled="processing"
                    >
                      <span v-if="processing" class="spinner-border spinner-border-sm me-2"></span>
                      {{ processing ? 'Procesando...' : 'Pagar Ahora' }}
                    </button>
                  </form>
                </div>

                <!-- QR Payment Tab -->
                <div class="tab-pane fade" id="qr-payment" role="tabpanel">
                  <div class="text-center py-5">
                    <div class="qr-code-placeholder mb-3 mx-auto" style="width: 200px; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                      <i class="bi bi-qr-code" style="font-size: 80px; color: #666;"></i>
                    </div>
                    <p class="text-muted">Escanea el código QR con tu aplicación de banca móvil</p>
                    <button class="btn btn-danger w-100 mt-3" @click="processPayment">Confirmar Pago</button>
                  </div>
                </div>

                <!-- Mobile Wallet Tab -->
                <div class="tab-pane fade" id="mobile-wallet" role="tabpanel">
                  <div class="py-3">
                    <p class="text-muted mb-3">Selecciona tu billetera móvil:</p>
                    <div class="list-group">
                      <button class="list-group-item list-group-item-action d-flex align-items-center" @click="processPayment">
                        <i class="bi bi-wallet2 me-3 fs-4"></i>
                        <span>Tigo Money</span>
                      </button>
                      <button class="list-group-item list-group-item-action d-flex align-items-center" @click="processPayment">
                        <i class="bi bi-phone me-3 fs-4"></i>
                        <span>Simple</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Cancellation Policy -->
          <div class="card shadow-sm border-0 mt-4">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-3">Política de Cancelación</h5>
              <p class="mb-2">
                Cancelación gratuita hasta <strong>24 horas</strong> antes del inicio de la experiencia. 
                Después de este período, se cobrará una <strong>tarifa de servicio del 50%</strong> por cancelaciones tardías, 
                y <strong>100%</strong> por no presentarse.
              </p>
              <a href="#" class="text-danger">Leer política completa</a>
            </div>
          </div>

          <!-- Booking Notes -->
          <div class="card shadow-sm border-0 mt-4">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-3">Notas de Reserva</h5>
              <textarea 
                v-model="bookingNotes"
                class="form-control" 
                rows="3" 
                placeholder="Incluye cualquier solicitud especial o notas para tu guía (ej., restricciones dietéticas, necesidades de movilidad)."
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Right Column - Booking Summary -->
        <div class="col-lg-5">
          <div class="card shadow-sm border-0 position-sticky" style="top: 20px;">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-4">Resumen de Reserva</h5>

              <div class="d-flex mb-4">
                <img 
                  :src="experienceImage" 
                  alt="Experience" 
                  class="rounded me-3"
                  style="width: 80px; height: 80px; object-fit: cover;"
                >
                <div>
                  <h6 class="fw-bold mb-1">{{ bookingSummary.title }}</h6>
                  <p class="text-muted small mb-0">{{ bookingSummary.guide }}</p>
                </div>
              </div>

              <div class="mb-3">
                <div class="d-flex align-items-start mb-2">
                  <i class="bi bi-calendar3 me-2 text-muted"></i>
                  <div>
                    <div class="fw-semibold">{{ bookingSummary.date }}</div>
                  </div>
                </div>
                <div class="d-flex align-items-start mb-2">
                  <i class="bi bi-clock me-2 text-muted"></i>
                  <div>{{ bookingSummary.time }}</div>
                </div>
                <div class="d-flex align-items-start">
                  <i class="bi bi-people me-2 text-muted"></i>
                  <div>{{ bookingSummary.participants }} Participante(s)</div>
                </div>
              </div>

              <hr>

              <div class="mb-3">
                <div class="d-flex justify-content-between mb-2">
                  <span>Subtotal ({{ bookingSummary.participants }}x Bs. {{ bookingSummary.pricePerPerson }})</span>
                  <span>Bs. {{ subtotal }}</span>
                </div>
                <div v-if="discount > 0" class="d-flex justify-content-between mb-2 text-success">
                  <span>Descuento</span>
                  <span>-Bs. {{ discount }}</span>
                </div>
              </div>

              <hr>

              <div class="d-flex justify-content-between mb-4">
                <span class="fw-bold fs-5">Total</span>
                <span class="fw-bold fs-5 text-danger">Bs. {{ total }}</span>
              </div>

              <button 
                class="btn btn-danger w-100 py-3 fw-bold mb-3"
                @click="processPayment"
                :disabled="processing"
              >
                Confirmar Pago
              </button>

              <p class="text-center small text-muted mb-0">
                Al confirmar, aceptas los 
                <a href="#" class="text-danger">Términos de Servicio</a> y la 
                <a href="#" class="text-danger">Política de Cancelación</a> de Nomadia.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import reservaRepository from '@/api/reservaRepository'
import pagoRepository from '@/api/pagoRepository'

const route = useRoute()
const router = useRouter()
const userStore = useUserStore()

const processing = ref(false)
const discountCode = ref('')
const bookingNotes = ref('')
const discount = ref(0)

const paymentForm = ref({
  cardName: '',
  cardNumber: '',
  expiryDate: '',
  cvv: ''
})

// Booking data from route params/query
const bookingSummary = ref({
  experienceId: route.query.experienceId || '',
  title: route.query.title || 'Experiencia Cultural',
  guide: route.query.guide || 'Guía Local',
  date: route.query.date || 'Sábado, 25 Noviembre 2024',
  time: route.query.time || '10:00 AM - 1:00 PM',
  participants: parseInt(route.query.participants) || 2,
  pricePerPerson: parseFloat(route.query.price) || 75.00
})

const experienceImage = computed(() => {
  return route.query.image || 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=200&h=200&fit=crop'
})

const subtotal = computed(() => {
  return (bookingSummary.value.participants * bookingSummary.value.pricePerPerson).toFixed(2)
})

const total = computed(() => {
  return (parseFloat(subtotal.value) - discount.value).toFixed(2)
})

const formatCardNumber = (e) => {
  let value = e.target.value.replace(/\s/g, '')
  let formatted = value.match(/.{1,4}/g)?.join(' ') || value
  paymentForm.value.cardNumber = formatted
}

const formatExpiry = (e) => {
  let value = e.target.value.replace(/\D/g, '')
  if (value.length >= 2) {
    value = value.substring(0, 2) + '/' + value.substring(2, 4)
  }
  paymentForm.value.expiryDate = value
}

const applyDiscount = () => {
  if (discountCode.value.toUpperCase() === 'NOMADIA10') {
    discount.value = parseFloat(subtotal.value) * 0.10
    alert('¡Código de descuento aplicado! 10% de descuento')
  } else if (discountCode.value) {
    alert('Código de descuento inválido')
  }
}

const processPayment = async () => {
  if (!userStore.user) {
    alert('Debes iniciar sesión para continuar')
    router.push('/login')
    return
  }

  try {
    processing.value = true

    // 1. Create reservation with confirmed status (payment is immediate)
    const reservaPayload = {
      experiencia_id: parseInt(bookingSummary.value.experienceId),
      usuario_id: userStore.user.id,
      fecha_reserva: route.query.fecha_reserva || new Date().toISOString().slice(0, 19).replace('T', ' '),
      cantidad: bookingSummary.value.participants,
      total: parseFloat(total.value),
      status: 'confirmed', // Payment is immediate, so set as confirmed
      notes: bookingNotes.value
    }

    console.log('Creating reserva with payload:', reservaPayload)

    const reservaRes = await reservaRepository.create(reservaPayload)
    
    console.log('Reserva response:', reservaRes)
    
    if (!reservaRes || !reservaRes.reserva) {
      throw new Error('Error al crear la reserva')
    }

    const reservaId = reservaRes.reserva.id

    // 2. Process payment
    const pagoPayload = {
      reserva_id: reservaId,
      amount: parseFloat(total.value),
      method: 'credit_card',
      status: 'completed',
      card_last4: paymentForm.value.cardNumber.slice(-4)
    }

    console.log('Creating pago with payload:', pagoPayload)

    await pagoRepository.create(pagoPayload)

    // 3. Success - redirect to my reservations
    alert('¡Pago procesado exitosamente! Tu reserva ha sido confirmada.')
    router.push({ name: 'mis-reservas' })

  } catch (error) {
    console.error('Payment error:', error)
    alert('Error al procesar el pago: ' + (error.message || 'Error desconocido'))
  } finally {
    processing.value = false
  }
}

onMounted(() => {
  if (!route.query.experienceId) {
    alert('Información de reserva inválida')
    router.push('/experiencias')
  }
})
</script>

<style scoped>
.checkout-page {
  background: #f8f9fa;
}

.nav-pills .nav-link {
  color: #666;
  border: 1px solid #dee2e6;
  margin-right: 0.5rem;
  border-radius: 8px;
}

.nav-pills .nav-link.active {
  background-color: #e63946;
  border-color: #e63946;
}

.card {
  border-radius: 12px;
}

.btn-danger {
  background-color: #e63946;
  border-color: #e63946;
}

.btn-danger:hover {
  background-color: #d62839;
  border-color: #d62839;
}

.list-group-item {
  border-radius: 8px !important;
  margin-bottom: 0.5rem;
}

.list-group-item:hover {
  background-color: #f8f9fa;
}
</style>
