<template>
  <div class="news-article-page">
    <div v-if="loading" class="loading-state">
      <div class="loading-spinner"></div>
      <p>Loading article...</p>
    </div>

    <div v-else-if="article" class="article-content">
      <!-- Article Header -->
      <section class="article-header">
        <div class="container">
          <div class="breadcrumb">
            <router-link to="/">Home</router-link>
            <span class="separator">/</span>
            <router-link to="/news">News</router-link>
            <span class="separator">/</span>
            <span class="current">{{ article.title }}</span>
          </div>

          <div class="article-hero">
            <div class="article-meta">
              <div class="meta-item">
                <i class="fas fa-calendar"></i>
                <span>{{ formatDate(article.published_at) }}</span>
              </div>
              <div class="meta-item">
                <i class="fas fa-user"></i>
                <span>{{ article.author }}</span>
              </div>
              <div class="meta-item">
                <i class="fas fa-folder"></i>
                <span>{{ getCategoryDisplayName(article.category) }}</span>
              </div>
              <div v-if="article.views" class="meta-item">
                <i class="fas fa-eye"></i>
                <span>{{ article.views }} views</span>
              </div>
            </div>

            <h1 class="article-title">{{ article.title }}</h1>

            <p v-if="article.excerpt" class="article-excerpt">
              {{ article.excerpt }}
            </p>

            <div v-if="article.is_featured" class="featured-badge">
              <i class="fas fa-star"></i>
              Featured Article
            </div>
          </div>
        </div>
      </section>

      <!-- Article Image -->
      <section v-if="article.image_url" class="article-image-section">
        <div class="container">
          <div class="article-image">
            <img :src="article.image_url" :alt="article.title">
          </div>
        </div>
      </section>

      <!-- Article Content -->
      <section class="article-main">
        <div class="container">
          <div class="article-layout">
            <main class="article-body">
              <div class="content-wrapper">
                <div class="article-text" v-html="formatContent(article.content)"></div>

                <!-- Share Section -->
                <div class="article-share">
                  <h4>Share this article</h4>
                  <div class="share-buttons">
                    <button @click="shareOnTwitter" class="share-btn twitter">
                      <i class="fab fa-twitter"></i>
                      Twitter
                    </button>
                    <button @click="shareOnFacebook" class="share-btn facebook">
                      <i class="fab fa-facebook-f"></i>
                      Facebook
                    </button>
                    <button @click="shareOnLinkedIn" class="share-btn linkedin">
                      <i class="fab fa-linkedin-in"></i>
                      LinkedIn
                    </button>
                    <button @click="copyLink" class="share-btn copy">
                      <i class="fas fa-link"></i>
                      Copy Link
                    </button>
                  </div>
                </div>

                <!-- Article Tags -->
                <div class="article-tags">
                  <h4>Tags</h4>
                  <div class="tags-list">
                    <span class="tag">{{ getCategoryDisplayName(article.category) }}</span>
                    <span v-if="article.is_featured" class="tag featured">Featured</span>
                  </div>
                </div>
              </div>
            </main>

            <!-- Sidebar -->
            <aside class="article-sidebar">
              <!-- Related Articles -->
              <div class="sidebar-card">
                <h3>Related Articles</h3>
                <div v-if="relatedArticles.length > 0" class="related-list">
                  <article
                    v-for="related in relatedArticles"
                    :key="related.id"
                    class="related-item"
                  >
                    <div class="related-image">
                      <img
                        v-if="related.image_url"
                        :src="related.image_url"
                        :alt="related.title"
                      >
                      <div v-else class="no-image">
                        <i class="fas fa-newspaper"></i>
                      </div>
                    </div>
                    <div class="related-content">
                      <h4>
                        <router-link :to="`/news/${related.slug}`">
                          {{ related.title }}
                        </router-link>
                      </h4>
                      <p class="related-date">{{ formatDate(related.published_at) }}</p>
                    </div>
                  </article>
                </div>
                <p v-else class="no-related">No related articles found.</p>
              </div>

              <!-- Categories -->
              <div class="sidebar-card">
                <h3>Categories</h3>
                <div class="categories-list">
                  <router-link
                    v-for="(displayName, key) in categories"
                    :key="key"
                    :to="`/news?category=${encodeURIComponent(key)}`"
                    class="category-link"
                  >
                    {{ displayName }}
                  </router-link>
                </div>
              </div>

              <!-- Back to News -->
              <div class="sidebar-card">
                <router-link to="/news" class="btn btn-outline btn-full">
                  <i class="fas fa-arrow-left"></i>
                  Back to All News
                </router-link>
              </div>
            </aside>
          </div>
        </div>
      </section>

      <!-- Navigation -->
      <section class="article-navigation">
        <div class="container">
          <div class="nav-buttons">
            <router-link
              v-if="previousArticle"
              :to="`/news/${previousArticle.slug}`"
              class="nav-btn prev"
            >
              <i class="fas fa-chevron-left"></i>
              <div class="nav-content">
                <span class="nav-label">Previous Article</span>
                <span class="nav-title">{{ previousArticle.title }}</span>
              </div>
            </router-link>

            <router-link
              v-if="nextArticle"
              :to="`/news/${nextArticle.slug}`"
              class="nav-btn next"
            >
              <div class="nav-content">
                <span class="nav-label">Next Article</span>
                <span class="nav-title">{{ nextArticle.title }}</span>
              </div>
              <i class="fas fa-chevron-right"></i>
            </router-link>
          </div>
        </div>
      </section>
    </div>

    <!-- Article Not Found -->
    <div v-else class="not-found">
      <div class="container">
        <div class="not-found-content">
          <div class="not-found-icon">
            <i class="fas fa-search"></i>
          </div>
          <h1>Article Not Found</h1>
          <p>The article you're looking for doesn't exist or has been removed.</p>
          <router-link to="/news" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i>
            Back to News
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

