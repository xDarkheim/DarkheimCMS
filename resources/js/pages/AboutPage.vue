<template>
  <div class="about-page page-with-header-offset">
    <!-- Our Story Section -->
    <section class="section">
      <div class="container">
        <div class="section__header animate-fade-in">
          <h1 class="section__title">About Darkheim Development Studio</h1>
          <p class="section__subtitle">
            A growing development studio creating quality web solutions
            for businesses ready for digital presence.
          </p>
        </div>

        <div class="grid grid--2 gap-12 items-center">
          <div class="animate-fade-in">
            <h2 class="section__title text-left">Our Journey</h2>
            <p class="section__text mb-6">
              Darkheim Development Studio was founded in late 2025 with a mission to help local businesses
              and growing companies establish their online presence through modern, reliable web solutions.
            </p>
            <p class="section__text mb-6">
              While we're a young studio, our team combines years of individual experience in web development.
              We focus on quality over quantity, ensuring each project gets the attention it deserves.
            </p>
            <p class="section__text mb-8">
              We believe that every business, regardless of size, deserves a professional web presence.
              Our approach combines modern technologies with practical solutions that actually work for our clients.
            </p>
            <div class="stats" v-if="!loading">
              <div class="stats__item"
                   v-for="(stat, index) in statsArray"
                   :key="stat.key"
                   :style="{ animationDelay: `${index * 0.1}s` }"
                   @click="animateNumber(stat.key)">
                <i :class="`${stat.icon} stats__icon`"></i>
                <div class="stats__number" :ref="`number-${stat.key}`">{{ stat.value }}</div>
                <div class="stats__label">{{ stat.label }}</div>
              </div>
            </div>
            <div class="stats" v-else>
              <div class="stats__item stats__item--loading" v-for="i in 4" :key="i">
                <div class="stats__skeleton"></div>
              </div>
            </div>
          </div>
          <div class="animate-slide-up">
            <div class="about-visual">
              <div class="about-visual__card">
                <h3>Our Philosophy</h3>
                <p>We believe in building long-term relationships with our clients, creating reliable,
                quality websites that help their businesses grow.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Our Approach Section -->
    <section class="section section--alt">
      <div class="container">
        <div class="section__header animate-fade-in">
          <h2 class="section__title">Our Approach</h2>
          <p class="section__subtitle">
            We keep things simple and focused, creating websites that work well and look professional.
          </p>
        </div>

        <div class="grid grid--3 animate-slide-up">
          <div class="approach-card">
            <div class="approach-card__icon">
              <i class="fas fa-handshake"></i>
            </div>
            <h3 class="approach-card__title">Personal Service</h3>
            <p class="approach-card__text">
              We work closely with each client to understand their needs and deliver solutions that fit their business.
            </p>
          </div>

          <div class="approach-card">
            <div class="approach-card__icon">
              <i class="fas fa-code"></i>
            </div>
            <h3 class="approach-card__title">Modern Technologies</h3>
            <p class="approach-card__text">
              We use proven technologies like Laravel, Vue.js, and modern web standards to build reliable websites.
            </p>
          </div>

          <div class="approach-card">
            <div class="approach-card__icon">
              <i class="fas fa-mobile-alt"></i>
            </div>
            <h3 class="approach-card__title">Mobile-First</h3>
            <p class="approach-card__text">
              Every website we create looks great and works perfectly on all devices.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Technology Stack Section -->
    <section class="section">
      <div class="container">
        <div class="section__header animate-fade-in">
          <h2 class="section__title">Technologies We Use</h2>
          <p class="section__subtitle">
            We work with a modern, reliable technology stack that ensures performance and maintainability.
          </p>
        </div>

        <div class="tech-categories">
          <div class="tech-category animate-slide-up">
            <h3 class="tech-category__title">Backend Development</h3>
            <div class="tech-list">
              <div class="tech-item">
                <i class="fab fa-php tech-icon"></i>
                <span>PHP</span>
              </div>
              <div class="tech-item">
                <i class="fab fa-laravel tech-icon"></i>
                <span>Laravel</span>
              </div>
              <div class="tech-item">
                <i class="fas fa-database tech-icon"></i>
                <span>MySQL</span>
              </div>
              <div class="tech-item">
                <i class="fas fa-database tech-icon"></i>
                <span>MariaDB</span>
              </div>
            </div>
          </div>

          <div class="tech-category animate-slide-up">
            <h3 class="tech-category__title">Frontend Development</h3>
            <div class="tech-list">
              <div class="tech-item">
                <i class="fab fa-js tech-icon"></i>
                <span>JavaScript</span>
              </div>
              <div class="tech-item">
                <i class="fab fa-vuejs tech-icon"></i>
                <span>Vue.js</span>
              </div>
              <div class="tech-item">
                <i class="fab fa-html5 tech-icon"></i>
                <span>HTML5</span>
              </div>
              <div class="tech-item">
                <i class="fab fa-css3-alt tech-icon"></i>
                <span>CSS</span>
              </div>
              <div class="tech-item">
                <i class="fab fa-sass tech-icon"></i>
                <span>Sass</span>
              </div>
            </div>
          </div>

          <div class="tech-category animate-slide-up">
            <h3 class="tech-category__title">Development & Deployment</h3>
            <div class="tech-list">
              <div class="tech-item">
                <i class="fab fa-git-alt tech-icon"></i>
                <span>Git</span>
              </div>
              <div class="tech-item">
                <i class="fab fa-docker tech-icon"></i>
                <span>Docker</span>
              </div>
              <div class="tech-item">
                <i class="fab fa-linux tech-icon"></i>
                <span>Linux</span>
              </div>
              <div class="tech-item">
                <i class="fab fa-windows tech-icon"></i>
                <span>Windows Server</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Our Values Section -->
    <section class="section section--alt">
      <div class="container">
        <div class="section__header animate-fade-in">
          <h2 class="section__title">What Drives Us</h2>
          <p class="section__subtitle">
            Our core values guide every project and every relationship we build.
          </p>
        </div>

        <div class="grid grid--2 gap-8 animate-slide-up">
          <div class="value-card">
            <div class="value-card__icon">
              <i class="fas fa-gem"></i>
            </div>
            <h3 class="value-card__title">Quality First</h3>
            <p class="value-card__text">
              We believe in doing things right the first time. Every line of code and every design element
              is crafted with attention to detail and long-term thinking.
            </p>
          </div>

          <div class="value-card">
            <div class="value-card__icon">
              <i class="fas fa-comments"></i>
            </div>
            <h3 class="value-card__title">Clear Communication</h3>
            <p class="value-card__text">
              We keep our clients informed throughout the development process, explaining technical concepts
              in plain language and ensuring everyone is on the same page.
            </p>
          </div>

          <div class="value-card">
            <div class="value-card__icon">
              <i class="fas fa-seedling"></i>
            </div>
            <h3 class="value-card__title">Growing Together</h3>
            <p class="value-card__text">
              As a young studio, we grow alongside our clients. We build relationships that extend beyond
              project completion, providing ongoing support as businesses evolve.
            </p>
          </div>

          <div class="value-card">
            <div class="value-card__icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h3 class="value-card__title">Reliable Service</h3>
            <p class="value-card__text">
              Our clients can count on us to deliver what we promise, when we promise it.
              We build trust through consistent, dependable service.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Our Achievements Section -->
    <section class="section">
      <div class="container">
        <div class="section__header animate-fade-in">
          <h2 class="section__title">Our Achievements</h2>
          <p class="section__subtitle">
            Milestones we've reached and the impact we've made since our founding.
          </p>
        </div>

        <div class="grid grid--3 gap-8 animate-slide-up">
          <div class="achievement-card">
            <div class="achievement-card__icon">
              <i class="fas fa-trophy"></i>
            </div>
            <h3 class="achievement-card__title">Quality Delivery</h3>
            <p class="achievement-card__text">
              Successfully delivered {{ stats.projects_completed }} projects with 100% client satisfaction rate.
              Every project was completed on time and within budget.
            </p>
          </div>

          <div class="achievement-card">
            <div class="achievement-card__icon">
              <i class="fas fa-users-cog"></i>
            </div>
            <h3 class="achievement-card__title">Growing Team</h3>
            <p class="achievement-card__text">
              Built a dedicated team of {{ stats.team_members }} professionals who are passionate about
              creating exceptional web experiences.
            </p>
          </div>

          <div class="achievement-card">
            <div class="achievement-card__icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="achievement-card__title">Technology Expertise</h3>
            <p class="achievement-card__text">
              Mastered {{ stats.technologies_used }} modern technologies to provide comprehensive
              web development solutions.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Our Process Section -->
    <section class="section section--alt">
      <div class="container">
        <div class="section__header animate-fade-in">
          <h2 class="section__title">How We Work</h2>
          <p class="section__subtitle">
            Our proven development process ensures transparent communication and successful project delivery.
          </p>
        </div>

        <div class="process-timeline animate-slide-up">
          <div class="process-step">
            <div class="process-step__number">1</div>
            <div class="process-step__content">
              <h3 class="process-step__title">Discovery & Planning</h3>
              <p class="process-step__text">
                We start by understanding your business goals, target audience, and technical requirements.
                This phase includes detailed project planning and timeline creation.
              </p>
            </div>
          </div>

          <div class="process-step">
            <div class="process-step__number">2</div>
            <div class="process-step__content">
              <h3 class="process-step__title">Design & Prototyping</h3>
              <p class="process-step__text">
                Our team creates wireframes and designs that align with your brand and user experience goals.
                We iterate based on your feedback before moving to development.
              </p>
            </div>
          </div>

          <div class="process-step">
            <div class="process-step__number">3</div>
            <div class="process-step__content">
              <h3 class="process-step__title">Development & Testing</h3>
              <p class="process-step__text">
                We build your website using modern technologies and best practices.
                Rigorous testing ensures everything works perfectly across all devices.
              </p>
            </div>
          </div>

          <div class="process-step">
            <div class="process-step__number">4</div>
            <div class="process-step__content">
              <h3 class="process-step__title">Launch & Support</h3>
              <p class="process-step__text">
                After final approval, we launch your website and provide ongoing support.
                We're here to help your business grow with continued maintenance and updates.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="section section--cta">
      <div class="container">
        <div class="cta animate-fade-in">
          <h2 class="cta__title">Ready to work with us?</h2>
          <p class="cta__subtitle">
            Let's discuss how we can help establish or improve your business's online presence.
          </p>
          <router-link to="/contact" class="btn btn--primary btn--xl">
            Get in Touch
            <i class="fas fa-paper-plane btn__icon btn__icon--right"></i>
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'AboutPage',
  data() {
    return {
      loading: true,
      stats: {
        projects_completed: 0,
        total_projects: 0,
        featured_projects: 0,
        team_members: 0,
        years_experience: 1,
        open_positions: 0,
        news_articles: 0,
        client_satisfaction: 0,
        technologies_used: 0
      },
      animatingNumbers: new Set()
    }
  },
  computed: {
    statsArray() {
      return [
        {
          key: 'projects_completed',
          value: this.stats.projects_completed,
          label: this.getProjectLabel(this.stats.projects_completed),
          icon: 'fas fa-check-circle'
        },
        {
          key: 'client_satisfaction',
          value: this.stats.client_satisfaction,
          label: 'client satisfaction',
          icon: 'fas fa-heart',
          suffix: '%'
        },
        {
          key: 'team_members',
          value: this.stats.team_members,
          label: this.getTeamLabel(this.stats.team_members),
          icon: 'fas fa-users'
        },
        {
          key: 'technologies_used',
          value: this.stats.technologies_used,
          label: 'technologies used',
          icon: 'fas fa-code'
        }
      ]
    }
  },
  async mounted() {
    document.title = 'About Us - Darkheim Development Studio'
    await this.loadStats()
    this.setupIntersectionObserver()
  },
  methods: {
    async loadStats() {
      try {
        const response = await axios.get('/api/stats')
        if (response.data.success) {
          this.stats = response.data.data
          // Animate numbers when they load
          this.$nextTick(() => {
            this.animateAllNumbers()
          })
        }
      } catch (error) {
        console.error('Error loading stats:', error)
        // Use default values if API is unavailable
      } finally {
        this.loading = false
      }
    },
    animateNumber(key) {
      if (this.animatingNumbers.has(key)) return

      this.animatingNumbers.add(key)
      const element = this.$refs[`number-${key}`]?.[0]

      if (element) {
        element.style.animation = 'pulse 0.6s ease-in-out'
        setTimeout(() => {
          element.style.animation = ''
          this.animatingNumbers.delete(key)
        }, 600)
      }
    },
    animateAllNumbers() {
      this.statsArray.forEach((stat, index) => {
        setTimeout(() => {
          this.animateCountUp(stat.key, stat.value)
        }, index * 200)
      })
    },
    animateCountUp(key, finalValue) {
      const element = this.$refs[`number-${key}`]?.[0]
      if (!element) return

      const stat = this.statsArray.find(s => s.key === key)
      const suffix = stat?.suffix || ''
      const duration = 1000
      const startTime = performance.now()

      const animate = (currentTime) => {
        const elapsed = currentTime - startTime
        const progress = Math.min(elapsed / duration, 1)

        // Easing function for smooth animation
        const easeOutCubic = 1 - Math.pow(1 - progress, 3)
        const currentValue = Math.floor(finalValue * easeOutCubic)

        element.textContent = currentValue + suffix

        if (progress < 1) {
          requestAnimationFrame(animate)
        } else {
          element.textContent = finalValue + suffix
        }
      }

      requestAnimationFrame(animate)
    },
    setupIntersectionObserver() {
      if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('in-view')
            }
          })
        }, { threshold: 0.1 })

        // Observe all animatable elements
        this.$nextTick(() => {
          const elements = this.$el.querySelectorAll('.tech-category, .approach-card, .value-card, .stats__item')
          elements.forEach(el => observer.observe(el))
        })
      }
    },
    getYearLabel(count) {
      if (count === 1) return 'year'
      return 'years'
    },
    getProjectLabel(count) {
      if (count === 1) return 'completed project'
      return 'completed projects'
    },
    getTeamLabel(count) {
      if (count === 1) return 'team member'
      return 'team members'
    },
    getNewsLabel(count) {
      if (count === 1) return 'article'
      return 'articles'
    }
  }
}
</script>

<style lang="scss" scoped>
@use '../../css/pages/about-page';

// Additional component-specific animations
.stats__item {
  cursor: pointer;

  &:hover {
    .stats__number {
      animation: pulse 0.6s ease-in-out;
    }
  }
}

// Enhanced entrance animations
.in-view {
  opacity: 1 !important;
  transform: translateY(0) !important;
}

// Smooth transitions for all interactive elements
* {
  transition: transform 0.3s ease, opacity 0.3s ease;
}
</style>
