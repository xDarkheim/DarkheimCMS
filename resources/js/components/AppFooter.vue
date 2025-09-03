<template>
  <footer class="footer">
    <div class="container">
      <!-- Main Footer Content -->
      <div class="footer__content">
        <!-- Company Section -->
        <div class="footer__section footer__section--main">
          <div class="footer__brand">
            <div class="footer__logo">
              <div class="footer__logo-icon">
                <i class="fas fa-terminal"></i>
              </div>
              <span class="footer__logo-text">Darkheim</span>
            </div>
            <p class="footer__description">
              {{ companyDescription || 'We create exceptional digital experiences that drive business growth and user engagement.' }}
            </p>
          </div>

          <div class="footer__social">
            <h4 class="footer__social-title">Follow Us</h4>
            <div class="footer__social-links">
              <a
                v-for="social in socialLinks"
                :key="social.key"
                :href="social.value"
                class="footer__social-link"
                :aria-label="social.label"
                :title="`Follow us on ${social.label}`"
                target="_blank"
                rel="noopener noreferrer"
              >
                <i :class="social.icon"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Services Section -->
        <div class="footer__section">
          <h3 class="footer__section-title">
            <i class="fas fa-laptop-code"></i>
            Services
          </h3>
          <ul class="footer__links">
            <li>
              <router-link to="/services?filter=web-development" class="footer__link">
                <i class="fas fa-code"></i>
                <span>Web Development</span>
              </router-link>
            </li>
            <li>
              <router-link to="/services?filter=ui-ux-design" class="footer__link">
                <i class="fas fa-palette"></i>
                <span>UI/UX Design</span>
              </router-link>
            </li>
            <li>
              <router-link to="/services?filter=mobile-apps" class="footer__link">
                <i class="fas fa-mobile-alt"></i>
                <span>Mobile Apps</span>
              </router-link>
            </li>
            <li>
              <router-link to="/services?filter=consulting" class="footer__link">
                <i class="fas fa-lightbulb"></i>
                <span>Consulting</span>
              </router-link>
            </li>
            <li>
              <router-link to="/services?filter=support" class="footer__link">
                <i class="fas fa-headset"></i>
                <span>Support & Maintenance</span>
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Portfolio Section -->
        <div class="footer__section">
          <h3 class="footer__section-title">
            <i class="fas fa-folder-open"></i>
            Portfolio
          </h3>
          <ul class="footer__links">
            <li>
              <router-link to="/portfolio" class="footer__link">
                <i class="fas fa-th-large"></i>
                <span>All Projects</span>
              </router-link>
            </li>
            <li>
              <router-link to="/portfolio?category=web-development" class="footer__link">
                <i class="fas fa-code"></i>
                <span>Web Development</span>
              </router-link>
            </li>
            <li>
              <router-link to="/portfolio?category=mobile-applications" class="footer__link">
                <i class="fas fa-mobile-alt"></i>
                <span>Mobile Applications</span>
              </router-link>
            </li>
            <li>
              <router-link to="/portfolio?category=ecommerce-solutions" class="footer__link">
                <i class="fas fa-shopping-cart"></i>
                <span>E-commerce Solutions</span>
              </router-link>
            </li>
            <li>
              <router-link to="/portfolio?category=business-applications" class="footer__link">
                <i class="fas fa-briefcase"></i>
                <span>Business Applications</span>
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Company Section -->
        <div class="footer__section">
          <h3 class="footer__section-title">
            <i class="fas fa-building"></i>
            Company
          </h3>
          <ul class="footer__links">
            <li>
              <router-link to="/about" class="footer__link">
                <i class="fas fa-info-circle"></i>
                <span>About Us</span>
              </router-link>
            </li>
            <li>
              <router-link to="/team" class="footer__link">
                <i class="fas fa-users"></i>
                <span>Our Team</span>
              </router-link>
            </li>
            <li>
              <router-link to="/careers" class="footer__link">
                <i class="fas fa-briefcase"></i>
                <span>Careers</span>
              </router-link>
            </li>
            <li>
              <router-link to="/news" class="footer__link">
                <i class="fas fa-newspaper"></i>
                <span>News & Blog</span>
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Contact Section -->
        <div class="footer__section">
          <h3 class="footer__section-title">
            <i class="fas fa-paper-plane"></i>
            Get In Touch
          </h3>
          <div class="footer__contact">
            <div
              v-for="contact in contactInfo"
              :key="contact.key"
              class="footer__contact-item"
            >
              <i :class="contact.icon"></i>
              <a
                v-if="contact.type === 'email'"
                :href="`mailto:${contact.value}`"
                class="footer__contact-link"
              >
                {{ contact.value }}
              </a>
              <a
                v-else-if="contact.type === 'phone'"
                :href="`tel:${contact.value}`"
                class="footer__contact-link"
              >
                {{ contact.value }}
              </a>
              <span v-else class="footer__contact-text">{{ contact.value }}</span>
            </div>
          </div>

          <router-link to="/contact" class="footer__cta-btn">
            <span>Start Your Project</span>
            <i class="fas fa-arrow-right"></i>
          </router-link>
        </div>
      </div>

      <!-- Bottom Section -->
      <div class="footer__bottom">
        <div class="footer__bottom-left">
          <div class="footer__copyright">
            <i class="far fa-copyright"></i>
            <span>{{ currentYear }} Darkheim. All rights reserved.</span>
          </div>
          <div class="footer__creator">
            <span>Developed by</span>
            <a href="https://hovenko.com/" target="_blank" rel="noopener noreferrer" class="footer__creator-link">
              Dmytro Hovenko
            </a>
          </div>
        </div>

        <div class="footer__bottom-center">
          <div class="footer__legal-links">
            <router-link to="/privacy-policy" class="footer__legal-link">Privacy Policy</router-link>
            <router-link to="/terms-of-service" class="footer__legal-link">Terms of Service</router-link>
            <router-link to="/cookie-policy" class="footer__legal-link">Cookie Policy</router-link>
          </div>
        </div>

        <div class="footer__bottom-right">
          <div class="footer__back-to-top" @click="scrollToTop">
            <i class="fas fa-chevron-up"></i>
            <span>Back to Top</span>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
