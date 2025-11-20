import { defineStore } from 'pinia';
import experienciaRepository from '../api/experienciaRepository';

export const useExperienciaStore = defineStore('experiencia', {
  state: () => ({
    experiencias: [],
    experienciaActual: null,
    loading: false,
  }),

  actions: {
    async fetchExperiencias(filtros = {}) {
      this.loading = true;
      try {
        const data = await experienciaRepository.getAll(filtros);
        this.experiencias = data;
        return data;
      } finally {
        this.loading = false;
      }
    },

    async fetchExperienciaById(id) {
      this.loading = true;
      try {
        const data = await experienciaRepository.getById(id);
        this.experienciaActual = data;
        return data;
      } finally {
        this.loading = false;
      }
    },
  },
});

export default useExperienciaStore;
