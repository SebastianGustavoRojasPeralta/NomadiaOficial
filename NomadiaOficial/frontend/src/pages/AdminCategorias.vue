<template>
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Gestión de Categorías</h3>
      <router-link class="btn btn-outline-secondary" to="/admin">Volver</router-link>
    </div>

    <div class="card mb-4">
      <div class="card-body">
        <h5>Crear Nueva Categoría</h5>
        <form @submit.prevent="createCategoria">
          <div class="mb-3">
            <label class="form-label">Nombre de la Categoría:</label>
            <input v-model="form.nombre" class="form-control" placeholder="Ej: Gastronómico, Cultural" />
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción (opcional):</label>
            <textarea v-model="form.descripcion" class="form-control" rows="2"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Estado:</label>
            <select v-model="form.estado" class="form-select">
              <option value="Activo">Activo</option>
              <option value="Inactivo">Inactivo</option>
            </select>
          </div>
          <button class="btn btn-danger">Guardar Categoría</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5>Categorías Existentes</h5>
        <div v-if="loading" class="text-muted">Cargando...</div>
        <table class="table table-striped" v-if="!loading">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Estado</th>
              <th>Creado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in categorias" :key="c.id">
              <td>{{ c.id }}</td>
              <td>
                <div v-if="editId !== c.id">{{ c.nombre }}</div>
                <input v-else v-model="editForm.nombre" class="form-control form-control-sm" />
              </td>
              <td>
                <div v-if="editId !== c.id">{{ c.estado }}</div>
                <select v-else v-model="editForm.estado" class="form-select form-select-sm">
                  <option value="Activo">Activo</option>
                  <option value="Inactivo">Inactivo</option>
                </select>
              </td>
              <td>{{ c.created_at || c.created }}</td>
              <td>
                <div v-if="editId !== c.id">
                  <button class="btn btn-warning btn-sm me-1" @click="startEdit(c)">Editar</button>
                  <button class="btn btn-danger btn-sm" @click="remove(c)">Eliminar</button>
                </div>
                <div v-else>
                  <button class="btn btn-primary btn-sm me-1" @click="saveEdit(c)">Guardar</button>
                  <button class="btn btn-secondary btn-sm" @click="cancelEdit">Cancelar</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import adminRepo from '../api/adminRepository'
import { useRouter } from 'vue-router'

const router = useRouter()
const categorias = ref([])
const loading = ref(false)
const form = ref({ nombre: '', descripcion: '', estado: 'Activo' })
const editId = ref(null)
const editForm = ref({ nombre: '', descripcion: '', estado: 'Activo' })

const load = async () => {
  loading.value = true
  try {
    const res = await adminRepo.listCategorias()
    categorias.value = res.data || []
  } catch (e) { console.error(e); categorias.value = [] } finally { loading.value = false }
}

const createCategoria = async () => {
  if (!form.value.nombre) return alert('Ingrese un nombre')
  try {
    const res = await adminRepo.createCategoria(form.value)
    if (res.data && res.data.categoria) {
      form.value = { nombre:'', descripcion:'', estado:'Activo' }
      await load()
    }
  } catch (e) { console.error(e); alert('Error creando categoría') }
}

const startEdit = (c) => {
  editId.value = c.id
  editForm.value = { nombre: c.nombre || '', descripcion: c.descripcion || '', estado: c.estado || 'Activo' }
}

const cancelEdit = () => { editId.value = null }

const saveEdit = async (c) => {
  if (!editForm.value.nombre) return alert('Ingrese un nombre')
  try {
    const payload = { id: c.id, nombre: editForm.value.nombre, descripcion: editForm.value.descripcion, estado: editForm.value.estado }
    const res = await adminRepo.updateCategoria(payload)
    if (res.data && res.data.categoria) {
      editId.value = null
      await load()
    }
  } catch (e) { console.error(e); alert('Error actualizando') }
}

const remove = async (c) => {
  if (!confirm('Eliminar esta categoría?')) return
  try {
    const res = await adminRepo.deleteCategoria({ id: c.id })
    if (res.data && res.data.deleted) {
      await load()
    }
  } catch (e) { console.error(e); alert('Error eliminando: ' + (e?.response?.data?.message || e.message)) }
}

onMounted(() => { load() })
</script>

<style scoped>
.card { border-radius: 8px }
</style>