import axios from 'axios'

export default {
  name: 'AppFooter',
  data() {
    return {
      newsletterEmail: '',
      isSubscribing: false,
      contactInfo: [],
      socialLinks: [],
      companyDescription: ''
    }
  },
  computed: {
    currentYear() {
      return new Date().getFullYear()
    }
  },
  async mounted() {
    await this.loadCompanyInfo()
  },
  methods: {
    async loadCompanyInfo() {
      try {
        const response = await axios.get('/api/company-info')
        if (response.data.success) {
          this.contactInfo = response.data.data.contact_info || []
          this.socialLinks = response.data.data.social_links || []

          // Find company description
          const description = this.contactInfo.find(item => item.key === 'company_description')
          if (description) {
            this.companyDescription = description.value
          }
        }
      } catch (error) {
        console.error('Error loading company info:', error)
        // Fallback to default values if API fails
        this.setDefaultValues()
      }
    },
    setDefaultValues() {
      this.contactInfo = [
        {
          key: 'company_email',
          value: 'darkheim.studio@gmail.com',
          type: 'email',
          icon: 'fas fa-envelope'
        },
        {
          key: 'company_phone',
          value: '+1 (555) 123-4567',
          type: 'phone',
          icon: 'fas fa-phone'
        },
        {
          key: 'company_address',
          value: 'San Francisco, CA',
          type: 'address',
          icon: 'fas fa-map-marker-alt'
        }
      ]

      this.socialLinks = [
        { key: 'github', label: 'GitHub', value: '#', icon: 'fab fa-github' },
        { key: 'linkedin', label: 'LinkedIn', value: '#', icon: 'fab fa-linkedin' },
        { key: 'twitter', label: 'Twitter', value: '#', icon: 'fab fa-twitter' },
        { key: 'instagram', label: 'Instagram', value: '#', icon: 'fab fa-instagram' },
        { key: 'discord', label: 'Discord', value: '#', icon: 'fab fa-discord' },
        { key: 'youtube', label: 'YouTube', value: '#', icon: 'fab fa-youtube' }
      ]
    },
    subscribeNewsletter() {
      this.isSubscribing = true;
      // Simulate an API call
      setTimeout(() => {
        this.isSubscribing = false;
        this.newsletterEmail = '';
        alert('Subscribed successfully!');
      }, 2000);
    },
    scrollToTop() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }
  }
}
</script>

