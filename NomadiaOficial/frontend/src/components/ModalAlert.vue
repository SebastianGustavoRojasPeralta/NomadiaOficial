<template>
  <div 
    class="modal fade" 
    id="globalAlertModal" 
    tabindex="-1" 
    aria-labelledby="globalAlertModalLabel" 
    aria-hidden="true"
    data-bs-backdrop="static"
  >
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header border-0" :class="headerClass">
          <h5 class="modal-title fw-bold" id="globalAlertModalLabel">
            <i :class="iconClass" class="me-2"></i>
            {{ title }}
          </h5>
          <button 
            type="button" 
            class="btn-close" 
            :class="{ 'btn-close-white': type === 'error' || type === 'success' }"
            data-bs-dismiss="modal" 
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body py-4">
          <p class="mb-0" v-html="message"></p>
        </div>
        <div class="modal-footer border-0 bg-light">
          <button 
            type="button" 
            class="btn"
            :class="btnClass"
            data-bs-dismiss="modal"
          >
            {{ btnText }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const type = ref('info') // 'success', 'error', 'warning', 'info'
const title = ref('Notificación')
const message = ref('')
const btnText = ref('Aceptar')

const headerClass = computed(() => {
  switch (type.value) {
    case 'success':
      return 'bg-success text-white'
    case 'error':
      return 'bg-danger text-white'
    case 'warning':
      return 'bg-warning'
    case 'info':
    default:
      return 'bg-primary text-white'
  }
})

const iconClass = computed(() => {
  switch (type.value) {
    case 'success':
      return 'bi bi-check-circle-fill'
    case 'error':
      return 'bi bi-x-circle-fill'
    case 'warning':
      return 'bi bi-exclamation-triangle-fill'
    case 'info':
    default:
      return 'bi bi-info-circle-fill'
  }
})

const btnClass = computed(() => {
  switch (type.value) {
    case 'success':
      return 'btn-success'
    case 'error':
      return 'btn-danger'
    case 'warning':
      return 'btn-warning'
    case 'info':
    default:
      return 'btn-primary'
  }
})

const show = (options) => {
  type.value = options.type || 'info'
  title.value = options.title || getTitleByType(options.type)
  message.value = options.message || ''
  btnText.value = options.btnText || 'Aceptar'
}

const getTitleByType = (t) => {
  switch (t) {
    case 'success':
      return 'Éxito'
    case 'error':
      return 'Error'
    case 'warning':
      return 'Advertencia'
    case 'info':
    default:
      return 'Información'
  }
}

defineExpose({ show })
</script>

<style scoped>
.modal-content {
  border-radius: 12px;
  overflow: hidden;
}

.modal-header {
  padding: 1.25rem 1.5rem;
}

.modal-body {
  padding: 1.5rem;
  font-size: 1rem;
  line-height: 1.6;
}

.modal-footer {
  padding: 1rem 1.5rem;
}
</style>
