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

        <!-- Featured News -->
        <div class="featured-news">
          <article class="news-card featured">
            <div class="news-image">
              <img src="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="New Laravel Project" loading="lazy">
              <div class="news-category">Development</div>
            </div>
            <div class="news-content">
              <div class="news-meta">
                <span class="news-date">
                  <i class="fas fa-calendar"></i>
                  December 15, 2024
                </span>
                <span class="news-author">
                  <i class="fas fa-user"></i>
                  Darkheim Team
                </span>
              </div>
              <h3 class="news-title">
                <a href="#" class="news-link">
                  Completed Advanced E-Commerce Platform with Vue.js Integration
                </a>
              </h3>
              <p class="news-excerpt">
                We successfully delivered a complex e-commerce solution for a local retail business, featuring real-time inventory management, payment processing, and a modern Vue.js frontend. The project showcases our growing expertise in full-stack development.
              </p>
              <div class="news-tags">
                <span class="tag">Laravel</span>
                <span class="tag">Vue.js</span>
                <span class="tag">E-Commerce</span>
              </div>
            </div>
          </article>
        </div>

        <!-- News Grid -->
        <div class="news-grid">
          <article class="news-card">
            <div class="news-image">
              <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="New Technology" loading="lazy">
              <div class="news-category">Technology</div>
            </div>
            <div class="news-content">
              <div class="news-meta">
                <span class="news-date">
                  <i class="fas fa-calendar"></i>
                  December 10, 2024
                </span>
              </div>
              <h3 class="news-title">
                <a href="#" class="news-link">
                  Adopting Docker for All New Projects
                </a>
              </h3>
              <p class="news-excerpt">
                Starting this month, we're implementing Docker containerization for all new development projects to ensure consistent environments and easier deployment.
              </p>
              <div class="news-tags">
                <span class="tag">Docker</span>
                <span class="tag">DevOps</span>
              </div>
            </div>
          </article>

          <article class="news-card">
            <div class="news-image">
              <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Team Growth" loading="lazy">
              <div class="news-category">Studio</div>
            </div>
            <div class="news-content">
              <div class="news-meta">
                <span class="news-date">
                  <i class="fas fa-calendar"></i>
                  December 5, 2024
                </span>
              </div>
              <h3 class="news-title">
                <a href="#" class="news-link">
                  10 Months of Darkheim Development Studio
                </a>
              </h3>
              <p class="news-excerpt">
                Reflecting on our first 10 months in business - from our first client to building a reputation for quality web development in the local community.
              </p>
              <div class="news-tags">
                <span class="tag">Milestone</span>
                <span class="tag">Studio News</span>
              </div>
            </div>
          </article>

          <article class="news-card">
            <div class="news-image">
              <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Client Success" loading="lazy">
              <div class="news-category">Success Story</div>
            </div>
            <div class="news-content">
              <div class="news-meta">
                <span class="news-date">
                  <i class="fas fa-calendar"></i>
                  November 28, 2024
                </span>
              </div>
              <h3 class="news-title">
                <a href="#" class="news-link">
                  Client Spotlight: Sunrise Bakery's Online Success
                </a>
              </h3>
              <p class="news-excerpt">
                How we helped Sunrise Bakery increase their online orders by 300% with a custom Laravel-powered website and ordering system.
              </p>
              <div class="news-tags">
                <span class="tag">Case Study</span>
                <span class="tag">Laravel</span>
              </div>
            </div>
          </article>

          <article class="news-card">
            <div class="news-image">
              <img src="https://images.unsplash.com/photo-1504639725590-34d0984388bd?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Learning" loading="lazy">
              <div class="news-category">Learning</div>
            </div>
            <div class="news-content">
              <div class="news-meta">
                <span class="news-date">
                  <i class="fas fa-calendar"></i>
                  November 20, 2024
                </span>
              </div>
              <h3 class="news-title">
                <a href="#" class="news-link">
                  Mastering Sass: Advanced CSS Techniques
                </a>
              </h3>
              <p class="news-excerpt">
                Our deep dive into advanced Sass features and how they're helping us create more maintainable and scalable stylesheets for client projects.
              </p>
              <div class="news-tags">
                <span class="tag">Sass</span>
                <span class="tag">CSS</span>
              </div>
            </div>
          </article>

          <article class="news-card">
            <div class="news-image">
              <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Tools Update" loading="lazy">
              <div class="news-category">Tools</div>
            </div>
            <div class="news-content">
              <div class="news-meta">
                <span class="news-date">
                  <i class="fas fa-calendar"></i>
                  November 15, 2024
                </span>
              </div>
              <h3 class="news-title">
                <a href="#" class="news-link">
                  Upgrading Our Development Workflow
                </a>
              </h3>
              <p class="news-excerpt">
                New tools and processes we've added to our development workflow to improve code quality and project delivery times.
              </p>
              <div class="news-tags">
                <span class="tag">Workflow</span>
                <span class="tag">Git</span>
              </div>
            </div>
          </article>

          <article class="news-card">
            <div class="news-image">
              <img src="https://images.unsplash.com/photo-1553877522-43269d4ea984?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Future Plans" loading="lazy">
              <div class="news-category">Future</div>
            </div>
            <div class="news-content">
              <div class="news-meta">
                <span class="news-date">
                  <i class="fas fa-calendar"></i>
                  November 10, 2024
                </span>
              </div>
              <h3 class="news-title">
                <a href="#" class="news-link">
                  2025 Goals: Expanding Our Services
                </a>
              </h3>
              <p class="news-excerpt">
                Looking ahead to 2025, we're planning to expand our service offerings and take on more complex projects while maintaining our commitment to quality.
              </p>
              <div class="news-tags">
                <span class="tag">Planning</span>
                <span class="tag">Growth</span>
              </div>
            </div>
          </article>
        </div>

        <!-- Load More Button -->
        <div class="text-center news-load-more">
          <button class="btn btn-outline btn-large" @click="loadMoreNews">
            <i class="fas fa-plus"></i>
            Load More News
          </button>
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
export default {
  name: 'HomePage',
  methods: {
    loadMoreNews() {
      // Заглушка для загрузки новостей
      console.log('Loading more news...')
    }
  }
}
</script>

<style lang="scss" scoped>
@use '../../css/pages/home-page';
</style>
