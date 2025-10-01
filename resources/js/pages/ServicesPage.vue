<template>
  <div class="services-page page-with-header-offset">
    <!-- Hero Section -->
    <section class="services-hero">
      <div class="container">
        <div class="services-hero__content">
          <h1 class="services-hero__title">Our Services</h1>
          <p class="services-hero__subtitle">
            Professional web solutions tailored for small businesses and local companies.
            Modern, reliable, and affordable.
          </p>
        </div>
      </div>
    </section>

    <!-- Services Filter Tabs -->
    <section class="services-filter">
      <div class="container">
        <div class="filter-tabs">
          <button
            v-for="tab in filterTabs"
            :key="tab.id"
            :class="['filter-tab', { 'filter-tab--active': activeFilter === tab.id }]"
            @click="setActiveFilter(tab.id)"
          >
            <i :class="tab.icon"></i>
            <span>{{ tab.name }}</span>
          </button>
        </div>
      </div>
    </section>

    <!-- Services Content -->
    <section class="services-content">
      <div class="container">
        <div class="services-grid">
          <div
            v-for="service in filteredServices"
            :key="service.id"
            class="service-card"
            :class="{ 'service-card--featured': service.featured }"
          >
            <div v-if="service.featured" class="service-card__badge">
              <i class="fas fa-star"></i>
              Most Popular
            </div>

            <div class="service-card__header">
              <div class="service-card__icon">
                <i :class="service.icon"></i>
              </div>
              <h3 class="service-card__title">{{ service.title }}</h3>
              <div class="service-card__price">{{ service.price }}</div>
            </div>

            <div class="service-card__content">
              <p class="service-card__description">{{ service.description }}</p>

              <ul class="service-card__features">
                <li v-for="feature in service.features" :key="feature">
                  <i class="fas fa-check"></i>
                  {{ feature }}
                </li>
              </ul>

              <div class="service-card__tags">
                <span
                  v-for="tech in service.technologies"
                  :key="tech"
                  class="service-tag"
                >
                  {{ tech }}
                </span>
              </div>

              <div class="service-card__actions">
                <router-link to="/contact" class="btn btn--primary btn--sm">
                  Get Quote
                  <i class="fas fa-arrow-right"></i>
                </router-link>
                <button class="btn btn--ghost btn--sm" @click="showServiceDetails(service)">
                  Learn More
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredServices.length === 0" class="services-empty">
          <div class="services-empty__icon">
            <i class="fas fa-search"></i>
          </div>
          <h3 class="services-empty__title">No services found</h3>
          <p class="services-empty__text">Try selecting a different category</p>
        </div>
      </div>
    </section>

    <!-- Service Details Modal -->
    <div v-if="selectedService" class="service-modal" @click="closeModal">
      <div class="service-modal__content" @click.stop>
        <button class="service-modal__close" @click="closeModal" aria-label="Close modal">
          <i class="fas fa-times"></i>
        </button>

        <div class="service-modal__header">
          <div class="service-modal__icon">
            <i :class="selectedService.icon"></i>
          </div>
          <h2 class="service-modal__title">{{ selectedService.title }}</h2>
          <div class="service-modal__price">{{ selectedService.price }}</div>

          <!-- Additional close button in header for mobile -->
          <button class="service-modal__header-close" @click="closeModal" aria-label="Close">
            <i class="fas fa-arrow-down"></i>
            <span>Close</span>
          </button>
        </div>

        <div class="service-modal__body">
          <p class="service-modal__description">{{ selectedService.detailedDescription }}</p>

          <div class="service-modal__section">
            <h4>What's Included:</h4>
            <ul class="service-modal__features">
              <li v-for="feature in selectedService.features" :key="feature">
                <i class="fas fa-check"></i>
                {{ feature }}
              </li>
            </ul>
          </div>

          <div class="service-modal__section">
            <h4>Technologies Used:</h4>
            <div class="service-modal__technologies">
              <span
                v-for="tech in selectedService.technologies"
                :key="tech"
                class="service-modal__tech-tag"
              >
                {{ tech }}
              </span>
            </div>
          </div>

          <div class="service-modal__section">
            <h4>Timeline & Process:</h4>
            <p>{{ selectedService.timeline }}</p>
          </div>

          <div class="service-modal__actions">
            <router-link to="/contact" class="btn btn--primary btn--lg">
              Get Started
              <i class="fas fa-rocket"></i>
            </router-link>
            <router-link to="/portfolio" class="btn btn--ghost btn--lg">
              View Examples
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <section class="services-cta">
      <div class="container">
        <div class="services-cta__content">
          <h2 class="services-cta__title">Ready to Start Your Project?</h2>
          <p class="services-cta__text">
            Let's discuss your needs and create a custom solution that fits your business perfectly.
          </p>
          <div class="services-cta__actions">
            <router-link to="/contact" class="btn btn--primary btn--xl">
              Get Free Consultation
              <i class="fas fa-comments"></i>
            </router-link>
            <router-link to="/portfolio" class="btn btn--ghost btn--xl">
              View Our Work
            </router-link>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'ServicesPage',
  data() {
    return {
      activeFilter: 'all',
      selectedService: null,
      filterTabs: [
        { id: 'all', name: 'All Services', icon: 'fas fa-th' },
        { id: 'web-development', name: 'Web Development', icon: 'fas fa-code' },
        { id: 'ui-ux-design', name: 'UI/UX Design', icon: 'fas fa-palette' },
        { id: 'mobile-apps', name: 'Mobile Apps', icon: 'fas fa-mobile-alt' },
        { id: 'consulting', name: 'Consulting', icon: 'fas fa-lightbulb' }
      ],
      services: [
        // Web Development Services
        {
          id: 1,
          title: 'Custom Website Development',
          category: 'web-development',
          price: 'From $299',
          icon: 'fas fa-code',
          featured: false,
          description: 'Professional custom websites built with modern technologies and tailored to your business needs.',
          detailedDescription: 'We create fully custom websites from scratch using the latest web technologies. Each website is designed to reflect your brand identity and optimized for performance, SEO, and user experience. Perfect for businesses that need unique functionality or design.',
          features: [
            'Custom Design & Development',
            'Responsive & Mobile-First',
            'SEO Optimized Structure',
            'Fast Loading Performance',
            'Content Management System',
            'Contact Forms & Analytics'
          ],
          technologies: ['Laravel', 'Vue.js', 'HTML5', 'CSS3', 'JavaScript'],
          timeline: 'Typical timeline: 2-4 weeks. Includes planning, design, development, testing, and launch phases.'
        },
        {
          id: 2,
          title: 'E-commerce Development',
          category: 'web-development',
          price: 'From $599',
          icon: 'fas fa-shopping-cart',
          featured: true,
          description: 'Complete online store solutions with shopping cart, payments, and inventory management.',
          detailedDescription: 'Full-featured e-commerce platforms that enable you to sell products online effectively. Includes product catalog, shopping cart, secure payment processing, order management, and customer accounts. Ideal for retail businesses and online entrepreneurs.',
          features: [
            'Product Catalog Management',
            'Shopping Cart & Checkout',
            'Payment Gateway Integration',
            'Order & Inventory Management',
            'Customer Account System',
            'Sales Reports & Analytics'
          ],
          technologies: ['Laravel', 'Stripe/PayPal', 'MySQL', 'Vue.js'],
          timeline: 'Typical timeline: 4-6 weeks. Includes store setup, payment integration, testing, and training.'
        },
        {
          id: 3,
          title: 'Business Web Applications',
          category: 'web-development',
          price: 'From $499',
          icon: 'fas fa-cogs',
          featured: false,
          description: 'Custom web applications and management systems for business automation and workflow optimization.',
          detailedDescription: 'Tailored web applications that solve specific business challenges. From CRM systems to inventory management, we build tools that streamline your operations and improve efficiency.',
          features: [
            'Custom Business Logic',
            'User Role Management',
            'Database Design & Integration',
            'Reporting & Analytics',
            'API Development',
            'Third-party Integrations'
          ],
          technologies: ['Laravel', 'MySQL', 'Vue.js', 'API Integration'],
          timeline: 'Typical timeline: 3-8 weeks depending on complexity. Includes analysis, development, and training.'
        },

        // UI/UX Design Services
        {
          id: 4,
          title: 'Website UI/UX Design',
          category: 'ui-ux-design',
          price: 'From $199',
          icon: 'fas fa-palette',
          featured: false,
          description: 'User-centered design that creates intuitive and engaging web experiences for your visitors.',
          detailedDescription: 'Complete design process from user research to final mockups. We focus on creating designs that not only look great but also guide users toward your business goals effectively.',
          features: [
            'User Research & Analysis',
            'Wireframing & Prototyping',
            'Visual Design & Branding',
            'User Experience Optimization',
            'Mobile-First Design Approach',
            'Design System Creation'
          ],
          technologies: ['Figma', 'Adobe XD', 'Sketch', 'InVision'],
          timeline: 'Typical timeline: 1-3 weeks. Includes research, wireframing, design, and revisions.'
        },
        {
          id: 5,
          title: 'Mobile App Design',
          category: 'ui-ux-design',
          price: 'From $399',
          icon: 'fas fa-mobile-screen',
          featured: false,
          description: 'Native mobile app designs optimized for iOS and Android platforms with focus on usability.',
          detailedDescription: 'Specialized mobile app design following platform-specific guidelines. We ensure your app feels native on each platform while maintaining consistent branding and user experience.',
          features: [
            'iOS & Android Design Guidelines',
            'User Flow Optimization',
            'Interactive Prototypes',
            'Icon & Graphic Design',
            'App Store Asset Creation',
            'Usability Testing'
          ],
          technologies: ['Figma', 'Principle', 'After Effects', 'Sketch'],
          timeline: 'Typical timeline: 2-4 weeks. Includes wireframes, high-fidelity designs, and prototypes.'
        },
        {
          id: 6,
          title: 'Brand Identity Design',
          category: 'ui-ux-design',
          price: 'From $399',
          icon: 'fas fa-paint-brush',
          featured: false,
          description: 'Complete brand identity packages including logos, colors, typography, and brand guidelines.',
          detailedDescription: 'Comprehensive brand identity that reflects your business values and appeals to your target audience. Includes logo design, color palette, typography, and brand application guidelines.',
          features: [
            'Logo Design & Variations',
            'Color Palette Selection',
            'Typography System',
            'Brand Guidelines Document',
            'Business Card Design',
            'Social Media Templates'
          ],
          technologies: ['Illustrator', 'Photoshop', 'Figma', 'InDesign'],
          timeline: 'Typical timeline: 1-2 weeks. Includes concept development, design, and brand guide creation.'
        },

        // Mobile Apps Services
        {
          id: 7,
          title: 'Cross-Platform Mobile Apps',
          category: 'mobile-apps',
          price: 'From $2,199',
          icon: 'fas fa-mobile-alt',
          featured: false,
          description: 'Native-quality mobile apps that work on both iOS and Android with single codebase.',
          detailedDescription: 'Cost-effective mobile app development using modern cross-platform technologies. One codebase for both iOS and Android, maintaining native performance and user experience.',
          features: [
            'iOS & Android Compatible',
            'Native Performance',
            'App Store Deployment',
            'Push Notifications',
            'Offline Functionality',
            'Device API Integration'
          ],
          technologies: ['React Native', 'Flutter', 'Firebase', 'Native APIs'],
          timeline: 'Typical timeline: 6-12 weeks. Includes development, testing, and app store submission.'
        },
        {
          id: 8,
          title: 'Progressive Web Apps (PWA)',
          category: 'mobile-apps',
          price: 'From $1,299',
          icon: 'fas fa-globe',
          featured: false,
          description: 'Web applications that work like native mobile apps with offline capabilities and push notifications.',
          detailedDescription: 'Modern web applications that provide app-like experience without requiring app store downloads. Perfect for businesses wanting mobile presence without native app complexity.',
          features: [
            'App-like User Experience',
            'Offline Functionality',
            'Push Notifications',
            'Home Screen Installation',
            'Fast Loading Performance',
            'Cross-Platform Compatibility'
          ],
          technologies: ['Vue.js', 'Service Workers', 'PWA APIs', 'Firebase'],
          timeline: 'Typical timeline: 3-6 weeks. Includes development, testing, and optimization.'
        },
        {
          id: 9,
          title: 'Native Mobile Apps',
          category: 'mobile-apps',
          price: 'From $2,999',
          icon: 'fas fa-rocket',
          featured: false,
          description: 'Platform-specific native mobile apps for maximum performance and platform integration.',
          detailedDescription: 'True native mobile apps built specifically for iOS or Android. Provides best possible performance and access to all platform-specific features and APIs.',
          features: [
            'Maximum Performance',
            'Full Platform API Access',
            'Platform-Specific UI/UX',
            'Advanced Device Features',
            'App Store Optimization',
            'Enterprise-Grade Security'
          ],
          technologies: ['Swift/iOS', 'Kotlin/Android', 'Xcode', 'Android Studio'],
          timeline: 'Typical timeline: 8-16 weeks. Separate development for each platform.'
        },

        // Consulting Services
        {
          id: 10,
          title: 'Technical Consulting',
          category: 'consulting',
          price: 'From $99/hour',
          icon: 'fas fa-lightbulb',
          featured: false,
          description: 'Strategic technical advice to help you make the right technology decisions for your business.',
          detailedDescription: 'Expert guidance on technology strategy, architecture decisions, and implementation planning. Perfect for businesses planning digital transformation or technical projects.',
          features: [
            'Technology Strategy Planning',
            'Architecture Review & Design',
            'Performance Optimization Audit',
            'Security Assessment',
            'Scalability Planning',
            'Technology Stack Recommendations'
          ],
          technologies: ['Various based on needs'],
          timeline: 'Flexible engagement: One-time consultations or ongoing advisory relationships.'
        },
        {
          id: 11,
          title: 'Code Review & Audit',
          category: 'consulting',
          price: 'From $299',
          icon: 'fas fa-search',
          featured: false,
          description: 'Comprehensive analysis of existing code and systems to identify improvements and issues.',
          detailedDescription: 'Detailed review of your existing codebase, infrastructure, and development practices. We provide actionable recommendations for improvement, security fixes, and optimization.',
          features: [
            'Code Quality Assessment',
            'Security Vulnerability Analysis',
            'Performance Bottleneck Identification',
            'Best Practices Review',
            'Documentation Assessment',
            'Improvement Roadmap'
          ],
          technologies: ['PHP', 'JavaScript', 'Database', 'Server Configuration'],
          timeline: 'Typical timeline: 3-5 days for comprehensive audit and report delivery.'
        },
        {
          id: 12,
          title: 'Team Training & Mentoring',
          category: 'consulting',
          price: 'From $129/hour',
          icon: 'fas fa-graduation-cap',
          featured: false,
          description: 'Training sessions and mentoring for development teams on modern technologies and practices.',
          detailedDescription: 'Hands-on training and mentoring to level up your development team. Covers modern development practices, specific technologies, and project management approaches.',
          features: [
            'Technology-Specific Training',
            'Best Practices Workshops',
            'Code Review Sessions',
            'Agile Methodology Training',
            'Tool & Workflow Optimization',
            'One-on-One Mentoring'
          ],
          technologies: ['Laravel', 'Vue.js', 'Modern PHP', 'Git', 'Development Tools'],
          timeline: 'Flexible: Workshop sessions, ongoing mentoring, or intensive boot camps.'
        }
      ]
    }
  },
  computed: {
    filteredServices() {
      if (this.activeFilter === 'all') {
        return this.services
      }
      return this.services.filter(service => service.category === this.activeFilter)
    }
  },
  methods: {
    setActiveFilter(filterId) {
      this.activeFilter = filterId

      // On mobile, scroll the active tab into view
      this.$nextTick(() => {
        this.scrollActiveTabIntoView()
      })
    },

    scrollActiveTabIntoView() {
      if (window.innerWidth <= 768) {
        const activeTab = document.querySelector('.filter-tab--active')
        const tabsContainer = document.querySelector('.filter-tabs')

        if (activeTab && tabsContainer) {
          const containerRect = tabsContainer.getBoundingClientRect()
          const activeTabRect = activeTab.getBoundingClientRect()

          // Calculate scroll position to center the active tab
          const scrollLeft = activeTab.offsetLeft - containerRect.width / 2 + activeTabRect.width / 2

          tabsContainer.scrollTo({
            left: scrollLeft,
            behavior: 'smooth'
          })
        }
      }
    },

    handleTabsScroll() {
      const tabsContainer = document.querySelector('.filter-tabs')
      const filterSection = document.querySelector('.services-filter')

      if (tabsContainer && filterSection && window.innerWidth <= 768) {
        const { scrollLeft, scrollWidth, clientWidth } = tabsContainer

        // Add/remove scroll indicator classes
        if (scrollLeft > 10) {
          filterSection.classList.add('has-scroll-left')
        } else {
          filterSection.classList.remove('has-scroll-left')
        }

        if (scrollLeft < scrollWidth - clientWidth - 10) {
          filterSection.classList.add('has-scroll-right')
        } else {
          filterSection.classList.remove('has-scroll-right')
        }
      }
    },

    setupMobileScrollHandlers() {
      const tabsContainer = document.querySelector('.filter-tabs')

      if (tabsContainer) {
        tabsContainer.addEventListener('scroll', this.handleTabsScroll)

        // Initial check
        this.handleTabsScroll()

        // Check on resize
        window.addEventListener('resize', () => {
          setTimeout(this.handleTabsScroll, 100)
        })
      }
    },

    showServiceDetails(service) {
      this.selectedService = service
      document.body.style.overflow = 'hidden'

      // Добавляем обработчик для свайпа вниз на мобильных устройствах
      this.$nextTick(() => {
        this.setupMobileModalInteractions()
      })
    },

    closeModal() {
      this.selectedService = null
      document.body.style.overflow = 'auto'

      // Убираем обработчики свайпа
      this.removeMobileModalInteractions()
    },

    // Новый метод для настройки мобильных взаимодействий с модальным окном
    setupMobileModalInteractions() {
      const modal = document.querySelector('.service-modal')
      const modalContent = document.querySelector('.service-modal__content')

      if (!modal || !modalContent) return

      let startY = 0
      let currentY = 0
      let isDragging = false

      const handleTouchStart = (e) => {
        startY = e.touches[0].clientY
        isDragging = true
        modalContent.style.transition = 'none'
      }

      const handleTouchMove = (e) => {
        if (!isDragging) return

        currentY = e.touches[0].clientY
        const deltaY = currentY - startY

        // Позволяем только свайп вниз и только если прокрутка контента в верхней позиции
        const modalBody = modalContent.querySelector('.service-modal__body')
        const isScrolledToTop = modalBody.scrollTop === 0

        if (deltaY > 0 && isScrolledToTop) {
          e.preventDefault()
          // Применяем трансформацию только если свайп вниз
          const translateY = Math.min(deltaY * 0.5, 100)
          modalContent.style.transform = `translateY(${translateY}px)`

          // Изменяем прозрачность фона
          const opacity = Math.max(0.8 - (deltaY / 300), 0.3)
          modal.style.background = `rgba(0, 0, 0, ${opacity})`
        }
      }

      const handleTouchEnd = (e) => {
        if (!isDragging) return

        isDragging = false
        modalContent.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)'

        const deltaY = currentY - startY

        // Если свайп достаточно большой, закрываем модальное окно
        if (deltaY > 100) {
          modalContent.style.transform = 'translateY(100vh)'
          setTimeout(() => {
            this.closeModal()
          }, 300)
        } else {
          // Возвращаем в исходную позицию
          modalContent.style.transform = 'translateY(0)'
          modal.style.background = 'rgba(0, 0, 0, 0.8)'
        }
      }

      // Добавляем обработчики событий
      modalContent.addEventListener('touchstart', handleTouchStart, { passive: false })
      modalContent.addEventListener('touchmove', handleTouchMove, { passive: false })
      modalContent.addEventListener('touchend', handleTouchEnd, { passive: false })

      // Сохраняем ссылки на обработчики для последующего удаления
      this.modalTouchHandlers = {
        touchstart: handleTouchStart,
        touchmove: handleTouchMove,
        touchend: handleTouchEnd,
        modalContent
      }
    },

    // Удаляем обработчики свайпа
    removeMobileModalInteractions() {
      if (this.modalTouchHandlers) {
        const { touchstart, touchmove, touchend, modalContent } = this.modalTouchHandlers

        if (modalContent) {
          modalContent.removeEventListener('touchstart', touchstart)
          modalContent.removeEventListener('touchmove', touchmove)
          modalContent.removeEventListener('touchend', touchend)
        }

        this.modalTouchHandlers = null
      }
    },

    // Метод для обработки изменений URL параметров
    handleRouteChange() {
      const urlParams = new URLSearchParams(window.location.search)
      const filterParam = urlParams.get('filter')

      if (filterParam && this.filterTabs.some(tab => tab.id === filterParam)) {
        this.activeFilter = filterParam
      } else {
        this.activeFilter = 'all'
      }
    }
  },
  // Добавляем watch для отслеживания изменений маршрута
  watch: {
    '$route'() {
      this.handleRouteChange()
    }
  },
  mounted() {
    // Handle escape key for modal
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.selectedService) {
        this.closeModal()
      }
    })

    // Добавляем обработчик для закрытия модального окна по клику на задний фон
    document.addEventListener('click', (e) => {
      if (this.selectedService && e.target.classList.contains('service-modal')) {
        this.closeModal()
      }
    })

    // Check for filter parameter in URL
    this.handleRouteChange()

    // Setup mobile scroll handlers
    this.setupMobileScrollHandlers()

    // Scroll active tab into view on initial load
    this.$nextTick(() => {
      this.scrollActiveTabIntoView()
    })
  },

  beforeUnmount() {
    document.body.style.overflow = 'auto'

    // Clean up event listeners
    const tabsContainer = document.querySelector('.filter-tabs')
    if (tabsContainer) {
      tabsContainer.removeEventListener('scroll', this.handleTabsScroll)
    }
    window.removeEventListener('resize', this.handleTabsScroll)

    // Убираем обработчики модального окна
    this.removeMobileModalInteractions()
  }
}
</script>

<style lang="scss" scoped>
@use '../../css/pages/services-page';
</style>
