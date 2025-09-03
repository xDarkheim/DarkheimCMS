import axios from 'axios'

// Создаем экземпляр axios с базовой конфигурацией
const api = axios.create({
  baseURL: '/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Интерсептор для добавления токена авторизации
api.interceptors.request.use(
  config => {
    const token = localStorage.getItem('admin_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  error => {
    return Promise.reject(error)
  }
)

// Интерсептор для обработки ответов
api.interceptors.response.use(
  response => response,
  error => {
    console.error('API Error:', error)

    // Если получили 401 ошибку (неавторизован), перенаправляем на логин
    if (error.response?.status === 401) {
      localStorage.removeItem('admin_token')
      // Если мы находимся на админской странице, перенаправляем на логин
      if (window.location.pathname.startsWith('/admin') && !window.location.pathname.includes('/login')) {
        window.location.href = '/admin/login'
      }
    }

    return Promise.reject(error)
  }
)

export default api
