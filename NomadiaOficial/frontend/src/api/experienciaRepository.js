import api from './axiosConfig';

const experienciaRepository = {
  async getAll(params = {}) {
    // PHP dev server serves files with .php extension; request the explicit PHP endpoint.
    const res = await api.get('/experiencias.php', { params });
    return res.data;
  },

  async getById(id) {
    // Use dedicated endpoint to get single experiencia with all data including images
    const res = await api.get('/experiencia_by_id.php', { params: { id } });
    return res.data;
  },

  async create(data) {
    // Support both JSON and FormData
    const config = {};
    if (data instanceof FormData) {
      config.headers = { 'Content-Type': 'multipart/form-data' };
    }
    const res = await api.post('/experiencias_create.php', data, config);
    return res.data;
  },
};

export default experienciaRepository;
