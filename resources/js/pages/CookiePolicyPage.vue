<template>
  <div class="legal-page page-with-header-offset">
    <div class="legal-hero">
      <div class="container">
        <div class="legal-hero__content">
          <h1 class="legal-hero__title">
            <i class="fas fa-cookie-bite"></i>
            Cookie Policy
          </h1>
          <p class="legal-hero__subtitle">
            How we use cookies and similar technologies on our website
          </p>
          <div class="legal-hero__meta">
            <span class="legal-hero__date">
              <i class="fas fa-calendar-alt"></i>
              Last updated: {{ lastUpdated }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="legal-content">
      <div class="container">
        <div class="legal-content__wrapper">
          <div class="legal-sidebar">
            <div class="legal-toc">
              <h3 class="legal-toc__title">Table of Contents</h3>
              <ul class="legal-toc__list">
                <li><a href="#what-are-cookies" class="legal-toc__link">What Are Cookies</a></li>
                <li><a href="#types-cookies" class="legal-toc__link">Types of Cookies</a></li>
                <li><a href="#cookies-we-use" class="legal-toc__link">Cookies We Use</a></li>
                <li><a href="#third-party-cookies" class="legal-toc__link">Third-Party Cookies</a></li>
                <li><a href="#managing-cookies" class="legal-toc__link">Managing Cookies</a></li>
                <li><a href="#contact" class="legal-toc__link">Contact Us</a></li>
              </ul>
            </div>
          </div>

          <div class="legal-main">
            <div class="legal-section">
              <p class="legal-intro">
                This Cookie Policy explains how Darkheim Development Studio uses cookies and similar technologies when you visit our website. We want to ensure you understand what cookies are, how we use them, and how you can manage your preferences.
              </p>
            </div>

            <section id="what-are-cookies" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-question-circle"></i>
                What Are Cookies?
              </h2>
              <p>Cookies are small text files that are stored on your device (computer, tablet, or mobile) when you visit a website. They are widely used to make websites work more efficiently and to provide information to website owners.</p>

              <h3 class="legal-subsection__title">How Cookies Work</h3>
              <p>When you visit our website, we may place cookies on your device to:</p>
              <ul class="legal-list">
                <li>Remember your preferences and settings</li>
                <li>Analyze how you use our website</li>
                <li>Improve your browsing experience</li>
                <li>Provide personalized content</li>
                <li>Ensure website security and functionality</li>
              </ul>

              <h3 class="legal-subsection__title">Session vs Persistent Cookies</h3>
              <div class="cookie-types">
                <div class="cookie-type">
                  <h4><i class="fas fa-clock"></i> Session Cookies</h4>
                  <p>Temporary cookies that are deleted when you close your browser. They help us track your movement through our site during a single session.</p>
                </div>
                <div class="cookie-type">
                  <h4><i class="fas fa-save"></i> Persistent Cookies</h4>
                  <p>Remain on your device for a set period or until you delete them. They remember your preferences across multiple visits.</p>
                </div>
              </div>
            </section>

            <section id="types-cookies" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-layer-group"></i>
                Types of Cookies
              </h2>

              <div class="cookie-categories">
                <div class="cookie-category">
                  <div class="cookie-category__header">
                    <i class="fas fa-shield-check"></i>
                    <h3>Essential Cookies</h3>
                    <span class="cookie-status required">Required</span>
                  </div>
                  <p>These cookies are necessary for the website to function properly. They enable basic functions like page navigation and access to secure areas.</p>
                  <ul class="legal-list">
                    <li>Authentication and security</li>
                    <li>Form submission and validation</li>
                    <li>Load balancing and performance</li>
                    <li>Basic website functionality</li>
                  </ul>
                </div>

                <div class="cookie-category">
                  <div class="cookie-category__header">
                    <i class="fas fa-chart-line"></i>
                    <h3>Analytics Cookies</h3>
                    <span class="cookie-status optional">Optional</span>
                  </div>
                  <p>Help us understand how visitors interact with our website by collecting and reporting information anonymously.</p>
                  <ul class="legal-list">
                    <li>Page views and traffic sources</li>
                    <li>Time spent on pages</li>
                    <li>User journey and behavior</li>
                    <li>Website performance metrics</li>
                  </ul>
                </div>

                <div class="cookie-category">
                  <div class="cookie-category__header">
                    <i class="fas fa-cog"></i>
                    <h3>Functional Cookies</h3>
                    <span class="cookie-status optional">Optional</span>
                  </div>
                  <p>Enable enhanced functionality and personalization, such as remembering your preferences and settings.</p>
                  <ul class="legal-list">
                    <li>Language and region preferences</li>
                    <li>Theme and display settings</li>
                    <li>Form data and user choices</li>
                    <li>Accessibility options</li>
                  </ul>
                </div>

                <div class="cookie-category">
                  <div class="cookie-category__header">
                    <i class="fas fa-bullhorn"></i>
                    <h3>Marketing Cookies</h3>
                    <span class="cookie-status optional">Optional</span>
                  </div>
                  <p>Used to track visitors across websites to display relevant advertisements and measure campaign effectiveness.</p>
                  <ul class="legal-list">
                    <li>Targeted advertising</li>
                    <li>Campaign performance tracking</li>
                    <li>Social media integration</li>
                    <li>Retargeting and remarketing</li>
                  </ul>
                </div>
              </div>
            </section>

            <section id="cookies-we-use" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-list"></i>
                Specific Cookies We Use
              </h2>

              <div class="cookie-table">
                <div class="cookie-table__header">
                  <span>Cookie Name</span>
                  <span>Purpose</span>
                  <span>Duration</span>
                  <span>Type</span>
                </div>

                <div class="cookie-table__row">
                  <span class="cookie-name">darkheim_session</span>
                  <span>Maintains user session and security</span>
                  <span>Session</span>
                  <span class="cookie-type-essential">Essential</span>
                </div>

                <div class="cookie-table__row">
                  <span class="cookie-name">csrf_token</span>
                  <span>Prevents cross-site request forgery attacks</span>
                  <span>Session</span>
                  <span class="cookie-type-essential">Essential</span>
                </div>

                <div class="cookie-table__row">
                  <span class="cookie-name">user_preferences</span>
                  <span>Stores theme, language, and display preferences</span>
                  <span>1 year</span>
                  <span class="cookie-type-functional">Functional</span>
                </div>

                <div class="cookie-table__row">
                  <span class="cookie-name">_ga</span>
                  <span>Google Analytics - distinguishes unique users</span>
                  <span>2 years</span>
                  <span class="cookie-type-analytics">Analytics</span>
                </div>

                <div class="cookie-table__row">
                  <span class="cookie-name">_gid</span>
                  <span>Google Analytics - distinguishes unique users</span>
                  <span>24 hours</span>
                  <span class="cookie-type-analytics">Analytics</span>
                </div>

                <div class="cookie-table__row">
                  <span class="cookie-name">_gat</span>
                  <span>Google Analytics - throttles request rate</span>
                  <span>1 minute</span>
                  <span class="cookie-type-analytics">Analytics</span>
                </div>
              </div>
            </section>

            <section id="third-party-cookies" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-external-link-alt"></i>
                Third-Party Cookies
              </h2>
              <p>We may use third-party services that set their own cookies. These services include:</p>

              <div class="third-party-services">
                <div class="third-party-service">
                  <div class="service-header">
                    <i class="fab fa-google"></i>
                    <h3>Google Analytics</h3>
                  </div>
                  <p>Helps us understand how visitors use our website through anonymous data collection.</p>
                  <a href="https://policies.google.com/privacy" target="_blank" rel="noopener" class="service-link">
                    <i class="fas fa-external-link-alt"></i>
                    Google Privacy Policy
                  </a>
                </div>

                <div class="third-party-service">
                  <div class="service-header">
                    <i class="fab fa-font-awesome"></i>
                    <h3>Font Awesome</h3>
                  </div>
                  <p>Provides icons used throughout our website for enhanced visual experience.</p>
                  <a href="https://fontawesome.com/privacy" target="_blank" rel="noopener" class="service-link">
                    <i class="fas fa-external-link-alt"></i>
                    Font Awesome Privacy Policy
                  </a>
                </div>

                <div class="third-party-service">
                  <div class="service-header">
                    <i class="fab fa-cloudflare"></i>
                    <h3>Cloudflare</h3>
                  </div>
                  <p>Provides security, performance optimization, and content delivery services.</p>
                  <a href="https://www.cloudflare.com/privacypolicy/" target="_blank" rel="noopener" class="service-link">
                    <i class="fas fa-external-link-alt"></i>
                    Cloudflare Privacy Policy
                  </a>
                </div>
              </div>
            </section>

            <section id="managing-cookies" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-sliders-h"></i>
                Managing Your Cookie Preferences
              </h2>

              <h3 class="legal-subsection__title">Browser Settings</h3>
              <p>You can control and manage cookies through your browser settings. Most browsers allow you to:</p>
              <ul class="legal-list">
                <li>View cookies stored on your device</li>
                <li>Delete existing cookies</li>
                <li>Block cookies from specific sites</li>
                <li>Block all cookies (may affect functionality)</li>
                <li>Set preferences for different types of cookies</li>
              </ul>

              <div class="browser-guides">
                <h3 class="legal-subsection__title">Browser-Specific Instructions</h3>
                <div class="browser-links">
                  <a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener" class="browser-link">
                    <i class="fab fa-chrome"></i>
                    Chrome
                  </a>
                  <a href="https://support.mozilla.org/en-US/kb/enhanced-tracking-protection-firefox-desktop" target="_blank" rel="noopener" class="browser-link">
                    <i class="fab fa-firefox"></i>
                    Firefox
                  </a>
                  <a href="https://support.apple.com/guide/safari/manage-cookies-and-website-data-sfri11471/mac" target="_blank" rel="noopener" class="browser-link">
                    <i class="fab fa-safari"></i>
                    Safari
                  </a>
                  <a href="https://support.microsoft.com/en-us/microsoft-edge/delete-cookies-in-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank" rel="noopener" class="browser-link">
                    <i class="fab fa-edge"></i>
                    Edge
                  </a>
                </div>
              </div>

              <div class="legal-note">
                <p><strong>Important:</strong> Disabling certain cookies may impact your experience on our website. Essential cookies cannot be disabled as they are required for basic functionality.</p>
              </div>

              <h3 class="legal-subsection__title">Opt-Out Tools</h3>
              <p>You can also use these industry tools to manage tracking:</p>
              <ul class="legal-list">
                <li><a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener">Google Analytics Opt-out Browser Add-on</a></li>
                <li><a href="http://optout.aboutads.info/" target="_blank" rel="noopener">Digital Advertising Alliance Opt-Out</a></li>
                <li><a href="http://optout.networkadvertising.org/" target="_blank" rel="noopener">Network Advertising Initiative Opt-Out</a></li>
              </ul>
            </section>

            <section id="contact" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-envelope"></i>
                Contact Information
              </h2>
              <p>If you have any questions about our use of cookies, please contact us:</p>

              <div class="legal-contact">
                <div v-for="contact in contactInfo" :key="contact.key" class="legal-contact__item">
                  <strong>{{ contact.label }}:</strong>
                  <a
                    v-if="contact.type === 'email'"
                    :href="`mailto:${contact.value}`"
                    class="legal-contact__link"
                  >
                    {{ contact.value }}
                  </a>
                  <a
                    v-else-if="contact.type === 'phone'"
                    :href="`tel:${contact.value}`"
                    class="legal-contact__link"
                  >
                    {{ contact.value }}
                  </a>
                  <span v-else class="legal-contact__text">{{ contact.value }}</span>
                </div>
              </div>

              <div class="legal-note">
                <p><strong>Updates to this Policy:</strong> We may update this Cookie Policy to reflect changes in our practices or for legal reasons. Please check this page regularly for updates.</p>
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'CookiePolicyPage',
  data() {
    return {
      lastUpdated: 'September 3, 2025',
      contactInfo: []
    }
  },
  async mounted() {
    // Smooth scrolling for anchor links
    this.initSmoothScrolling()
    // Load contact info from API
    await this.loadContactInfo()
  },
  methods: {
    async loadContactInfo() {
      try {
        const response = await axios.get('/api/contact-info')
        if (response.data.success) {
          this.contactInfo = response.data.data.contacts || []
        }
      } catch (error) {
        console.error('Error loading contact info:', error)
        // Fallback to default values if API fails
        this.setDefaultContactInfo()
      }
    },
    setDefaultContactInfo() {
      this.contactInfo = [
        { key: 'email', label: 'Email', value: 'privacy@darkheim.studio', type: 'email' },
        { key: 'generalContact', label: 'General Contact', value: 'darkheim.studio@gmail.com', type: 'email' },
        { key: 'address', label: 'Address', value: 'San Francisco, CA', type: 'text' }
      ]
    },
    initSmoothScrolling() {
      const links = document.querySelectorAll('.legal-toc__link')
      links.forEach(link => {
        link.addEventListener('click', (e) => {
          e.preventDefault()
          const targetId = link.getAttribute('href')
          const targetElement = document.querySelector(targetId)
          if (targetElement) {
            targetElement.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            })
          }
        })
      })
    }
  }
}
</script>

