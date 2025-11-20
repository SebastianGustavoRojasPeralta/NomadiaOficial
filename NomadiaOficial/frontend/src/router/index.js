import { createRouter, createWebHistory } from 'vue-router'
import Home from '../pages/Home.vue'
import RegistroView from '../views/RegistroView.vue'
import Login from '../pages/Login.vue'
import ExperienciaShow from '../pages/ExperienciaShow.vue'
import MyReservations from '../pages/MyReservations.vue'
import GuideDashboard from '../pages/GuideDashboard.vue'
import AdminDashboard from '../pages/AdminDashboard.vue'
import ExploreExperiences from '../pages/ExploreExperiences.vue'
import CheckoutPage from '../pages/CheckoutPage.vue'
import CreateExperience from '../pages/CreateExperience.vue'
import EditarPerfil from '../pages/EditarPerfil.vue'
import GuiaPublico from '../pages/GuiaPublico.vue'
import { useUserStore } from '../stores/user'

const routes = [
  { path: '/', component: Home, name: 'home' },
  { path: '/experiencias', component: ExploreExperiences, name: 'explore-experiences' },
  { path: '/register', component: RegistroView, name: 'register' },
  { path: '/login', component: Login, name: 'login' },
  { path: '/experiencia/:id', component: ExperienciaShow, name: 'experiencia-show' },
  { path: '/guia/:id', component: GuiaPublico, name: 'guia-publico' },
  { path: '/checkout', component: CheckoutPage, name: 'checkout', meta: { requiresAuth: true } },
  { path: '/mis-reservas', component: MyReservations, name: 'mis-reservas', meta: { requiresAuth: true } },
  { path: '/guia', component: GuideDashboard, name: 'guia-dashboard', meta: { requiresAuth: true, requiresGuide: true } },
  { path: '/guia/perfil/editar', component: EditarPerfil, name: 'editar-perfil', meta: { requiresAuth: true, requiresGuide: true } },
  { path: '/guia/experiencias/nueva', component: CreateExperience, name: 'create-experience', meta: { requiresAuth: true, requiresGuide: true } },
  { path: '/admin', component: AdminDashboard, name: 'admin-dashboard', meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/admin/categorias', component: () => import('../pages/AdminCategorias.vue'), name: 'admin-categorias', meta: { requiresAuth: true, requiresAdmin: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Simple global guard: load user from session and protect routes marked `requiresAuth`.
router.beforeEach((to, from, next) => {
  const store = useUserStore()
  store.loadFromSession()
  const role = store.user && store.user.role ? String(store.user.role).toLowerCase() : ''
  const isGuide = ['guide','guia','guía','host','anfitrion'].includes(role)
  // Only treat as admin when role is exactly 'admin'
  const isAdmin = role === 'admin'
  if (to.meta && to.meta.requiresAuth && !store.user) {
    next({ name: 'login', query: { redirect: to.fullPath } })
  } else if (to.meta && to.meta.requiresGuide && !isGuide) {
    // user is authenticated but not a guide -> redirect home
    next({ name: 'home' })
  } else if (to.meta && to.meta.requiresAdmin && !isAdmin) {
    next({ name: 'home' })
  } else next()
})

export default router
