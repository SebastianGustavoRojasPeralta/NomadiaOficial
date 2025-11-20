<template>
  <div class="login-page">
    <div class="container-fluid p-0">
      <div class="row g-0 min-vh-100">
        <!-- Left side - Image/Brand -->
        <div class="col-lg-6 d-none d-lg-flex login-hero">
          <div class="hero-overlay">
            <div class="text-center text-white p-5">
              <div class="brand-mark-large mb-4">N</div>
              <h1 class="display-4 fw-bold mb-3">Bienvenido a Nomadia</h1>
              <p class="lead">Conecta con guías locales y vive experiencias auténticas</p>
              <div class="mt-5">
                <div class="d-flex justify-content-center gap-4 text-start">
                  <div class="feature-item">
                    <i class="bi bi-globe fs-1 mb-2"></i>
                    <p class="small">Destinos únicos</p>
                  </div>
                  <div class="feature-item">
                    <i class="bi bi-people fs-1 mb-2"></i>
                    <p class="small">Guías locales</p>
                  </div>
                  <div class="feature-item">
                    <i class="bi bi-star fs-1 mb-2"></i>
                    <p class="small">Experiencias auténticas</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right side - Login Form -->
        <div class="col-lg-6 d-flex align-items-center">
          <div class="login-form-container w-100 p-4 p-lg-5">
            <div class="mx-auto" style="max-width: 420px;">
              <!-- Logo for mobile -->
              <div class="d-lg-none text-center mb-4">
                <div class="brand-mark-mobile mx-auto mb-2">N</div>
                <h2 class="fw-bold">NOMADIA</h2>
              </div>

              <div class="text-center mb-4">
                <h2 class="fw-bold mb-2">Iniciar Sesión</h2>
                <p class="text-muted">Ingresa tus credenciales para continuar</p>
              </div>

              <form @submit.prevent="onLogin">
                <div class="mb-3">
                  <label for="email" class="form-label fw-semibold">
                    <i class="bi bi-envelope me-2"></i>Correo Electrónico
                  </label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="form-control form-control-lg"
                    placeholder="tu@email.com"
                    required
                    :disabled="loading"
                  />
                </div>

                <div class="mb-3">
                  <label for="password" class="form-label fw-semibold">
                    <i class="bi bi-lock me-2"></i>Contraseña
                  </label>
                  <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="form-control form-control-lg"
                    placeholder="••••••••"
                    required
                    :disabled="loading"
                  />
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="rememberMe">
                    <label class="form-check-label small" for="rememberMe">
                      Recordarme
                    </label>
                  </div>
                  <a href="#" class="text-decoration-none small">¿Olvidaste tu contraseña?</a>
                </div>

                <button
                  type="submit"
                  class="btn btn-primary btn-lg w-100 mb-3"
                  :disabled="loading"
                >
                  <span v-if="loading">
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Ingresando...
                  </span>
                  <span v-else>
                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                  </span>
                </button>

                <div class="text-center">
                  <p class="text-muted mb-0">
                    ¿No tienes cuenta?
                    <router-link to="/register" class="text-decoration-none fw-semibold">
                      Regístrate aquí
                    </router-link>
                  </p>
                </div>

                <!-- Status message -->
                <div v-if="errorMessage" class="alert alert-danger mt-3 fade-in" role="alert">
                  <i class="bi bi-exclamation-triangle me-2"></i>{{ errorMessage }}
                </div>

                <div v-if="lastAction === 'starting'" class="text-muted small text-center mt-3">
                  Estado: Iniciando sesión...
                </div>
              </form>

              <!-- Divider -->
              <div class="divider my-4">
                <span class="divider-text">o continúa con</span>
              </div>

              <!-- Social login buttons (placeholder) -->
              <div class="d-grid gap-2">
                <button class="btn btn-outline-secondary" disabled>
                  <i class="bi bi-google me-2"></i>Google
                </button>
                <button class="btn btn-outline-secondary" disabled>
                  <i class="bi bi-facebook me-2"></i>Facebook
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
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../stores/user'
import authRepository from '../api/authRepository'

const router = useRouter()
const form = ref({ email: '', password: '' })
const loading = ref(false)
const lastAction = ref('idle')
const errorMessage = ref('')

async function onLogin() {
  try {
    lastAction.value = 'starting'
    errorMessage.value = ''
    loading.value = true
    
    const res = await authRepository.login({
      email: form.value.email,
      password: form.value.password
    })
    
    if (res && res.user) {
      const store = useUserStore()
      store.setUser(res.user)
      lastAction.value = 'success'
      
      // Redirect según el rol del usuario
      let redirectPath = '/'
      const role = (res.user.role || '').toLowerCase()
      
      if (role === 'admin') {
        redirectPath = '/admin'
      } else if (['guide', 'guia', 'guía', 'host', 'anfitrion'].includes(role)) {
        redirectPath = '/guia'
      }
      
      try {
        await router.push(redirectPath)
      } catch (navErr) {
        console.warn('router.push failed, falling back to full reload', navErr)
        try {
          sessionStorage.setItem('user', JSON.stringify(res.user))
        } catch (e) {}
        window.location.href = redirectPath
      }
    } else {
      throw new Error('No se recibió información del usuario')
    }
  } catch (err) {
    console.error('Login error', err)
    lastAction.value = 'error'
    
    // Friendly error messages
    if (err?.response?.status === 401) {
      errorMessage.value = 'Credenciales incorrectas. Por favor verifica tu email y contraseña.'
    } else if (err?.response?.status === 500) {
      errorMessage.value = 'Error del servidor. Por favor intenta de nuevo más tarde.'
    } else if (err?.message?.includes('Network')) {
      errorMessage.value = 'Error de conexión. Verifica tu conexión a internet.'
    } else {
      errorMessage.value = err?.response?.data?.message || err?.message || 'Error al iniciar sesión'
    }
  } finally {
    loading.value = false
    if (lastAction.value !== 'success') {
      lastAction.value = 'idle'
    }
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
}

.login-hero {
  background-image: linear-gradient(rgba(230, 57, 70, 0.85), rgba(26, 26, 46, 0.85)),
    url('https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=1200&h=800&fit=crop');
  background-size: cover;
  background-position: center;
  position: relative;
}

.hero-overlay {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 3rem;
}

.brand-mark-large {
  width: 80px;
  height: 80px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.2);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-weight: 700;
  font-size: 48px;
  backdrop-filter: blur(10px);
}

.brand-mark-mobile {
  width: 60px;
  height: 60px;
  border-radius: 15px;
  background: var(--brand-gradient);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-weight: 700;
  font-size: 32px;
}

.feature-item {
  text-align: center;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  backdrop-filter: blur(10px);
  min-width: 120px;
}

.login-form-container {
  background: white;
}

.form-control-lg {
  padding: 0.75rem 1rem;
  font-size: 1rem;
  border-radius: 8px;
}

.form-control:focus {
  border-color: var(--brand-primary);
  box-shadow: 0 0 0 0.25rem rgba(230, 57, 70, 0.15);
}

.btn-primary {
  padding: 0.75rem 1rem;
  font-weight: 600;
  border-radius: 8px;
}

.divider {
  position: relative;
  text-align: center;
}

.divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: #dee2e6;
}

.divider-text {
  position: relative;
  background: white;
  padding: 0 1rem;
  color: #6c757d;
  font-size: 0.875rem;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-in {
  animation: fadeIn 0.3s ease-in;
}

@media (max-width: 991px) {
  .login-form-container {
    min-height: 100vh;
  }
}
</style>