<style lang="scss" scoped>
// Base variables and styles (same as other legal pages)
$primary-color: #3498db;
$primary-hover: #2980b9;
$accent-color: #e74c3c;
$success-color: #27ae60;
$warning-color: #f39c12;
$text-light: #ffffff;
$text-muted: #bdc3c7;
$text-dark: #34495e;
$text-gray: #7f8c8d;
$bg-dark: #2c3e50;
$bg-darker: #1a252f;
$bg-light: #ecf0f1;
$bg-white: #ffffff;
$border-light: #e1e8ed;
$shadow-light: rgba(0, 0, 0, 0.1);
$shadow-medium: rgba(0, 0, 0, 0.15);
$transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
$border-radius: 12px;

// Import common legal page styles
.legal-page {
  min-height: 100vh;
  background: $bg-light;
}

.legal-hero {
  background: linear-gradient(135deg, $bg-dark 0%, $bg-darker 100%);
  color: $text-light;
  padding: 6rem 0 4rem;
  position: relative;

  // Mobile header spacing fix - increased padding
  @media (max-width: 768px) {
    padding: 10rem 0 3rem; // Increased from 8rem to 10rem for mobile header
  }

  @media (max-width: 480px) {
    padding: 9rem 0 2.5rem; // Increased from 7rem to 9rem for smaller mobile screens
  }

  @media (max-width: 360px) {
    padding: 8.5rem 0 2rem; // Additional spacing for very small screens
  }

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="%23ffffff" opacity="0.05"><polygon points="1000,100 1000,0 0,100"/></svg>');
    background-size: cover;
  }

  .container {
    position: relative;
    z-index: 2;
  }

  &__content {
    max-width: 800px;
    text-align: center;
    margin: 0 auto;
  }

  &__title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;

    @media (max-width: 768px) {
      font-size: 2.5rem;
      flex-direction: column;
      gap: 0.5rem;
    }

    i {
      color: $primary-color;
      font-size: 3rem;

      @media (max-width: 768px) {
        font-size: 2rem;
      }
    }
  }

  &__subtitle {
    font-size: 1.3rem;
    color: $text-muted;
    margin-bottom: 2rem;
    line-height: 1.6;

    @media (max-width: 768px) {
      font-size: 1.1rem;
    }
  }

  &__meta {
    display: flex;
    justify-content: center;
    gap: 2rem;

    @media (max-width: 768px) {
      flex-direction: column;
      gap: 1rem;
    }
  }

  &__date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: $text-muted;
    font-size: 0.9rem;

    i {
      color: $primary-color;
    }
  }
}

