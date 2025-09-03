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
              We create exceptional digital experiences that drive business growth and user engagement.
              Our team combines creativity with technical expertise to deliver outstanding results.
            </p>
          </div>

          <div class="footer__social">
            <h4 class="footer__social-title">Follow Us</h4>
            <div class="footer__social-links">
              <a href="#" class="footer__social-link" aria-label="GitHub" title="Follow us on GitHub">
                <i class="fab fa-github"></i>
              </a>
              <a href="#" class="footer__social-link" aria-label="LinkedIn" title="Connect on LinkedIn">
                <i class="fab fa-linkedin"></i>
              </a>
              <a href="#" class="footer__social-link" aria-label="Twitter" title="Follow us on Twitter">
                <i class="fab fa-twitter"></i>
              </a>
              <a href="#" class="footer__social-link" aria-label="Instagram" title="Follow us on Instagram">
                <i class="fab fa-instagram"></i>
              </a>
              <a href="#" class="footer__social-link" aria-label="Discord" title="Join our Discord">
                <i class="fab fa-discord"></i>
              </a>
              <a href="#" class="footer__social-link" aria-label="YouTube" title="Subscribe to our YouTube">
                <i class="fab fa-youtube"></i>
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
            <li>
              <router-link to="/portfolio" class="footer__link">
                <i class="fas fa-folder-open"></i>
                <span>Portfolio</span>
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
            <div class="footer__contact-item">
              <i class="fas fa-envelope"></i>
              <span>hello@darkheim.dev</span>
            </div>
            <div class="footer__contact-item">
              <i class="fas fa-phone"></i>
              <span>+1 (555) 123-4567</span>
            </div>
            <div class="footer__contact-item">
              <i class="fas fa-map-marker-alt"></i>
              <span>San Francisco, CA</span>
            </div>
          </div>

          <router-link to="/contact" class="footer__cta-btn">
            <span>Start Your Project</span>
            <i class="fas fa-arrow-right"></i>
          </router-link>
        </div>
      </div>

      <!-- Newsletter Section -->
      <div class="footer__newsletter">
        <div class="footer__newsletter-content">
          <h3 class="footer__newsletter-title">Stay Updated</h3>
          <p class="footer__newsletter-desc">
            Subscribe to our newsletter and get the latest updates on web development trends and our projects.
          </p>
        </div>
        <form class="footer__newsletter-form" @submit.prevent="subscribeNewsletter">
          <div class="footer__newsletter-input">
            <i class="fas fa-envelope"></i>
            <input
              type="email"
              placeholder="Enter your email address"
              v-model="newsletterEmail"
              required
            >
          </div>
          <button type="submit" class="footer__newsletter-btn" :disabled="isSubscribing">
            <span v-if="!isSubscribing">Subscribe</span>
            <span v-else>Subscribing...</span>
            <i class="fas fa-paper-plane"></i>
          </button>
        </form>
      </div>

      <!-- Bottom Section -->
      <div class="footer__bottom">
        <div class="footer__bottom-left">
          <div class="footer__copyright">
            <i class="far fa-copyright"></i>
            <span>{{ currentYear }} Darkheim. All rights reserved.</span>
          </div>
        </div>

        <div class="footer__bottom-center">
          <div class="footer__legal-links">
            <a href="#" class="footer__legal-link">Privacy Policy</a>
            <a href="#" class="footer__legal-link">Terms of Service</a>
            <a href="#" class="footer__legal-link">Cookie Policy</a>
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
export default {
  name: 'AppFooter',
  data() {
    return {
      newsletterEmail: '',
      isSubscribing: false,
    }
  },
  computed: {
    currentYear() {
      return new Date().getFullYear()
    }
  },
  methods: {
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
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);

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

// Newsletter Section
.footer__newsletter {
  background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(155, 89, 182, 0.1));
  border-radius: $border-radius;
  padding: 2.5rem;
  margin: 2rem 0;
  border: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);

  &-content {
    text-align: center;
    margin-bottom: 2rem;
  }

  &-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    background: linear-gradient(135deg, $primary-color, #9b59b6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  &-desc {
    color: $text-muted;
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
  }

  &-form {
    display: flex;
    gap: 1rem;
    max-width: 500px;
    margin: 0 auto;

    @media (max-width: 480px) {
      flex-direction: column;
    }
  }

  &-input {
    position: relative;
    flex: 1;

    i {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: $primary-color;
      z-index: 2;
    }

    input {
      width: 100%;
      padding: 1rem 1rem 1rem 3rem;
      background: rgba(255, 255, 255, 0.05);
      border: 2px solid rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      color: $text-light;
      font-size: 0.95rem;
      transition: $transition;

      &::placeholder {
        color: $text-muted;
      }

      &:focus {
        outline: none;
        border-color: $primary-color;
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
      }
    }
  }

  &-btn {
    padding: 1rem 2rem;
    background: linear-gradient(135deg, $primary-color, $primary-hover);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: $transition;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);

    &:hover:not(:disabled) {
      background: linear-gradient(135deg, $primary-hover, $primary-color);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
    }

    &:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }
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

  .footer__newsletter {
    padding: 2rem;
    margin: 1.5rem 0;
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

  .footer__newsletter {
    padding: 1.5rem;
    text-align: center;

    &-title {
      font-size: 1.25rem;
    }
  }
}

@media (max-width: 480px) {
  .footer__content {
    padding: 2rem 0;
  }

  .footer__newsletter {
    padding: 1.25rem;

    &-form {
      gap: 0.75rem;
    }

    &-input input {
      padding: 0.875rem 0.875rem 0.875rem 2.75rem;
    }

    &-btn {
      padding: 0.875rem 1.5rem;
    }
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
