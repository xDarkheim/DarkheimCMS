<template>
  <header class="header" :class="{ 'header--scrolled': isScrolled }">
    <div class="header__container">
      <!-- Logo section - левая сторона -->
      <div class="header__left">
        <router-link to="/" class="logo">
          <div class="logo__icon">
            <span class="logo__letter">D</span>
          </div>
          <span class="logo__text">Darkheim</span>
        </router-link>
      </div>

      <!-- Navigation section - правая сторона -->
      <div class="header__right">
        <nav class="nav" @mouseleave="closeAllDropdowns">
          <ul class="nav__list">
            <li class="nav__item">
              <router-link
                to="/"
                class="nav__link"
                :class="{ 'nav__link--active': $route.name === 'Home' }"
              >
                <i class="fas fa-home"></i>
                <span>Home</span>
              </router-link>
            </li>

            <!-- Services with dropdown -->
            <li class="nav__item nav__item--dropdown" @mouseenter="showDropdown('services')" @mouseleave="hideDropdown('services')">
              <router-link
                to="/services"
                class="nav__link"
                :class="{ 'nav__link--active': $route.name === 'Services' }"
              >
                <i class="fas fa-laptop-code"></i>
                <span>Services</span>
                <i class="fas fa-chevron-down nav__dropdown-icon"></i>
              </router-link>
              <div class="nav__dropdown" :class="{ 'nav__dropdown--visible': dropdowns.services }" @mouseenter="handleDropdownEnter('services')" @mouseleave="handleDropdownLeave('services')">
                <div class="nav__dropdown-content">
                  <router-link to="/services?filter=web-development" class="nav__dropdown-item">
                    <div class="nav__dropdown-icon">
                      <i class="fas fa-code"></i>
                    </div>
                    <div class="nav__dropdown-text">
                      <span class="nav__dropdown-title">Web Development</span>
                      <span class="nav__dropdown-desc">Custom websites & web applications</span>
                    </div>
                  </router-link>
                  <router-link to="/services?filter=ui-ux-design" class="nav__dropdown-item">
                    <div class="nav__dropdown-icon">
                      <i class="fas fa-palette"></i>
                    </div>
                    <div class="nav__dropdown-text">
                      <span class="nav__dropdown-title">UI/UX Design</span>
                      <span class="nav__dropdown-desc">User interface & experience design</span>
                    </div>
                  </router-link>
                  <router-link to="/services?filter=mobile-apps" class="nav__dropdown-item">
                    <div class="nav__dropdown-icon">
                      <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="nav__dropdown-text">
                      <span class="nav__dropdown-title">Mobile Apps</span>
                      <span class="nav__dropdown-desc">iOS & Android applications</span>
                    </div>
                  </router-link>
                  <router-link to="/services?filter=consulting" class="nav__dropdown-item">
                    <div class="nav__dropdown-icon">
                      <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="nav__dropdown-text">
                      <span class="nav__dropdown-title">Consulting</span>
                      <span class="nav__dropdown-desc">Technical consulting & strategy</span>
                    </div>
                  </router-link>
                </div>
              </div>
            </li>

            <!-- Portfolio with dropdown -->
            <li class="nav__item nav__item--dropdown" @mouseenter="showDropdown('portfolio')" @mouseleave="hideDropdown('portfolio')">
              <router-link
                to="/portfolio"
                class="nav__link"
                :class="{ 'nav__link--active': $route.name === 'Portfolio' }"
              >
                <i class="fas fa-folder-open"></i>
                <span>Portfolio</span>
                <i class="fas fa-chevron-down nav__dropdown-icon"></i>
              </router-link>
              <div
                class="nav__dropdown"
                :class="{ 'nav__dropdown--visible': dropdowns.portfolio }"
                @mouseenter="handleDropdownEnter('portfolio')"
                @mouseleave="handleDropdownLeave('portfolio')"
              >
                <div class="nav__dropdown-content">
                  <router-link to="/portfolio?category=web-development" class="nav__dropdown-item">
                    <div class="nav__dropdown-icon">
                      <i class="fas fa-code"></i>
                    </div>
                    <div class="nav__dropdown-text">
                      <span class="nav__dropdown-title">Web Development</span>
                      <span class="nav__dropdown-desc">Websites & web applications</span>
                    </div>
                  </router-link>
                  <router-link to="/portfolio?category=mobile-applications" class="nav__dropdown-item">
                    <div class="nav__dropdown-icon">
                      <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="nav__dropdown-text">
                      <span class="nav__dropdown-title">Mobile Applications</span>
                      <span class="nav__dropdown-desc">iOS & Android apps</span>
                    </div>
                  </router-link>
                  <router-link to="/portfolio?category=ecommerce-solutions" class="nav__dropdown-item">
                    <div class="nav__dropdown-icon">
                      <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="nav__dropdown-text">
                      <span class="nav__dropdown-title">E-commerce Solutions</span>
                      <span class="nav__dropdown-desc">Online stores & marketplaces</span>
                    </div>
                  </router-link>
                  <router-link to="/portfolio?category=business-applications" class="nav__dropdown-item">
                    <div class="nav__dropdown-icon">
                      <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="nav__dropdown-text">
                      <span class="nav__dropdown-title">Business Applications</span>
                      <span class="nav__dropdown-desc">Enterprise & business solutions</span>
                    </div>
                  </router-link>
                  <router-link to="/portfolio?category=landing-pages" class="nav__dropdown-item">
                    <div class="nav__dropdown-icon">
                      <i class="fas fa-rocket"></i>
                    </div>
                    <div class="nav__dropdown-text">
                      <span class="nav__dropdown-title">Landing Pages</span>
                      <span class="nav__dropdown-desc">Marketing & promotional pages</span>
                    </div>
                  </router-link>
                </div>
              </div>
            </li>

            <!-- Company with dropdown -->
            <li class="nav__item nav__item--dropdown" @mouseenter="showDropdown('company')" @mouseleave="hideDropdown('company')">
              <a href="#" class="nav__link" @click.prevent>
                <i class="fas fa-building"></i>
                <span>Company</span>
                <i class="fas fa-chevron-down nav__dropdown-icon"></i>
              </a>
              <div class="nav__dropdown" :class="{ 'nav__dropdown--visible': dropdowns.company }" @mouseenter="handleDropdownEnter('company')" @mouseleave="handleDropdownLeave('company')">
                <div class="nav__dropdown-content">
                  <router-link to="/about" class="nav__dropdown-item">
                    <div class="nav__dropdown-icon">
                      <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="nav__dropdown-text">
                      <span class="nav__dropdown-title">About Us</span>
                      <span class="nav__dropdown-desc">Our story & mission</span>
                    </div>
                  </router-link>
                  <router-link to="/team" class="nav__dropdown-item">
                    <div class="nav__dropdown-icon">
                      <i class="fas fa-users"></i>
                    </div>
                    <div class="nav__dropdown-text">
                      <span class="nav__dropdown-title">Our Team</span>
                      <span class="nav__dropdown-desc">Meet the talented people</span>
                    </div>
                  </router-link>
                  <router-link to="/careers" class="nav__dropdown-item">
                    <div class="nav__dropdown-icon">
                      <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="nav__dropdown-text">
                      <span class="nav__dropdown-title">Careers</span>
                      <span class="nav__dropdown-desc">Join our growing team</span>
                    </div>
                  </router-link>
                  <router-link to="/news" class="nav__dropdown-item">
                    <div class="nav__dropdown-icon">
                      <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="nav__dropdown-text">
                      <span class="nav__dropdown-title">News & Blog</span>
                      <span class="nav__dropdown-desc">Latest updates & insights</span>
                    </div>
                  </router-link>
                </div>
              </div>
            </li>

            <li class="nav__item">
              <router-link
                to="/contact"
                class="nav__link nav__link--cta"
                :class="{ 'nav__link--active': $route.name === 'Contact' }"
              >
                <i class="fas fa-paper-plane"></i>
                <span>Contact</span>
              </router-link>
            </li>
          </ul>
        </nav>

        <!-- Mobile menu button -->
        <button
          class="mobile-menu-toggle"
          @click="toggleMobileMenu"
          :class="{ 'mobile-menu-toggle--active': isMobileMenuOpen }"
          aria-label="Toggle mobile menu"
        >
          <span class="mobile-menu-toggle__line"></span>
          <span class="mobile-menu-toggle__line"></span>
          <span class="mobile-menu-toggle__line"></span>
        </button>
      </div>

      <!-- Mobile menu overlay -->
      <div class="mobile-menu" :class="{ 'mobile-menu--open': isMobileMenuOpen }">
        <div class="mobile-menu__content">
          <!-- Mobile menu header with close button -->
          <div class="mobile-menu__header">
            <div class="mobile-menu__logo">
              <div class="logo__icon">
                <span class="logo__letter">D</span>
              </div>
              <span class="logo__text">Darkheim</span>
            </div>
            <button
              class="mobile-menu__close"
              @click="closeMobileMenu"
              aria-label="Close menu"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>

          <nav class="mobile-nav">
            <!-- Home -->
            <router-link to="/" class="mobile-nav__link" @click="closeMobileMenu">
              <i class="fas fa-home"></i>
              <span>Home</span>
            </router-link>

            <!-- Services - Collapsible Section -->
            <div class="mobile-nav__section">
              <button
                class="mobile-nav__section-toggle"
                @click="toggleMobileSection('services')"
                :class="{ 'mobile-nav__section-toggle--active': mobileSections.services }"
              >
                <i class="fas fa-laptop-code"></i>
                <span>Services</span>
                <i class="fas fa-chevron-down mobile-nav__chevron"></i>
              </button>
              <div class="mobile-nav__submenu" :class="{ 'mobile-nav__submenu--open': mobileSections.services }">
                <router-link to="/services" class="mobile-nav__submenu-link" @click="closeMobileMenu">
                  <i class="fas fa-list"></i>
                  <span>All Services</span>
                </router-link>
                <router-link to="/services?filter=web-development" class="mobile-nav__submenu-link" @click="closeMobileMenu">
                  <i class="fas fa-code"></i>
                  <span>Web Development</span>
                </router-link>
                <router-link to="/services?filter=ui-ux-design" class="mobile-nav__submenu-link" @click="closeMobileMenu">
                  <i class="fas fa-palette"></i>
                  <span>UI/UX Design</span>
                </router-link>
                <router-link to="/services?filter=mobile-apps" class="mobile-nav__submenu-link" @click="closeMobileMenu">
                  <i class="fas fa-mobile-alt"></i>
                  <span>Mobile Apps</span>
                </router-link>
                <router-link to="/services?filter=consulting" class="mobile-nav__submenu-link" @click="closeMobileMenu">
                  <i class="fas fa-lightbulb"></i>
                  <span>Consulting</span>
                </router-link>
              </div>
            </div>

            <!-- Portfolio - Collapsible Section -->
            <div class="mobile-nav__section">
              <button
                class="mobile-nav__section-toggle"
                @click="toggleMobileSection('portfolio')"
                :class="{ 'mobile-nav__section-toggle--active': mobileSections.portfolio }"
              >
                <i class="fas fa-folder-open"></i>
                <span>Portfolio</span>
                <i class="fas fa-chevron-down mobile-nav__chevron"></i>
              </button>
              <div class="mobile-nav__submenu" :class="{ 'mobile-nav__submenu--open': mobileSections.portfolio }">
                <router-link to="/portfolio" class="mobile-nav__submenu-link" @click="closeMobileMenu">
                  <i class="fas fa-th-large"></i>
                  <span>All Projects</span>
                </router-link>
                <router-link to="/portfolio?category=web-development" class="mobile-nav__submenu-link" @click="closeMobileMenu">
                  <i class="fas fa-code"></i>
                  <span>Web Development</span>
                </router-link>
                <router-link to="/portfolio?category=mobile-applications" class="mobile-nav__submenu-link" @click="closeMobileMenu">
                  <i class="fas fa-mobile-alt"></i>
                  <span>Mobile Applications</span>
                </router-link>
                <router-link to="/portfolio?category=ecommerce-solutions" class="mobile-nav__submenu-link" @click="closeMobileMenu">
                  <i class="fas fa-shopping-cart"></i>
                  <span>E-commerce Solutions</span>
                </router-link>
                <router-link to="/portfolio?category=business-applications" class="mobile-nav__submenu-link" @click="closeMobileMenu">
                  <i class="fas fa-briefcase"></i>
                  <span>Business Applications</span>
                </router-link>
                <router-link to="/portfolio?category=landing-pages" class="mobile-nav__submenu-link" @click="closeMobileMenu">
                  <i class="fas fa-rocket"></i>
                  <span>Landing Pages</span>
                </router-link>
              </div>
            </div>

            <!-- Company - Collapsible Section -->
            <div class="mobile-nav__section">
              <button
                class="mobile-nav__section-toggle"
                @click="toggleMobileSection('company')"
                :class="{ 'mobile-nav__section-toggle--active': mobileSections.company }"
              >
                <i class="fas fa-building"></i>
                <span>Company</span>
                <i class="fas fa-chevron-down mobile-nav__chevron"></i>
              </button>
              <div class="mobile-nav__submenu" :class="{ 'mobile-nav__submenu--open': mobileSections.company }">
                <router-link to="/about" class="mobile-nav__submenu-link" @click="closeMobileMenu">
                  <i class="fas fa-info-circle"></i>
                  <span>About Us</span>
                </router-link>
                <router-link to="/team" class="mobile-nav__submenu-link" @click="closeMobileMenu">
                  <i class="fas fa-users"></i>
                  <span>Our Team</span>
                </router-link>
                <router-link to="/careers" class="mobile-nav__submenu-link" @click="closeMobileMenu">
                  <i class="fas fa-briefcase"></i>
                  <span>Careers</span>
                </router-link>
                <router-link to="/news" class="mobile-nav__submenu-link" @click="closeMobileMenu">
                  <i class="fas fa-newspaper"></i>
                  <span>News & Blog</span>
                </router-link>
              </div>
            </div>

            <!-- Contact -->
            <router-link to="/contact" class="mobile-nav__link mobile-nav__link--cta" @click="closeMobileMenu">
              <i class="fas fa-paper-plane"></i>
              <span>Contact</span>
            </router-link>
          </nav>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'

