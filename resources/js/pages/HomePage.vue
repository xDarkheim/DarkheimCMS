<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container">
        <div class="hero-content">
          <div class="hero-left">
            <div class="intro-badge">
              <i class="fas fa-code"></i>
              <span>Development Studio</span>
            </div>
            <h1 class="hero-title">
              Darkheim<br>
              <span class="highlight">Development Studio</span>
            </h1>
            <p class="hero-subtitle">
              We specialize in creating professional websites for small businesses.
              From simple landing pages to complex e-commerce systems - we build
              digital solutions that help your business grow.
            </p>
            <div class="hero-actions">
              <router-link to="/contact" class="btn btn-primary btn-large">
                <i class="fas fa-rocket"></i>
                Start Project
              </router-link>
              <router-link to="/portfolio" class="btn btn-secondary btn-large">
                <i class="fas fa-eye"></i>
                View Portfolio
              </router-link>
            </div>

            <!-- Quick Stats -->
            <div class="hero-stats">
              <div class="stat-item">
                <div class="stat-number">5</div>
                <div class="stat-label">Completed Projects</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">Client Satisfaction</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">10</div>
                <div class="stat-label">Months Experience</div>
              </div>
            </div>
          </div>

          <div class="hero-right">
            <div class="hero-image">
              <div class="code-preview">
                <div class="code-header">
                  <span class="dot red"></span>
                  <span class="dot yellow"></span>
                  <span class="dot green"></span>
                  <span class="filename">index.php</span>
                </div>
                <div class="code-content">
                  <div class="code-line"><span class="purple">&lt;?php</span></div>
                  <div class="code-line"></div>
                  <div class="code-line"><span class="blue">namespace</span> <span class="green">App\Http\Controllers</span>;</div>
                  <div class="code-line"></div>
                  <div class="code-line"><span class="blue">use</span> <span class="green">Illuminate\Http\Request</span>;</div>
                  <div class="code-line"></div>
                  <div class="code-line"><span class="blue">class</span> <span class="yellow">PortfolioController</span> <span class="blue">extends</span> <span class="yellow">Controller</span></div>
                  <div class="code-line">{</div>
                  <div class="code-line">&nbsp;&nbsp;<span class="blue">public function</span> <span class="yellow">index</span>()</div>
                  <div class="code-line">&nbsp;&nbsp;{</div>
                  <div class="code-line">&nbsp;&nbsp;&nbsp;&nbsp;<span class="purple">$projects</span> = <span class="yellow">Portfolio</span>::<span class="yellow">all</span>();</div>
                  <div class="code-line"></div>
                  <div class="code-line">&nbsp;&nbsp;&nbsp;&nbsp;<span class="blue">return</span> <span class="yellow">view</span>(<span class="green">'portfolio'</span>, <span class="yellow">compact</span>(<span class="green">'projects'</span>));</div>
                  <div class="code-line">&nbsp;&nbsp;}</div>
                  <div class="code-line">}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- News Section -->
    <section class="news-section">
      <div class="container">
        <div class="section-header">
          <h2>Latest News & Updates</h2>
          <p>Stay up to date with our latest projects, technologies, and studio news</p>
        </div>

        <div v-if="newsLoading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading latest news...</p>
        </div>

        <div v-else-if="featuredNews.length > 0">
          <!-- Featured News -->
          <div class="featured-news" v-if="featuredNews[0]">
            <article class="news-card featured">
              <div class="news-image">
                <img
                  v-if="featuredNews[0].image_url"
                  :src="featuredNews[0].image_url"
                  :alt="featuredNews[0].title"
                  loading="lazy"
                >
                <div v-else class="no-image">
                  <i class="fas fa-newspaper"></i>
                </div>
                <div class="news-category">{{ getCategoryDisplayName(featuredNews[0].category) }}</div>
              </div>
              <div class="news-content">
                <div class="news-meta">
                  <span class="news-date">
                    <i class="fas fa-calendar"></i>
                    {{ formatDate(featuredNews[0].published_at) }}
                  </span>
                  <span class="news-author">
                    <i class="fas fa-user"></i>
                    {{ featuredNews[0].author }}
                  </span>
                </div>
                <h3 class="news-title">
                  <router-link :to="`/news/${featuredNews[0].slug}`" class="news-link">
                    {{ featuredNews[0].title }}
                  </router-link>
                </h3>
                <p class="news-excerpt">
                  {{ featuredNews[0].excerpt || featuredNews[0].content?.substring(0, 200) + '...' }}
                </p>
                <div class="news-tags">
                  <span class="tag">{{ getCategoryDisplayName(featuredNews[0].category) }}</span>
                  <span v-if="featuredNews[0].views" class="tag">{{ featuredNews[0].views }} views</span>
                </div>
              </div>
            </article>
          </div>

          <!-- News Grid -->
          <div class="news-grid" v-if="featuredNews.length > 1">
            <article
              v-for="article in featuredNews.slice(1, 7)"
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
              </div>
              <div class="news-content">
                <div class="news-meta">
                  <span class="news-date">
                    <i class="fas fa-calendar"></i>
                    {{ formatDate(article.published_at) }}
                  </span>
                </div>
                <h3 class="news-title">
                  <router-link :to="`/news/${article.slug}`" class="news-link">
                    {{ article.title }}
                  </router-link>
                </h3>
                <p class="news-excerpt">
                  {{ article.excerpt || article.content?.substring(0, 150) + '...' }}
                </p>
                <div class="news-tags">
                  <span class="tag">{{ getCategoryDisplayName(article.category) }}</span>
                </div>
              </div>
            </article>
          </div>
        </div>

        <div v-else class="empty-state">
          <div class="empty-icon">
            <i class="fas fa-newspaper"></i>
          </div>
          <h3>No News Available</h3>
          <p>Check back soon for our latest updates and announcements.</p>
        </div>

        <!-- View All News Button -->
        <div class="text-center news-load-more">
          <router-link to="/news" class="btn btn-outline btn-large">
            <i class="fas fa-newspaper"></i>
            View All News
          </router-link>
        </div>
      </div>
    </section>

    <!-- Featured Projects Section -->
    <section class="featured-projects-section">
      <div class="container">
        <div class="section-header">
          <h2>Featured Projects</h2>
          <p>Showcase of our most impressive and successful projects</p>
        </div>

        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading featured projects...</p>
        </div>

        <div v-else-if="featuredProjects.length > 0" class="projects-grid">
          <div
            v-for="project in featuredProjects"
            :key="project.id"
            class="project-card"
          >
            <div class="project-image">
              <img
                v-if="project.image_url"
                :src="project.image_url"
                :alt="project.title"
                loading="lazy"
              >
              <div v-else class="no-image">
                <i class="fas fa-image"></i>
              </div>
              <div class="project-overlay">
                <div class="project-actions">
                  <a
                    v-if="project.project_url"
                    :href="project.project_url"
                    target="_blank"
                    class="btn btn-primary btn-sm"
                  >
                    <i class="fas fa-external-link-alt"></i>
                    Live Demo
                  </a>
                  <a
                    v-if="project.github_url"
                    :href="project.github_url"
                    target="_blank"
                    class="btn btn-secondary btn-sm"
                  >
                    <i class="fab fa-github"></i>
                    Code
                  </a>
                </div>
              </div>
              <div class="featured-badge">
                <i class="fas fa-star"></i>
                Featured
              </div>
            </div>

            <div class="project-content">
              <div class="project-header">
                <h3 class="project-title">{{ project.title }}</h3>
                <div class="project-category">{{ project.category }}</div>
              </div>

              <p class="project-description">
                {{ project.short_description || project.description?.substring(0, 120) + '...' }}
              </p>

              <div v-if="project.technologies && project.technologies.length" class="project-tech">
                <span
                  v-for="tech in project.technologies.slice(0, 4)"
                  :key="tech"
                  class="tech-tag"
                >
                  {{ tech }}
                </span>
                <span v-if="project.technologies.length > 4" class="tech-more">
                  +{{ project.technologies.length - 4 }}
                </span>
              </div>

              <div class="project-meta">
                <div v-if="project.client" class="meta-item">
                  <i class="fas fa-user"></i>
                  <span>{{ project.client }}</span>
                </div>
                <div v-if="project.completed_at" class="meta-item">
                  <i class="fas fa-calendar"></i>
                  <span>{{ formatDate(project.completed_at) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="empty-state">
          <div class="empty-icon">
            <i class="fas fa-folder-open"></i>
          </div>
          <h3>No Featured Projects Yet</h3>
          <p>Check back soon for our highlighted work and successful projects.</p>
        </div>

        <!-- View All Projects Button -->
        <div class="text-center projects-cta">
          <router-link to="/portfolio" class="btn btn-outline btn-large">
            <i class="fas fa-folder-open"></i>
            View All Projects
          </router-link>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-content">
          <h2>Ready to Build Something Great?</h2>
          <p>Let's discuss your project and create a website that helps your business succeed online.</p>
          <router-link to="/contact" class="btn btn-primary btn-xl">
            <i class="fas fa-paper-plane"></i>
            Get Started Today
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'HomePage',
  setup() {
    const featuredProjects = ref([])
    const featuredNews = ref([])
    const categories = ref({})
    const loading = ref(false)
    const newsLoading = ref(false)

    const loadFeaturedProjects = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/portfolios/featured')
        // API возвращает {success: true, data: [projects]}, поэтому берем data
        featuredProjects.value = response.data.data || response.data
      } catch (error) {
        console.error('Failed to load featured projects:', error)
        featuredProjects.value = []
      } finally {
        loading.value = false
      }
    }

    const loadFeaturedNews = async () => {
      try {
        newsLoading.value = true
        const response = await axios.get('/api/news/featured')
        featuredNews.value = response.data.data || response.data
      } catch (error) {
        console.error('Failed to load featured news:', error)
        featuredNews.value = []
      } finally {
        newsLoading.value = false
      }
    }

    const loadCategories = async () => {
      try {
        const response = await axios.get('/api/news/all-categories')
        categories.value = response.data.data || response.data
        console.log('Loaded categories for HomePage:', categories.value)
      } catch (error) {
        console.error('Failed to load categories:', error)
        categories.value = {}
      }
    }

    const getCategoryDisplayName = (categoryKey) => {
      return categories.value[categoryKey] || categoryKey
    }

    const formatDate = (dateString) => {
      if (!dateString) return ''
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }

    const loadMoreNews = () => {
      // Заглушка для загрузки новостей
      console.log('Loading more news...')
    }

    onMounted(() => {
      loadFeaturedProjects()
      loadFeaturedNews()
      loadCategories()
    })

    return {
      featuredProjects,
      featuredNews,
      categories,
      loading,
      newsLoading,
      formatDate,
      loadMoreNews,
      getCategoryDisplayName
    }
  }
}
</script>

<style lang="scss" scoped>
@use '../../css/pages/home-page';
</style>
