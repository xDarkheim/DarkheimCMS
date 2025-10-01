<template>
  <div class="legal-page page-with-header-offset">
    <div class="legal-hero">
      <div class="container">
        <div class="legal-hero__content">
          <h1 class="legal-hero__title">
            <i class="fas fa-file-contract"></i>
            Terms of Service
          </h1>
          <p class="legal-hero__subtitle">
            Terms and conditions for using our services and website
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
                <li><a href="#acceptance" class="legal-toc__link">Acceptance of Terms</a></li>
                <li><a href="#services" class="legal-toc__link">Our Services</a></li>
                <li><a href="#user-responsibilities" class="legal-toc__link">User Responsibilities</a></li>
                <li><a href="#intellectual-property" class="legal-toc__link">Intellectual Property</a></li>
                <li><a href="#project-terms" class="legal-toc__link">Project Terms</a></li>
                <li><a href="#payment-terms" class="legal-toc__link">Payment Terms</a></li>
                <li><a href="#limitation-liability" class="legal-toc__link">Limitation of Liability</a></li>
                <li><a href="#termination" class="legal-toc__link">Termination</a></li>
                <li><a href="#contact" class="legal-toc__link">Contact Information</a></li>
              </ul>
            </div>
          </div>

          <div class="legal-main">
            <div class="legal-section">
              <p class="legal-intro">
                Welcome to Darkheim Development Studio. These Terms of Service ("Terms") govern your use of our website and services. By accessing our website or engaging our services, you agree to be bound by these Terms.
              </p>
            </div>

            <section id="acceptance" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-handshake"></i>
                Acceptance of Terms
              </h2>
              <p>By accessing and using our website or services, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service and our Privacy Policy. If you do not agree to these terms, please do not use our services.</p>

              <h3 class="legal-subsection__title">Changes to Terms</h3>
              <p>We reserve the right to modify these Terms at any time. Any changes will be effective immediately upon posting on this page. Your continued use of our services after any such changes constitutes your acceptance of the new Terms.</p>
            </section>

            <section id="services" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-laptop-code"></i>
                Our Services
              </h2>
              <p>Darkheim Development Studio provides the following services:</p>
              <ul class="legal-list">
                <li>Web development and design</li>
                <li>Mobile application development</li>
                <li>UI/UX design services</li>
                <li>E-commerce solutions</li>
                <li>Technical consulting</li>
                <li>Website maintenance and support</li>
                <li>Custom software development</li>
              </ul>

              <div class="legal-note">
                <p><strong>Service Availability:</strong> We strive to maintain high service availability, but we do not guarantee uninterrupted access to our website or services. Maintenance, updates, or technical issues may cause temporary interruptions.</p>
              </div>
            </section>

            <section id="user-responsibilities" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-user-check"></i>
                User Responsibilities
              </h2>
              <p>When using our services, you agree to:</p>
              <ul class="legal-list">
                <li>Provide accurate and complete information</li>
                <li>Maintain the confidentiality of any account credentials</li>
                <li>Use our services only for lawful purposes</li>
                <li>Respect intellectual property rights</li>
                <li>Not engage in any activity that could harm our systems or other users</li>
                <li>Comply with all applicable laws and regulations</li>
              </ul>

              <h3 class="legal-subsection__title">Prohibited Activities</h3>
              <p>You are strictly prohibited from:</p>
              <ul class="legal-list">
                <li>Using our services for illegal or unauthorized purposes</li>
                <li>Attempting to gain unauthorized access to our systems</li>
                <li>Transmitting viruses, malware, or other harmful code</li>
                <li>Spamming or sending unsolicited communications</li>
                <li>Infringing on intellectual property rights</li>
                <li>Impersonating others or providing false information</li>
              </ul>
            </section>

            <section id="intellectual-property" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-copyright"></i>
                Intellectual Property Rights
              </h2>

              <h3 class="legal-subsection__title">Our Intellectual Property</h3>
              <p>All content on our website, including but not limited to text, graphics, logos, images, and software, is the property of Darkheim Development Studio and is protected by intellectual property laws.</p>

              <h3 class="legal-subsection__title">Client Work Product</h3>
              <p>For development projects:</p>
              <ul class="legal-list">
                <li>Upon full payment, clients receive ownership of custom-developed code</li>
                <li>Third-party libraries and frameworks remain under their original licenses</li>
                <li>We retain the right to showcase completed work in our portfolio</li>
                <li>Source code and documentation are provided upon project completion</li>
              </ul>

              <h3 class="legal-subsection__title">License to Use</h3>
              <p>We grant you a limited, non-exclusive, non-transferable license to access and use our website for its intended purpose. This license does not include any rights to:</p>
              <ul class="legal-list">
                <li>Reproduce, distribute, or create derivative works</li>
                <li>Reverse engineer or decompile any software</li>
                <li>Remove or modify any proprietary notices</li>
              </ul>
            </section>

            <section id="project-terms" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-project-diagram"></i>
                Project Terms and Conditions
              </h2>

              <h3 class="legal-subsection__title">Project Scope</h3>
              <p>All projects begin with a detailed scope document that outlines:</p>
              <ul class="legal-list">
                <li>Project deliverables and specifications</li>
                <li>Timeline and milestones</li>
                <li>Responsibilities of both parties</li>
                <li>Communication protocols</li>
                <li>Change request procedures</li>
              </ul>

              <h3 class="legal-subsection__title">Client Responsibilities</h3>
              <p>For successful project completion, clients must:</p>
              <ul class="legal-list">
                <li>Provide necessary content, assets, and information in a timely manner</li>
                <li>Respond to requests for feedback within agreed timeframes</li>
                <li>Ensure authorized personnel make decisions and approvals</li>
                <li>Provide access to required systems and platforms</li>
                <li>Test deliverables and provide feedback during development phases</li>
              </ul>

              <h3 class="legal-subsection__title">Revisions and Changes</h3>
              <p>Minor revisions within the original scope are included. Significant changes or additions may require:</p>
              <ul class="legal-list">
                <li>Written approval from both parties</li>
                <li>Updated timeline and cost estimates</li>
                <li>Formal change order documentation</li>
              </ul>
            </section>

            <section id="payment-terms" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-credit-card"></i>
                Payment Terms
              </h2>

              <h3 class="legal-subsection__title">Payment Schedule</h3>
              <p>Unless otherwise agreed in writing:</p>
              <ul class="legal-list">
                <li>50% deposit required to begin work</li>
                <li>Remaining balance due upon project completion</li>
                <li>Monthly payments for ongoing maintenance services</li>
                <li>Net 30 payment terms for invoiced amounts</li>
              </ul>

              <h3 class="legal-subsection__title">Late Payments</h3>
              <p>Late payments may result in:</p>
              <ul class="legal-list">
                <li>Suspension of work until payment is received</li>
                <li>Late fees of 1.5% per month on overdue amounts</li>
                <li>Termination of ongoing services</li>
                <li>Legal action to recover outstanding amounts</li>
              </ul>

              <h3 class="legal-subsection__title">Refund Policy</h3>
              <p>Refunds are considered on a case-by-case basis. Generally:</p>
              <ul class="legal-list">
                <li>Deposits are non-refundable once work has begun</li>
                <li>Completed work phases are non-refundable</li>
                <li>Refunds may be provided for undelivered services due to our fault</li>
              </ul>
            </section>

            <section id="limitation-liability" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-shield-alt"></i>
                Limitation of Liability
              </h2>

              <h3 class="legal-subsection__title">Warranty Disclaimer</h3>
              <p>Our services are provided "as is" without any warranties, express or implied. We do not warrant that:</p>
              <ul class="legal-list">
                <li>Our services will be uninterrupted or error-free</li>
                <li>Results will meet your specific requirements</li>
                <li>All errors or defects will be corrected</li>
                <li>The services will be compatible with all systems</li>
              </ul>

              <h3 class="legal-subsection__title">Liability Limits</h3>
              <p>To the maximum extent permitted by law, our total liability shall not exceed the amount paid by you for the specific service that gave rise to the claim.</p>

              <h3 class="legal-subsection__title">Excluded Damages</h3>
              <p>We shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including but not limited to:</p>
              <ul class="legal-list">
                <li>Loss of profits or revenue</li>
                <li>Loss of data or business interruption</li>
                <li>Cost of substitute services</li>
                <li>Loss of goodwill or reputation</li>
              </ul>
            </section>

            <section id="termination" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-ban"></i>
                Termination
              </h2>

              <h3 class="legal-subsection__title">Termination by Either Party</h3>
              <p>Either party may terminate services with written notice:</p>
              <ul class="legal-list">
                <li>30 days notice for ongoing maintenance services</li>
                <li>Immediate termination for material breach</li>
                <li>Immediate termination for non-payment</li>
              </ul>

              <h3 class="legal-subsection__title">Effect of Termination</h3>
              <p>Upon termination:</p>
              <ul class="legal-list">
                <li>Payment is due for all completed work</li>
                <li>We will provide deliverables for paid work</li>
                <li>All confidential information must be returned</li>
                <li>Licenses to use our proprietary tools terminate</li>
              </ul>
            </section>

            <section id="contact" class="legal-section">
              <h2 class="legal-section__title">
                <i class="fas fa-envelope"></i>
                Contact Information
              </h2>
              <p>For questions about these Terms of Service, please contact us:</p>

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
                <p><strong>Governing Law:</strong> These Terms shall be governed by and construed in accordance with the laws of California, United States, without regard to its conflict of law provisions.</p>
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
  name: 'TermsOfServicePage',
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
        { key: 'email', label: 'Email', value: 'legal@darkheim.studio', type: 'email' },
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
// Import the same styles as Privacy Policy
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
