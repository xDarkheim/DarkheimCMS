<template>
  <div class="news-page page-with-header-offset">
    <!-- Page Header -->
    <section class="page-header">
      <div class="container">
        <div class="header-content">
          <h1>Latest News & Updates</h1>
          <p>Stay informed about our latest projects, technologies, and studio developments</p>

          <!-- Enhanced Search Bar -->
          <div class="search-bar">
            <div class="search-input-wrapper">
              <i class="fas fa-search search-icon"></i>
              <input
                v-model="searchQuery"
                @input="debounceSearch"
                type="text"
                placeholder="Search articles..."
                class="search-input"
              >
              <button v-if="searchQuery" @click="clearSearch" class="clear-search">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- News Content -->
    <section class="news-content">
      <div class="container">
        <div class="news-layout">
          <!-- Simplified Sidebar -->
          <aside class="news-sidebar">
            <!-- Categories Filter -->
            <div class="filter-card">
              <h3>Categories</h3>
              <div class="category-filters">
                <button
                  @click="setCategory('')"
                  :class="['category-btn', { active: selectedCategory === '' }]"
                >
                  <i class="fas fa-th-large"></i>
                  All News
                </button>
                <button
                  v-for="(displayName, key) in categories"
                  :key="key"
                  @click="setCategory(key)"
                  :class="['category-btn', { active: selectedCategory === key }]"
                >
                  <i :class="getCategoryIcon(key)"></i>
                  {{ displayName }}
                </button>
              </div>
            </div>

            <!-- Featured Articles -->
            <div class="featured-sidebar">
              <h3>Featured Articles</h3>
              <div v-if="featuredNews.length > 0" class="featured-list">
                <article
                  v-for="article in featuredNews.slice(0, 3)"
                  :key="article.id"
                  class="featured-item"
                >
                  <div class="featured-image">
                    <img
                      v-if="article.image_url"
                      :src="article.image_url"
                      :alt="article.title"
                      loading="lazy"
                    >
                    <div v-else class="no-image">
                      <i class="fas fa-newspaper"></i>
                    </div>
                  </div>
                  <div class="featured-content">
                    <h4>
                      <router-link :to="`/news/${article.slug}`">
                        {{ article.title }}
                      </router-link>
                    </h4>
                    <p class="featured-date">{{ formatDate(article.published_at) }}</p>
                  </div>
                </article>
              </div>
            </div>

            <!-- Back to Home -->
            <div class="filter-card">
              <router-link to="/" class="btn btn-outline btn-full">
                <i class="fas fa-home"></i>
                Back to Home
              </router-link>
            </div>
          </aside>

          <!-- Main News List -->
          <main class="news-main">
            <div class="news-controls">
              <div class="results-info">
                <span v-if="news.total">
                  Showing {{ news.from }}-{{ news.to }} of {{ news.total }} articles
                </span>
              </div>
            </div>

            <div v-if="loading" class="loading-state">
              <div class="loading-spinner"></div>
              <p>Loading news articles...</p>
            </div>

            <div v-else-if="news.data && news.data.length > 0" class="news-grid">
              <article
                v-for="article in news.data"
                :key="article.id"
                class="news-card"
              >
                <div class="news-image">
                  <img
                    v-if="article.image_url"
                    :src="article.image_url"
                    :alt="article.title"
                    loading="lazy"
                  >
                  <div v-else class="no-image">
                    <i class="fas fa-newspaper"></i>
                  </div>
                  <div class="news-category">{{ getCategoryDisplayName(article.category) }}</div>
                  <div v-if="article.is_featured" class="featured-badge">
                    <i class="fas fa-star"></i>
                    Featured
                  </div>
                </div>

                <div class="news-content">
                  <div class="news-meta">
                    <span class="news-date">
                      <i class="fas fa-calendar"></i>
                      {{ formatDate(article.published_at) }}
                    </span>
                    <span class="news-author">
                      <i class="fas fa-user"></i>
                      {{ article.author }}
                    </span>
                    <span v-if="article.views" class="news-views">
                      <i class="fas fa-eye"></i>
                      {{ article.views }} views
                    </span>
                  </div>

                  <h2 class="news-title">
                    <router-link :to="`/news/${article.slug}`">
                      {{ article.title }}
                    </router-link>
                  </h2>

                  <p class="news-excerpt">
                    {{ article.excerpt || article.content?.substring(0, 200) + '...' }}
                  </p>

                  <div class="news-footer">
                    <div class="news-tags">
                      <span class="tag">{{ getCategoryDisplayName(article.category) }}</span>
                    </div>
                    <router-link
                      :to="`/news/${article.slug}`"
                      class="read-more"
                    >
                      Read More
                      <i class="fas fa-arrow-right"></i>
                    </router-link>
                  </div>
                </div>
              </article>
            </div>

            <div v-else class="empty-state">
              <div class="empty-icon">
                <i class="fas fa-newspaper"></i>
              </div>
              <h3>No Articles Found</h3>
              <p v-if="selectedCategory">
                No articles found in the "{{ selectedCategory }}" category.
              </p>
              <p v-else>
                No news articles are currently available.
              </p>
              <button
                v-if="selectedCategory"
                @click="selectedCategory = ''"
                class="btn btn-primary"
              >
                View All Articles
              </button>
            </div>

            <!-- Pagination -->
            <div v-if="news.last_page > 1" class="pagination">
              <button
                @click="loadNews(news.current_page - 1)"
                :disabled="news.current_page === 1"
                class="btn btn-secondary"
              >
                <i class="fas fa-chevron-left"></i>
                Previous
              </button>

              <div class="page-numbers">
                <button
                  v-for="page in visiblePages"
                  :key="page"
                  @click="loadNews(page)"
                  :class="['page-btn', { active: page === news.current_page }]"
                >
                  {{ page }}
                </button>
              </div>

              <button
                @click="loadNews(news.current_page + 1)"
                :disabled="news.current_page === news.last_page"
                class="btn btn-secondary"
              >
                Next
                <i class="fas fa-chevron-right"></i>
              </button>
            </div>
          </main>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import debounce from 'lodash/debounce'

