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
        <template v-else>
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
        </template>
      </div>
    </section>

    <!-- Portfolio Grid -->
    <section class="section" v-if="!loading && !error">
      <div class="container">
        <div v-if="portfolios.length === 0" class="empty-state">
          <p>No projects found matching your criteria.</p>
        </div>

        <template v-else>
          <div class="portfolio-grid animate-slide-up">
            <div
              v-for="project in portfolios"
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
                <div v-else class="portfolio-placeholder">{{ project.category }}</div>
                <div class="portfolio-item__overlay">
                  <div class="portfolio-item__actions">
                    <button class="portfolio-action-btn">
                      <i class="fas fa-eye"></i>
                    </button>
                    <a :href="project.project_url" target="_blank" class="portfolio-action-btn" v-if="project.project_url" @click.stop>
                      <i class="fas fa-external-link-alt"></i>
                    </a>
                    <a :href="project.github_url" target="_blank" class="portfolio-action-btn" v-if="project.github_url" @click.stop>
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
          <div class="project-modal__category">{{ selectedProject.category }}</div>
          <div class="project-modal__client" v-if="selectedProject.client">
            Client: {{ selectedProject.client }}
          </div>
          <div class="project-modal__date" v-if="selectedProject.completed_at">
            Completed: {{ formatDate(selectedProject.completed_at) }}
          </div>
        </div>

        <div class="project-modal__content">
          <div v-if="selectedProject.gallery_images?.length" class="project-modal__gallery">
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
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import portfolioService from '../services/portfolioService'

export default {
  name: 'PortfolioPage',
  setup() {
    const loading = ref(true)
    const error = ref(null)
    const portfolios = ref([])
    const portfolioMeta = ref(null)
    const categories = ref([])
    const activeCategory = ref('all')
    const selectedProject = ref(null)
    const currentPage = ref(1)
    const isInitialLoad = ref(true)

    // Load portfolios from API
    const loadPortfolios = async (page = 1) => {
      try {
        // Показываем loading только при первичной загрузке страницы
        if (isInitialLoad.value) {
          loading.value = true
          error.value = null
        }

        const params = {
          page,
          per_page: 6,
          category: activeCategory.value !== 'all' ? activeCategory.value : undefined
        }

        const response = await portfolioService.getAll(params)

        if (response && response.success) {
          // Успешный ответ - обрабатываем данные
          portfolios.value = Array.isArray(response.data) ? response.data : []
          portfolioMeta.value = response.meta || {
            current_page: page,
            last_page: 1,
            per_page: 6,
            total: Array.isArray(response.data) ? response.data.length : 0
          }
          currentPage.value = page
        } else {
          // API вернул неуспешный ответ
          portfolios.value = []
          portfolioMeta.value = {
            current_page: page,
            last_page: 1,
            per_page: 6,
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
          per_page: 6,
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
        const response = await portfolioService.getCategories()
        if (response && response.success && response.data) {
          // Получаем все категории из API
          const allCategories = Object.entries(response.data).map(([key, name]) => ({
            id: key,
            name
          }))

          // Проверяем каждую категорию на наличие проектов
          const categoriesWithProjects = []

          for (const category of allCategories) {
            try {
              const categoryResponse = await portfolioService.getAll({
                page: 1,
                per_page: 1,
                category: category.id
              })

              // Если в категории есть проекты, добавляем её в список
              if (categoryResponse && categoryResponse.success &&
                  categoryResponse.data && categoryResponse.data.length > 0) {
                categoriesWithProjects.push(category)
              }
            } catch (err) {
              console.warn(`Failed to check category ${category.id}:`, err)
              // В случае ошибки оставляем категорию (лучше показать лишнее, чем скрыть нужное)
              categoriesWithProjects.push(category)
            }
          }

          categories.value = categoriesWithProjects
        } else {
          // Fallback categories
          categories.value = [
            { id: 'web', name: 'Web Development' },
            { id: 'mobile', name: 'Mobile Apps' }
          ]
        }
      } catch (err) {
        console.error('Categories loading error:', err)
        // Fallback categories
        categories.value = [
          { id: 'web', name: 'Web Development' },
          { id: 'mobile', name: 'Mobile Apps' }
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
      await loadPortfolios(1)
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

    onMounted(async () => {
      // Load initial data
      await Promise.all([
        loadCategories(),
        loadPortfolios()
      ])

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
      formatDate
    }
  }
}
</script>

<style lang="scss" scoped>
@use '../../css/pages/portfolio-page';
</style>