<style lang="scss" scoped>
// Variables
$primary-color: #3498db;
$primary-hover: #2980b9;
$accent-color: #e74c3c;
$text-light: #ffffff;
$text-muted: #bdc3c7;
$text-dark: #34495e;
$bg-dark: #2c3e50;
$bg-darker: #1a252f;
$bg-darkest: #0f1419;
$shadow-light: rgba(0, 0, 0, 0.1);
$shadow-medium: rgba(0, 0, 0, 0.2);
$shadow-dark: rgba(0, 0, 0, 0.3);
$transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
$border-radius: 12px;

.footer {
  background: linear-gradient(180deg, $bg-dark 0%, $bg-darker 50%, $bg-darkest 100%);
  color: $text-light;
  position: relative;
  overflow: hidden;

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, $primary-color, transparent);
  }

  .container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
  }
}

// Main Footer Content
.footer__content {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
  gap: 2.5rem;
  padding: 4rem 0;

  @media (max-width: 1200px) {
    grid-template-columns: 2fr 1fr 1fr;
    gap: 2rem;

    .footer__section:nth-child(4),
    .footer__section:nth-child(5) {
      grid-column: span 1;
    }
  }

  @media (max-width: 768px) {
    grid-template-columns: 1fr;
    gap: 2rem;
    padding: 3rem 0;
  }
}

.footer__section {
  &--main {
    .footer__brand {
      margin-bottom: 2rem;
    }
  }
}

// Brand Section
.footer__logo {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1.5rem;

  &-icon {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, $primary-color, $primary-hover);
    border-radius: $border-radius;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: white;
    box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
  }

  &-text {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, $primary-color, #9b59b6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
}

.footer__description {
  color: $text-muted;
  line-height: 1.6;
  margin-bottom: 2rem;
  font-size: 0.95rem;
}

// Social Section
.footer__social {
  &-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: $text-light;
  }

  &-links {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
    max-width: 200px;

    @media (max-width: 768px) {
      grid-template-columns: repeat(6, 1fr);
      max-width: 100%;
      justify-items: center;
    }
  }

  &-link {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    color: $text-muted;
    text-decoration: none;
    transition: $transition;
    border: 1px solid rgba(255, 255, 255, 0.1);

    // Specific social media colors on hover
    &:hover {
      transform: translateY(-3px) scale(1.05);
      border-color: currentColor;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    // GitHub
    &:nth-child(1):hover {
      background: #333;
      color: #fff;
      box-shadow: 0 8px 25px rgba(51, 51, 51, 0.4);
    }

    // LinkedIn
    &:nth-child(2):hover {
      background: #0077b5;
      color: #fff;
      box-shadow: 0 8px 25px rgba(0, 119, 181, 0.4);
    }

    // Twitter
    &:nth-child(3):hover {
      background: #1da1f2;
      color: #fff;
      box-shadow: 0 8px 25px rgba(29, 161, 242, 0.4);
    }

    // Instagram
    &:nth-child(4):hover {
      background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
      color: #fff;
      box-shadow: 0 8px 25px rgba(240, 148, 51, 0.4);
    }

    // Discord
    &:nth-child(5):hover {
      background: #5865f2;
      color: #fff;
      box-shadow: 0 8px 25px rgba(88, 101, 242, 0.4);
    }

    // YouTube
    &:nth-child(6):hover {
      background: #ff0000;
      color: #fff;
      box-shadow: 0 8px 25px rgba(255, 0, 0, 0.4);
    }

    i {
      font-size: 1.25rem;
      transition: $transition;
    }

    // Tooltip on hover
    &::after {
      content: attr(title);
      position: absolute;
      bottom: 120%;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(0, 0, 0, 0.9);
      color: white;
      padding: 0.5rem 0.75rem;
      border-radius: 6px;
      font-size: 0.8rem;
      white-space: nowrap;
      opacity: 0;
      visibility: hidden;
      transition: $transition;
      z-index: 10;
    }

    &::before {
      content: '';
      position: absolute;
      bottom: 105%;
      left: 50%;
      transform: translateX(-50%);
      border-left: 5px solid transparent;
      border-right: 5px solid transparent;
      border-top: 5px solid rgba(0, 0, 0, 0.9);
      opacity: 0;
      visibility: hidden;
      transition: $transition;
      z-index: 10;
    }

    &:hover::after,
    &:hover::before {
      opacity: 1;
      visibility: visible;
    }
  }
}