export default {
  name: 'AppHeader',
  setup() {
    const isScrolled = ref(false)
    const isMobileMenuOpen = ref(false)
    const dropdowns = ref({
      services: false,
      portfolio: false,
      company: false
    })

    const mobileSections = ref({
      services: false,
      portfolio: false,
      company: false
    })

    // Add timeout refs for better dropdown control
    const dropdownTimeouts = ref({
      services: null,
      portfolio: null,
      company: null
    })

    const handleScroll = () => {
      isScrolled.value = window.scrollY > 50
    }

    const toggleMobileMenu = () => {
      isMobileMenuOpen.value = !isMobileMenuOpen.value
      // Close all dropdowns when mobile menu opens
      if (isMobileMenuOpen.value) {
        closeAllDropdowns()
      }
    }

    const closeMobileMenu = () => {
      isMobileMenuOpen.value = false
      // Close all mobile sections when menu closes
      Object.keys(mobileSections.value).forEach(key => {
        mobileSections.value[key] = false
      })
    }

    const closeAllDropdowns = () => {
      // Clear all timeouts
      Object.keys(dropdownTimeouts.value).forEach(key => {
        if (dropdownTimeouts.value[key]) {
          clearTimeout(dropdownTimeouts.value[key])
          dropdownTimeouts.value[key] = null
        }
      })
      // Close all dropdowns
      Object.keys(dropdowns.value).forEach(key => {
        dropdowns.value[key] = false
      })
    }

    const showDropdown = (menu) => {
      // Немедленно закрываем все другие dropdown'ы
      Object.keys(dropdowns.value).forEach(key => {
        if (key !== menu) {
          dropdowns.value[key] = false
          if (dropdownTimeouts.value[key]) {
            clearTimeout(dropdownTimeouts.value[key])
            dropdownTimeouts.value[key] = null
          }
        }
      })

      // Очищаем таймер для текущего меню если есть
      if (dropdownTimeouts.value[menu]) {
        clearTimeout(dropdownTimeouts.value[menu])
        dropdownTimeouts.value[menu] = null
      }

      // Открываем нужное меню
      dropdowns.value[menu] = true
    }

    const hideDropdown = (menu) => {
      // Очищаем существующий таймер
      if (dropdownTimeouts.value[menu]) {
        clearTimeout(dropdownTimeouts.value[menu])
        dropdownTimeouts.value[menu] = null
      }

      // Устанавливаем задержку перед закрытием
      dropdownTimeouts.value[menu] = setTimeout(() => {
        dropdowns.value[menu] = false
        dropdownTimeouts.value[menu] = null
      }, 150)
    }

    const handleDropdownEnter = (menu) => {
      // Отменяем закрытие если пользователь вернулся в область dropdown'а
      if (dropdownTimeouts.value[menu]) {
        clearTimeout(dropdownTimeouts.value[menu])
        dropdownTimeouts.value[menu] = null
      }
    }

    const handleDropdownLeave = (menu) => {
      hideDropdown(menu)
    }

    // Enhanced outside click handler
    const handleOutsideClick = (e) => {
      const headerEl = document.querySelector('.header')
      if (headerEl && !headerEl.contains(e.target)) {
        closeAllDropdowns()
      }
    }

    // Handle escape key
    const handleKeyDown = (e) => {
      if (e.key === 'Escape') {
        closeAllDropdowns()
        if (isMobileMenuOpen.value) {
          closeMobileMenu()
        }
      }
    }

    const toggleMobileSection = (section) => {
      // Close the section if it's already open, otherwise open it
      mobileSections.value[section] = !mobileSections.value[section]

      // Close other sections
      Object.keys(mobileSections.value).forEach(key => {
        if (key !== section) {
          mobileSections.value[key] = false
        }
      })
    }

    onMounted(() => {
      window.addEventListener('scroll', handleScroll)
      document.addEventListener('click', handleOutsideClick)
      document.addEventListener('keydown', handleKeyDown)
    })

    onUnmounted(() => {
      window.removeEventListener('scroll', handleScroll)
      document.removeEventListener('click', handleOutsideClick)
      document.removeEventListener('keydown', handleKeyDown)

      // Clear all timeouts on unmount
      Object.values(dropdownTimeouts.value).forEach(timeout => {
        if (timeout) clearTimeout(timeout)
      })
    })

    return {
      isScrolled,
      isMobileMenuOpen,
      dropdowns,
      mobileSections,
      toggleMobileMenu,
      closeMobileMenu,
      showDropdown,
      hideDropdown,
      handleDropdownEnter,
      handleDropdownLeave,
      closeAllDropdowns,
      toggleMobileSection
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
$bg-dark: #2c3e50;
$bg-darker: #1a252f;
$shadow-light: rgba(0, 0, 0, 0.1);
$shadow-medium: rgba(0, 0, 0, 0.2);
$shadow-dark: rgba(0, 0, 0, 0.3);
$transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
$border-radius: 12px;

.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: rgba(44, 62, 80, 0.95);
  backdrop-filter: blur(20px);
  transition: $transition;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);

  &--scrolled {
    background: rgba(44, 62, 80, 0.98);
    box-shadow: 0 8px 32px $shadow-dark;
  }

  .header__container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 2rem;
    max-width: 1400px;
    margin: 0 auto;
  }

  &__left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  &__right {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
}

// Logo Styles
.logo {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  text-decoration: none;
  transition: $transition;

  &:hover {
    transform: translateY(-2px);
  }

  &__icon {
    width: 48px;
    height: 48px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  &__letter {
    font-size: 2rem;
    font-weight: 700;
    color: $primary-color;
    position: relative;
    z-index: 2;
    text-shadow: 0 2px 10px rgba(52, 152, 219, 0.3);
  }

  &__text {
    font-size: 1.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, $primary-color, #9b59b6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    filter: drop-shadow(0 2px 4px rgba(52, 152, 219, 0.2));
  }
}

// Navigation Styles
.nav {
  flex: 1;
  display: flex;
  justify-content: flex-end; // moved from center to right align the menu
  margin: 0 2rem;

  &__list {
    display: flex;
    align-items: center;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 0.5rem;
  }

  &__item {
    position: relative;

    &--dropdown {
      .nav__link {
        position: relative;
      }

      &:hover .nav__dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0); // updated: only vertical translate
      }
    }
  }

  &__link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    color: $text-light;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95rem;
    border-radius: 10px;
    transition: $transition;
    position: relative;

    &:hover {
      background: rgba(255, 255, 255, 0.1);
      color: $primary-color;
      transform: translateY(-2px);
    }

    &--active {
      background: linear-gradient(135deg, $primary-color, $primary-hover);
      color: white;
      box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);

      &:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
      }
    }

    &--cta {
      background: linear-gradient(135deg, $accent-color, #c0392b);
      color: white;
      margin-left: 1rem;
      box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);

      &:hover {
        background: linear-gradient(135deg, #c0392b, $accent-color);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
      }
    }

    i {
      font-size: 0.9rem;
    }
  }

  &__dropdown-icon {
    font-size: 0.7rem !important;
    margin-left: 0.25rem;
    transition: $transition;

    .nav__item--dropdown:hover & {
      transform: rotate(180deg);
    }
  }

  &__dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    left: auto;
    transform: translateY(-10px);
    min-width: 320px;
    background: $bg-darker;
    border-radius: $border-radius;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    opacity: 0;
    visibility: hidden;
    transition: $transition;
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    z-index: 1100; // ensure above neighbors

    &--visible {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    &::before {
      content: '';
      position: absolute;
      top: -8px;
      right: 16px;
      border-left: 8px solid transparent;
      border-right: 8px solid transparent;
      border-bottom: 8px solid $bg-darker;
    }
  }

  &__dropdown-content {
    padding: 1rem 0;
  }

  &__dropdown-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    color: $text-light;
    text-decoration: none;
    transition: $transition;

    &:hover {
      background: rgba(52, 152, 219, 0.1);
      color: $primary-color;
    }
  }

  &__dropdown-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, $primary-color, $primary-hover);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: white;
    flex-shrink: 0;
  }

  &__dropdown-text {
    flex: 1;
  }

  &__dropdown-title {
    display: block;
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 0.25rem;
  }

  &__dropdown-desc {
    display: block;
    font-size: 0.8rem;
    color: $text-muted;
    line-height: 1.4;
  }
}

