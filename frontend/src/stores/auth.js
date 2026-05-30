import { defineStore } from 'pinia'
import { apiLogin, apiRegister, apiLogout } from '../services/api.js'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || '',
    user: JSON.parse(localStorage.getItem('user')) || null,
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin' || state.user?.role === 'super_admin',
    isMember: (state) => state.user?.role === 'member',
  },

  actions: {
    async login(email, password) {
      const res = await apiLogin(email, password)
      if (!res.success) return { success: false, message: res.message }

      this.token = res.token
      this.user = res.user
      localStorage.setItem('token', res.token)
      localStorage.setItem('user', JSON.stringify(res.user))

      return { success: true }
    },

    async register(name, email, password) {
      const res = await apiRegister(name, email, password)
      return res
    },

    async logout() {
      await apiLogout()
      this.token = ''
      this.user = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    },
  },
})
