<template>
  <nav class="navbar navbar-expand-lg navbar-dark" style="background: var(--brand-gradient);">
    <div class="container">
      <router-link class="navbar-brand d-flex align-items-center" to="/">
        <div class="brand-mark me-2">N</div>
        <div class="brand-text">NOMADIA</div>
      </router-link>
      
      <!-- Botón hamburger para móviles -->
      <button 
        class="navbar-toggler" 
        type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#navbarMain" 
        aria-controls="navbarMain" 
        aria-expanded="false" 
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarMain">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <router-link class="nav-link text-white" to="/">Inicio</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link text-white" to="/experiencias">Explorar Experiencias</router-link>
          </li>
          <li v-if="user" class="nav-item">
            <router-link class="nav-link text-white" to="/mis-reservas">Mis Reservas</router-link>
          </li>
          
          <!-- Dashboard Links según rol -->
          <li v-if="isAdmin" class="nav-item">
            <router-link class="nav-link text-white fw-bold" to="/admin">
              <i class="bi bi-speedometer2 me-1"></i>Panel Admin
            </router-link>
          </li>
          <li v-if="isGuide && !isAdmin" class="nav-item">
            <router-link class="nav-link text-white fw-bold" to="/guia">
              <i class="bi bi-compass me-1"></i>Panel Guía
            </router-link>
          </li>
          
          <li v-if="!user" class="nav-item">
            <router-link class="nav-link text-white" to="/como-funciona">Cómo Funciona</router-link>
          </li>
          <li v-if="user && !isGuide && !isAdmin" class="nav-item">
            <button class="nav-link text-white btn btn-link" @click="convertirseEnGuia">
              <i class="bi bi-briefcase me-1"></i>Ser Guía
            </button>
          </li>
        </ul>

        <!-- Search Bar -->
        <form class="d-flex me-3" style="max-width: 300px;" @submit.prevent="handleSearch">
          <div class="input-group">
            <input 
              v-model="searchQuery"
              class="form-control form-control-sm border-white" 
              type="search" 
              placeholder="Buscar experiencias..." 
              aria-label="Search"
              style="background: rgba(255,255,255,0.2); color: white; border-radius: 20px 0 0 20px;"
            >
            <button class="btn btn-light btn-sm" type="submit" style="border-radius: 0 20px 20px 0;">
              <i class="bi bi-search"></i>
            </button>
          </div>
        </form>

        <div class="d-flex align-items-center flex-column flex-lg-row gap-2">
          <template v-if="user">
            <span class="text-white fw-bold">{{ user.name }}</span>
            <button class="btn btn-outline-light btn-sm" @click="logout">
              <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
            </button>
          </template>
          <template v-else>
            <router-link class="btn btn-outline-light btn-sm" to="/login">
              <i class="bi bi-person me-1"></i>Iniciar Sesión
            </router-link>
            <router-link class="btn btn-light btn-sm px-3" to="/register">
              <i class="bi bi-person-plus me-1"></i>Registrarse
            </router-link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useUserStore } from '../stores/user'
import { useRouter } from 'vue-router'
import api from '../api/axiosConfig'

const router = useRouter()
const store = useUserStore()
const user = computed(() => store.user)
const searchQuery = ref('')

const isGuide = computed(() => {
  const role = (store.user?.role || '').toLowerCase()
  return ['guide', 'guia', 'guía', 'host', 'anfitrion'].includes(role)
})

const isAdmin = computed(() => {
  const role = (store.user?.role || '').toLowerCase()
  return role === 'admin'
})

const logout = () => {
  store.logout()
  window.location.href = '/'
}

const convertirseEnGuia = async () => {
  if (!user.value) {
    router.push('/login')
    return
  }
  
  if (confirm('¿Deseas convertirte en guía? Podrás crear y gestionar experiencias para viajeros.')) {
    try {
      const res = await api.post('/make_me_guide.php')
      if (res?.data?.user) {
        store.setUser(res.data.user)
        alert('¡Felicitaciones! Ahora eres un guía. Serás redirigido a tu panel de guía.')
        router.push('/guia')
        setTimeout(() => {
          window.location.reload()
        }, 500)
      }
    } catch (e) {
      console.error('Error al convertirse en guía:', e)
      alert('Error al procesar la solicitud. Por favor intenta nuevamente.')
    }
  }
}

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({
      name: 'explore-experiences',
      query: { search: searchQuery.value }
    })
  }
}
</script>

<style scoped>
.navbar {
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.brand-mark {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: rgba(255,255,255,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-weight: 700;
  font-size: 20px;
}

.brand-text {
  color: #fff;
  font-weight: 700;
  letter-spacing: 1.5px;
  font-size: 1.3rem;
}

.nav-link {
  transition: opacity 0.2s;
}

.nav-link:hover {
  opacity: 0.8;
}

@media (max-width: 991px) {
  .navbar-collapse {
    background: rgba(0,0,0,0.1);
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
  }
}
</style>
