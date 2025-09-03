<template>
  <header class="header" :class="{ 'header--scrolled': isScrolled }">
    <div class="container">
      <router-link to="/" class="logo">
        <i class="fas fa-terminal"></i>
        <span class="text-gradient">Darkheim</span>
      </router-link>

      <nav class="nav">
        <ul class="nav__list">
          <li class="nav__item">
            <router-link
              to="/"
              class="nav__link"
              :class="{ 'nav__link--active': $route.name === 'Home' }"
            >
              <i class="fas fa-home"></i>
              Home
            </router-link>
          </li>
          <li class="nav__item">
            <router-link
              to="/news"
              class="nav__link"
              :class="{ 'nav__link--active': $route.name === 'News' }"
            >
              <i class="fas fa-newspaper"></i>
              News
            </router-link>
          </li>
          <li class="nav__item">
            <router-link
              to="/about"
              class="nav__link"
              :class="{ 'nav__link--active': $route.name === 'About' }"
            >
              <i class="fas fa-user-tie"></i>
              About
            </router-link>
          </li>
          <li class="nav__item">
            <router-link
              to="/services"
              class="nav__link"
              :class="{ 'nav__link--active': $route.name === 'Services' }"
            >
              <i class="fas fa-laptop-code"></i>
              Services
            </router-link>
          </li>
          <li class="nav__item">
            <router-link
              to="/portfolio"
              class="nav__link"
              :class="{ 'nav__link--active': $route.name === 'Portfolio' }"
            >
              <i class="fas fa-folder-open"></i>
              Portfolio
            </router-link>
          </li>
          <li class="nav__item">
            <router-link
              to="/contact"
              class="nav__link"
              :class="{ 'nav__link--active': $route.name === 'Contact' }"
            >
              <i class="fas fa-paper-plane"></i>
              Contact
            </router-link>
          </li>
        </ul>
      </nav>

      <button
        class="mobile-menu-toggle"
        @click="toggleMobileMenu"
        aria-label="Toggle mobile menu"
      >
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </header>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'

export default {
  name: 'AppHeader',
  setup() {
    const isScrolled = ref(false)

    const handleScroll = () => {
      isScrolled.value = window.scrollY > 50
    }

    const toggleMobileMenu = () => {
      // Mobile menu functionality - можно расширить позже
      console.log('Mobile menu toggle')
    }

    onMounted(() => {
      window.addEventListener('scroll', handleScroll)
    })

    onUnmounted(() => {
      window.removeEventListener('scroll', handleScroll)
    })

    return {
      isScrolled,
      toggleMobileMenu
    }
  }
}
</script>
