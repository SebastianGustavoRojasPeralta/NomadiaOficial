<template>
  <div class="app-container d-flex flex-column min-vh-100">
    <NavbarComponent />
    
    <main class="flex-grow-1">
      <router-view />
    </main>
    
    <FooterComponent />
    
    <!-- Modal Global de Alertas -->
    <ModalAlert ref="globalAlertModal" />
  </div>
</template>

<script setup>
import NavbarComponent from './components/NavbarComponent.vue'
import FooterComponent from './components/FooterComponent.vue'
import ModalAlert from './components/ModalAlert.vue'
import { useUserStore } from './stores/user'
import { useAlert } from './composables/useAlert'
import { onMounted, ref } from 'vue'

const store = useUserStore()
const globalAlertModal = ref(null)
const { setModalComponent, showAlert } = useAlert()

onMounted(() => {
  store.loadFromSession()
  
  // Inicializar el componente modal global
  if (globalAlertModal.value) {
    setModalComponent(globalAlertModal.value)
  }
  
  // Sobrescribir alert global para usar el modal
  const _origAlert = window.alert.bind(window)
  window.alert = function (msg) {
    try {
      // Suprimir alerts de scripts inyectados
      if (typeof msg === 'string' && msg.trim().startsWith('Token:')) {
        console.debug('Suppressed injected alert:', msg)
        return
      }
      // Usar el modal en lugar de alert
      showAlert(msg)
    } catch (e) {
      _origAlert(msg)
    }
  }
})
</script>

<style>
:root{
  --brand-primary: #e63946; /* rojo principal */
  --brand-accent: #ff6b6b; /* acento cálido */
  --brand-gradient: linear-gradient(90deg,var(--brand-primary), #ff6b6b 40%, #f1a1a1);
  --muted: #6b7280;
}
body { font-family: system-ui, sans-serif; background:#faf7fb }
.navbar .brand-mark{ width:36px;height:36px;border-radius:8px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700}
.navbar .brand-text{ color:#fff;font-weight:700;letter-spacing:1px}
.hero-banner{ background-image: linear-gradient(rgba(10,10,10,0.35), rgba(10,10,10,0.15)), url('/api/hero.jpg'); background-size:cover;background-position:center;border-radius:8px;padding:60px 24px; color:#fff}
.search-box { max-width:760px; margin: 0 auto; }
.experience-card .price-badge{ background: #ff6b6b;color:#fff;padding:6px 10px;border-radius:8px;font-weight:700}
.experience-card .card-img-top{ height:160px;background-size:cover;background-position:center;border-top-left-radius: calc(.25rem - 1px); border-top-right-radius: calc(.25rem - 1px)}
.popular-title{ color:var(--brand-primary); font-weight:700; margin-top:24px }
</style>
