import api from './axiosConfig'

const disponibilidadRepository = {
  async list(params = {}) {
    const res = await api.get('/disponibilidades.php', { params })
    return res.data
  },
  async create(payload) {
    const res = await api.post('/disponibilidades.php', payload)
    return res.data
  }
}

export default disponibilidadRepository
