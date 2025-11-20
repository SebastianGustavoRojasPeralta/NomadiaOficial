<template>
  <div class="admin-layout">
    <!-- Modal de Detalles de Experiencia -->
    <div class="modal fade" id="experienceDetailsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">
              <i class="bi bi-info-circle me-2"></i>Detalles de la Experiencia
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" v-if="selectedExperience">
            <div class="row g-4">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="text-muted small mb-1">ID</label>
                  <div class="fw-bold">{{ selectedExperience.id }}</div>
                </div>
                <div class="mb-3">
                  <label class="text-muted small mb-1">Título</label>
                  <div class="fw-bold">{{ selectedExperience.title }}</div>
                </div>
                <div class="mb-3">
                  <label class="text-muted small mb-1">Precio</label>
                  <div class="fw-bold text-success">Bs. {{ selectedExperience.price }}</div>
                </div>
                <div class="mb-3">
                  <label class="text-muted small mb-1">Capacidad</label>
                  <div class="fw-bold">{{ selectedExperience.cantidad || selectedExperience.capacity || '-' }} personas</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="text-muted small mb-1">Categoría</label>
                  <div class="fw-bold">{{ selectedExperience.categoria || '-' }}</div>
                </div>
                <div class="mb-3">
                  <label class="text-muted small mb-1">Ubicación</label>
                  <div class="fw-bold">{{ selectedExperience.location || '-' }}</div>
                </div>
                <div class="mb-3">
                  <label class="text-muted small mb-1">Duración</label>
                  <div class="fw-bold">{{ selectedExperience.duration_minutes || '-' }} minutos</div>
                </div>
                <div class="mb-3">
                  <label class="text-muted small mb-1">Estado</label>
                  <div>
                    <span class="badge" :class="selectedExperience.published ? 'bg-success' : 'bg-warning'">{{ selectedExperience.published ? 'Publicado' : 'Pendiente' }}</span>
                  </div>
                </div>
              </div>
              <div class="col-12" v-if="selectedExperience.description">
                <div class="mb-3">
                  <label class="text-muted small mb-1">Descripción</label>
                  <div class="p-3 bg-light rounded">{{ selectedExperience.description }}</div>
                </div>
              </div>
              <div class="col-12" v-if="selectedExperience.imagen">
                <div class="mb-3">
                  <label class="text-muted small mb-1">Imagen</label>
                  <div>
                    <img :src="getImageUrl(selectedExperience.imagen)" class="img-fluid rounded" style="max-height: 300px; object-fit: cover;" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="bi bi-x-circle me-2"></i>Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar bg-white border-end shadow-sm">
      <div class="p-3 border-bottom">
        <div class="d-flex align-items-center">
          <div class="rounded-circle bg-danger d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-star-fill text-white"></i>
          </div>
          <span class="ms-2 fw-bold fs-5">Nomadia</span>
        </div>
      </div>
      
      <nav class="nav flex-column p-3">
        <a 
          href="#" 
          class="nav-link d-flex align-items-center py-2 mb-1"
          :class="{ 'active-link': activeTab === 'dashboard' }"
          @click.prevent="activeTab = 'dashboard'"
        >
          <i class="bi bi-speedometer2 me-2"></i>
          <span>Admin Dashboard</span>
        </a>
        <a 
          href="#" 
          class="nav-link d-flex align-items-center py-2 mb-1"
          :class="{ 'active-link': activeTab === 'users' }"
          @click.prevent="activeTab = 'users'"
        >
          <i class="bi bi-people me-2"></i>
          <span>User Management</span>
        </a>
        <a 
          href="#" 
          class="nav-link d-flex align-items-center py-2 mb-1"
          :class="{ 'active-link': activeTab === 'experiences' }"
          @click.prevent="activeTab = 'experiences'"
        >
          <i class="bi bi-card-list me-2"></i>
          <span>Experience Management</span>
        </a>
        <a 
          href="#" 
          class="nav-link d-flex align-items-center py-2 mb-1"
          :class="{ 'active-link': activeTab === 'reports' }"
          @click.prevent="activeTab = 'reports'"
        >
          <i class="bi bi-bar-chart-line me-2"></i>
          <span>Business Reports</span>
        </a>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Header -->
      <header class="bg-danger text-white py-3 px-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
          <router-link to="/" class="text-white text-decoration-none">Home</router-link>
          <router-link 
            v-if="activeTab === 'experiences'" 
            to="/admin/categorias" 
            class="text-white text-decoration-none"
          >
            Experience Management
          </router-link>
        </div>
        <div class="d-flex align-items-center gap-3">
          <div class="input-group" style="max-width: 300px;">
            <input 
              v-model="searchQuery"
              type="search" 
              class="form-control form-control-sm" 
              placeholder="Search experiences..."
              @keyup.enter="handleSearch"
              style="background: rgba(255,255,255,0.2); border: none; color: white;"
            >
            <button class="btn btn-light btn-sm" @click="handleSearch">
              <i class="bi bi-search"></i>
            </button>
          </div>
          <div class="position-relative" style="cursor: pointer;">
            <i class="bi bi-bell fs-5" @click="toggleNotifications"></i>
            <span v-if="unreadNotifications > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark" style="font-size: 0.6rem;">
              {{ unreadNotifications }}
            </span>
            <!-- Dropdown de notificaciones -->
            <div v-if="showNotifications" class="dropdown-menu dropdown-menu-end show position-absolute" style="min-width: 300px; max-height: 400px; overflow-y: auto; top: 100%; right: 0; margin-top: 0.5rem;">
              <div class="dropdown-header d-flex justify-content-between align-items-center">
                <span class="fw-bold">Notificaciones</span>
                <button class="btn btn-sm btn-link text-decoration-none" @click="markAllAsRead">Marcar todas como leídas</button>
              </div>
              <div v-if="notifications.length === 0" class="dropdown-item text-center text-muted py-3">
                No hay notificaciones
              </div>
              <a v-for="notif in notifications" :key="notif.id" href="#" class="dropdown-item" :class="{'bg-light': !notif.read}" @click.prevent="markAsRead(notif)">
                <div class="d-flex align-items-start">
                  <i class="bi bi-info-circle text-primary me-2 mt-1"></i>
                  <div class="flex-grow-1">
                    <div class="fw-bold small">{{ notif.title }}</div>
                    <div class="text-muted small">{{ notif.message }}</div>
                    <div class="text-muted" style="font-size: 0.7rem;">{{ formatNotificationTime(notif.created_at) }}</div>
                  </div>
                </div>
              </a>
            </div>
          </div>
          <div class="dropdown">
            <div class="rounded-circle bg-white d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; cursor: pointer;" data-bs-toggle="dropdown">
              <i class="bi bi-person-fill text-danger fs-5"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configuración</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><router-link to="/" class="dropdown-item"><i class="bi bi-house me-2"></i>Ir al Inicio</router-link></li>
              <li><a class="dropdown-item text-danger" href="#" @click.prevent="logout"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
            </ul>
          </div>
        </div>
      </header>

      <!-- Content Area -->
      <div class="content-area p-4">
        <!-- Dashboard Tab -->
        <div v-if="activeTab === 'dashboard'">
          <h2 class="mb-4 fw-bold">Dashboard Overview</h2>
          
          <!-- Stats Cards -->
          <div class="row g-3 mb-4">
            <div class="col-md-3">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <p class="text-muted mb-1 small">Total Users</p>
                      <h3 class="mb-0 fw-bold">{{ report?.total_users || 0 }}</h3>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                      <i class="bi bi-people fs-4 text-primary"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <p class="text-muted mb-1 small">Total Experiences</p>
                      <h3 class="mb-0 fw-bold">{{ report?.total_experiencias || 0 }}</h3>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                      <i class="bi bi-geo-alt fs-4 text-success"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <p class="text-muted mb-1 small">Total Reservations</p>
                      <h3 class="mb-0 fw-bold">{{ report?.total_reservas || 0 }}</h3>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                      <i class="bi bi-calendar-check fs-4 text-warning"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <p class="text-muted mb-1 small">Payments Completed</p>
                      <h3 class="mb-0 fw-bold">{{ report?.total_pagos_completed || 0 }}</h3>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                      <i class="bi bi-currency-dollar fs-4 text-danger"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Top Experiences -->
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h5 class="fw-bold mb-3">Top Experiences</h5>
              <div v-if="loadingReport" class="text-center py-3">
                <div class="spinner-border text-danger" role="status"></div>
              </div>
              <div v-else class="table-responsive">
                <table class="table table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>ID</th>
                      <th>Experience Name</th>
                      <th>Reservations</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="t in top" :key="t.id">
                      <td>{{ t.id }}</td>
                      <td>{{ t.title }}</td>
                      <td><span class="badge bg-success">{{ t.reservas_count }}</span></td>
                    </tr>
                    <tr v-if="top.length === 0">
                      <td colspan="3" class="text-center text-muted">No data available</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- User Management Tab -->
        <div v-if="activeTab === 'users'">
          <h2 class="mb-4 fw-bold">User Management</h2>
          
          <!-- Create User Form -->
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
              <h5 class="fw-bold mb-3">Create New User</h5>
              <form @submit.prevent="createUser" class="row g-3">
                <div class="col-md-3">
                  <input 
                    v-model="newUser.name" 
                    class="form-control" 
                    placeholder="Full Name" 
                    required
                  />
                </div>
                <div class="col-md-3">
                  <input 
                    v-model="newUser.email" 
                    type="email"
                    class="form-control" 
                    placeholder="Email Address" 
                    required
                  />
                </div>
                <div class="col-md-2">
                  <input 
                    v-model="newUser.password" 
                    type="password" 
                    class="form-control" 
                    placeholder="Password" 
                    required
                  />
                </div>
                <div class="col-md-2">
                  <select v-model="newUser.role" class="form-select">
                    <option value="traveler">Traveler</option>
                    <option value="guia">Guide</option>
                    <option value="admin">Admin</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <button class="btn btn-danger w-100" type="submit">
                    <i class="bi bi-plus-circle me-2"></i>Create
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Users List -->
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h5 class="fw-bold mb-3">All Users</h5>
              <div v-if="loadingUsers" class="text-center py-3">
                <div class="spinner-border text-danger" role="status"></div>
              </div>
              <div v-else class="table-responsive">
                <table class="table table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Current Role</th>
                      <th>Change Role</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="u in users" :key="u.id">
                      <td>{{ u.id }}</td>
                      <td>{{ u.name }}</td>
                      <td>{{ u.email }}</td>
                      <td><span class="badge bg-primary">{{ u.role }}</span></td>
                      <td>
                        <div class="d-flex gap-2">
                          <select 
                            v-model="u.roleTemp" 
                            class="form-select form-select-sm" 
                            style="width:120px;"
                          >
                            <option value="traveler">Traveler</option>
                            <option value="guia">Guide</option>
                            <option value="admin">Admin</option>
                          </select>
                          <button 
                            class="btn btn-sm btn-success" 
                            @click="changeRole(u)"
                          >
                            <i class="bi bi-check-lg"></i>
                          </button>
                        </div>
                      </td>
                      <td>
                        <button 
                          class="btn btn-sm btn-outline-danger" 
                          @click="deleteUser(u)"
                        >
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Experience Management Tab -->
        <div v-if="activeTab === 'experiences'">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Experience Management</h2>
            <router-link to="/admin/categorias" class="btn btn-outline-danger">
              <i class="bi bi-tag me-2"></i>Manage Categories
            </router-link>
          </div>
          
          <!-- Experience Listings Table -->
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Experience Listings</h5>
                <!-- Filter Buttons -->
                <div class="btn-group" role="group">
                  <button 
                    class="btn btn-sm"
                    :class="experienceFilter === 'all' ? 'btn-danger' : 'btn-outline-danger'"
                    @click="experienceFilter = 'all'"
                  >
                    All ({{ experiencias.length }})
                  </button>
                  <button 
                    class="btn btn-sm"
                    :class="experienceFilter === 'pending' ? 'btn-warning' : 'btn-outline-warning'"
                    @click="experienceFilter = 'pending'"
                  >
                    Pending ({{ experiencias.filter(e => e.published === 0).length }})
                  </button>
                  <button 
                    class="btn btn-sm"
                    :class="experienceFilter === 'approved' ? 'btn-success' : 'btn-outline-success'"
                    @click="experienceFilter = 'approved'"
                  >
                    Approved ({{ experiencias.filter(e => e.published === 1).length }})
                  </button>
                  <button 
                    class="btn btn-sm"
                    :class="experienceFilter === 'rejected' ? 'btn-dark' : 'btn-outline-dark'"
                    @click="experienceFilter = 'rejected'"
                  >
                    Rejected ({{ experiencias.filter(e => e.published === -1).length }})
                  </button>
                </div>
              </div>
              
              <div v-if="loadingExp" class="text-center py-3">
                <div class="spinner-border text-danger" role="status"></div>
              </div>
              <div v-else class="table-responsive">
                <table class="table table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>Experience Name</th>
                      <th>Guide</th>
                      <th>Publication Date</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="e in filteredExperiencias" :key="e.id">
                      <td>
                        <div class="fw-semibold">{{ e.title }}</div>
                        <small class="text-muted">ID: {{ e.id }} | Price: Bs. {{ e.price }} | Capacity: {{ e.cantidad || e.capacity || '-' }}</small>
                      </td>
                      <td>{{ e.guia_name || 'N/A' }}</td>
                      <td>{{ formatDate(e.created_at) }}</td>
                      <td>
                        <span v-if="e.published === 1" class="badge bg-success">Approved</span>
                        <span v-else-if="e.published === -1" class="badge bg-danger">Rejected</span>
                        <span v-else class="badge bg-warning">Pending</span>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <button 
                            class="btn btn-sm btn-outline-secondary"
                            @click="viewDetails(e)"
                          >
                            Review
                          </button>
                          <button 
                            v-if="e.published === 0"
                            class="btn btn-sm btn-success" 
                            @click="approve(e.id)"
                          >
                            Approve
                          </button>
                          <button 
                            v-if="e.published === 0"
                            class="btn btn-sm btn-danger" 
                            @click="reject(e.id)"
                          >
                            Reject
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="filteredExperiencias.length === 0">
                      <td colspan="5" class="text-center text-muted py-4">
                        No experiences found for this filter
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Business Reports Tab -->
        <div v-if="activeTab === 'reports'">
          <h2 class="mb-4 fw-bold">Business Reports</h2>
          
          <!-- Gráficas de Analytics -->
          <div class="row g-3 mb-4">
            <!-- Gráfica de Reservas Mensuales -->
            <div class="col-lg-6">
              <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                  <h6 class="fw-bold mb-3">Monthly Bookings</h6>
                  <div class="chart-container">
                    <canvas id="bookingsChart"></canvas>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Gráfica de Usuarios por Rol -->
            <div class="col-lg-6">
              <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                  <h6 class="fw-bold mb-3">User Registrations by Role</h6>
                  <div class="chart-container">
                    <canvas id="usersChart"></canvas>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Gráfica de Ganancias Mensuales -->
            <div class="col-lg-6">
              <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                  <h6 class="fw-bold mb-3">Monthly Revenue</h6>
                  <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Gráfica de Categorías Más Populares -->
            <div class="col-lg-6">
              <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                  <h6 class="fw-bold mb-3">Top Categories</h6>
                  <div class="chart-container">
                    <canvas id="categoriesChart"></canvas>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Gráfica de Estado de Experiencias -->
            <div class="col-lg-6">
              <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                  <h6 class="fw-bold mb-3">Experience Status Distribution</h6>
                  <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Gráfica de Top Guías por Ganancias -->
            <div class="col-lg-6">
              <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                  <h6 class="fw-bold mb-3">Top Guides by Earnings</h6>
                  <div class="chart-container">
                    <canvas id="guidesChart"></canvas>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Audit Log -->
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h5 class="fw-bold mb-3">Recent Audit Log</h5>
              <div v-if="loadingAudit" class="text-center py-3">
                <div class="spinner-border text-danger" role="status"></div>
              </div>
              <div v-else>
                <div 
                  v-for="a in audits" 
                  :key="a.id" 
                  class="border-bottom py-3"
                >
                  <div class="d-flex justify-content-between">
                    <div>
                      <div class="fw-semibold">{{ a.action }}</div>
                      <div class="text-muted small">
                        Admin: {{ a.admin_name }} (ID: {{ a.admin_id }})
                      </div>
                      <div class="text-muted small">
                        Target: {{ a.target_type }} #{{ a.target_id }}
                      </div>
                      <div class="small mt-1">{{ a.details }}</div>
                    </div>
                    <div class="text-muted small">
                      {{ formatDate(a.created_at) }}
                    </div>
                  </div>
                </div>
                <div v-if="audits.length === 0" class="text-center text-muted py-4">
                  No audit logs available
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch, nextTick } from 'vue'
import adminRepo from '../api/adminRepository'
import { Modal } from 'bootstrap'
import Chart from 'chart.js/auto'

