import axios from 'axios';

const api = axios.create({
  // Default to PHP built-in server used in examples. Change if you run under Apache.
  // Use Vite environment variables in the browser via `import.meta.env`.
  baseURL: import.meta.env.VITE_API_BASE || 'http://localhost:8000/api/v1',
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

export default api;
