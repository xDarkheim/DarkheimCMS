import { createRouter, createWebHistory } from 'vue-router'
import AdminLayout from './admin-components/AdminLayout.vue'
import AdminLogin from './admin-pages/AdminLogin.vue'
import AdminDashboard from './admin-pages/AdminDashboard.vue'
import AdminPortfolio from './admin-pages/AdminPortfolio.vue'
import AdminNews from './admin-pages/AdminNews.vue'
import AdminUsers from './admin-pages/AdminUsers.vue'
import AdminContactMessages from './admin-pages/AdminContactMessages.vue'
import AdminContactInfo from './admin-pages/AdminContactInfo.vue'
import AdminSettings from './admin-pages/AdminSettings.vue'
import AdminCareers from './admin-pages/AdminCareers.vue'
import AdminTeam from './admin-pages/AdminTeam.vue'
import AdminFileManager from './admin-pages/AdminFileManager.vue'
import AdminActivityLogs from './admin-pages/AdminActivityLogs.vue'

const routes = [
  {
    path: '/admin/login',
    name: 'admin.login',
    component: AdminLogin,
    meta: {
      requiresGuest: true,
      title: 'Admin Login - Darkheim Development Studio'
    }
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
          title: 'Admin Dashboard - Darkheim Development Studio',
          icon: 'fas fa-tachometer-alt'
        }
      },
      {
        path: 'portfolio',
        name: 'admin.portfolio',
        component: AdminPortfolio,
        meta: {
          title: 'Portfolio Management - Admin Panel',
          icon: 'fas fa-briefcase'
        }
      },
      {
        path: 'news',
        name: 'admin.news',
        component: AdminNews,
        meta: {
          title: 'News Management - Admin Panel',
          icon: 'fas fa-newspaper'
        }
      },
      {
        path: 'users',
        name: 'admin.users',
        component: AdminUsers,
        meta: {
          title: 'User Management - Admin Panel',
          icon: 'fas fa-users'
        }
      },
      {
        path: 'contact-messages',
        name: 'admin.contact-messages',
        component: AdminContactMessages,
        meta: {
          title: 'Contact Messages - Admin Panel',
          icon: 'fas fa-envelope'
        }
      },
      {
        path: 'contact-info',
        name: 'admin.contact-info',
        component: AdminContactInfo,
        meta: {
          title: 'Contact Information - Admin Panel',
          icon: 'fas fa-address-book'
        }
      },
      {
        path: 'careers',
        name: 'admin.careers',
        component: AdminCareers,
        meta: {
          title: 'Career Management - Admin Panel',
          icon: 'fas fa-briefcase'
        }
      },
      {
        path: 'team',
        name: 'admin.team',
        component: AdminTeam,
        meta: {
          title: 'Team Management - Admin Panel',
          icon: 'fas fa-users'
        }
      },
      {
        path: 'settings',
        name: 'admin.settings',
        component: AdminSettings,
        meta: {
          title: 'Settings - Admin Panel',
          icon: 'fas fa-cog'
        }
      },
      {
        path: 'file-manager',
        name: 'admin.file-manager',
        component: AdminFileManager,
        meta: {
          title: 'File Manager - Admin Panel',
          icon: 'fas fa-file'
        }
      },
      {
        path: 'activity-logs',
        name: 'admin.activity-logs',
        component: AdminActivityLogs,
        meta: {
          title: 'Activity Logs - Admin Panel',
          icon: 'fas fa-list'
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

  // Устанавливаем title из meta данных маршрута только пр�� навигации
  if (to.meta && to.meta.title) {
    document.title = to.meta.title
  } else {
    document.title = 'Admin Panel - Darkheim Development Studio'
  }

  if (to.meta.requiresAuth && !token) {
    next('/admin/login')
  } else if (to.meta.requiresGuest && token) {
    next('/admin/dashboard')
  } else {
    next()
  }
})

export default router
