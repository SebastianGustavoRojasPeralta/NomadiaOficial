<template>
  <div class="edit-profile bg-light min-vh-100 py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
              <h2 class="text-center mb-4">Editar Mi Perfil de Guía</h2>
              
              <form @submit.prevent="updateProfile">
                <!-- Foto de Perfil -->
                <div class="text-center mb-4">
                  <div class="profile-photo-wrapper mx-auto" style="width: 150px; height: 150px;">
                    <img 
                      :src="photoPreview || defaultPhoto" 
                      class="rounded-circle w-100 h-100 object-fit-cover border border-3 border-primary"
                      alt="Foto de perfil"
                    />
                  </div>
                  <label class="btn btn-outline-primary btn-sm mt-3">
                    <i class="bi bi-camera"></i> Cambiar Foto
                    <input type="file" @change="onPhotoSelect" accept="image/*" class="d-none">
                  </label>
                </div>

                <!-- Nombre -->
                <div class="mb-3">
                  <label class="form-label fw-bold">Nombre Completo *</label>
                  <input v-model="form.name" type="text" class="form-control" required>
                </div>

                <!-- Email (readonly) -->
                <div class="mb-3">
                  <label class="form-label fw-bold">Email</label>
                  <input v-model="form.email" type="email" class="form-control" readonly>
                </div>

                <!-- Bio -->
                <div class="mb-3">
                  <label class="form-label fw-bold">Biografía</label>
                  <textarea v-model="form.bio" class="form-control" rows="4" 
                    placeholder="Cuéntanos sobre ti, tu experiencia como guía y qué hace únicos tus tours..."></textarea>
                  <small class="text-muted">{{ form.bio?.length || 0 }} / 500 caracteres</small>
                </div>

                <!-- Ubicación -->
                <div class="mb-3">
                  <label class="form-label fw-bold">Ubicación</label>
                  <input v-model="form.ubicacion" type="text" class="form-control" 
                    placeholder="Ej: Sucre, Chuquisaca, Bolivia">
                </div>

                <!-- Idiomas Hablados -->
                <div class="mb-3">
                  <label class="form-label fw-bold">Idiomas que Hablas</label>
                  <input v-model="form.idiomas_hablados" type="text" class="form-control" 
                    placeholder="Ej: Spanish, English, Quechua (Basic)">
                  <small class="text-muted">Separa los idiomas con comas</small>
                </div>

                <!-- Certificaciones -->
                <div class="mb-3">
                  <label class="form-label fw-bold">Certificaciones y Logros</label>
                  <textarea v-model="form.certificaciones" class="form-control" rows="3" 
                    placeholder="Ej: First Aid Certified, Cultural Heritage Tour Guide (Certified), etc."></textarea>
                </div>

                <!-- Años de Experiencia -->
                <div class="mb-4">
                  <label class="form-label fw-bold">Años de Experiencia</label>
                  <input v-model.number="form.anos_experiencia" type="number" class="form-control" 
                    min="0" max="50" placeholder="Ej: 5">
                </div>

                <!-- Botones -->
                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary btn-lg" :disabled="saving">
                    <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                    {{ saving ? 'Guardando...' : 'Guardar Cambios' }}
                  </button>
                  <router-link to="/guide/dashboard" class="btn btn-outline-secondary">
                    Cancelar
                  </router-link>
                </div>
              </form>

              <div v-if="message" class="alert mt-3" :class="messageType === 'success' ? 'alert-success' : 'alert-danger'">
                {{ message }}
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

const router = useRouter()
const apiBase = 'http://localhost/NomadiaOficial/backend/public/api/v1'

const form = ref({
  name: '',
  email: '',
  bio: '',
  ubicacion: '',
  idiomas_hablados: '',
  certificaciones: '',
  anos_experiencia: 0
})

const photoFile = ref(null)
const photoPreview = ref(null)
const defaultPhoto = 'https://ui-avatars.com/api/?name=Guia&size=150&background=dc3545&color=fff'
const saving = ref(false)
const message = ref('')
const messageType = ref('')

onMounted(async () => {
  await loadProfile()
})

async function loadProfile() {
  try {
    const res = await fetch(`${apiBase}/perfil_guia_me.php`, {
      credentials: 'include'
    })
    
    if (!res.ok) {
      if (res.status === 401) {
        router.push('/login')
        return
      }
      throw new Error('Error al cargar perfil')
    }
    
    const data = await res.json()
    form.value = {
      name: data.name || '',
      email: data.email || '',
      bio: data.bio || '',
      ubicacion: data.ubicacion || '',
      idiomas_hablados: data.idiomas_hablados || '',
      certificaciones: data.certificaciones || '',
      anos_experiencia: data.anos_experiencia || 0
    }
    
    if (data.foto) {
      photoPreview.value = `${apiBase.replace('/api/v1', '')}${data.foto}`
    }
  } catch (error) {
    console.error('Error:', error)
    showMessage('Error al cargar el perfil', 'error')
  }
}

function onPhotoSelect(event) {
  const file = event.target.files[0]
  if (file) {
    photoFile.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      photoPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

async function updateProfile() {
  saving.value = true
  message.value = ''

  try {
    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('bio', form.value.bio || '')
    formData.append('ubicacion', form.value.ubicacion || '')
    formData.append('idiomas_hablados', form.value.idiomas_hablados || '')
    formData.append('certificaciones', form.value.certificaciones || '')
    formData.append('anos_experiencia', form.value.anos_experiencia || 0)
    
    if (photoFile.value) {
      formData.append('foto', photoFile.value)
    }

    const res = await fetch(`${apiBase}/perfil_guia_update.php`, {
      method: 'POST',
      body: formData,
      credentials: 'include'
    })

    const data = await res.json()

    if (!res.ok) {
      throw new Error(data.message || 'Error al actualizar perfil')
    }

    showMessage('Perfil actualizado exitosamente', 'success')
    setTimeout(() => {
      router.push('/guide/dashboard')
    }, 1500)
  } catch (error) {
    console.error('Error:', error)
    showMessage(error.message || 'Error al actualizar perfil', 'error')
  } finally {
    saving.value = false
  }
}

function showMessage(msg, type) {
  message.value = msg
  messageType.value = type
  setTimeout(() => {
    message.value = ''
  }, 5000)
}
</script>

<style scoped>
.profile-photo-wrapper {
  position: relative;
  overflow: hidden;
}

.object-fit-cover {
  object-fit: cover;
}

textarea {
  resize: vertical;
}
</style>