.legal-content {
  padding: 4rem 0;

  .container {
    max-width: 1400px;
  }

  &__wrapper {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 3rem;

    @media (max-width: 1024px) {
      grid-template-columns: 1fr;
      gap: 2rem;
    }
  }
}

.legal-sidebar {
  @media (max-width: 1024px) {
    order: 2;
  }
}

.legal-toc {
  background: $bg-white;
  border-radius: $border-radius;
  padding: 2rem;
  box-shadow: 0 4px 20px $shadow-light;
  position: sticky;
  top: 2rem;

  @media (max-width: 1024px) {
    position: static;
  }

  &__title {
    font-size: 1.2rem;
    font-weight: 600;
    color: $text-dark;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid $primary-color;
  }

  &__list {
    list-style: none;
    margin: 0;
    padding: 0;
  }

  &__link {
    display: block;
    padding: 0.75rem 0;
    color: $text-gray;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: $transition;
    border-left: 3px solid transparent;
    padding-left: 1rem;
    margin-left: -1rem;

    &:hover {
      color: $primary-color;
      border-left-color: $primary-color;
      background: rgba(52, 152, 219, 0.05);
    }
  }
}

.legal-main {
  background: $bg-white;
  border-radius: $border-radius;
  padding: 3rem;
  box-shadow: 0 4px 20px $shadow-light;

  @media (max-width: 768px) {
    padding: 2rem;
  }

  @media (max-width: 1024px) {
    order: 1;
  }
}

