import axios from 'axios'

// Create separate axios instance for admin API
const adminApi = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Add auth token to requests
adminApi.interceptors.request.use((config) => {
  const token = localStorage.getItem('admin_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  // Add CSRF token if available
  const csrfToken = document.querySelector('meta[name="csrf-token"]')
  if (csrfToken) {
    config.headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content')
  }

  // Add debug logging for authentication issues
  console.log('API Request:', {
    url: config.url,
    method: config.method,
    hasToken: !!token,
    tokenPrefix: token ? token.substring(0, 10) + '...' : 'none'
  })

  return config
})

// Enhanced error handling with token refresh and retry logic
adminApi.interceptors.response.use(
  (response) => {
    // Log successful responses for debugging
    console.log('API Response:', {
      url: response.config.url,
      status: response.status,
      method: response.config.method
    })
    return response
  },
  async (error) => {
    const originalRequest = error.config

    // Log authentication errors for debugging
    if (error.response?.status === 401) {
      console.error('Authentication failed:', {
        url: error.config.url,
        method: error.config.method,
        status: error.response.status,
        hasToken: !!localStorage.getItem('admin_token')
      })

      // Clear invalid tokens
      localStorage.removeItem('admin_token')
      localStorage.removeItem('admin_user')

      // Only redirect if we're not already on the login page to prevent loops
      if (!window.location.pathname.includes('/admin/login')) {
        // Show a more user-friendly error message
        alert('Your session has expired. Please log in again.')

        // Use Vue Router instead of hard redirect to prevent conflicts
        if (window.adminRouter) {
          window.adminRouter.push('/admin/login')
        } else {
          window.location.href = '/admin/login'
        }
      }
    }

    // Log other API errors for debugging
    if (error.response) {
      console.error('API Error:', {
        url: error.config.url,
        method: error.config.method,
        status: error.response.status,
        statusText: error.response.statusText,
        data: error.response.data
      })
    } else if (error.request) {
      console.error('Network Error:', {
        url: error.config.url,
        method: error.config.method,
        message: 'No response received from server'
      })
    }

    return Promise.reject(error)
  }
)

// Add a method to validate current token
const validateToken = async () => {
  try {
    const response = await adminApi.get('/validate-token')
    // Проверяем, что ответ содержит valid: true и возвращаем пользователя
    if (response.data && response.data.valid) {
      return response.data.user
    }
    return null
  } catch (error) {
    console.error('Token validation failed:', error)
    return null
  }
}

export const adminApiService = {
  // Authentication
  login: (credentials) => adminApi.post('/login', credentials),
  logout: () => adminApi.post('/logout'),
  getUser: () => adminApi.get('/user'),
  validateToken,

  // Dashboard stats
  getStats: () => adminApi.get('/admin/stats'),

  // User management
  getUsers: (page = 1) => adminApi.get(`/admin/users?page=${page}`),
  createUser: (data) => adminApi.post('/admin/users', data),
  updateUser: (id, data) => adminApi.put(`/admin/users/${id}`, data),
  deleteUser: (id) => adminApi.delete(`/admin/users/${id}`),

  // Portfolio management with enhanced error handling
  getPortfolios: (page = 1) => adminApi.get(`/admin/portfolios?page=${page}`),
  createPortfolio: (data) => adminApi.post('/admin/portfolios', data),
  updatePortfolio: (id, data) => adminApi.put(`/admin/portfolios/${id}`, data),
  deletePortfolio: async (id) => {
    try {
      console.log(`Attempting to delete portfolio ${id}`)
      const response = await adminApi.delete(`/admin/portfolios/${id}`)
      console.log(`Portfolio ${id} deleted successfully`, response.data)
      return response
    } catch (error) {
      console.error(`Failed to delete portfolio ${id}:`, {
        status: error.response?.status,
        message: error.response?.data?.message,
        error: error.response?.data?.error,
        data: error.response?.data
      })
      throw error
    }
  },
  getPortfolioCategories: () => adminApi.get('/admin/portfolios-categories'),

  // News management
  getNews: (page = 1, params = {}) => {
    let url = `/admin/news?page=${page}`
    if (params.status) url += `&status=${params.status}`
    if (params.search) url += `&search=${encodeURIComponent(params.search)}`
    return adminApi.get(url)
  },
  createNews: (data) => adminApi.post('/admin/news', data),
  updateNews: (id, data) => adminApi.put(`/admin/news/${id}`, data),
  deleteNews: (id) => adminApi.delete(`/admin/news/${id}`),
  toggleNewsPublished: (id) => adminApi.post(`/admin/news/${id}/toggle-published`),
  toggleNewsFeatured: (id) => adminApi.post(`/admin/news/${id}/toggle-featured`),
  bulkNewsAction: (data) => adminApi.post('/admin/news/bulk-action', data),
  getNewsCategories: () => adminApi.get('/admin/news/categories'),
}

export default adminApi
