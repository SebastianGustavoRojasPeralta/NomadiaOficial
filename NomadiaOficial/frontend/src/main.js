import { createApp } from 'vue'
import App from './App.vue'
import { createPinia } from 'pinia'
import router from './router'
import axios from 'axios'

// Bootstrap CSS y JS
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

// Estilos globales personalizados
import './assets/styles.css'

// API base - ajusta si usas php artisan serve en otro puerto
axios.defaults.baseURL = import.meta.env.VITE_API_BASE || 'http://localhost:8000/api/v1'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app')