// Section Titles
.footer__section-title {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: $text-light;

  i {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, $primary-color, $primary-hover);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    color: white;
  }
}

// Links
.footer__links {
  list-style: none;
  margin: 0;
  padding: 0;

  li {
    margin-bottom: 0.75rem;
  }
}

.footer__link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 0;
  color: $text-muted;
  text-decoration: none;
  transition: $transition;
  border-radius: 6px;

  &:hover {
    color: $primary-color;
    transform: translateX(8px);
  }

  i {
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    color: $primary-color;
    background: rgba(52, 152, 219, 0.1);
    border-radius: 4px;
  }

  span {
    font-size: 0.9rem;
    font-weight: 500;
  }
}

// Contact Section
.footer__contact {
  margin-bottom: 2rem;

  &-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    color: $text-muted;
    font-size: 0.9rem;

    i {
      width: 24px;
      height: 24px;
      background: rgba(52, 152, 219, 0.1);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: $primary-color;
      font-size: 0.8rem;
    }
  }

  &-link {
    color: $text-light;
    text-decoration: none;
    transition: $transition;
    font-weight: 500;

    &:hover {
      color: $primary-color;
      text-decoration: underline;
    }
  }

  &-text {
    color: $text-light;
    font-weight: 500;
  }
}

.footer__cta-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  background: linear-gradient(135deg, $accent-color, #c0392b);
  color: white;
  text-decoration: none;
  border-radius: 10px;
  font-weight: 600;
  transition: $transition;
  box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);

  &:hover {
    background: linear-gradient(135deg, #c0392b, $accent-color);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
  }

  i {
    font-size: 0.9rem;
  }
}

// Bottom Section
.footer__bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 2rem 0;
  border-top: 1px solid rgba(255, 255, 255, 0.1);

  @media (max-width: 768px) {
    flex-direction: column;
    gap: 1.5rem;
    text-align: center;
  }
}

.footer__copyright {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: $text-muted;
  font-size: 0.9rem;

  i {
    color: $primary-color;
  }
}

.footer__creator {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: $text-muted;
  font-size: 0.8rem;
  margin-top: 0.5rem;

  span {
    color: $text-muted;
  }
}

.footer__creator-link {
  color: $primary-color;
  text-decoration: none;
  font-weight: 500;
  transition: $transition;

  &:hover {
    color: $primary-hover;
    text-decoration: underline;
  }
}

.footer__legal-links {
  display: flex;
  gap: 2rem;

  @media (max-width: 480px) {
    flex-direction: column;
    gap: 1rem;
  }
}

.footer__legal-link {
  color: $text-muted;
  text-decoration: none;
  font-size: 0.9rem;
  transition: $transition;

  &:hover {
    color: $primary-color;
  }
}

.footer__back-to-top {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: $text-muted;
  cursor: pointer;
  transition: $transition;
  padding: 0.5rem 1rem;
  border-radius: 8px;

  &:hover {
    color: $primary-color;
    background: rgba(52, 152, 219, 0.1);
    transform: translateY(-2px);
  }

  i {
    font-size: 0.8rem;
  }

  span {
    font-size: 0.9rem;
    font-weight: 500;
  }
}

// Responsive adjustments
@media (max-width: 1024px) {
  .footer .container {
    padding: 0 1.5rem;
  }
}

@media (max-width: 768px) {
  .footer .container {
    padding: 0 1rem;
  }

  .footer__logo {
    justify-content: center;

    &-icon {
      width: 48px;
      height: 48px;
      font-size: 1.5rem;
    }

    &-text {
      font-size: 1.75rem;
    }
  }

  .footer__social-links {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .footer__content {
    padding: 2rem 0;
  }
}

// Animation for newsletter success
@keyframes bounce {
  0%, 20%, 50%, 80%, 100% {
    transform: translateY(0);
  }
  40% {
    transform: translateY(-10px);
  }
  60% {
    transform: translateY(-5px);
  }
}

.newsletter-success {
  animation: bounce 0.6s ease-in-out;
}
</style>