export default {
  name: 'NewsPage',
  setup() {
    const route = useRoute()
    const router = useRouter()
    const news = ref({ data: [], current_page: 1, last_page: 1, total: 0, from: 0, to: 0 })
    const featuredNews = ref([])
    const categories = ref([])
    const selectedCategory = ref('')
    const loading = ref(false)
    const searchQuery = ref('')

    // Initialize selectedCategory from URL parameter
    const initializeFromRoute = () => {
      const categoryFromUrl = route.query.category
      if (categoryFromUrl) {
        selectedCategory.value = categoryFromUrl
      }
    }

    const visiblePages = computed(() => {
      const current = news.value.current_page
      const total = news.value.last_page
      const pages = []

      // Show up to 5 pages around current page
      let start = Math.max(1, current - 2)
      let end = Math.min(total, current + 2)

      for (let i = start; i <= end; i++) {
        pages.push(i)
      }

      return pages
    })

    const loadNews = async (page = 1) => {
      try {
        loading.value = true

        let url = `/api/news?page=${page}`

        if (selectedCategory.value) {
          url += `&category=${encodeURIComponent(selectedCategory.value)}`
        }

        if (searchQuery.value) {
          url += `&search=${encodeURIComponent(searchQuery.value)}`
        }

        const response = await axios.get(url)

        if (response.data.success) {
          news.value = response.data.data || { data: [], current_page: 1, last_page: 1, total: 0, from: 0, to: 0 }

          // Update meta data if available
          if (response.data.meta) {
            if (response.data.meta.categories) {
              categories.value = response.data.meta.categories
            }
          }
        } else {
          throw new Error(response.data.message || 'Failed to load news')
        }
      } catch (error) {
        console.error('Failed to load news:', error)
        news.value = { data: [], current_page: 1, last_page: 1, total: 0, from: 0, to: 0 }
      } finally {
        loading.value = false
      }
    }

    const loadFeaturedNews = async () => {
      try {
        const response = await axios.get('/api/news/featured')

        if (response.data.success) {
          featuredNews.value = response.data.data || []
        } else {
          throw new Error('Failed to load featured news')
        }
      } catch (error) {
        console.error('Failed to load featured news:', error)
        featuredNews.value = []
      }
    }

    const loadCategories = async () => {
      try {
        const response = await axios.get('/api/news/categories')

        if (response.data.success) {
          categories.value = response.data.data || {}
        } else {
          throw new Error('Failed to load categories')
        }
      } catch (error) {
        console.error('Failed to load categories:', error)
        categories.value = {}
      }
    }

    const formatDate = (dateString) => {
      if (!dateString) return ''
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }

    const getCategoryDisplayName = (categoryKey) => {
      return categories.value[categoryKey] || categoryKey
    }

    const getCategoryIcon = (categoryKey) => {
      // Return a specific icon class based on the category key
      const icons = {
        technology: 'fas fa-laptop-code',
        health: 'fas fa-heartbeat',
        sports: 'fas fa-futbol',
        entertainment: 'fas fa-film',
        business: 'fas fa-briefcase',
        science: 'fas fa-flask',
        politics: 'fas fa-gavel',
        default: 'fas fa-newspaper'
      }

      return icons[categoryKey] || icons.default
    }

    const setCategory = (key) => {
      selectedCategory.value = key
      loadNews(1) // Reset to first page when category changes
    }

    const clearSearch = () => {
      searchQuery.value = ''
      loadNews(1)
    }

    // Debounced search function
    const debounceSearch = debounce(() => {
      loadNews(1)
    }, 300)

    // Watch for route changes to handle direct URL navigation
    watch(() => route.query, (newQuery) => {
      if (newQuery.category !== selectedCategory.value) {
        selectedCategory.value = newQuery.category || ''
      }
      if (newQuery.search !== searchQuery.value) {
        searchQuery.value = newQuery.search || ''
      }
      if (newQuery.page) {
        loadNews(parseInt(newQuery.page) || 1)
      }
    }, { immediate: true })

    // Watch for filter changes to update URL
    watch([selectedCategory, searchQuery], () => {
      const query = { ...route.query }

      if (selectedCategory.value) {
        query.category = selectedCategory.value
      } else {
        delete query.category
      }

      if (searchQuery.value) {
        query.search = searchQuery.value
      } else {
        delete query.search
      }

      // Reset page to 1 when filters change
      delete query.page

      // Update URL without causing navigation
      router.replace({ query })
    })

    onMounted(async () => {
      // Используем setPageTitle вместо прямого обращения к document.title
      try {
        const { setPageTitle } = await import('../composables/usePageTitle.js')
        setPageTitle('News & Updates')
      } catch (error) {
        console.warn('Could not load usePageTitle composable:', error)
      }

      loadCategories().then(() => {
        initializeFromRoute()
        loadNews()
      })
      loadFeaturedNews()
    })

    return {
      news,
      featuredNews,
      categories,
      selectedCategory,
      loading,
      searchQuery,
      visiblePages,
      loadNews,
      formatDate,
      getCategoryDisplayName,
      getCategoryIcon,
      setCategory,
      clearSearch,
      debounceSearch
    }
  }
}
</script>

