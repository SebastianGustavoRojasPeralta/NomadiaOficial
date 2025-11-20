import api from './axiosConfig'

const reservaRepository = {
  async list(params = {}) {
    const res = await api.get('/reservas.php', { params })
    return res.data
  },
  async create(payload) {
    const res = await api.post('/reservas.php', payload)
    return res.data
  }
}

export default reservaRepository
