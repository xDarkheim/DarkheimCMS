import api from './api.js'

export const contactService = {
  /**
   * Submit contact form
   */
  async submit(formData) {
    try {
      const response = await api.post('/contact', formData)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  }
}

export default contactService
