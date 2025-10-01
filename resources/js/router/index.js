import { createRouter, createWebHistory } from 'vue-router'

// Import pages/views for main site only
import HomePage from '../pages/HomePage.vue'
import AboutPage from '../pages/AboutPage.vue'
import ServicesPage from '../pages/ServicesPage.vue'
import PortfolioPage from '../pages/PortfolioPage.vue'
import ContactPage from '../pages/ContactPage.vue'
import NewsPage from '../pages/NewsPage.vue'
import NewsArticle from '../pages/NewsArticle.vue'
import CareerPage from '../pages/CareerPage.vue'
import TeamPage from '../pages/TeamPage.vue'
import PrivacyPolicyPage from '../pages/PrivacyPolicyPage.vue'
import TermsOfServicePage from '../pages/TermsOfServicePage.vue'
import CookiePolicyPage from '../pages/CookiePolicyPage.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomePage,
    meta: {
      title: 'Darkheim Development Studio - Professional Web Development'
    }
  },
  {
    path: '/about',
    name: 'about',
    component: AboutPage,
    meta: {
      title: 'About Us - Darkheim Development Studio'
    }
  },
  {
    path: '/services',
    name: 'services',
    component: ServicesPage,
    meta: {
      title: 'Our Services - Web Development & Design'
    }
  },
  {
    path: '/portfolio',
    name: 'portfolio',
    component: PortfolioPage,
    meta: {
      title: 'Portfolio - Our Work & Projects'
    }
  },
  {
    path: '/contact',
    name: 'contact',
    component: ContactPage,
    meta: {
      title: 'Contact Us - Get Your Project Started'
    }
  },
  {
    path: '/news',
    name: 'news',
    component: NewsPage,
    meta: {
      title: 'News & Blog - Latest Updates'
    }
  },
  {
    path: '/news/:slug',
    name: 'news-article',
    component: NewsArticle,
    meta: {
      title: 'News Article - Darkheim Development Studio'
    }
  },
  {
    path: '/careers',
    name: 'careers',
    component: CareerPage,
    meta: {
      title: 'Careers - Join Our Team'
    }
  },
  {
    path: '/team',
    name: 'team',
    component: TeamPage,
    meta: {
      title: 'Our Team - Meet the People Behind Darkheim'
    }
  },
  {
    path: '/privacy-policy',
    name: 'privacy-policy',
    component: PrivacyPolicyPage,
    meta: {
      title: 'Privacy Policy - Darkheim Development Studio'
    }
  },
  {
    path: '/terms-of-service',
    name: 'terms-of-service',
    component: TermsOfServicePage,
    meta: {
      title: 'Terms of Service - Darkheim Development Studio'
    }
  },
  {
    path: '/cookie-policy',
    name: 'cookie-policy',
    component: CookiePolicyPage,
    meta: {
      title: 'Cookie Policy - Darkheim Development Studio'
    }
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

// Guard для обновления title страницы
router.beforeEach((to, from, next) => {
  // Устанавливаем title из meta данных маршрута только при навигации
  if (to.meta && to.meta.title) {
    document.title = to.meta.title
  } else {
    document.title = 'Darkheim Development Studio'
  }
  next()
})

export default router