const activeTab = ref('dashboard')
const experienceFilter = ref('all')
const searchQuery = ref('')
const showNotifications = ref(false)
const users = ref([])
const experiencias = ref([])
const report = ref(null)
const top = ref([])
const audits = ref([])
const selectedExperience = ref(null)
const notifications = ref([
  { id: 1, title: 'Nueva experiencia pendiente', message: 'Tour Central requiere aprobación', created_at: new Date().toISOString(), read: false },
  { id: 2, title: 'Nuevo usuario registrado', message: 'Carmen García se ha registrado como guía', created_at: new Date(Date.now() - 3600000).toISOString(), read: false }
])
const loadingUsers = ref(false)
const loadingExp = ref(false)
const loadingReport = ref(false)
const loadingAudit = ref(false)

const newUser = ref({ name:'', email:'', password:'', role:'traveler' })

// Unread notifications count
const unreadNotifications = computed(() => {
  return notifications.value.filter(n => !n.read).length
})

// Filtered experiences based on selected filter and search query
const filteredExperiencias = computed(() => {
  let filtered = experiencias.value
  
  // Filtrar por estado (pending, approved, rejected)
  if (experienceFilter.value === 'pending') filtered = filtered.filter(e => e.published === 0)
  else if (experienceFilter.value === 'approved') filtered = filtered.filter(e => e.published === 1)
  else if (experienceFilter.value === 'rejected') filtered = filtered.filter(e => e.published === -1)
  
  // Filtrar por búsqueda
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(e => 
      (e.title && e.title.toLowerCase().includes(query)) ||
      (e.description && e.description.toLowerCase().includes(query)) ||
      (e.categoria && e.categoria.toLowerCase().includes(query)) ||
      (e.location && e.location.toLowerCase().includes(query))
    )
  }
  
  return filtered
})

