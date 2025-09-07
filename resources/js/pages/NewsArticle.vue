<template>
  <div class="news-article-page page-with-header-offset">
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
              <div class="meta-item reading-time">
                <i class="fas fa-clock"></i>
                <span>{{ calculateReadingTime(article.content) }} min read</span>
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

                <!-- Author Info -->
                <div v-if="article.author" class="author-info">
                  <div class="author-avatar">
                    {{ article.author.charAt(0).toUpperCase() }}
                  </div>
                  <div class="author-details">
                    <div class="author-name">{{ article.author }}</div>
                    <div class="author-bio">Article author and content creator</div>
                  </div>
                </div>

                <!-- Enhanced Share Section -->
                <div class="article-share">
                  <h4>Share this article</h4>
                  <p class="share-description">Help others discover this content</p>
                  <div class="share-buttons">
                    <button @click="shareOnTwitter" class="share-btn twitter">
                      <svg class="x-icon" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                      </svg>
                      <span class="share-text">
                        <span class="share-title">X (Twitter)</span>
                        <span class="share-subtitle">Post on X</span>
                      </span>
                    </button>

                    <button @click="shareOnFacebook" class="share-btn facebook">
                      <i class="fab fa-facebook"></i>
                      <span class="share-text">
                        <span class="share-title">Facebook</span>
                        <span class="share-subtitle">Share on Facebook</span>
                      </span>
                    </button>

                    <button @click="shareOnLinkedIn" class="share-btn linkedin">
                      <i class="fab fa-linkedin"></i>
                      <span class="share-text">
                        <span class="share-title">LinkedIn</span>
                        <span class="share-subtitle">Share professionally</span>
                      </span>
                    </button>

                    <button @click="shareOnWhatsApp" class="share-btn whatsapp">
                      <i class="fab fa-whatsapp"></i>
                      <span class="share-text">
                        <span class="share-title">WhatsApp</span>
                        <span class="share-subtitle">Send to contacts</span>
                      </span>
                    </button>

                    <button @click="shareOnTelegram" class="share-btn telegram">
                      <i class="fab fa-telegram"></i>
                      <span class="share-text">
                        <span class="share-title">Telegram</span>
                        <span class="share-subtitle">Share on Telegram</span>
                      </span>
                    </button>

                    <button @click="shareByEmail" class="share-btn email">
                      <i class="fas fa-envelope"></i>
                      <span class="share-text">
                        <span class="share-title">Email</span>
                        <span class="share-subtitle">Send via email</span>
                      </span>
                    </button>

                    <button @click="copyLink" class="share-btn copy" :class="{ copied: linkCopied }">
                      <i :class="linkCopied ? 'fas fa-check' : 'fas fa-copy'"></i>
                      <span class="share-text">
                        <span class="share-title">{{ linkCopied ? 'Copied!' : 'Copy Link' }}</span>
                        <span class="share-subtitle">{{ linkCopied ? 'Link copied to clipboard' : 'Copy to clipboard' }}</span>
                      </span>
                    </button>
                  </div>

                  <!-- Native Share API Button (for mobile) -->
                  <button v-if="canUseNativeShare" @click="nativeShare" class="share-btn native">
                    <i class="fas fa-share"></i>
                    <span class="share-text">
                      <span class="share-title">More Options</span>
                      <span class="share-subtitle">Use device sharing</span>
                    </span>
                  </button>
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

            <!-- Simplified Sidebar -->
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
import { ref, onMounted, watch, computed } from 'vue'
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
    const linkCopied = ref(false)

    // Check if native share API is available
    const canUseNativeShare = computed(() => {
      return navigator.share && /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
    })

    const loadArticle = async (slug) => {
      try {
        loading.value = true
        const response = await axios.get(`/api/news/${slug}`)

        if (response.data.success) {
          article.value = response.data.data

          if (article.value && article.value.title) {
            const { setPageTitle } = await import('../composables/usePageTitle.js')
            setPageTitle(article.value.title)
          }

          loadRelatedArticles()
          loadNavigation()
        } else {
          throw new Error(response.data.message || 'Failed to load article')
        }
      } catch (error) {
        console.error('Failed to load article:', error)
        article.value = null
        const { setPageTitle } = await import('../composables/usePageTitle.js')
        setPageTitle('Article Not Found')
      } finally {
        loading.value = false
      }
    }

    const loadRelatedArticles = async () => {
      if (!route.params.slug) return

      try {
        const response = await axios.get(`/api/news/${route.params.slug}/related`)

        if (response.data.success) {
          relatedArticles.value = response.data.data || []
        } else {
          throw new Error('Failed to load related articles')
        }
      } catch (error) {
        console.error('Failed to load related articles:', error)
        relatedArticles.value = []

        try {
          const fallbackResponse = await axios.get('/api/news/latest')
          if (fallbackResponse.data.success) {
            const latestArticles = fallbackResponse.data.data || []
            relatedArticles.value = latestArticles
              .filter(item => item.slug !== route.params.slug)
              .slice(0, 3)
          }
        } catch (fallbackError) {
          console.error('Failed to load fallback articles:', fallbackError)
        }
      }
    }

    const loadNavigation = async () => {
      previousArticle.value = null
      nextArticle.value = null
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

    const getCategoryDisplayName = (categoryKey) => {
      return categories.value[categoryKey] || categoryKey
    }

    const formatContent = (content) => {
      if (!content) return ''

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

    const calculateReadingTime = (content) => {
      if (!content) return 0
      const wordsPerMinute = 200
      const wordCount = content.split(/\s+/).length
      return Math.ceil(wordCount / wordsPerMinute)
    }

    // Enhanced sharing functions
    const getShareUrl = () => window.location.href
    const getShareTitle = () => article.value?.title || 'Check out this article'
    const getShareText = () => article.value?.excerpt || article.value?.title || 'Interesting article'

    const shareOnTwitter = () => {
      const url = encodeURIComponent(getShareUrl())
      const text = encodeURIComponent(`${getShareTitle()} - ${getShareText()}`)
      const hashtags = encodeURIComponent('news,article')
      window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}&hashtags=${hashtags}`, '_blank', 'width=550,height=420')
    }

    const shareOnFacebook = () => {
      const url = encodeURIComponent(getShareUrl())
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=550,height=420')
    }

    const shareOnLinkedIn = () => {
      const url = encodeURIComponent(getShareUrl())
      const title = encodeURIComponent(getShareTitle())
      const summary = encodeURIComponent(getShareText())
      window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}&title=${title}&summary=${summary}`, '_blank', 'width=550,height=420')
    }

    const shareOnWhatsApp = () => {
      const text = encodeURIComponent(`${getShareTitle()}\n\n${getShareText()}\n\n${getShareUrl()}`)
      window.open(`https://wa.me/?text=${text}`, '_blank')
    }

    const shareOnTelegram = () => {
      const url = encodeURIComponent(getShareUrl())
      const text = encodeURIComponent(`${getShareTitle()}\n\n${getShareText()}`)
      window.open(`https://t.me/share/url?url=${url}&text=${text}`, '_blank')
    }

    const shareByEmail = () => {
      const subject = encodeURIComponent(`Check out: ${getShareTitle()}`)
      const body = encodeURIComponent(`I thought you might be interested in this article:\n\n${getShareTitle()}\n\n${getShareText()}\n\nRead more: ${getShareUrl()}`)
      window.location.href = `mailto:?subject=${subject}&body=${body}`
    }

    const copyLink = async () => {
      try {
        await navigator.clipboard.writeText(getShareUrl())
        linkCopied.value = true
        setTimeout(() => {
          linkCopied.value = false
        }, 2000)
      } catch (error) {
        console.error('Failed to copy link:', error)
        // Fallback for older browsers
        const textArea = document.createElement('textarea')
        textArea.value = getShareUrl()
        document.body.appendChild(textArea)
        textArea.select()
        try {
          document.execCommand('copy')
          linkCopied.value = true
          setTimeout(() => {
            linkCopied.value = false
          }, 2000)
        } catch (fallbackError) {
          console.error('Fallback copy failed:', fallbackError)
        }
        document.body.removeChild(textArea)
      }
    }

    const nativeShare = async () => {
      if (navigator.share) {
        try {
          await navigator.share({
            title: getShareTitle(),
            text: getShareText(),
            url: getShareUrl()
          })
        } catch (error) {
          if (error.name !== 'AbortError') {
            console.error('Error sharing:', error)
          }
        }
      }
    }

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
      linkCopied,
      canUseNativeShare,
      calculateReadingTime,
      formatContent,
      formatDate,
      shareOnTwitter,
      shareOnFacebook,
      shareOnLinkedIn,
      shareOnWhatsApp,
      shareOnTelegram,
      shareByEmail,
      copyLink,
      nativeShare,
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

  // Enhanced mobile responsiveness
  @media (max-width: 768px) {
    gap: 1rem;
    margin-bottom: 1.5rem;
    font-size: 0.85rem;
  }

  @media (max-width: 480px) {
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
    font-size: 0.8rem;
  }

  // Ultra small screens - compact layout
  @media (max-width: 360px) {
    gap: 0.5rem;
    font-size: 0.75rem;
  }
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  white-space: nowrap;

  // Mobile optimizations
  @media (max-width: 768px) {
    gap: 0.4rem;
    min-width: 0; // Allow shrinking
  }

  @media (max-width: 480px) {
    // Create two-column layout on mobile
    flex: 1;
    min-width: calc(50% - 0.375rem);
    max-width: 100%;

    // Allow text to wrap if needed
    white-space: normal;
    word-break: break-word;
  }

  @media (max-width: 360px) {
    // Single column on very small screens
    flex: 1;
    min-width: 100%;
  }

  i {
    font-size: 0.85em;
    color: #9ca3af;
    flex-shrink: 0;
    width: 14px;
    text-align: center;

    @media (max-width: 768px) {
      font-size: 0.8em;
      width: 12px;
    }

    @media (max-width: 480px) {
      font-size: 0.75em;
      width: 10px;
    }
  }

  span {
    line-height: 1.4;

    @media (max-width: 480px) {
      font-size: inherit;
      line-height: 1.3;
    }
  }

  // Special styling for reading time
  &.reading-time {
    @media (max-width: 480px) {
      // Make reading time more prominent on mobile
      background: #f3f4f6;
      padding: 0.25rem 0.5rem;
      border-radius: 12px;
      font-weight: 500;
      color: #4b5563;

      i {
        color: #6b7280;
      }
    }
  }

  // Responsive text truncation for longer content
  @media (max-width: 480px) {
    &:not(.reading-time) span {
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      max-height: 2.6em; // Accommodate 2 lines
    }
  }
}