// Mobile Menu Toggle
.mobile-menu-toggle {
  display: none;
  width: 44px;
  height: 44px;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 8px;
  border-radius: 8px;
  transition: $transition;

  &:hover {
    background: rgba(255, 255, 255, 0.1);
  }

  &__line {
    display: block;
    width: 100%;
    height: 2px;
    background: $text-light;
    margin: 6px 0;
    transition: $transition;
    border-radius: 1px;
  }

  &--active {
    .mobile-menu-toggle__line {
      &:nth-child(1) {
        transform: rotate(45deg) translate(9px, 9px);
      }

      &:nth-child(2) {
        opacity: 0;
      }

      &:nth-child(3) {
        transform: rotate(-45deg) translate(9px, -9px);
      }
    }
  }
}

// Mobile Menu
.mobile-menu {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background: rgba(26, 37, 47, 0.98);
  backdrop-filter: blur(20px);
  z-index: 999;
  opacity: 0;
  visibility: hidden;
  transition: $transition;

  &--open {
    opacity: 1;
    visibility: visible;

    .mobile-menu__content {
      transform: translateY(0);
    }
  }

  &__content {
    padding: 0;
    height: 100%;
    transform: translateY(-50px);
    transition: $transition;
    display: flex;
    flex-direction: column;
  }

  &__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(26, 37, 47, 0.95);
  }

  &__logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;

    .logo__icon {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;

      .logo__letter {
        font-size: 1.5rem;
        font-weight: 700;
        color: $primary-color;
        text-shadow: 0 2px 10px rgba(52, 152, 219, 0.3);
      }
    }

    .logo__text {
      font-size: 1.5rem;
      font-weight: 700;
      background: linear-gradient(135deg, $primary-color, #9b59b6);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      filter: drop-shadow(0 2px 4px rgba(52, 152, 219, 0.2));
    }
  }

  &__close {
    width: 44px;
    height: 44px;
    background: transparent;
    border: none;
    cursor: pointer;
    border-radius: 8px;
    transition: $transition;
    display: flex;
    align-items: center;
    justify-content: center;
    color: $text-light;

    &:hover {
      background: rgba(231, 76, 60, 0.1);
      color: $accent-color;
      transform: scale(1.1);
    }

    i {
      font-size: 1.2rem;
    }
  }
}