const loadUsers = async () => {
  loadingUsers.value = true
  try {
    const res = await adminRepo.listUsers()
    users.value = res.data.users.map(u => ({...u, roleTemp: u.role}))
  } catch (e) { 
    console.error(e)
    users.value = [] 
  } finally { 
    loadingUsers.value = false 
  }
}

const createUser = async () => {
  try {
    const payload = { 
      name: newUser.value.name, 
      email: newUser.value.email, 
      password: newUser.value.password, 
      role: newUser.value.role 
    }
    const res = await adminRepo.createUser(payload)
    if (res && res.data && res.data.ok) {
      await loadUsers()
      newUser.value = { name:'', email:'', password:'', role:'traveler' }
      alert('User created successfully')
    }
  } catch (e) { 
    console.error(e)
    alert('Error creating user') 
  }
}

const loadExperiencias = async () => {
  loadingExp.value = true
  try {
    const res = await adminRepo.listPendingExperiencias()
    experiencias.value = res.data.experiencias || []
  } catch (e) { 
    console.error(e)
    experiencias.value = [] 
  } finally { 
    loadingExp.value = false 
  }
}

const loadReport = async () => {
  loadingReport.value = true
  try {
    const res = await adminRepo.getReports()
    report.value = res.data.report
    top.value = res.data.top_experiencias || []
  } catch (e) { 
    console.error(e)
    report.value = null 
  } finally { 
    loadingReport.value = false 
  }
}