.legal-intro {
  font-size: 1.1rem;
  line-height: 1.7;
  color: $text-gray;
  margin-bottom: 3rem;
  padding: 1.5rem;
  background: rgba(52, 152, 219, 0.05);
  border-left: 4px solid $primary-color;
  border-radius: 0 8px 8px 0;
}

.legal-section {
  margin-bottom: 3rem;

  &:last-child {
    margin-bottom: 0;
  }

  &__title {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 2rem;
    font-weight: 600;
    color: $text-dark;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid $border-light;

    i {
      color: $primary-color;
      font-size: 1.5rem;
    }

    @media (max-width: 768px) {
      font-size: 1.6rem;
    }
  }

  p {
    line-height: 1.7;
    color: $text-gray;
    margin-bottom: 1.5rem;
    font-size: 1rem;
  }
}

.legal-subsection__title {
  font-size: 1.3rem;
  font-weight: 600;
  color: $text-dark;
  margin: 2rem 0 1rem;
}

.legal-list {
  list-style: none;
  margin: 1.5rem 0;
  padding: 0;

  li {
    position: relative;
    padding: 0.5rem 0 0.5rem 2rem;
    color: $text-gray;
    line-height: 1.6;

    &::before {
      content: '▸';
      position: absolute;
      left: 0;
      top: 0.5rem;
      color: $primary-color;
      font-weight: 600;
    }

    strong {
      color: $text-dark;
    }
  }
}

