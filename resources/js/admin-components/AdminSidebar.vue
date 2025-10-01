<template>
  <aside
    class="admin-sidebar"
    :class="{
      'collapsed': isCollapsed,
      'mobile-open': isMobileOpen
    }"
  >
    <!-- Sidebar Header -->
    <div class="sidebar-header">
      <div class="logo-section">
        <div class="logo-icon">
          <i class="fas fa-code"></i>
        </div>
        <transition name="fade">
          <div v-if="!isCollapsed" class="logo-text">
            <h2>Admin Panel</h2>
            <span>Darkheim CMS</span>
          </div>
        </transition>
      </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
      <div class="nav-section">
        <div v-if="!isCollapsed" class="nav-section-title">Main</div>

        <ul class="nav-list">
          <li v-for="route in mainRoutes" :key="route.name">
            <router-link
              :to="{ name: route.name }"
              class="nav-link"
              :class="{ 'active': isActiveRoute(route.name) }"
              @click="handleNavClick"
            >
              <div class="nav-icon">
                <i :class="route.meta.icon"></i>
              </div>
              <transition name="fade">
                <span v-if="!isCollapsed" class="nav-text">
                  {{ route.meta.title }}
                </span>
              </transition>
              <div v-if="route.badge && !isCollapsed" class="nav-badge">
                {{ route.badge }}
              </div>
            </router-link>
          </li>
        </ul>
      </div>

      <div class="nav-section">
        <div v-if="!isCollapsed" class="nav-section-title">Content</div>

        <ul class="nav-list">
          <li v-for="route in contentRoutes" :key="route.name">
            <router-link
              :to="{ name: route.name }"
              class="nav-link"
              :class="{ 'active': isActiveRoute(route.name) }"
              @click="handleNavClick"
            >
              <div class="nav-icon">
                <i :class="route.meta.icon"></i>
              </div>
              <transition name="fade">
                <span v-if="!isCollapsed" class="nav-text">
                  {{ route.meta.title }}
                </span>
              </transition>
              <div v-if="route.badge && !isCollapsed" class="nav-badge">
                {{ route.badge }}
              </div>
            </router-link>
          </li>
        </ul>
      </div>

      <div class="nav-section">
        <div v-if="!isCollapsed" class="nav-section-title">System</div>

        <ul class="nav-list">
          <li v-for="route in systemRoutes" :key="route.name">
            <router-link
              :to="{ name: route.name }"
              class="nav-link"
              :class="{ 'active': isActiveRoute(route.name) }"
              @click="handleNavClick"
            >
              <div class="nav-icon">
                <i :class="route.meta.icon"></i>
              </div>
              <transition name="fade">
                <span v-if="!isCollapsed" class="nav-text">
                  {{ route.meta.title }}
                </span>
              </transition>
              <div v-if="route.badge && !isCollapsed" class="nav-badge">
                {{ route.badge }}
              </div>
            </router-link>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
      <div class="user-section">
        <div class="user-avatar">
          <i class="fas fa-user-circle"></i>
        </div>
        <transition name="fade">
          <div v-if="!isCollapsed" class="user-info">
            <div class="user-name">Admin User</div>
            <div class="user-role">Administrator</div>
          </div>
        </transition>
      </div>

      <div class="footer-actions">
        <button
          class="action-btn"
          @click="handleLogout"
          :title="isCollapsed ? 'Logout' : ''"
        >
          <i class="fas fa-sign-out-alt"></i>
          <span v-if="!isCollapsed">Logout</span>
        </button>
      </div>
    </div>

    <!-- Collapse Toggle -->
    <button
      class="collapse-toggle"
      @click="$emit('toggle')"
      :title="isCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'"
    >
      <i class="fas fa-chevron-left" :class="{ 'rotated': isCollapsed }"></i>
    </button>
  </aside>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import adminContactService from '../admin-services/contactService.js'

const props = defineProps({
  isCollapsed: {
    type: Boolean,
    default: false
  },
  isMobileOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['toggle', 'close-mobile'])

const route = useRoute()
const router = useRouter()

// Reactive data for unread messages count
const unreadMessagesCount = ref(0)

// Load unread messages count
const loadUnreadCount = async () => {
  try {
    const response = await adminContactService.getStats()
    if (response.success) {
      unreadMessagesCount.value = response.data.unread || 0
    }
  } catch (error) {
    console.error('Failed to load unread messages count:', error)
  }
}

// Navigation routes organized by sections
const mainRoutes = computed(() => [
  {
    name: 'admin.dashboard',
    meta: { title: 'Dashboard', icon: 'fas fa-tachometer-alt' }
  }
])

const contentRoutes = computed(() => [
  {
    name: 'admin.portfolio',
    meta: { title: 'Portfolio', icon: 'fas fa-briefcase' },
    badge: null
  },
  {
    name: 'admin.news',
    meta: { title: 'News', icon: 'fas fa-newspaper' },
    badge: null
  },
  {
    name: 'admin.careers',
    meta: { title: 'Careers', icon: 'fas fa-user-tie' }
  },
  {
    name: 'admin.team',
    meta: { title: 'Team', icon: 'fas fa-users' }
  },
  {
    name: 'admin.contact-messages',
    meta: { title: 'Messages', icon: 'fas fa-envelope' },
    badge: unreadMessagesCount.value > 0 ? unreadMessagesCount.value.toString() : null
  },
  {
    name: 'admin.contact-info',
    meta: { title: 'Contact Info', icon: 'fas fa-address-book' }
  }
])

const systemRoutes = computed(() => [
  {
    name: 'admin.users',
    meta: { title: 'Users', icon: 'fas fa-user-shield' },
    badge: null
  },
  {
    name: 'admin.file-manager',
    meta: { title: 'File Manager', icon: 'fas fa-folder-open' },
    badge: null
  },
  {
    name: 'admin.activity-logs',
    meta: { title: 'Activity Logs', icon: 'fas fa-history' },
    badge: null
  },
  {
    name: 'admin.settings',
    meta: { title: 'Settings', icon: 'fas fa-cog' },
    badge: null
  }
])

const isActiveRoute = (routeName) => {
  return route.name === routeName
}

const handleNavClick = () => {
  emit('close-mobile')
}

const handleLogout = () => {
  localStorage.removeItem('admin_token')
  router.push('/admin/login')
}

// Load data on mount and refresh periodically
onMounted(() => {
  loadUnreadCount()
  // Refresh unread count every 30 seconds
  setInterval(loadUnreadCount, 30000)
})
</script>

<style lang="scss" scoped>
$sidebar-width: 280px;
$sidebar-collapsed-width: 80px;
$primary-color: #3498db;
$primary-hover: #2980b9;
$text-primary: #2c3e50;
$text-secondary: #6c757d;
$text-muted: #adb5bd;
$bg-dark: #2c3e50;
$bg-darker: #1a252f;
$bg-light: #f8f9fa;
$border-color: #34495e;
$transition: all 0.3s ease;

.admin-sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: $sidebar-width;
  height: 100vh;
  background: linear-gradient(180deg, $bg-dark 0%, $bg-darker 100%);
  color: white;
  display: flex;
  flex-direction: column;
  z-index: 1000;
  transition: $transition;
  box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);

  &.collapsed {
    width: $sidebar-collapsed-width;
  }

  &.mobile-open {
    transform: translateX(0);
  }
}

