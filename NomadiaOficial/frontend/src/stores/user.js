import { defineStore } from 'pinia'
import authRepository from '../api/authRepository'

export const useUserStore = defineStore('user', {
  state: () => ({ user: null }),
  actions: {
    setUser(u) {
      this.user = u
      try { 
        if (u) sessionStorage.setItem('user', JSON.stringify(u))
        else sessionStorage.removeItem('user')
      } catch (e) {}
    },
    loadFromSession() {
      try {
        const s = sessionStorage.getItem('user')
        if (s) this.user = JSON.parse(s)
      } catch (e) { this.user = null }
    },
    async login(payload) {
      const res = await authRepository.login(payload)
      if (res && res.user) this.setUser(res.user)
      return res
    },
    logout() {
      this.setUser(null)
    }
  }
})