.legal-note {
  background: rgba(231, 76, 60, 0.05);
  border: 1px solid rgba(231, 76, 60, 0.2);
  border-radius: 8px;
  padding: 1.5rem;
  margin: 2rem 0;

  p {
    margin: 0;
    color: $text-dark;
    font-weight: 500;
  }
}

.legal-contact {
  background: $bg-light;
  border-radius: 8px;
  padding: 2rem;
  margin: 2rem 0;

  &__item {
    margin-bottom: 1rem;
    font-size: 1rem;
    color: $text-gray;

    &:last-child {
      margin-bottom: 0;
    }

    strong {
      color: $text-dark;
      display: inline-block;
      min-width: 120px;
    }
  }
}

// Cookie-specific styles
.cookie-types {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  margin: 2rem 0;

  @media (max-width: 768px) {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
}

.cookie-type {
  background: $bg-light;
  border-radius: 8px;
  padding: 1.5rem;

  h4 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: $text-dark;
    margin-bottom: 1rem;

    i {
      color: $primary-color;
    }
  }

  p {
    margin: 0;
    color: $text-gray;
    line-height: 1.6;
  }
}

.cookie-categories {
  display: grid;
  gap: 2rem;
  margin: 2rem 0;
}

.cookie-category {
  background: $bg-light;
  border-radius: $border-radius;
  padding: 2rem;
  border-left: 4px solid $primary-color;

  &__header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;

    i {
      color: $primary-color;
      font-size: 1.5rem;
    }

    h3 {
      color: $text-dark;
      margin: 0;
      flex-grow: 1;
    }
  }

  p {
    color: $text-gray;
    line-height: 1.6;
    margin-bottom: 1rem;
  }
}

