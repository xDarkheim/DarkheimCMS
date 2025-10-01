import api from './api'

class PortfolioService {
  /**
   * Получить все портфолио с фильтрацией и пагинацией
   */
  async getAll(params = {}) {
    try {
      const response = await api.get('/portfolios', { params })
      return response.data
    } catch (error) {
      throw new Error('Failed to fetch portfolios')
    }
  }

  /**
   * Получить избранные портфолио
   */
  async getFeatured() {
    try {
      const response = await api.get('/portfolios/featured')
      return response.data
    } catch (error) {
      throw new Error('Failed to fetch featured portfolios')
    }
  }

  /**
   * Получить конкретный проект по slug
   */
  async getBySlug(slug) {
    try {
      const response = await api.get(`/portfolios/${slug}`)
      return response.data
    } catch (error) {
      throw new Error('Failed to fetch portfolio details')
    }
  }

  /**
   * Получить доступные категории
   */
  async getCategories() {
    try {
      const response = await api.get('/portfolios/categories')
      return response.data
    } catch (error) {
      throw new Error('Failed to fetch categories')
    }
  }

  /**
   * Создать новое портфолио (для админки)
   */
  async create(data) {
    try {
      const response = await api.post('/portfolios', data)
      return response.data
    } catch (error) {
      throw new Error('Failed to create portfolio')
    }
  }

  /**
   * Обновить портфолио (для админки)
   */
  async update(slug, data) {
    try {
      const response = await api.put(`/portfolios/${slug}`, data)
      return response.data
    } catch (error) {
      throw new Error('Failed to update portfolio')
    }
  }

  /**
   * Удалить портфолио (для админки)
   */
  async delete(slug) {
    try {
      const response = await api.delete(`/portfolios/${slug}`)
      return response.data
    } catch (error) {
      throw new Error('Failed to delete portfolio')
    }
  }
}

export default new PortfolioService()
