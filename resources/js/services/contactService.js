import api from './api.js'

export const contactService = {
  /**
   * Submit contact form (text data only)
   */
  async submit(formData) {
    try {
      const response = await api.post('/contact', formData)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  /**
   * Submit contact form with file upload support
   */
  async submitWithFile(formData) {
    try {
      const response = await api.post('/contact', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  }
}

export default contactService