.cookie-status {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;

  &.required {
    background: rgba(231, 76, 60, 0.1);
    color: $accent-color;
    border: 1px solid rgba(231, 76, 60, 0.3);
  }

  &.optional {
    background: rgba(39, 174, 96, 0.1);
    color: $success-color;
    border: 1px solid rgba(39, 174, 96, 0.3);
  }
}

.cookie-table {
  background: $bg-white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 10px $shadow-light;
  margin: 2rem 0;

  &__header {
    display: grid;
    grid-template-columns: 2fr 3fr 1fr 1fr;
    gap: 1rem;
    padding: 1rem 1.5rem;
    background: $primary-color;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;

    @media (max-width: 768px) {
      grid-template-columns: 1fr;
      gap: 0.5rem;
    }
  }

  &__row {
    display: grid;
    grid-template-columns: 2fr 3fr 1fr 1fr;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid $border-light;
    align-items: center;

    @media (max-width: 768px) {
      grid-template-columns: 1fr;
      gap: 0.5rem;
    }

    &:last-child {
      border-bottom: none;
    }

    &:nth-child(even) {
      background: rgba(52, 152, 219, 0.02);
    }
  }
}

.cookie-name {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
  font-size: 0.85rem;
  background: rgba(52, 152, 219, 0.1);
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  color: $text-dark;
  font-weight: 600;
}

.cookie-type-essential {
  background: rgba(231, 76, 60, 0.1);
  color: $accent-color;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: 600;
  text-align: center;
}

.cookie-type-analytics {
  background: rgba(52, 152, 219, 0.1);
  color: $primary-color;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: 600;
  text-align: center;
}

.cookie-type-functional {
  background: rgba(39, 174, 96, 0.1);
  color: $success-color;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: 600;
  text-align: center;
}

.third-party-services {
  display: grid;
  gap: 1.5rem;
  margin: 2rem 0;
}

.third-party-service {
  background: $bg-light;
  border-radius: 8px;
  padding: 1.5rem;

  .service-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;

    i {
      color: $primary-color;
      font-size: 1.5rem;
    }

    h3 {
      color: $text-dark;
      margin: 0;
    }
  }

  p {
    color: $text-gray;
    margin-bottom: 1rem;
  }

  .service-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: $primary-color;
    text-decoration: none;
    font-weight: 500;
    transition: $transition;

    &:hover {
      color: $primary-hover;
      text-decoration: underline;
    }

    i {
      font-size: 0.8rem;
    }
  }
}

.browser-guides {
  margin: 2rem 0;
}

.browser-links {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
}

.browser-link {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem;
  background: $bg-light;
  border-radius: 8px;
  text-decoration: none;
  color: $text-dark;
  transition: $transition;

  &:hover {
    background: rgba(52, 152, 219, 0.1);
    color: $primary-color;
    transform: translateY(-2px);
  }

  i {
    font-size: 2rem;
    color: $primary-color;
  }
}

// Responsive
@media (max-width: 768px) {
  .legal-hero {
    padding: 4rem 0 3rem;
  }

  .legal-content {
    padding: 2rem 0;
  }

  .legal-toc {
    padding: 1.5rem;
  }
}
</style>
