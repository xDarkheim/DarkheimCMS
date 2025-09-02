import { ref, reactive, computed } from 'vue'
import portfolioService from '../services/portfolioService.js'

// Глобальное состояние портфолио
const portfolios = ref([])
const featuredPortfolios = ref([])
const categories = ref({})
const loading = ref(false)
const error = ref(null)

// Пагинация и фильтры
const filters = reactive({
  category: 'all',
  search: '',
  featured: false
})

const pagination = reactive({
  currentPage: 1,
  lastPage: 1,
  perPage: 12,
  total: 0
})

export function usePortfolio() {
  // Загрузка портфолио с фильтрами
  const loadPortfolios = async (params = {}) => {
    try {
      loading.value = true
      error.value = null

      const mergedParams = {
        ...filters,
        page: pagination.currentPage,
        per_page: pagination.perPage,
        ...params
      }

      const response = await portfolioService.getPortfolios(mergedParams)

      if (response.success) {
        portfolios.value = response.data
        pagination.currentPage = response.meta.current_page
        pagination.lastPage = response.meta.last_page
        pagination.perPage = response.meta.per_page
        pagination.total = response.meta.total
      }
    } catch (err) {
      error.value = 'Ошибка при загрузке портфолио'
      console.error('Error loading portfolios:', err)
    } finally {
      loading.value = false
    }
  }

  // Загрузка избранных портфолио
  const loadFeaturedPortfolios = async () => {
    try {
      const response = await portfolioService.getFeaturedPortfolios()
      if (response.success) {
        featuredPortfolios.value = response.data
      }
    } catch (err) {
      console.error('Error loading featured portfolios:', err)
    }
  }

  // Загрузка категорий
  const loadCategories = async () => {
    try {
      const response = await portfolioService.getCategories()
      if (response.success) {
        categories.value = response.data
      }
    } catch (err) {
      console.error('Error loading categories:', err)
    }
  }

  // Получение конкретного портфолио
  const getPortfolio = async (slug) => {
    try {
      loading.value = true
      error.value = null

      const response = await portfolioService.getPortfolio(slug)

      if (response.success) {
        return response.data
      }
    } catch (err) {
      error.value = 'Портфолио не найдено'
      console.error('Error loading portfolio:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  // Фильтрация по категории
  const filterByCategory = async (category) => {
    filters.category = category
    pagination.currentPage = 1
    await loadPortfolios()
  }

  // Поиск
  const searchPortfolios = async (searchTerm) => {
    filters.search = searchTerm
    pagination.currentPage = 1
    await loadPortfolios()
  }

  // Переход на страницу
  const goToPage = async (page) => {
    pagination.currentPage = page
    await loadPortfolios()
  }

  // Сброс фильтров
  const resetFilters = async () => {
    filters.category = 'all'
    filters.search = ''
    filters.featured = false
    pagination.currentPage = 1
    await loadPortfolios()
  }

  // Вычисляемые свойства
  const hasNextPage = computed(() => pagination.currentPage < pagination.lastPage)
  const hasPrevPage = computed(() => pagination.currentPage > 1)
  const totalPages = computed(() => pagination.lastPage)

  return {
    // Состояние
    portfolios,
    featuredPortfolios,
    categories,
    loading,
    error,
    filters,
    pagination,

    // Методы
    loadPortfolios,
    loadFeaturedPortfolios,
    loadCategories,
    getPortfolio,
    filterByCategory,
    searchPortfolios,
    goToPage,
    resetFilters,

    // Вычисляемые свойства
    hasNextPage,
    hasPrevPage,
    totalPages
  }
}
