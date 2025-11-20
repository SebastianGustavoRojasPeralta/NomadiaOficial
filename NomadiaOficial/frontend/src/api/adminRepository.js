import api from './axiosConfig'

export default {
  listUsers() { return api.get('/admin_users.php') },
  createUser(payload) { return api.post('/admin_users.php', { action: 'create', ...payload }) },
  updateUserRole(userId, role) { return api.post('/admin_users.php', { action: 'update_role', user_id: userId, role }) },
  deleteUser(userId) { return api.post('/admin_users.php', { action: 'delete', user_id: userId }) },
  listPendingExperiencias() { return api.post('/admin_approve_experiencia.php', { action: 'list' }) },
  approveExperiencia(id) { return api.post('/admin_approve_experiencia.php', { action: 'approve', experiencia_id: id }) },
  rejectExperiencia(id) { return api.post('/admin_approve_experiencia.php', { action: 'reject', experiencia_id: id }) },
  getReports() { return api.get('/admin_reports.php') }
  ,getAudits() { return api.get('/admin_audit.php') }
  ,listCategorias() { return api.get('/categorias.php') }
  ,createCategoria(payload) { return api.post('/categorias_create.php', payload) }
  ,updateCategoria(payload) { return api.post('/categorias_update.php', payload) }
  ,deleteCategoria(payload) { return api.post('/categorias_delete.php', payload) }
}
