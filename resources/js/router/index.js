import { createRouter, createWebHistory } from 'vue-router'

// Import pages/views
import HomePage from '../pages/HomePage.vue'
import AboutPage from '../pages/AboutPage.vue'
import ServicesPage from '../pages/ServicesPage.vue'
import PortfolioPage from '../pages/PortfolioPage.vue'
import ContactPage from '../pages/ContactPage.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomePage
  },
  {
    path: '/about',
    name: 'about',
    component: AboutPage
  },
  {
    path: '/services',
    name: 'services',
    component: ServicesPage
  },
  {
    path: '/portfolio',
    name: 'portfolio',
    component: PortfolioPage
  },
  {
    path: '/contact',
    name: 'contact',
    component: ContactPage
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth'
      }
    }
    if (savedPosition) {
      return savedPosition
    }
    return { top: 0 }
  }
})

export default router