<style lang="scss" scoped>
.news-page {
  min-height: 100vh;
  background: #f8fafc;
}

.page-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 6rem 0 4rem;
  text-align: center;
  position: relative;
  overflow: hidden;

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" patternUnits="userSpaceOnUse" width="100" height="100"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
  }

  .header-content {
    position: relative;
    z-index: 2;
    max-width: 800px;
    margin: 0 auto;

    h1 {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    p {
      font-size: 1.25rem;
      opacity: 0.9;
      margin-bottom: 3rem;
      line-height: 1.6;
    }
  }
}

.search-bar {
  max-width: 500px;
  margin: 0 auto;
}

.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;

  .search-icon {
    position: absolute;
    left: 1.5rem;
    color: #6c757d;
    z-index: 2;
  }

  .search-input {
    width: 100%;
    padding: 1rem 1rem 1rem 3.5rem;
    border: none;
    border-radius: 50px;
    font-size: 1rem;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;

    &:focus {
      outline: none;
      background: white;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
      transform: translateY(-2px);
    }

    &::placeholder {
      color: #6c757d;
    }
  }

  .clear-search {
    position: absolute;
    right: 1rem;
    background: #e9ecef;
    border: none;
    border-radius: 50%;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.2s ease;

    &:hover {
      background: #dee2e6;
      color: #495057;
    }
  }
}

.news-content {
  padding: 4rem 0;
}

.news-layout {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 3rem;
  align-items: start;
}

.news-sidebar {
  position: sticky;
  top: 2rem;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.filter-card {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);

  h3 {
    margin-bottom: 1.5rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
}

.category-filters {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.category-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  background: #f8f9fa;
  border: none;
  border-radius: 8px;
  color: #2c3e50;
  text-decoration: none;
  transition: all 0.2s ease;
  cursor: pointer;
  font-size: 0.9rem;
  width: 100%;
  text-align: left;

  i {
    width: 16px;
    text-align: center;
  }

  &:hover {
    background: #e9ecef;
    color: #667eea;
    transform: translateX(4px);
  }

  &.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
  }
}

.featured-sidebar {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);

  h3 {
    margin-bottom: 1.5rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
  }
}

