<template>
  <div class="create-experience-page">
    <div class="container py-4">
      <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
          <!-- Header -->
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
              <h2 class="fw-bold mb-1">Crear Nueva Experiencia</h2>
              <p class="text-muted mb-0">Completa la información para publicar tu experiencia</p>
            </div>
            <router-link to="/guia" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left me-2"></i>Volver
            </router-link>
          </div>

          <form @submit.prevent="createExperience">
            <!-- Información Básica -->
            <div class="card shadow-sm border-0 mb-3">
              <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Información Básica</h5>

                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label fw-semibold small">Título de la Experiencia *</label>
                    <input 
                      v-model="form.titulo" 
                      type="text" 
                      class="form-control" 
                      placeholder="Ej: Clase de Cocina Boliviana Tradicional"
                      required
                    >
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-semibold small">Descripción *</label>
                    <textarea 
                      v-model="form.descripcion" 
                      class="form-control" 
                      rows="3" 
                      placeholder="Describe tu experiencia de manera atractiva..."
                      required
                    ></textarea>
                    <small class="text-muted">{{ form.descripcion.length }} caracteres</small>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">Categoría *</label>
                    <select v-model="form.categoria" class="form-select" required>
                      <option value="">-- Selecciona --</option>
                      <option v-for="cat in categorias" :key="cat.id" :value="cat.nombre">
                        {{ cat.nombre }}
                      </option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">Ubicación *</label>
                    <input 
                      v-model="form.location" 
                      type="text" 
                      class="form-control" 
                      placeholder="Sucre, Bolivia"
                      required
                    >
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">Idioma Principal *</label>
                    <select v-model="form.idioma_principal" class="form-select" required>
                      <option value="">-- Selecciona --</option>
                      <option value="Español">Español</option>
                      <option value="Inglés">Inglés</option>
                      <option value="Quechua">Quechua</option>
                      <option value="Aymara">Aymara</option>
                      <option value="Francés">Francés</option>
                      <option value="Alemán">Alemán</option>
                      <option value="Portugués">Portugués</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">Idiomas Adicionales</label>
                    <input 
                      v-model="form.idiomas_adicionales" 
                      type="text" 
                      class="form-control" 
                      placeholder="Ej: Inglés, Quechua (separados por comas)"
                    >
                    <small class="text-muted">Lista los otros idiomas disponibles separados por comas</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- Precios y Logística -->
            <div class="card shadow-sm border-0 mb-3">
              <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-currency-dollar me-2 text-success"></i>Precios y Logística</h5>

                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label fw-semibold small">Precio (Bs) *</label>
                    <div class="input-group">
                      <span class="input-group-text">Bs.</span>
                      <input 
                        v-model.number="form.precio" 
                        type="number" 
                        class="form-control" 
                        placeholder="55"
                        min="0"
                        step="0.01"
                        required
                      >
                    </div>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label fw-semibold small">Duración *</label>
                    <div class="input-group">
                      <input 
                        v-model.number="form.duracion" 
                        type="number" 
                        class="form-control" 
                        placeholder="3"
                        min="1"
                        required
                      >
                      <select v-model="durationUnit" class="form-select" style="max-width: 110px;">
                        <option value="minutes">minutos</option>
                        <option value="hours">horas</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label fw-semibold small">Capacidad *</label>
                    <input 
                      v-model.number="form.cantidad" 
                      type="number" 
                      class="form-control" 
                      placeholder="10"
                      min="1"
                      required
                    >
                  </div>
                </div>
              </div>
            </div>

            <!-- Fotos -->
            <div class="card shadow-sm border-0 mb-4">
              <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                  <i class="bi bi-image me-2 text-info"></i>Fotos de la Experiencia
                  <span class="badge bg-info ms-2">{{ uploadedImages.length }} foto{{ uploadedImages.length !== 1 ? 's' : '' }}</span>
                </h5>

                <div class="upload-area border-2 border-dashed rounded p-4 text-center mb-3" @click="triggerFileInput">
                  <i class="bi bi-cloud-upload fs-2 text-muted mb-2 d-block"></i>
                  <p class="text-muted mb-2">Arrastra fotos aquí o haz clic para seleccionar</p>
                  <button type="button" class="btn btn-sm btn-outline-primary">Subir Fotos</button>
                  <p class="text-muted small mt-2 mb-0">JPG o PNG, máx 5MB por imagen. Puedes subir múltiples fotos.</p>
                  <input 
                    ref="fileInput"
                    type="file" 
                    multiple 
                    accept="image/*"
                    class="d-none"
                    @change="handleFileUpload"
                  >
                </div>

                <!-- Preview Images -->
                <div v-if="uploadedImages.length > 0" class="row g-2">
                  <div v-for="(img, index) in uploadedImages" :key="index" class="col-md-3 col-6">
                    <div class="position-relative">
                      <img :src="img.preview" class="img-fluid rounded" style="width: 100%; height: 140px; object-fit: cover;">
                      <span 
                        v-if="index === 0" 
                        class="badge bg-primary position-absolute top-0 start-0 m-1"
                      >
                        <i class="bi bi-star-fill me-1"></i>Principal
                      </span>
                      <button 
                        type="button"
                        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                        @click="removeImage(index)"
                      >
                        <i class="bi bi-x"></i>
                      </button>
                      <span class="badge bg-dark position-absolute bottom-0 end-0 m-1">
                        {{ index + 1 }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="d-flex justify-content-end gap-2 mb-4">
              <button type="button" class="btn btn-outline-secondary px-4" @click="cancel">
                Cancelar
              </button>
              <button 
                type="submit" 
                class="btn btn-danger px-4"
                :disabled="processing"
              >
                <span v-if="processing" class="spinner-border spinner-border-sm me-2"></span>
                {{ processing ? 'Guardando...' : 'Publicar Experiencia' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import categoriaRepository from '@/api/categoriaRepository'
import experienciaRepository from '@/api/experienciaRepository'

const router = useRouter()
const userStore = useUserStore()

const processing = ref(false)
const categorias = ref([])
const fileInput = ref(null)
const uploadedImages = ref([])
const durationUnit = ref('hours')

const form = ref({
  titulo: '',
  descripcion: '',
  categoria: '',
  precio: null,
  duracion: null,
  cantidad: null,
  idioma_principal: '',
  idiomas_adicionales: '',
  location: ''
})

const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleFileUpload = (event) => {
  const files = Array.from(event.target.files)
  files.forEach(file => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader()
      reader.onload = (e) => {
        uploadedImages.value.push({
          file,
          preview: e.target.result
        })
      }
      reader.readAsDataURL(file)
    }
  })
}

const removeImage = (index) => {
  uploadedImages.value.splice(index, 1)
}

const createExperience = async () => {
  if (!userStore.user) {
    alert('Debes iniciar sesión')
    router.push('/login')
    return
  }

  try {
    processing.value = true

    // Validations
    if (!form.value.titulo || !form.value.descripcion || !form.value.categoria) {
      alert('Por favor completa todos los campos requeridos')
      processing.value = false
      return
    }

    if (!form.value.idioma_principal) {
      alert('Por favor selecciona el idioma principal')
      processing.value = false
      return
    }

    if (uploadedImages.value.length === 0) {
      alert('Por favor sube al menos una imagen')
      processing.value = false
      return
    }

    // Convert duration to minutes
    let durationInMinutes = form.value.duracion
    if (durationUnit.value === 'hours') {
      durationInMinutes = form.value.duracion * 60
    }

    const formData = new FormData()
    
    // Add experience data - backend expects English column names
    formData.append('title', form.value.titulo)
    formData.append('description', form.value.descripcion)
    formData.append('categoria', form.value.categoria)
    formData.append('price', form.value.precio)
    formData.append('duration_minutes', durationInMinutes)
    formData.append('capacity', form.value.cantidad)
    formData.append('location', form.value.location || 'La Paz')
    formData.append('idioma_principal', form.value.idioma_principal)
    formData.append('idiomas_adicionales', form.value.idiomas_adicionales || '')
    formData.append('published', 0) // Pending approval

    // Add ALL images - first one is 'image', rest are 'images[]'
    if (uploadedImages.value.length > 0) {
      formData.append('image', uploadedImages.value[0].file)
      console.log('✅ Imagen principal agregada:', uploadedImages.value[0].file.name)
      
      // Add additional images
      for (let i = 1; i < uploadedImages.value.length; i++) {
        formData.append('images[]', uploadedImages.value[i].file)
        console.log(`✅ Imagen adicional ${i} agregada:`, uploadedImages.value[i].file.name)
      }
      console.log(`📸 Total de imágenes a subir: ${uploadedImages.value.length}`)
    }

    console.log('Sending FormData:', {
      title: form.value.titulo,
      description: form.value.descripcion,
      categoria: form.value.categoria,
      price: form.value.precio,
      duration_minutes: durationInMinutes,
      capacity: form.value.cantidad,
      location: form.value.location || 'La Paz',
      hasImage: uploadedImages.value.length > 0
    })

    const response = await experienciaRepository.create(formData)
    
    console.log('Response:', response)
    
    if (response && response.experiencia) {
      alert('¡Experiencia creada exitosamente! Está pendiente de aprobación.')
      router.push('/guia')
    } else {
      throw new Error('No se recibió respuesta del servidor')
    }

  } catch (error) {
    console.error('Error creating experience:', error)
    alert('Error al crear la experiencia: ' + (error.response?.data?.message || error.message || 'Error desconocido'))
  } finally {
    processing.value = false
  }
}

const cancel = () => {
  if (confirm('¿Estás seguro de que deseas cancelar? Se perderán todos los cambios.')) {
    router.push('/guia')
  }
}

onMounted(async () => {
  try {
    categorias.value = await categoriaRepository.getAll()
  } catch (error) {
    console.error('Error loading categories:', error)
  }
})
</script>

<style scoped>
.create-experience-page {
  background: #f8f9fa;
  min-height: 100vh;
}

.card {
  border-radius: 12px;
  transition: box-shadow 0.2s;
}

.card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.upload-area {
  cursor: pointer;
  transition: all 0.3s;
  background: #fafafa;
}

.upload-area:hover {
  background: #f0f0f0;
  border-color: #e63946 !important;
}

.btn-danger {
  background-color: #e63946;
  border-color: #e63946;
}

.btn-danger:hover {
  background-color: #d62839;
  border-color: #d62839;
}

.form-control:focus,
.form-select:focus {
  border-color: #e63946;
  box-shadow: 0 0 0 0.2rem rgba(230, 57, 70, 0.25);
}

.form-label.small {
  font-size: 0.875rem;
  margin-bottom: 0.375rem;
}

.card-body {
  padding: 1.25rem !important;
}

@media (min-width: 768px) {
  .card-body {
    padding: 1.5rem !important;
  }
}
</style>
