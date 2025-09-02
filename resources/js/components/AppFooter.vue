<template>
  <footer class="footer">
    <div class="container">
      <div class="footer__content">
        <div class="footer__section">
          <h3 class="footer__section-title">
            <i class="fas fa-terminal"></i>
            Darkheim
          </h3>
          <p class="footer__section-text">
            We create exceptional digital experiences that drive business growth and user engagement.
          </p>
          <div class="footer__social">
            <a href="#" aria-label="GitHub">
              <i class="fab fa-github"></i>
            </a>
            <a href="#" aria-label="LinkedIn">
              <i class="fab fa-linkedin"></i>
            </a>
            <a href="#" aria-label="Twitter">
              <i class="fab fa-twitter"></i>
            </a>
          </div>
        </div>

        <div class="footer__section">
          <h3 class="footer__section-title">
            <i class="fas fa-laptop-code"></i>
            Services
          </h3>
          <ul class="footer__links">
            <li>
              <a href="/services">
                <i class="fas fa-code"></i>
                Web Development
              </a>
            </li>
            <li>
              <a href="/services">
                <i class="fas fa-palette"></i>
                UI/UX Design
              </a>
            </li>
            <li>
              <a href="/services">
                <i class="fas fa-mobile-screen"></i>
                Mobile Apps
              </a>
            </li>
            <li>
              <a href="/services">
                <i class="fas fa-lightbulb"></i>
                Consulting
              </a>
            </li>
          </ul>
        </div>

        <div class="footer__section">
          <h3 class="footer__section-title">
            <i class="fas fa-building"></i>
            Company
          </h3>
          <ul class="footer__links">
            <li>
              <router-link to="/about">
                <i class="fas fa-info-circle"></i>
                About Us
              </router-link>
            </li>
            <li>
              <router-link to="/portfolio">
                <i class="fas fa-briefcase"></i>
                Portfolio
              </router-link>
            </li>
            <li>
              <a href="#">
                <i class="fas fa-users"></i>
                Careers
              </a>
            </li>
            <li>
              <router-link to="/contact">
                <i class="fas fa-envelope"></i>
                Contact
              </router-link>
            </li>
          </ul>
        </div>

        <div class="footer__section">
          <h3 class="footer__section-title">
            <i class="fas fa-phone"></i>
            Get In Touch
          </h3>
          <p class="footer__section-text">
            Ready to start your project? Let's discuss your ideas and bring them to life.
          </p>
          <router-link to="/contact" class="btn btn--secondary btn--base">
            <span>Contact Us</span>
            <i class="fas fa-arrow-right btn__icon btn__icon--right"></i>
          </router-link>
        </div>
      </div>

      <div class="footer__bottom">
        <div class="footer__copyright">
          <i class="far fa-copyright"></i>
          {{ currentYear }} Portfolio. All rights reserved.
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'AppFooter',
  computed: {
    currentYear() {
      return new Date().getFullYear()
    }
  }
}
</script>