.sidebar-header {
  padding: 1.5rem;
  border-bottom: 1px solid $border-color;
  background: rgba(255, 255, 255, 0.05);

  .logo-section {
    display: flex;
    align-items: center;
    gap: 1rem;

    .logo-icon {
      width: 48px;
      height: 48px;
      background: $primary-color;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      color: white;
      box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    }

    .logo-text {
      h2 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        color: white;
      }

      span {
        font-size: 0.875rem;
        color: $text-muted;
      }
    }
  }
}

.sidebar-nav {
  flex: 1;
  overflow-y: auto;
  padding: 1rem 0;

  &::-webkit-scrollbar {
    width: 4px;
  }

  &::-webkit-scrollbar-track {
    background: transparent;
  }

  &::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
  }
}

.nav-section {
  margin-bottom: 2rem;

  .nav-section-title {
    padding: 0 1.5rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: $text-muted;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 1rem;
  }
}

.nav-list {
  list-style: none;
  margin: 0;
  padding: 0;

  li {
    margin: 0.25rem 0;
  }
}

.nav-link {
  display: flex;
  align-items: center;
  padding: 1rem 1.5rem;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  transition: $transition;
  position: relative;
  border-radius: 0 25px 25px 0;
  margin-right: 1rem;

  &:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    transform: translateX(4px);
  }

  &.active {
    background: linear-gradient(135deg, $primary-color, $primary-hover);
    color: white;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);

    &::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 4px;
      background: white;
      border-radius: 0 2px 2px 0;
    }
  }

  .nav-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    margin-right: 1rem;
  }

  .nav-text {
    flex: 1;
    font-weight: 500;
    font-size: 0.875rem;
  }

  .nav-badge {
    background: #e74c3c;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    min-width: 20px;
    text-align: center;
  }
}

.sidebar-footer {
  padding: 1.5rem;
  border-top: 1px solid $border-color;
  background: rgba(0, 0, 0, 0.1);

  .user-section {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: $primary-color;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.25rem;
      color: white;
    }

    .user-info {
      .user-name {
        font-weight: 600;
        color: white;
        font-size: 0.875rem;
      }

      .user-role {
        font-size: 0.75rem;
        color: $text-muted;
      }
    }
  }

  .footer-actions {
    display: flex;
    gap: 0.5rem;

    .action-btn {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.75rem;
      background: rgba(255, 255, 255, 0.1);
      border: none;
      border-radius: 8px;
      color: rgba(255, 255, 255, 0.8);
      cursor: pointer;
      transition: $transition;
      font-size: 0.875rem;

      &:hover {
        background: rgba(231, 76, 60, 0.2);
        color: #e74c3c;
      }
    }
  }
}

.collapse-toggle {
  position: absolute;
  top: 50%;
  right: -15px;
  width: 30px;
  height: 30px;
  background: $primary-color;
  border: none;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
  transition: $transition;
  z-index: 1001;

  &:hover {
    background: $primary-hover;
    transform: scale(1.1);
  }

  i {
    transition: transform 0.3s ease;

    &.rotated {
      transform: rotate(180deg);
    }
  }
}

// Transitions
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

// Mobile styles
@media (max-width: 768px) {
  .admin-sidebar {
    transform: translateX(-100%);
    width: $sidebar-width;

    &.mobile-open {
      transform: translateX(0);
    }

    &.collapsed {
      width: $sidebar-width;
    }
  }

  .collapse-toggle {
    display: none;
  }
}

// Collapsed state adjustments
.admin-sidebar.collapsed {
  .nav-link {
    justify-content: center;
    margin-right: 0;
    border-radius: 0;

    .nav-icon {
      margin-right: 0;
    }
  }

  .nav-section-title {
    text-align: center;
    padding: 0.75rem;
  }

  .user-section {
    justify-content: center;

    .user-avatar {
      margin: 0;
    }
  }

  .footer-actions .action-btn {
    padding: 0.75rem 0.5rem;
  }
}
</style>