const loadAudit = async () => {
  loadingAudit.value = true
  try {
    const res = await adminRepo.getAudits()
    audits.value = res.data.logs || []
  } catch (e) {
    console.error(e)
    audits.value = []
  } finally {
    loadingAudit.value = false
  }
}

const changeRole = async (u) => {
  try {
    await adminRepo.updateUserRole(u.id, u.roleTemp)
    u.role = u.roleTemp
    alert('Role updated successfully')
  } catch (e) { 
    console.error(e)
    alert('Error updating role')
  }
}

const deleteUser = async (u) => {
  if (!confirm('Delete this user?')) return
  try { 
    await adminRepo.deleteUser(u.id)
    users.value = users.value.filter(x=>x.id!=u.id)
    alert('User deleted successfully')
  } catch (e) { 
    console.error(e)
    alert('Error deleting user')
  }
}

const approve = async (id) => { 
  try {
    await adminRepo.approveExperiencia(id)
    // Update the status instead of removing from list
    const exp = experiencias.value.find(x => x.id === id)
    if (exp) {
      exp.published = 1
    }
    alert('Experience approved successfully')
  } catch (e) {
    console.error(e)
    alert('Error approving experience')
  }
}

const reject = async (id) => { 
  if (!confirm('Are you sure you want to reject this experience?')) return
  try {
    await adminRepo.rejectExperiencia(id)
    // Update the status instead of removing from list
    const exp = experiencias.value.find(x => x.id === id)
    if (exp) {
      exp.published = -1
    }
    alert('Experience rejected')
  } catch (e) {
    console.error(e)
    alert('Error rejecting experience')
  }
}

