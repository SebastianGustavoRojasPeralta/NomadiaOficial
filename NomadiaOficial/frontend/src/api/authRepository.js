import api from './axiosConfig'

const authRepository = {
  async login(payload) {
    const res = await api.post('/login.php', payload)
    return res.data
  },
  async register(payload) {
    const res = await api.post('/register.php', payload)
    return res.data
  },
  async me() {
    const res = await api.get('/me.php')
    return res.data
  }
}

export default authRepository
