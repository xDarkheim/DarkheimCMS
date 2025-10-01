<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-background">
        <div class="hero-particles"></div>
        <div class="hero-gradient"></div>
      </div>

      <div class="container">
        <div class="hero-content">
          <div class="hero-left">
            <h1 class="hero-title">
              <span class="title-line main">Darkheim</span>
              <span class="title-line highlight">Development Studio</span>
            </h1>

            <p class="hero-subtitle">
              We specialize in creating <strong>professional websites</strong> for small businesses.
              From simple landing pages to complex e-commerce systems - we build
              <em>digital solutions</em> that help your business grow online.
            </p>

            <div class="hero-features centered">
              <div class="feature-item">
                <i class="fas fa-rocket"></i>
                <span>Fast Delivery</span>
              </div>
              <div class="feature-item">
                <i class="fas fa-mobile-alt"></i>
                <span>Mobile First</span>
              </div>
              <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                <span>Secure & Reliable</span>
              </div>
            </div>

            <div class="hero-actions centered">
              <router-link to="/contact" class="btn btn-primary btn-hero">
                <span class="btn-content">
                  <i class="fas fa-rocket"></i>
                  Start Your Project
                </span>
                <div class="btn-glow"></div>
              </router-link>

              <router-link to="/portfolio" class="btn btn-secondary btn-hero">
                <span class="btn-content">
                  <i class="fas fa-eye"></i>
                  View Portfolio
                </span>
              </router-link>
            </div>

            <!-- Enhanced Stats -->
            <div class="hero-stats">
              <div class="stat-item">
                <div class="stat-icon">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                  <div class="stat-number">{{ stats.total_projects || 0 }}+</div>
                  <div class="stat-label">Completed Projects</div>
                </div>
              </div>

              <div class="stat-item">
                <div class="stat-icon">
                  <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                  <div class="stat-number">{{ stats.team_members || 0 }}</div>
                  <div class="stat-label">Team Members</div>
                </div>
              </div>

              <div class="stat-item">
                <div class="stat-icon">
                  <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-content">
                  <div class="stat-number">{{ stats.open_positions || 0 }}</div>
                  <div class="stat-label">Open Positions</div>
                </div>
              </div>

              <div class="stat-item">
                <div class="stat-icon">
                  <i class="fas fa-calendar"></i>
                </div>
                <div class="stat-content">
                  <div class="stat-number">{{ stats.years_experience || 8 }}+</div>
                  <div class="stat-label">Years Experience</div>
                </div>
              </div>
            </div>

            <!-- Trust Indicators -->
            <div class="hero-trust">
              <p class="trust-text">Trusted by local businesses</p>
              <div class="trust-logos">
                <div class="trust-item">
                  <i class="fas fa-store"></i>
                  <span>Retail</span>
                </div>
                <div class="trust-item">
                  <i class="fas fa-utensils"></i>
                  <span>Restaurants</span>
                </div>
                <div class="trust-item">
                  <i class="fas fa-briefcase"></i>
                  <span>Services</span>
                </div>
                <div class="trust-item">
                  <i class="fas fa-home"></i>
                  <span>Real Estate</span>
                </div>
              </div>
            </div>
          </div>

          <div class="hero-right">
            <div class="hero-visual">
              <!-- Enhanced Code Preview -->
              <div class="code-window">
                <div class="code-header">
                  <div class="window-controls">
                    <span class="dot red"></span>
                    <span class="dot yellow"></span>
                    <span class="dot green"></span>
                  </div>
                  <div class="window-title">
                    <i class="fab fa-php"></i>
                    <span class="filename">darkheim-controller.php</span>
                  </div>
                  <div class="window-actions">
                    <i class="fas fa-search"></i>
                    <i class="fas fa-cog"></i>
                  </div>
                </div>

                <div class="code-content">
                  <div class="line-numbers">
                    <span>1</span>
                    <span>2</span>
                    <span>3</span>
                    <span>4</span>
                    <span>5</span>
                    <span>6</span>
                    <span>7</span>
                    <span>8</span>
                    <span>9</span>
                    <span>10</span>
                    <span>11</span>
                    <span>12</span>
                    <span>13</span>
                  </div>

                  <div class="code-text">
                    <div class="code-line"><span class="keyword">&lt;?php</span></div>
                    <div class="code-line"></div>
                    <div class="code-line"><span class="keyword">namespace</span> <span class="string">App\Http\Controllers</span>;</div>
                    <div class="code-line"></div>
                    <div class="code-line"><span class="keyword">use</span> <span class="string">Illuminate\Http\Request</span>;</div>
                    <div class="code-line"><span class="keyword">use</span> <span class="string">App\Models\Darkheim</span>;</div>
                    <div class="code-line"></div>
                    <div class="code-line"><span class="keyword">class</span> <span class="function">DarkheimController</span> <span class="keyword">extends</span> <span class="function">Controller</span></div>
                    <div class="code-line">{</div>
                    <div class="code-line">&nbsp;&nbsp;<span class="keyword">public function</span> <span class="function">index</span>()</div>
                    <div class="code-line">&nbsp;&nbsp;{</div>
                    <div class="code-line">&nbsp;&nbsp;&nbsp;&nbsp;<span class="variable">$projects</span> = <span class="function">Darkheim</span>::<span class="function">published</span>()-><span class="function">get</span>();</div>
                    <div class="code-line">&nbsp;&nbsp;&nbsp;&nbsp;<span class="keyword">return</span> <span class="function">response</span>()-><span class="function">json</span>(<span class="variable">$projects</span>);</div>
                    <div class="code-line">&nbsp;&nbsp;}</div>
                    <div class="code-line">}<span class="cursor">|</span></div>
                  </div>
                </div>
              </div>

              <!-- Achievement Badges -->
              <div class="achievement-badges">
                <div class="achievement-badge" style="--delay: 2s;">
                  <i class="fas fa-trophy"></i>
                  <span>Best Design</span>
                </div>
                <div class="achievement-badge" style="--delay: 2.5s;">
                  <i class="fas fa-bolt"></i>
                  <span>Fast Loading</span>
                </div>
                <div class="achievement-badge" style="--delay: 3s;">
                  <i class="fas fa-mobile-alt"></i>
                  <span>Mobile Ready</span>
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
                <div class="project-category">{{ project.category_name || project.category || 'Uncategorized' }}</div>
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
    const stats = ref({})

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

    const loadStats = async () => {
      try {
        const response = await axios.get('/api/stats')
        stats.value = response.data.data || response.data
        console.log('Loaded stats for HomePage:', stats.value)
      } catch (error) {
        console.error('Failed to load stats:', error)
        stats.value = {}
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
      loadStats()
    })

    return {
      featuredProjects,
      featuredNews,
      categories,
      loading,
      newsLoading,
      stats,
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
