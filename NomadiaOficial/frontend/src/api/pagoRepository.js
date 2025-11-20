import api from './axiosConfig'

const pagoRepository = {
  async list(params = {}) {
    const res = await api.get('/pagos.php', { params })
    return res.data
  },
  async create(payload) {
    const res = await api.post('/pagos.php', payload)
    return res.data
  }
}

export default pagoRepository