// Special grid layout for mobile meta items
@media (max-width: 480px) {
  .article-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;

    .meta-item {
      flex: none;
      min-width: 0;

      &.reading-time {
        grid-column: 1 / -1; // Span full width
        justify-self: center;
        max-width: 200px;
      }
    }
  }
}

// Stack layout for very small screens
@media (max-width: 360px) {
  .article-meta {
    display: flex;
    flex-direction: column;

    .meta-item {
      &.reading-time {
        order: -1; // Move to top
        align-self: center;
        margin-bottom: 0.5rem;
      }
    }
  }
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

  :deep(h1),
  :deep(h2),
  :deep(h3),
  :deep(h4),
  :deep(h5),
  :deep(h6) {
    color: #2c3e50;
    font-weight: 600;
    margin: 2rem 0 1rem;
    line-height: 1.3;
  }

  :deep(h1) { font-size: 2.5rem; }
  :deep(h2) { font-size: 2rem; }
  :deep(h3) { font-size: 1.5rem; }
  :deep(h4) { font-size: 1.25rem; }

  :deep(blockquote) {
    border-left: 4px solid #667eea;
    background: #f8f9fa;
    padding: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    border-radius: 0 8px 8px 0;

    p {
      margin-bottom: 0;
    }
  }

  :deep(ul),
  :deep(ol) {
    margin: 1.5rem 0;
    padding-left: 2rem;

    li {
      margin-bottom: 0.5rem;
      line-height: 1.6;
    }
  }

  :deep(code) {
    background: #f1f3f4;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-family: 'Fira Code', monospace;
    font-size: 0.9em;
  }

  :deep(pre) {
    background: #2d3748;
    color: #e2e8f0;
    padding: 1.5rem;
    border-radius: 8px;
    overflow-x: auto;
    margin: 2rem 0;

    code {
      background: none;
      padding: 0;
      color: inherit;
    }
  }

  :deep(a) {
    color: #667eea;
    text-decoration: none;
    border-bottom: 1px solid rgba(102, 126, 234, 0.3);
    transition: all 0.2s ease;

    &:hover {
      color: #764ba2;
      border-bottom-color: #764ba2;
    }
  }

  :deep(img) {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 2rem 0;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
}

.article-share {
  padding: 2rem 0;
  border-top: 1px solid #e9ecef;
  border-bottom: 1px solid #e9ecef;
  margin-bottom: 2rem;

  h4 {
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
    color: #2c3e50;
    font-weight: 600;
  }

  .share-description {
    color: #6c757d;
    margin-bottom: 2rem;
    font-size: 1rem;
  }
}

.share-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.share-btn {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.5rem;
  border: none;
  border-radius: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  background: white;
  border: 2px solid #e9ecef;
  position: relative;
  overflow: hidden;

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
  }

  &:hover::before {
    left: 100%;
  }

  i {
    font-size: 1.25rem;
    width: 24px;
    text-align: center;
    flex-shrink: 0;
  }

  .x-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
  }

  .share-text {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
  }

  .share-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
  }

  .share-subtitle {
    font-size: 0.8rem;
    opacity: 0.7;
  }

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  }

  &.twitter {
    &:hover {
      background: #1da1f2;
      color: white;
      border-color: #1da1f2;
    }
  }

  &.facebook {
    &:hover {
      background: #4267b2;
      color: white;
      border-color: #4267b2;
    }
  }

  &.linkedin {
    &:hover {
      background: #0077b5;
      color: white;
      border-color: #0077b5;
    }
  }

  &.whatsapp {
    &:hover {
      background: #25d366;
      color: white;
      border-color: #25d366;
    }
  }

  &.telegram {
    &:hover {
      background: #0088cc;
      color: white;
      border-color: #0088cc;
    }
  }

  &.email {
    &:hover {
      background: #ea4335;
      color: white;
      border-color: #ea4335;
    }
  }

  &.copy {
    &:hover {
      background: #6c757d;
      color: white;
      border-color: #6c757d;
    }

    &.copied {
      background: #28a745;
      color: white;
      border-color: #28a745;
    }
  }

  &.native {
    &:hover {
      background: #667eea;
      color: white;
      border-color: #667eea;
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

.no-related {
  color: #6c757d;
  font-style: italic;
  text-align: center;
  padding: 2rem;
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

.author-info {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 2rem;
  margin: 3rem 0;
  display: flex;
  align-items: center;
  gap: 1rem;

  .author-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    font-weight: 600;
  }

  .author-details {
    flex: 1;

    .author-name {
      font-size: 1.1rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 0.25rem;
    }

    .author-bio {
      color: #6c757d;
      font-size: 0.9rem;
      line-height: 1.5;
    }
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
    grid-template-columns: 1fr;
  }

  .article-meta {
    flex-direction: column;
    gap: 0.75rem;
  }

  .article-header {
    padding: 1.5rem 0 2rem;
  }

  .breadcrumb {
    .current {
      max-width: 150px;
    }
  }
}

@media (max-width: 480px) {
  .article-text {
    font-size: 1rem;
    line-height: 1.7;
  }

  .article-body {
    padding: 1.5rem;
  }

  .sidebar-card {
    padding: 1.5rem;
  }

  .share-btn {
    padding: 0.75rem 1rem;

    .share-title {
      font-size: 0.9rem;
    }

    .share-subtitle {
      font-size: 0.75rem;
    }
  }

  .nav-btn {
    padding: 1.5rem;
  }
}
</style>