const viewDetails = (exp) => {
  selectedExperience.value = exp
  const modalElement = document.getElementById('experienceDetailsModal')
  if (modalElement) {
    const modal = new Modal(modalElement)
    modal.show()
  }
}

const getImageUrl = (imagen) => {
  if (!imagen) return null
  const apiBase = (import.meta.env.VITE_API_BASE || 'http://localhost:8000/api/v1').replace(/\/api\/v1\/?$/i, '')
  if (/^https?:\/\//i.test(imagen)) return imagen
  return apiBase.replace(/\/$/, '') + '/' + imagen.replace(/^\//, '')
}

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    activeTab.value = 'experiences'
  }
}

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
}

const markAsRead = (notif) => {
  notif.read = true
  showNotifications.value = false
}

const markAllAsRead = () => {
  notifications.value.forEach(n => n.read = true)
}

const formatNotificationTime = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const now = new Date()
  const diffMs = now - date
  const diffMins = Math.floor(diffMs / 60000)
  const diffHours = Math.floor(diffMs / 3600000)
  const diffDays = Math.floor(diffMs / 86400000)
  
  if (diffMins < 1) return 'Hace un momento'
  if (diffMins < 60) return `Hace ${diffMins} min`
  if (diffHours < 24) return `Hace ${diffHours}h`
  if (diffDays < 7) return `Hace ${diffDays}d`
  return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' })
}