.featured-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.featured-item {
  display: flex;
  gap: 1rem;
}

.featured-image {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .no-image {
    width: 100%;
    height: 100%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
  }
}

.featured-content {
  flex: 1;
  min-width: 0;

  h4 {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    line-height: 1.4;

    a {
      color: #2c3e50;
      text-decoration: none;

      &:hover {
        color: #667eea;
      }
    }
  }

  .featured-date {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0;
  }
}

.news-main {
  min-height: 60vh;
}

.news-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding: 1rem 2rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);

  .results-info {
    color: #6c757d;
    font-size: 0.9rem;
  }
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 400px;
  text-align: center;

  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
  }

  p {
    color: #6c757d;
  }
}

.news-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.news-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;

  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
  }
}

.news-image {
  position: relative;
  height: 200px;
  overflow: hidden;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .no-image {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 2rem;
  }

  .news-category {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    color: #2c3e50;
  }

  .featured-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: linear-gradient(135deg, #f093fb, #f5576c);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }
}

.news-card:hover .news-image img {
  transform: scale(1.05);
}

.news-content {
  padding: 1.5rem;
}

.news-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1rem;
  font-size: 0.8rem;
  color: #6c757d;

  span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }
}

.news-title {
  margin-bottom: 1rem;
  font-size: 1.25rem;
  line-height: 1.4;

  a {
    color: #2c3e50;
    text-decoration: none;
    font-weight: 600;

    &:hover {
      color: #667eea;
    }
  }
}

.news-excerpt {
  color: #6c757d;
  line-height: 1.6;
  margin-bottom: 1.5rem;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  line-clamp: 3;
  overflow: hidden;
}

.news-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.news-tags {
  display: flex;
  gap: 0.5rem;

  .tag {
    background: #f8f9fa;
    color: #6c757d;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
  }
}

.read-more {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.9rem;
  transition: all 0.2s ease;

  &:hover {
    color: #764ba2;
    transform: translateX(4px);
  }
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 400px;
  text-align: center;
  background: white;
  border-radius: 16px;
  padding: 3rem;

  .empty-icon {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 2rem;
  }

  h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: #2c3e50;
  }

  p {
    color: #6c757d;
    margin-bottom: 2rem;
  }
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-top: 3rem;
  padding: 2rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.page-numbers {
  display: flex;
  gap: 0.5rem;
}

.page-btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 8px;
  background: #f8f9fa;
  color: #2c3e50;
  cursor: pointer;
  transition: all 0.2s ease;
  font-weight: 500;

  &:hover {
    background: #e9ecef;
    color: #667eea;
  }

  &.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
  }
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s ease;

  &.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;

    &:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
  }

  &.btn-secondary {
    background: #f8f9fa;
    color: #2c3e50;

    &:hover:not(:disabled) {
      background: #e9ecef;
      color: #667eea;
    }

    &:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }
  }

  &.btn-outline {
    background: transparent;
    color: #667eea;
    border: 2px solid #667eea;

    &:hover {
      background: #667eea;
      color: white;
    }
  }

  &.btn-full {
    width: 100%;
    justify-content: center;
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

// Mobile responsive
@media (max-width: 1200px) {
  .news-layout {
    grid-template-columns: 280px 1fr;
    gap: 2rem;
  }
}

@media (max-width: 768px) {
  .page-header {
    padding: 4rem 0 3rem;

    .header-content {
      h1 {
        font-size: 2.5rem;
      }

      p {
        font-size: 1.1rem;
      }
    }
  }

  .news-layout {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .news-sidebar {
    position: static;
    order: 2;
  }

  .news-main {
    order: 1;
  }

  .news-grid {
    grid-template-columns: 1fr;
  }

  .filter-card {
    padding: 1.5rem;
  }

  .pagination {
    flex-direction: column;
    gap: 1rem;

    .page-numbers {
      flex-wrap: wrap;
      justify-content: center;
    }
  }

  .news-controls {
    padding: 1rem;
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
}

@media (max-width: 480px) {
  .news-content {
    padding: 2rem 0;
  }

  .page-header {
    padding: 3rem 0 2rem;

    .header-content h1 {
      font-size: 2rem;
    }
  }

  .search-input-wrapper .search-input {
    padding: 0.75rem 0.75rem 0.75rem 3rem;
  }

  .filter-card {
    padding: 1rem;
  }

  .category-btn {
    padding: 0.5rem;
    font-size: 0.8rem;
  }
}
</style>
