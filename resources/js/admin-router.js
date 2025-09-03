import { createRouter, createWebHistory } from 'vue-router'
import AdminLayout from './admin-components/AdminLayout.vue'
import AdminLogin from './admin-pages/AdminLogin.vue'
import AdminDashboard from './admin-pages/AdminDashboard.vue'
import AdminPortfolio from './admin-pages/AdminPortfolio.vue'
import AdminNews from './admin-pages/AdminNews.vue'
import AdminUsers from './admin-pages/AdminUsers.vue'

const routes = [
  {
    path: '/admin/login',
    name: 'admin.login',
    component: AdminLogin,
    meta: { requiresGuest: true }
  },
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/admin/dashboard'
      },
      {
        path: 'dashboard',
        name: 'admin.dashboard',
        component: AdminDashboard,
        meta: {
          title: 'Dashboard',
          icon: 'fas fa-tachometer-alt'
        }
      },
      {
        path: 'portfolio',
        name: 'admin.portfolio',
        component: AdminPortfolio,
        meta: {
          title: 'Portfolio',
          icon: 'fas fa-briefcase'
        }
      },
      {
        path: 'news',
        name: 'admin.news',
        component: AdminNews,
        meta: {
          title: 'News',
          icon: 'fas fa-newspaper'
        }
      },
      {
        path: 'users',
        name: 'admin.users',
        component: AdminUsers,
        meta: {
          title: 'Users',
          icon: 'fas fa-users'
        }
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('admin_token')

  if (to.meta.requiresAuth && !token) {
    next('/admin/login')
  } else if (to.meta.requiresGuest && token) {
    next('/admin/dashboard')
  } else {
    next()
  }
})

export default router
