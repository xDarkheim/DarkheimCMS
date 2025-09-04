import adminApi from './adminApi.js'

export const adminContactService = {
  /**
   * Get all contact messages with pagination and filters
   */
  async getMessages(params = {}) {
    try {
      const response = await adminApi.get('/admin/contact-messages', { params })
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  /**
   * Get contact messages statistics
   */
  async getStats() {
    try {
      const response = await adminApi.get('/admin/contact-messages/stats')
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  /**
   * Get single contact message
   */
  async getMessage(id) {
    try {
      const response = await adminApi.get(`/admin/contact-messages/${id}`)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  /**
   * Mark message as read
   */
  async markAsRead(id) {
    try {
      const response = await adminApi.post(`/admin/contact-messages/${id}/mark-as-read`)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  /**
   * Delete message
   */
  async deleteMessage(id) {
    try {
      const response = await adminApi.delete(`/admin/contact-messages/${id}`)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  }
}

export default adminContactService
