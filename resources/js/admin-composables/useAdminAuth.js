import { ref } from 'vue'
import { adminApiService } from '../admin-services/adminApi'

const user = ref(null)
const isAuthenticated = ref(false)
const isLoading = ref(false)

export function useAdminAuth() {
  const login = async (credentials) => {
    try {
      isLoading.value = true
      const response = await adminApiService.login(credentials)

      const { user: userData, token } = response.data

      localStorage.setItem('admin_token', token)
      localStorage.setItem('admin_user', JSON.stringify(userData))

      user.value = userData
      isAuthenticated.value = true

      return { success: true, user: userData }
    } catch (error) {
      console.error('Admin login failed:', error)
      return {
        success: false,
        error: error.response?.data?.error || 'Login failed'
      }
    } finally {
      isLoading.value = false
    }
  }

  const logout = async () => {
    try {
      await adminApiService.logout()
    } catch (error) {
      console.error('Admin logout error:', error)
    } finally {
      localStorage.removeItem('admin_token')
      localStorage.removeItem('admin_user')
      user.value = null
      isAuthenticated.value = false
    }
  }

  const checkAuth = async () => {
    const token = localStorage.getItem('admin_token')
    const savedUser = localStorage.getItem('admin_user')

    if (!token || !savedUser) {
      return false
    }

    try {
      // Используем validateToken вместо getUser для более надежной проверки
      const userData = await adminApiService.validateToken()
      if (userData) {
        user.value = userData
        isAuthenticated.value = true
        return true
      } else {
        // Токен недействителен, очищаем данные
        localStorage.removeItem('admin_token')
        localStorage.removeItem('admin_user')
        user.value = null
        isAuthenticated.value = false
        return false
      }
    } catch (error) {
      console.error('Auth check failed:', error)
      localStorage.removeItem('admin_token')
      localStorage.removeItem('admin_user')
      user.value = null
      isAuthenticated.value = false
      return false
    }
  }

  const initializeAuth = () => {
    const savedUser = localStorage.getItem('admin_user')
    const token = localStorage.getItem('admin_token')

    if (savedUser && token) {
      try {
        user.value = JSON.parse(savedUser)
        isAuthenticated.value = true
      } catch (error) {
        localStorage.removeItem('admin_user')
        localStorage.removeItem('admin_token')
      }
    }
  }

  const isAdmin = () => {
    return user.value?.role === 'admin'
  }

  return {
    user,
    isAuthenticated,
    isLoading,
    login,
    logout,
    checkAuth,
    initializeAuth,
    isAdmin
  }
}