const logout = () => {
  if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
    sessionStorage.removeItem('user')
    window.location.href = '/'
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  try {
    return new Date(dateString).toLocaleDateString('es-BO', { 
      year: 'numeric', 
      month: '2-digit', 
      day: '2-digit' 
    })
  } catch (e) {
    return dateString
  }
}

// Variables para almacenar instancias de charts
let bookingsChartInstance = null
let usersChartInstance = null
let revenueChartInstance = null
let categoriesChartInstance = null
let statusChartInstance = null
let guidesChartInstance = null

// Función para inicializar todas las gráficas
const initCharts = async () => {
  // Esperar a que el DOM esté listo
  await nextTick()
  
  // Verificar que estemos en el tab de reports
  if (activeTab.value !== 'reports') return
  
  // Destruir gráficas previas si existen
  if (bookingsChartInstance) bookingsChartInstance.destroy()
  if (usersChartInstance) usersChartInstance.destroy()
  if (revenueChartInstance) revenueChartInstance.destroy()
  if (categoriesChartInstance) categoriesChartInstance.destroy()
  if (statusChartInstance) statusChartInstance.destroy()
  if (guidesChartInstance) guidesChartInstance.destroy()
  
  // Gráfica de Reservas Mensuales (Line Chart)
  const bookingsCtx = document.getElementById('bookingsChart')
  if (bookingsCtx) {
    bookingsChartInstance = new Chart(bookingsCtx, {
      type: 'line',
      data: {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [{
          label: 'Reservas',
          data: [12, 19, 15, 25, 22, 30, 28, 35, 32, 40, 38, 45],
          borderColor: '#dc3545',
          backgroundColor: 'rgba(220, 53, 69, 0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    })
  }
  
  // Gráfica de Usuarios por Rol (Bar Chart)
  const usersCtx = document.getElementById('usersChart')
  if (usersCtx) {
    const travelerCount = users.value.filter(u => u.role === 'traveler').length
    const guideCount = users.value.filter(u => u.role === 'guide').length
    const adminCount = users.value.filter(u => u.role === 'admin').length
    
    usersChartInstance = new Chart(usersCtx, {
      type: 'bar',
      data: {
        labels: ['Travelers', 'Guides', 'Admins'],
        datasets: [{
          label: 'Users',
          data: [travelerCount, guideCount, adminCount],
          backgroundColor: ['#dc3545', '#6c757d', '#343a40']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    })
  }
  
  // Gráfica de Ganancias Mensuales (Line Chart)
  const revenueCtx = document.getElementById('revenueChart')
  if (revenueCtx) {
    revenueChartInstance = new Chart(revenueCtx, {
      type: 'line',
      data: {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [{
          label: 'Ganancias (Bs)',
          data: [1200, 1900, 1500, 2500, 2200, 3000, 2800, 3500, 3200, 4000, 3800, 4500],
          borderColor: '#28a745',
          backgroundColor: 'rgba(40, 167, 69, 0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { 
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return 'Bs ' + value.toLocaleString()
              }
            }
          }
        }
      }
    })
  }
  
  // Gráfica de Categorías Más Populares (Doughnut Chart)
  const categoriesCtx = document.getElementById('categoriesChart')
  if (categoriesCtx) {
    // Contar experiencias por categoría
    const categoryCount = {}
    experiencias.value.forEach(exp => {
      const cat = exp.categoria || 'Sin categoría'
      categoryCount[cat] = (categoryCount[cat] || 0) + 1
    })
    
    categoriesChartInstance = new Chart(categoriesCtx, {
      type: 'doughnut',
      data: {
        labels: Object.keys(categoryCount),
        datasets: [{
          data: Object.values(categoryCount),
          backgroundColor: [
            '#dc3545',
            '#6c757d',
            '#ffc107',
            '#17a2b8',
            '#28a745',
            '#343a40'
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              boxWidth: 12,
              padding: 8,
              font: { size: 11 }
            }
          }
        }
      }
    })
  }
  
  // Gráfica de Estado de Experiencias (Pie Chart)
  const statusCtx = document.getElementById('statusChart')
  if (statusCtx) {
    const publishedCount = experiencias.value.filter(e => e.published === 1).length
    const pendingCount = experiencias.value.filter(e => e.published === 0).length
    const rejectedCount = experiencias.value.filter(e => e.published === -1).length
    
    statusChartInstance = new Chart(statusCtx, {
      type: 'pie',
      data: {
        labels: ['Published', 'Pending', 'Rejected'],
        datasets: [{
          data: [publishedCount, pendingCount, rejectedCount],
          backgroundColor: ['#28a745', '#ffc107', '#dc3545']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              boxWidth: 12,
              padding: 8,
              font: { size: 11 }
            }
          }
        }
      }
    })
  }
  
  // Gráfica de Top Guías por Ganancias (Horizontal Bar Chart)
  const guidesCtx = document.getElementById('guidesChart')
  if (guidesCtx) {
    // Obtener top 5 guías (datos simulados por ahora)
    const topGuides = users.value
      .filter(u => u.role === 'guide')
      .slice(0, 5)
      .map((u, i) => ({
        name: u.name,
        earnings: Math.floor(Math.random() * 5000) + 1000
      }))
      .sort((a, b) => b.earnings - a.earnings)
    
    guidesChartInstance = new Chart(guidesCtx, {
      type: 'bar',
      data: {
        labels: topGuides.map(g => g.name),
        datasets: [{
          label: 'Ganancias (Bs)',
          data: topGuides.map(g => g.earnings),
          backgroundColor: '#dc3545'
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2,
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: { 
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return 'Bs ' + value.toLocaleString()
              }
            }
          }
        }
      }
    })
  }
}

// Watch para reinicializar gráficas cuando cambie el tab
watch(activeTab, (newTab) => {
  if (newTab === 'reports') {
    // Esperar un pequeño delay para asegurar que el DOM esté renderizado
    setTimeout(initCharts, 100)
  }
})

onMounted(async () => { 
  await Promise.all([loadUsers(), loadExperiencias(), loadReport(), loadAudit()])
  // Si iniciamos en el tab de reports, inicializar gráficas
  if (activeTab.value === 'reports') {
    await initCharts()
  }
})
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f8f9fa;
}

.sidebar {
  width: 260px;
  position: fixed;
  height: 100vh;
  overflow-y: auto;
}

.main-content {
  flex: 1;
  margin-left: 260px;
  display: flex;
  flex-direction: column;
}

.content-area {
  flex: 1;
  overflow-y: auto;
}

.nav-link {
  color: #6c757d;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.2s;
}

.nav-link:hover {
  background-color: #f8f9fa;
  color: #dc3545;
}

.nav-link.active-link {
  background-color: #dc3545;
  color: white;
}

.nav-link.active-link:hover {
  background-color: #c82333;
}

.card {
  transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}

.table th {
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
}

.btn-group .btn {
  font-size: 0.875rem;
}

.chart-container {
  position: relative;
  width: 100%;
  max-height: 200px;
}

.chart-container canvas {
  max-height: 200px !important;
}

@media (max-width: 768px) {
  .sidebar {
    width: 80px;
  }
  
  .main-content {
    margin-left: 80px;
  }
  
  .sidebar .nav-link span {
    display: none;
  }
  
  .chart-container {
    max-height: 180px;
  }
  
  .chart-container canvas {
    max-height: 180px !important;
  }
}
</style>
