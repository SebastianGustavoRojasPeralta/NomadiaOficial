<template>
  <div class="container py-4">
    <!-- Search bar -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="input-group">
          <input
            type="text"
            class="form-control form-control-lg"
            placeholder="Connect with local guides..."
            v-model="search"
            @keyup.enter="onSearch"
          />
          <button class="btn btn-primary" @click="onSearch">Search</button>
        </div>
      </div>
    </div>

    <!-- Popular Experiences -->
    <div class="row mb-3">
      <div class="col-12">
        <h2>Popular Experiences</h2>
        <p class="text-muted">Discover curated experiences led by trusted local guides.</p>
      </div>
    </div>

    <div class="row">
      <template v-if="loading">
        <div class="col-12 text-center py-5">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </template>

      <template v-else>
        <div
          class="col-12 col-md-6 col-lg-4 mb-4"
          v-for="exp in filteredExperiencias"
          :key="exp.id || exp.uuid || exp.titulo"
        >
          <div class="card h-100 shadow-sm">
            <img
              :src="imageSrc(exp) || 'https://via.placeholder.com/600x350?text=Experience'"
              class="card-img-top"
              alt="Experience image"
            />
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">{{ exp.titulo || exp.title }}</h5>
              <p class="card-text text-muted mb-2">{{ exp.categoria || '' }}</p>
              <p class="card-text flex-grow-1">{{ exp.descripcion ? (exp.descripcion.slice(0,120) + (exp.descripcion.length>120? '...':'')) : 'No description available.' }}</p>
              <div class="d-flex justify-content-between align-items-center mt-3">
                <strong>{{ formatPrice(exp.precio || exp.price) }}</strong>
                <button class="btn btn-outline-primary btn-sm" @click="viewDetails(exp)">View</button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="!filteredExperiencias.length" class="col-12 text-center text-muted py-4">
          No experiences found.
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useExperienciaStore } from '../stores/experienciaStore';
import { useRouter } from 'vue-router';

const router = useRouter();
const store = useExperienciaStore();

const search = ref('');

const loading = computed(() => store.loading);
const experiencias = computed(() => store.experiencias || []);

const filteredExperiencias = computed(() => {
  if (!search.value) return experiencias.value;
  const q = search.value.toLowerCase();
  return experiencias.value.filter(e => {
    const title = (e.titulo || e.title || '').toLowerCase();
    const desc = (e.descripcion || e.description || '').toLowerCase();
    return title.includes(q) || desc.includes(q) || (e.categoria || '').toLowerCase().includes(q);
  });
});

function formatPrice(value) {
  const num = Number(value || 0);
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(num);
}

function viewDetails(exp) {
  const id = exp.id || exp.uuid || exp._id;
  if (id) router.push({ name: 'experiencia-show', params: { id } });
}

function onSearch() {
  // simple client-side filter already applied; could perform server search here
}

onMounted(async () => {
  await store.fetchExperiencias();
});

// helper to compute absolute image url
const apiBase = (import.meta.env.VITE_API_BASE || 'http://localhost:8000/api/v1').replace(/\/api\/v1\/?$/i, '')
function imageSrc(exp) {
  if (!exp) return null
  if (exp.image_url) return exp.image_url
  const img = exp.image || exp.imagen
  if (!img) return null
  if (/^https?:\/\//i.test(img)) return img
  return apiBase.replace(/\/$/, '') + '/' + img.replace(/^\//, '')
}
</script>

<style scoped>
.card-img-top {
  object-fit: cover;
  height: 200px;
}
</style>
