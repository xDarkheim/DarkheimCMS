import { createApp } from 'vue'
import AdminApp from './AdminApp.vue'
import router from './admin-router'
import './admin-styles.css'

const app = createApp(AdminApp)
app.use(router)

// Make router available globally for API interceptor
window.adminRouter = router

app.mount('#admin-app')
