<template>
  <div class="legal-page page-with-header-offset">
    <div class="legal-hero">
      <div class="container">
        <div class="legal-hero__content">
          <h1 class="legal-hero__title">
            <i class="fas fa-shield-alt"></i>
            Privacy Policy
          </h1>
          <p class="legal-hero__subtitle">
            How we collect, use, and protect your personal information
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
                <li><a href="#information-collection" class="legal-toc__link">Information We Collect</a></li>
                <li><a href="#information-use" class="legal-toc__link">How We Use Information</a></li>
                <li><a href="#information-sharing" class="legal-toc__link">Information Sharing</a></li>
                <li><a href="#data-security" class="legal-toc__link">Data Security</a></li>
                <li><a href="#cookies" class="legal-toc__link">Cookies & Tracking</a></li>
                <li><a href="#user-rights" class="legal-toc__link">Your Rights</a></li>
                <li><a href="#contact" class="legal-toc__link">Contact Us</a></li>
              </ul>
            </div>
          </div>

          <div class="legal-main">
            <div class="legal-section">
              <p class="legal-intro">
                At Darkheim Development Studio, we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or use our services.
              </p>
            </div>

            <section id="information-collection" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-database"></i>
                Information We Collect
              </h2>

              <h3 class="legal-subsection__title">Personal Information</h3>
              <p>We may collect personal information that you voluntarily provide to us, including:</p>
              <ul class="legal-list">
                <li>Name and contact information (email address, phone number)</li>
                <li>Company information and project details</li>
                <li>Messages and communications you send to us</li>
                <li>Resume and career-related information when applying for positions</li>
              </ul>

              <h3 class="legal-subsection__title">Automatically Collected Information</h3>
              <p>When you visit our website, we may automatically collect:</p>
              <ul class="legal-list">
                <li>IP address and browser information</li>
                <li>Pages visited and time spent on our site</li>
                <li>Device information and operating system</li>
                <li>Referral source and search terms used</li>
              </ul>
            </section>

            <section id="information-use" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-cogs"></i>
                How We Use Your Information
              </h2>
              <p>We use the collected information for various purposes:</p>
              <ul class="legal-list">
                <li>To provide and maintain our services</li>
                <li>To respond to your inquiries and project requests</li>
                <li>To process job applications and recruitment</li>
                <li>To improve our website and user experience</li>
                <li>To send you updates about our services (with your consent)</li>
                <li>To comply with legal obligations</li>
              </ul>
            </section>

            <section id="information-sharing" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-share-alt"></i>
                Information Sharing and Disclosure
              </h2>
              <p>We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:</p>
              <ul class="legal-list">
                <li><strong>Service Providers:</strong> With trusted third-party vendors who assist us in operating our website and conducting business</li>
                <li><strong>Legal Requirements:</strong> When required by law, court order, or government request</li>
                <li><strong>Business Protection:</strong> To protect our rights, property, or safety, or that of our users</li>
                <li><strong>Consent:</strong> With your explicit consent for specific purposes</li>
              </ul>
            </section>

            <section id="data-security" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-lock"></i>
                Data Security
              </h2>
              <p>We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. These measures include:</p>
              <ul class="legal-list">
                <li>SSL encryption for data transmission</li>
                <li>Secure servers and databases</li>
                <li>Regular security assessments</li>
                <li>Access controls and employee training</li>
                <li>Data backup and recovery procedures</li>
              </ul>
              <div class="legal-note">
                <p><strong>Note:</strong> While we strive to protect your personal information, no method of transmission over the Internet or electronic storage is 100% secure. We cannot guarantee absolute security.</p>
              </div>
            </section>

            <section id="cookies" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-cookie-bite"></i>
                Cookies and Tracking Technologies
              </h2>
              <p>We use cookies and similar tracking technologies to enhance your browsing experience:</p>

              <h3 class="legal-subsection__title">Types of Cookies We Use</h3>
              <ul class="legal-list">
                <li><strong>Essential Cookies:</strong> Required for basic website functionality</li>
                <li><strong>Analytics Cookies:</strong> Help us understand how visitors use our site</li>
                <li><strong>Preference Cookies:</strong> Remember your settings and preferences</li>
                <li><strong>Marketing Cookies:</strong> Used to deliver relevant advertisements (with consent)</li>
              </ul>

              <p>You can control cookies through your browser settings. However, disabling certain cookies may affect website functionality.</p>
            </section>

            <section id="user-rights" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-user-shield"></i>
                Your Rights and Choices
              </h2>
              <p>You have the following rights regarding your personal information:</p>
              <ul class="legal-list">
                <li><strong>Access:</strong> Request a copy of your personal information</li>
                <li><strong>Correction:</strong> Request correction of inaccurate information</li>
                <li><strong>Deletion:</strong> Request deletion of your personal information</li>
                <li><strong>Portability:</strong> Request transfer of your data to another service</li>
                <li><strong>Objection:</strong> Object to processing of your personal information</li>
                <li><strong>Withdrawal:</strong> Withdraw consent for specific processing activities</li>
              </ul>

              <div class="legal-note">
                <p>To exercise any of these rights, please contact us using the information provided below. We will respond to your request within a reasonable timeframe.</p>
              </div>
            </section>

            <section id="contact" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-envelope"></i>
                Contact Information
              </h2>
              <p>If you have any questions about this Privacy Policy or our data practices, please contact us:</p>

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
                <p><strong>Updates to this Policy:</strong> We may update this Privacy Policy from time to time. Any changes will be posted on this page with an updated "Last Modified" date.</p>
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
  name: 'PrivacyPolicyPage',
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
// Variables
$primary-color: #3498db;
$primary-hover: #2980b9;
$accent-color: #e74c3c;
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

.legal-page {
  min-height: 100vh;
  background: $bg-light;
}

// Hero Section
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

// Content Section
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

// Sidebar TOC
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

// Main Content
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
