import axiosInstance from './axiosConfig'

export default {
  async getAll() {
    try {
      const response = await axiosInstance.get('/categorias.php')
      return response.data
    } catch (error) {
      console.error('Error fetching categories:', error)
      throw error
    }
  }
}