.mobile-nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  padding: 2rem;
  flex: 1;
  overflow-y: auto;

  &__link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    color: $text-light;
    text-decoration: none;
    font-weight: 500;
    font-size: 1.1rem;
    border-radius: 12px;
    transition: $transition;

    &:hover {
      background: rgba(52, 152, 219, 0.1);
      color: $primary-color;
      transform: translateX(10px);
    }

    &--cta {
      background: linear-gradient(135deg, $accent-color, #c0392b);
      color: white;
      margin-top: 1rem;

      &:hover {
        background: linear-gradient(135deg, #c0392b, $accent-color);
        transform: translateX(10px) scale(1.02);
      }
    }

    i {
      font-size: 1.2rem;
      width: 24px;
      text-align: center;
    }
  }

  &__section {
    width: 100%;

    &-toggle {
      display: flex;
      align-items: center;
      gap: 1rem;
      width: 100%;
      padding: 1rem 1.5rem;
      background: transparent;
      border: none;
      cursor: pointer;
      font-size: 1.1rem;
      font-weight: 500;
      color: $text-light;
      border-radius: 12px;
      transition: $transition;
      text-align: left;

      &:hover {
        background: rgba(52, 152, 219, 0.1);
        color: $primary-color;
        transform: translateX(10px);
      }

      &--active {
        color: $primary-color;
        background: rgba(52, 152, 219, 0.1);

        .mobile-nav__chevron {
          transform: rotate(180deg);
        }
      }

      i:first-child {
        font-size: 1.2rem;
        width: 24px;
        text-align: center;
      }

      span {
        flex: 1;
      }
    }

    &-submenu {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);

      &--open {
        max-height: 400px;
      }
    }
  }

  &__submenu-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 1.5rem 0.75rem 3rem;
    color: $text-muted;
    text-decoration: none;
    font-weight: 400;
    font-size: 1rem;
    border-radius: 8px;
    margin: 0.25rem 0;
    transition: $transition;
    position: relative;

    &::before {
      content: '';
      position: absolute;
      left: 2.25rem;
      top: 50%;
      transform: translateY(-50%);
      width: 4px;
      height: 4px;
      background: $text-muted;
      border-radius: 50%;
      transition: $transition;
    }

    &:hover {
      background: rgba(52, 152, 219, 0.1);
      color: $primary-color;
      transform: translateX(15px);

      &::before {
        background: $primary-color;
        transform: translateY(-50%) scale(1.5);
      }
    }

    i {
      font-size: 1rem;
      width: 20px;
      text-align: center;
    }
  }

  &__chevron {
    font-size: 0.8rem !important;
    margin-left: auto;
    transition: transform 0.3s ease;
    width: auto !important;
  }
}

