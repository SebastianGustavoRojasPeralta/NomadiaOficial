import { defineStore } from 'pinia'
import reservaRepository from '../api/reservaRepository'

export const useReservaStore = defineStore('reserva', {
  state: () => ({ reservas: [], loading: false }),
  actions: {
    async fetchByUser(usuario_id) {
      this.loading = true
      try {
        const data = await reservaRepository.list({ usuario_id })
        this.reservas = data || []
        return this.reservas
      } finally {
        this.loading = false
      }
    },

    async crearReserva(payload) {
      const res = await reservaRepository.create(payload)
      return res
    }
  }
})

export default useReservaStore