export default {
  name: 'NewsArticle',
  setup() {
    const route = useRoute()
    const article = ref(null)
    const relatedArticles = ref([])
    const categories = ref({})
    const previousArticle = ref(null)
    const nextArticle = ref(null)
    const loading = ref(false)

    const loadArticle = async (slug) => {
      try {
        loading.value = true
        const response = await axios.get(`/api/news/${slug}`)
        article.value = response.data.data

        // Load related content after getting the article
        loadRelatedArticles()
        loadNavigation()
      } catch (error) {
        console.error('Failed to load article:', error)
        article.value = null
      } finally {
        loading.value = false
      }
    }

    const loadRelatedArticles = async () => {
      if (!article.value) return

      try {
        const response = await axios.get(`/api/news?category=${encodeURIComponent(article.value.category)}&per_page=4`)
        // Filter out current article
        relatedArticles.value = (response.data.data || response.data)
          .filter(item => item.id !== article.value.id)
          .slice(0, 3)
      } catch (error) {
        console.error('Failed to load related articles:', error)
        relatedArticles.value = []
      }
    }

    const loadNavigation = async () => {
      // This would ideally be implemented with proper prev/next endpoints
      // For now, we'll skip this functionality
      previousArticle.value = null
      nextArticle.value = null
    }

    const loadCategories = async () => {
      try {
        const response = await axios.get('/api/news/all-categories')
        categories.value = response.data.data || response.data
      } catch (error) {
        console.error('Failed to load categories:', error)
        categories.value = {}
      }
    }

    const getCategoryDisplayName = (categoryKey) => {
      return categories.value[categoryKey] || categoryKey
    }

    const formatContent = (content) => {
      if (!content) return ''

      // Convert line breaks to paragraphs
      return content
        .split('\n\n')
        .map(paragraph => paragraph.trim())
        .filter(paragraph => paragraph.length > 0)
        .map(paragraph => `<p>${paragraph.replace(/\n/g, '<br>')}</p>`)
        .join('')
    }

    const formatDate = (dateString) => {
      if (!dateString) return ''
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const shareOnTwitter = () => {
      const url = encodeURIComponent(window.location.href)
      const text = encodeURIComponent(article.value.title)
      window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank')
    }

    const shareOnFacebook = () => {
      const url = encodeURIComponent(window.location.href)
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank')
    }

    const shareOnLinkedIn = () => {
      const url = encodeURIComponent(window.location.href)
      const title = encodeURIComponent(article.value.title)
      window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}&title=${title}`, '_blank')
    }

    const copyLink = async () => {
      try {
        await navigator.clipboard.writeText(window.location.href)
        alert('Link copied to clipboard!')
      } catch (error) {
        console.error('Failed to copy link:', error)
      }
    }

    // Watch for route changes
    watch(() => route.params.slug, (newSlug) => {
      if (newSlug) {
        loadArticle(newSlug)
      }
    })

    onMounted(() => {
      loadArticle(route.params.slug)
      loadCategories()
    })

    return {
      article,
      relatedArticles,
      categories,
      previousArticle,
      nextArticle,
      loading,
      formatContent,
      formatDate,
      shareOnTwitter,
      shareOnFacebook,
      shareOnLinkedIn,
      copyLink,
      getCategoryDisplayName
    }
  }
}
</script>

<style lang="scss" scoped>
.news-article-page {
  min-height: 100vh;
  background: #f8fafc;
}

.article-header {
  background: white;
  padding: 2rem 0 3rem;
  border-bottom: 1px solid #e9ecef;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 2rem;
  font-size: 0.9rem;

  a {
    color: #667eea;
    text-decoration: none;

    &:hover {
      color: #764ba2;
    }
  }

  .separator {
    color: #6c757d;
  }

  .current {
    color: #6c757d;
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
}

.article-hero {
  max-width: 800px;
}

.article-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  margin-bottom: 2rem;
  font-size: 0.9rem;
  color: #6c757d;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.article-title {
  font-size: 3rem;
  font-weight: 700;
  line-height: 1.2;
  color: #2c3e50;
  margin-bottom: 1.5rem;
}

.article-excerpt {
  font-size: 1.25rem;
  color: #6c757d;
  line-height: 1.6;
  margin-bottom: 2rem;
}

.featured-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #f093fb, #f5576c);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 25px;
  font-weight: 500;
  font-size: 0.9rem;
}

.article-image-section {
  padding: 2rem 0;
}

.article-image {
  max-width: 1000px;
  margin: 0 auto;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);

  img {
    width: 100%;
    height: auto;
    display: block;
  }
}

.article-main {
  padding: 3rem 0;
}

.article-layout {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 3rem;
  align-items: start;
}

.article-body {
  background: white;
  border-radius: 16px;
  padding: 3rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.article-text {
  font-size: 1.1rem;
  line-height: 1.8;
  color: #2c3e50;
  margin-bottom: 3rem;

  :deep(p) {
    margin-bottom: 1.5rem;

    &:last-child {
      margin-bottom: 0;
    }
  }
}

.article-share {
  padding: 2rem 0;
  border-top: 1px solid #e9ecef;
  border-bottom: 1px solid #e9ecef;
  margin-bottom: 2rem;

  h4 {
    margin-bottom: 1rem;
    font-size: 1.1rem;
    color: #2c3e50;
  }
}

.share-buttons {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.share-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  font-size: 0.9rem;

  &.twitter {
    background: #1da1f2;
    color: white;

    &:hover {
      background: #0d8bd9;
    }
  }

  &.facebook {
    background: #4267b2;
    color: white;

    &:hover {
      background: #365899;
    }
  }

  &.linkedin {
    background: #0077b5;
    color: white;

    &:hover {
      background: #005885;
    }
  }

  &.copy {
    background: #6c757d;
    color: white;

    &:hover {
      background: #545b62;
    }
  }
}

.article-tags {
  h4 {
    margin-bottom: 1rem;
    font-size: 1.1rem;
    color: #2c3e50;
  }
}

.tags-list {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.tag {
  background: #f8f9fa;
  color: #6c757d;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 500;

  &.featured {
    background: linear-gradient(135deg, #f093fb, #f5576c);
    color: white;
  }
}

.article-sidebar {
  position: sticky;
  top: 2rem;
}

.sidebar-card {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  margin-bottom: 2rem;

  h3 {
    margin-bottom: 1.5rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
  }
}

.related-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.related-item {
  display: flex;
  gap: 1rem;
}

.related-image {
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

.related-content {
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
}

.related-date {
  font-size: 0.8rem;
  color: #6c757d;
  margin: 0;
}

.categories-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.category-link {
  padding: 0.75rem 1rem;
  background: #f8f9fa;
  border-radius: 8px;
  color: #2c3e50;
  text-decoration: none;
  transition: all 0.2s ease;

  &:hover {
    background: #e9ecef;
    color: #667eea;
  }
}

.article-navigation {
  padding: 3rem 0;
  background: white;
  border-top: 1px solid #e9ecef;
}

.nav-buttons {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.nav-btn {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 2rem;
  background: #f8f9fa;
  border-radius: 16px;
  text-decoration: none;
  color: #2c3e50;
  transition: all 0.2s ease;

  &:hover {
    background: #e9ecef;
    transform: translateY(-2px);
  }

  &.prev {
    justify-content: flex-start;
  }

  &.next {
    justify-content: flex-end;
    text-align: right;
  }
}

.nav-content {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.nav-label {
  font-size: 0.8rem;
  color: #6c757d;
  text-transform: uppercase;
  font-weight: 600;
}

.nav-title {
  font-weight: 600;
  line-height: 1.4;
}

.loading-state,
.not-found {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 60vh;
  text-align: center;

  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
  }
}

.not-found-content {
  max-width: 400px;

  .not-found-icon {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 2rem;
  }

  h1 {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #2c3e50;
  }

  p {
    color: #6c757d;
    margin-bottom: 2rem;
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
@media (max-width: 768px) {
  .article-layout {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .article-body {
    padding: 2rem;
  }

  .article-title {
    font-size: 2rem;
  }

  .article-excerpt {
    font-size: 1.1rem;
  }

  .nav-buttons {
    grid-template-columns: 1fr;
  }

  .nav-btn {
    &.next {
      text-align: left;
      justify-content: flex-start;
    }
  }

  .share-buttons {
    flex-direction: column;
  }

  .article-meta {
    flex-direction: column;
    gap: 0.75rem;
  }
}
</style>
