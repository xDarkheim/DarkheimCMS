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

  // Dashboard stats - updated to use new AdminDashboardController
  getStats: () => adminApi.get('/admin/dashboard/stats'),
  getDashboard: () => adminApi.get('/admin/dashboard'),
  getRecentActivity: () => adminApi.get('/admin/dashboard/recent-activity'),

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
  getNews: (page = 1, params = {}) => adminApi.get(`/admin/news`, { params: { page, ...params } }),
  createNews: (data) => adminApi.post('/admin/news', data),
  updateNews: (id, data) => adminApi.put(`/admin/news/${id}`, data),
  deleteNews: (id) => adminApi.delete(`/admin/news/${id}`),
  toggleNewsPublished: (id) => adminApi.post(`/admin/news/${id}/toggle-published`),
  toggleNewsFeatured: (id) => adminApi.post(`/admin/news/${id}/toggle-featured`),
  bulkNewsAction: (data) => adminApi.post('/admin/news/bulk-action', data),
  getNewsCategories: () => adminApi.get('/admin/news/categories'),

  // Contact Messages management
  getContactMessages: (page = 1) => adminApi.get(`/admin/contact-messages?page=${page}`),
  getContactMessage: (id) => adminApi.get(`/admin/contact-messages/${id}`),
  markContactMessageRead: (id) => adminApi.post(`/admin/contact-messages/${id}/mark-as-read`),
  deleteContactMessage: (id) => adminApi.delete(`/admin/contact-messages/${id}`),
  getContactMessageStats: () => adminApi.get('/admin/contact-messages/stats'),

  // Team management
  getTeam: () => adminApi.get('/team'),
  createTeamMember: (data) => adminApi.post('/admin/team', data),
  updateTeamMember: (id, data) => adminApi.put(`/admin/team/${id}`, data),
  deleteTeamMember: (id) => adminApi.delete(`/admin/team/${id}`),

  // Career management
  getCareers: () => adminApi.get('/careers'),
  createCareer: (data) => adminApi.post('/admin/careers', data),
  updateCareer: (id, data) => adminApi.put(`/admin/careers/${id}`, data),
  deleteCareer: (id) => adminApi.delete(`/admin/careers/${id}`),

  // Company Info management
  getCompanyInfo: () => adminApi.get('/admin/company-info'),
  createCompanyInfo: (data) => adminApi.post('/admin/company-info', data),
  updateCompanyInfo: (id, data) => adminApi.put(`/admin/company-info/${id}`, data),
  deleteCompanyInfo: (id) => adminApi.delete(`/admin/company-info/${id}`),
  updateCompanyInfoOrder: (data) => adminApi.post('/admin/company-info/update-order', data),

  // Settings management
  getSettings: () => adminApi.get('/admin/settings'),
  getSettingsByGroup: (group) => adminApi.get(`/admin/settings/group/${group}`),
  updateSettingsGroup: (group, settings) => adminApi.put(`/admin/settings/group/${group}`, { settings }),
  updateSetting: (key, data) => adminApi.put(`/admin/settings/${key}`, data),
  deleteSetting: (key) => adminApi.delete(`/admin/settings/${key}`),
  resetSettingsToDefaults: () => adminApi.post('/admin/settings/reset-defaults'),

  // File Manager
  getFiles: (path = '', type = 'all') => adminApi.get(`/admin/files?path=${path}&type=${type}`),
  uploadFiles: (files, path = '') => {
    const formData = new FormData();
    files.forEach(file => formData.append('files[]', file));
    if (path) formData.append('path', path);

    return adminApi.post('/admin/files/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
  },
  createDirectory: (name, path = '') => adminApi.post('/admin/files/directory', { name, path }),
  deleteFiles: (items) => adminApi.delete('/admin/files', { data: { items } }),

  // Activity Logs
  getActivityLogs: (params = {}) => {
    const query = new URLSearchParams(params).toString();
    return adminApi.get(`/admin/activity-logs?${query}`);
  },
  getActivityLogStats: () => adminApi.get('/admin/activity-logs/stats'),
  getActivityLogFilterOptions: () => adminApi.get('/admin/activity-logs/filter-options'),
  exportActivityLogs: (data) => adminApi.post('/admin/activity-logs/export', data),
  cleanupActivityLogs: (days) => adminApi.post('/admin/activity-logs/cleanup', { days })
}

export default adminApi
