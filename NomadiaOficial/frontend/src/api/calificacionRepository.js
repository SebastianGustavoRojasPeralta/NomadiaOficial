import api from './axiosConfig'

const calificacionRepository = {
  async list(params = {}) {
    const res = await api.get('/calificaciones.php', { params })
    return res.data
  },
  async create(payload) {
    const res = await api.post('/calificaciones.php', payload)
    return res.data
  }
}

export default calificacionRepository