// Responsive Design
@media (max-width: 768px) {
  .nav {
    display: none;
  }

  .mobile-menu-toggle {
    display: block;
  }

  .header .header__container {
    padding: 1rem;
  }

  .logo__text {
    font-size: 1.5rem;
  }

  .logo__icon {
    width: 40px;
    height: 40px;
    font-size: 1.2rem;
  }
}

@media (max-width: 480px) {
  .header .header__container {
    padding: 0.75rem;
  }

  .logo__text {
    font-size: 1.3rem;
  }

  .logo__icon {
    width: 36px;
    height: 36px;
    font-size: 1rem;
  }
}

// Animation delays for mobile menu items
.mobile-nav__link {
  animation-delay: calc(var(--item-index, 0) * 0.1s);
}

// Smooth transitions for dropdown
.nav__dropdown {
  transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
}

// Focus states for accessibility
.logo:focus,
.nav__link:focus,
.mobile-nav__link:focus,
.mobile-menu-toggle:focus {
  outline: none;
}

// Optional: Add subtle focus indication without outline
.logo:focus {
  .logo__text {
    opacity: 0.8;
  }
}

.nav__link:focus {
  background: rgba(255, 255, 255, 0.05);
}

.mobile-nav__link:focus {
  background: rgba(52, 152, 219, 0.05);
}

.mobile-menu-toggle:focus {
  background: rgba(255, 255, 255, 0.05);
}
</style>
