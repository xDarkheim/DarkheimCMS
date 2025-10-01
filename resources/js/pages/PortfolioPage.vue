<template>
  <div class="portfolio-page page-with-header-offset">
    <!-- Filter Section -->
    <section class="section">
      <div class="container">
        <div class="section__header animate-fade-in">
          <h1 class="section__title">Our Portfolio</h1>
          <p class="section__subtitle">
            Explore our latest projects and see how we've helped businesses
            achieve their goals through innovative digital solutions.
          </p>
        </div>

        <!-- Loading state -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading projects...</p>
        </div>

        <!-- Error state -->
        <div v-else-if="error" class="error-state">
          <p>{{ error }}</p>
          <button @click="loadPortfolios()" class="btn btn--primary">Try Again</button>
        </div>

        <!-- Search and Filters -->
        <div class="portfolio-filters animate-fade-in">
          <button
            @click="setActiveCategory('all')"
            class="filter-btn"
            :class="{ 'filter-btn--active': activeCategory === 'all' }"
          >
            <span>All Projects</span>
          </button>
          <button
            v-for="category in categories"
            :key="category.id"
            @click="setActiveCategory(category.id)"
            class="filter-btn"
            :class="{ 'filter-btn--active': activeCategory === category.id }"
          >
            <span>{{ category.name }}</span>
          </button>
        </div>
      </div>
    </section>

    <!-- Portfolio Grid -->
    <section class="section" v-if="!loading && !error">
      <div class="container">
        <div v-if="displayedProjects.length === 0" class="empty-state">
          <p>No projects found matching your criteria.</p>
        </div>

        <template v-else>
          <div class="portfolio-grid animate-slide-up">
            <div
              v-for="project in displayedProjects"
              :key="project.id"
              class="portfolio-item"
              @click="openProjectModal(project)"
            >
              <div class="portfolio-item__image">
                <img
                  v-if="project.image_url"
                  :src="project.image_url"
                  :alt="project.title"
                  class="portfolio-image"
                />
                <div v-else class="portfolio-placeholder" :class="`portfolio-placeholder--${project.categoryId}`">
                  <div class="portfolio-placeholder__icon">
                    <i :class="getPlaceholderIcon(project.categoryId)"></i>
                  </div>
                  <span class="portfolio-placeholder__text">{{ project.category_name || project.category || 'Uncategorized' }}</span>
                </div>
                <div class="portfolio-item__overlay">
                  <div class="portfolio-item__actions">
                    <button class="portfolio-action-btn eye-btn" title="View Details">
                      <i class="fas fa-eye"></i>
                    </button>
                    <a
                      v-if="project.project_url"
                      :href="project.project_url"
                      target="_blank"
                      class="portfolio-action-btn external-btn"
                      @click.stop
                      title="View Live Project"
                    >
                      <i class="fas fa-external-link-alt"></i>
                    </a>
                    <a
                      v-if="project.github_url"
                      :href="project.github_url"
                      target="_blank"
                      class="portfolio-action-btn github-btn"
                      @click.stop
                      title="View Source Code"
                    >
                      <i class="fab fa-github"></i>
                    </a>
                  </div>
                </div>
              </div>
              <div class="portfolio-item__content">
                <h3 class="portfolio-item__title">{{ project.title }}</h3>
                <p class="portfolio-item__description">{{ project.short_description }}</p>
                <div class="portfolio-item__meta" v-if="project.client || project.completed_at">
                  <span class="portfolio-meta__client" v-if="project.client">{{ project.client }}</span>
                  <span class="portfolio-meta__date" v-if="project.completed_at">{{ formatDate(project.completed_at) }}</span>
                </div>
                <div class="portfolio-item__tech" v-if="project.technologies?.length">
                  <span v-for="tech in project.technologies" :key="tech" class="tech-tag">
                    {{ tech }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="pagination">
            <button
              class="pagination-btn"
              @click="prevPage"
              :disabled="currentPage === 1"
            >
              Previous
            </button>
            <div class="pagination-info">
              Page {{ currentPage }} of {{ totalPages }}
            </div>
            <button
              class="pagination-btn"
              @click="nextPage"
              :disabled="currentPage === totalPages"
            >
              Next
            </button>
          </div>
        </template>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="section section--light" v-if="!loading && portfolios.length > 0">
      <div class="container">
        <div class="section__header animate-fade-in">
          <h2 class="section__title">Project Impact</h2>
          <p class="section__subtitle">
            Numbers that showcase the success of our portfolio projects.
          </p>
        </div>

        <div class="stats animate-slide-up">
          <div class="stats__item">
            <div class="stats__number">{{ portfolioStats.totalProjects }}+</div>
            <div class="stats__label">Projects Completed</div>
          </div>
          <div class="stats__item">
            <div class="stats__number">{{ portfolioStats.happyClients }}+</div>
            <div class="stats__label">Happy Clients</div>
          </div>
          <div class="stats__item">
            <div class="stats__number">{{ portfolioStats.averageRating }}</div>
            <div class="stats__label">Average Rating</div>
          </div>
          <div class="stats__item">
            <div class="stats__number">{{ portfolioStats.successRate }}%</div>
            <div class="stats__label">Success Rate</div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="section section--gradient">
      <div class="container">
        <div class="text-center animate-fade-in">
          <h2 class="section__title">Ready to Start Your Project?</h2>
          <p class="section__subtitle mb-8">
            Let's create something amazing together. Get in touch to discuss your ideas.
          </p>
          <router-link to="/contact" class="btn btn--secondary btn--xl">
            <span>Start Your Project</span>
            <i class="fas fa-arrow-right btn__icon btn__icon--right"></i>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Project Modal -->
    <div v-if="selectedProject" class="modal-overlay" @click="closeProjectModal">
      <div class="project-modal" @click.stop>
        <button class="modal-close" @click="closeProjectModal">
          <i class="fas fa-times"></i>
        </button>

        <div class="project-modal__header">
          <h2 class="project-modal__title">{{ selectedProject.title }}</h2>
          <div class="project-modal__category">{{ selectedProject.category_name || selectedProject.category || 'Uncategorized' }}</div>
          <div class="project-modal__client" v-if="selectedProject.client">
            Client: {{ selectedProject.client }}
          </div>
          <div class="project-modal__date" v-if="selectedProject.completed_at">
            Completed: {{ formatDate(selectedProject.completed_at) }}
          </div>
        </div>

        <div class="project-modal__content">
          <!-- Левая колонка с изображениями -->
          <div class="project-modal__left-column">
            <!-- Основное изображение проекта -->
            <div class="project-modal__main-image" v-if="selectedProject.image_url">
              <img
                :src="selectedProject.image_url"
                :alt="selectedProject.title"
                class="main-project-image"
              />
            </div>

            <!-- Дополнительная галерея изображений -->
            <div v-if="selectedProject.gallery_images?.length" class="project-modal__gallery">
              <h3>Project Gallery</h3>
              <div class="gallery">
                <img
                  v-for="(image, index) in selectedProject.gallery_images"
                  :key="index"
                  :src="image"
                  :alt="`${selectedProject.title} gallery image ${index + 1}`"
                  class="gallery-image"
                />
              </div>
            </div>
          </div>

          <!-- Правая колонка с текстом и деталями -->
          <div class="project-modal__right-column">
            <p class="project-modal__description">{{ selectedProject.description }}</p>

            <div class="project-modal__details">
              <div class="detail-section" v-if="selectedProject.technologies?.length">
                <h3>Technologies Used</h3>
                <div class="tech-list">
                  <span v-for="tech in selectedProject.technologies" :key="tech" class="tech-tag">
                    {{ tech }}
                  </span>
                </div>
              </div>
            </div>

            <div class="project-modal__actions" v-if="selectedProject.project_url || selectedProject.github_url">
              <a v-if="selectedProject.project_url" :href="selectedProject.project_url" target="_blank" class="btn btn--primary btn--base">
                <span>View Live Project</span>
                <i class="fas fa-external-link-alt btn__icon btn__icon--right"></i>
              </a>
              <a v-if="selectedProject.github_url" :href="selectedProject.github_url" target="_blank" class="btn btn--outline btn--base">
                <span>View on GitHub</span>
                <i class="fab fa-github btn__icon btn__icon--right"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import portfolioService from '../services/portfolioService'

export default {
  name: 'PortfolioPage',
  setup() {
    const route = useRoute()
    const loading = ref(true)
    const error = ref(null)
    const portfolios = ref([])
    const portfolioMeta = ref(null)
    const categories = ref([])
    const activeCategory = ref('all')
    const selectedProject = ref(null)
    const currentPage = ref(1)
    const isInitialLoad = ref(true)

    // Обновляем computed свойство для отображаемых проектов
    const displayedProjects = computed(() => {
      const realProjects = portfolios.value || []

      // Если выбрана категория "all"
      if (activeCategory.value === 'all') {
        return realProjects
      }

      // Для конкретной категории - ИСПРАВЛЕНО: убираем клиентскую фильтрацию
      // так как API уже возвращает отфильтрованные данные
      return realProjects
    })

    // Load portfolios from API
    const loadPortfolios = async (page = 1, categoryName = undefined) => {
      try {
        // Показываем loading только при первичной загрузке страницы
        if (isInitialLoad.value) {
          loading.value = true
          error.value = null
        }

        const params = {
          page,
          per_page: 12, // Увеличиваем до 12 проектов на странице, чтобы поместились все
          category: categoryName
        }

        const response = await portfolioService.getAll(params)

        if (response && response.success) {
          // Успешный ответ - обрабатываем данные
          portfolios.value = Array.isArray(response.data) ? response.data : []
          portfolioMeta.value = response.meta || {
            current_page: page,
            last_page: 1,
            per_page: 12,
            total: Array.isArray(response.data) ? response.data.length : 0
          }
          currentPage.value = page
        } else {
          // API вернул неуспешный ответ
          portfolios.value = []
          portfolioMeta.value = {
            current_page: page,
            last_page: 1,
            per_page: 12,
            total: 0
          }
        }
      } catch (err) {
        console.error('Portfolio loading error:', err)

        // При ошибке показываем пустой результат
        portfolios.value = []
        portfolioMeta.value = {
          current_page: page,
          last_page: 1,
          per_page: 12,
          total: 0
        }

        // Показываем ошибку только при первой загрузке страницы
        if (isInitialLoad.value) {
          error.value = 'Unable to connect to the database. Please check your connection and try again.'
        }
      } finally {
        // Всегда убираем loading и отмечаем что первичная загрузка завершена
        if (isInitialLoad.value) {
          loading.value = false
          isInitialLoad.value = false
        }
      }
    }

    // Load categories from API
    const loadCategories = async () => {
      try {
        // Используем API endpoint для получения категорий портфолио
        const response = await portfolioService.getCategories()
        if (response && response.success && response.data) {
          // Преобразуем ответ API в формат категорий
          const allCategories = Object.entries(response.data).map(([slug, name]) => ({
            id: slug,
            name,
            slug
          }))

          categories.value = allCategories
          console.log('Loaded portfolio categories:', allCategories)
        } else {
          // Fallback categories
          categories.value = [
            { id: 'web-development', name: 'Web Development', slug: 'web-development' },
            { id: 'mobile-applications', name: 'Mobile Applications', slug: 'mobile-applications' },
            { id: 'ecommerce-solutions', name: 'E-commerce Solutions', slug: 'ecommerce-solutions' },
            { id: 'business-applications', name: 'Business Applications', slug: 'business-applications' },
            { id: 'landing-pages', name: 'Landing Pages', slug: 'landing-pages' },
            { id: 'portfolio-websites', name: 'Portfolio Websites', slug: 'portfolio-websites' },
            { id: 'api-development', name: 'API Development', slug: 'api-development' }
          ]
        }
      } catch (err) {
        console.error('Categories loading error:', err)
        // Fallback categories - все предопределенные категории
        categories.value = [
          { id: 'web-development', name: 'Web Development', slug: 'web-development' },
          { id: 'mobile-applications', name: 'Mobile Applications', slug: 'mobile-applications' },
          { id: 'ecommerce-solutions', name: 'E-commerce Solutions', slug: 'ecommerce-solutions' },
          { id: 'business-applications', name: 'Business Applications', slug: 'business-applications' },
          { id: 'landing-pages', name: 'Landing Pages', slug: 'landing-pages' },
          { id: 'portfolio-websites', name: 'Portfolio Websites', slug: 'portfolio-websites' },
          { id: 'api-development', name: 'API Development', slug: 'api-development' }
        ]
      }
    }

    const portfolioStats = computed(() => ({
      totalProjects: portfolioMeta.value?.total || 0,
      happyClients: Math.max(20, Math.floor((portfolioMeta.value?.total || 0) * 0.8)),
      averageRating: 4.9,
      successRate: 98
    }))

    const totalPages = computed(() => portfolioMeta.value?.last_page || 1)

    const setActiveCategory = async (categoryId) => {
      activeCategory.value = categoryId
      currentPage.value = 1

      // Для API запроса используем slug категории, а не название
      let categoryForApi = undefined
      if (categoryId !== 'all') {
        // Находим категорию по ID и используем её slug для API (НЕ name!)
        const selectedCategory = categories.value.find(cat => cat.id === categoryId)
        if (selectedCategory) {
          categoryForApi = selectedCategory.slug // Исправлено: используем slug вместо name
        }
      }

      await loadPortfolios(1, categoryForApi)
    }

    const openProjectModal = (project) => {
      selectedProject.value = project
      document.body.style.overflow = 'hidden'
    }

    const closeProjectModal = () => {
      selectedProject.value = null
      document.body.style.overflow = 'auto'
    }

    const nextPage = async () => {
      if (currentPage.value < totalPages.value) {
        await loadPortfolios(currentPage.value + 1)
      }
    }

    const prevPage = async () => {
      if (currentPage.value > 1) {
        await loadPortfolios(currentPage.value - 1)
      }
    }

    // Date formatting function
    const formatDate = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short'
      })
    }

    // Function to get placeholder icons for different categories
    const getPlaceholderIcon = (categoryId) => {
      const iconMap = {
        'web-development': 'fas fa-globe',
        'mobile-applications': 'fas fa-mobile-alt',
        'ecommerce-solutions': 'fas fa-shopping-cart',
        'business-applications': 'fas fa-briefcase',
        'landing-pages': 'fas fa-rocket',
        'portfolio-websites': 'fas fa-image',
        'api-development': 'fas fa-code'
      }
      return iconMap[categoryId] || 'fas fa-project-diagram'
    }

    onMounted(async () => {
      // Проверяем URL параметры при загрузке страницы
      const urlParams = new URLSearchParams(window.location.search)
      const categoryParam = urlParams.get('category')

      // Load initial data
      await Promise.all([
        loadCategories(),
        loadPortfolios()
      ])

      // Если есть категория в URL, устанавливаем её после загрузки категорий
      if (categoryParam) {
        // Ищем категорию по параметру URL
        const foundCategory = categories.value.find(cat =>
          cat.id === categoryParam || cat.slug === categoryParam
        )

        if (foundCategory) {
          await setActiveCategory(foundCategory.id)
        }
      }

      // Add scroll-triggered animations
      const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      }

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.animationPlayState = 'running'
          }
        })
      }, observerOptions)

      // Observe all animated elements
      setTimeout(() => {
        document.querySelectorAll('.animate-fade-in, .animate-slide-up').forEach(el => {
          el.style.animationPlayState = 'paused'
          observer.observe(el)
        })
      }, 100)
    })

    // Watch for route changes to handle navigation within portfolio page
    watch(() => route.query.category, async (newCategory) => {
      if (!isInitialLoad.value && categories.value.length > 0) {
        if (newCategory) {
          // Ищем категорию по параметру URL
          const foundCategory = categories.value.find(cat =>
            cat.id === newCategory || cat.slug === newCategory
          )

          if (foundCategory) {
            await setActiveCategory(foundCategory.id)
          }
        } else {
          // Если нет категории в URL, показываем все проекты
          await setActiveCategory('all')
        }
      }
    })

    return {
      loading,
      error,
      portfolios,
      portfolioMeta,
      activeCategory,
      selectedProject,
      categories,
      portfolioStats,
      currentPage,
      totalPages,
      setActiveCategory,
      openProjectModal,
      closeProjectModal,
      nextPage,
      prevPage,
      loadPortfolios,
      formatDate,
      displayedProjects,
      getPlaceholderIcon
    }
  }
}
</script>

<style lang="scss" scoped>
@use '../../css/pages/portfolio-page';
</style>
