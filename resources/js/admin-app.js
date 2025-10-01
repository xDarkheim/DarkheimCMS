import { createApp } from 'vue'
import AdminLayout from './layouts/AdminLayout.vue'

// Import all admin pages
import AdminDashboard from './admin-pages/AdminDashboard.vue'
import AdminUsers from './admin-pages/AdminUsers.vue'
import AdminNews from './admin-pages/AdminNews.vue'
import AdminPortfolio from './admin-pages/AdminPortfolio.vue'
import AdminTeam from './admin-pages/AdminTeam.vue'
import AdminCareers from './admin-pages/AdminCareers.vue'
import AdminContactMessages from './admin-pages/AdminContactMessages.vue'
import AdminSettings from './admin-pages/AdminSettings.vue'
import AdminFileManager from './admin-pages/AdminFileManager.vue'
import AdminActivityLogs from './admin-pages/AdminActivityLogs.vue'
import AdminContactInfo from './admin-pages/AdminContactInfo.vue'
import AdminLogin from './admin-pages/AdminLogin.vue'

// Create Vue app
const app = createApp({
  components: {
    AdminLayout,
    AdminDashboard,
    AdminUsers,
    AdminNews,
    AdminPortfolio,
    AdminTeam,
    AdminCareers,
    AdminContactMessages,
    AdminSettings,
    AdminFileManager,
    AdminActivityLogs,
    AdminContactInfo,
    AdminLogin
  }
})

// Global error handler for better debugging
app.config.errorHandler = (err, instance, info) => {
  console.error('Global error:', err)
  console.error('Component instance:', instance)
  console.error('Error info:', info)
}

// Mount the app
app.mount('#admin-app')

export default app
