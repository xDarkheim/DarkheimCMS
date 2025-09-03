<template>
  <header class="admin-topbar">
    <div class="topbar-left">
      <button
        class="mobile-menu-toggle"
        @click="$emit('toggle-sidebar')"
      >
        <i class="fas fa-bars"></i>
      </button>

      <div class="breadcrumb">
        <span class="breadcrumb-item">
          <i :class="currentRoute.icon"></i>
          {{ currentRoute.title }}
        </span>
      </div>
    </div>

    <div class="topbar-right">
      <div class="search-box">
        <div class="search-input">
          <i class="fas fa-search search-icon"></i>
          <input
            type="text"
            placeholder="Search..."
            v-model="searchQuery"
            @input="handleSearch"
          />
        </div>
      </div>

      <div class="topbar-actions">
        <div class="notifications-dropdown">
          <button
            class="action-btn notifications-btn"
            @click="toggleNotifications"
            :class="{ active: showNotifications }"
          >
            <i class="fas fa-bell"></i>
            <span v-if="unreadCount > 0" class="notification-badge">{{ unreadCount }}</span>
          </button>

          <div v-if="showNotifications" class="dropdown notifications-panel">
            <div class="dropdown-header">
              <h3>Notifications</h3>
              <span class="badge">{{ unreadCount }}</span>
            </div>
            <div class="notifications-list">
              <div
                v-if="notifications.length === 0"
                class="notification-item empty"
              >
                <i class="fas fa-bell-slash"></i>
                No new notifications
              </div>
              <div
                v-for="notification in notifications"
                :key="notification.id"
                class="notification-item"
                :class="{ unread: !notification.read }"
                @click="markAsRead(notification.id)"
              >
                <div class="notification-icon">
                  <i :class="notification.icon"></i>
                </div>
                <div class="notification-content">
                  <h4 class="notification-title">{{ notification.title }}</h4>
                  <p class="notification-text">{{ notification.message }}</p>
                  <span class="notification-time">{{ formatTime(notification.created_at) }}</span>
                </div>
              </div>
            </div>
            <div class="dropdown-footer">
              <a href="/admin/notifications" class="view-all-btn">View All</a>
            </div>
          </div>
        </div>

        <a href="/admin/settings" class="action-btn">
          <i class="fas fa-cog"></i>
        </a>

        <div class="user-menu">
          <button
            class="user-menu-toggle"
            @click="toggleUserMenu"
            :class="{ active: showUserMenu }"
          >
            <div class="user-avatar">
              {{ userInitials }}
            </div>
            <span class="user-name">{{ userName }}</span>
            <i class="fas fa-chevron-down dropdown-icon"></i>
          </button>

          <div v-if="showUserMenu" class="dropdown user-dropdown">
            <a href="/admin/profile" class="dropdown-item">
              <i class="fas fa-user"></i>
              Profile
            </a>
            <a href="/admin/settings" class="dropdown-item">
              <i class="fas fa-cog"></i>
              Settings
            </a>
            <hr class="dropdown-divider">
            <a href="#" @click="logout" class="dropdown-item logout-item">
              <i class="fas fa-sign-out-alt"></i>
              Logout
            </a>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'

export default {
  name: 'AdminTopbar',
  emits: ['toggle-sidebar'],
  setup() {
    const showNotifications = ref(false)
    const showUserMenu = ref(false)
    const searchQuery = ref('')
    const notifications = ref([])
    const userName = ref('Admin User')

    const currentRoute = computed(() => {
      const path = window.location.pathname
      const routes = {
        '/admin': { title: 'Dashboard', icon: 'fas fa-tachometer-alt' },
        '/admin/portfolio': { title: 'Portfolio', icon: 'fas fa-briefcase' },
        '/admin/news': { title: 'News', icon: 'fas fa-newspaper' },
        '/admin/contacts': { title: 'Contacts', icon: 'fas fa-envelope' },
        '/admin/settings': { title: 'Settings', icon: 'fas fa-cog' },
      }
      return routes[path] || { title: 'Admin Panel', icon: 'fas fa-cog' }
    })

    const userInitials = computed(() => {
      return userName.value
        .split(' ')
        .map(name => name.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2)
    })

    const unreadCount = computed(() => {
      return notifications.value.filter(n => !n.read).length
    })

    const toggleNotifications = () => {
      showNotifications.value = !showNotifications.value
      showUserMenu.value = false
    }

    const toggleUserMenu = () => {
      showUserMenu.value = !showUserMenu.value
      showNotifications.value = false
    }

    const handleSearch = () => {
      console.log('Searching for:', searchQuery.value)
    }

    const markAsRead = (notificationId) => {
      const notification = notifications.value.find(n => n.id === notificationId)
      if (notification) {
        notification.read = true
      }
    }

    const formatTime = (timestamp) => {
      const date = new Date(timestamp)
      const now = new Date()
      const diff = now - date
      const minutes = Math.floor(diff / 60000)
      const hours = Math.floor(minutes / 60)
      const days = Math.floor(hours / 24)

      if (days > 0) return `${days}d ago`
      if (hours > 0) return `${hours}h ago`
      if (minutes > 0) return `${minutes}m ago`
      return 'Just now'
    }

    const logout = () => {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = '/admin/login'
      }
    }

    const closeDropdowns = (event) => {
      if (!event.target.closest('.notifications-dropdown') && !event.target.closest('.user-menu')) {
        showNotifications.value = false
        showUserMenu.value = false
      }
    }

    const loadNotifications = () => {
      notifications.value = [
        {
          id: 1,
          title: 'New contact message',
          message: 'You have received a new message from John Doe',
          icon: 'fas fa-envelope',
          read: false,
          created_at: new Date(Date.now() - 5 * 60000).toISOString()
        },
        {
          id: 2,
          title: 'Portfolio updated',
          message: 'Project "Website Redesign" has been updated',
          icon: 'fas fa-briefcase',
          read: false,
          created_at: new Date(Date.now() - 30 * 60000).toISOString()
        },
        {
          id: 3,
          title: 'System backup completed',
          message: 'Daily backup process completed successfully',
          icon: 'fas fa-check-circle',
          read: true,
          created_at: new Date(Date.now() - 2 * 60 * 60000).toISOString()
        }
      ]
    }

    onMounted(() => {
      document.addEventListener('click', closeDropdowns)
      loadNotifications()
    })

    onUnmounted(() => {
      document.removeEventListener('click', closeDropdowns)
    })

    return {
      showNotifications,
      showUserMenu,
      searchQuery,
      notifications,
      userName,
      currentRoute,
      userInitials,
      unreadCount,
      toggleNotifications,
      toggleUserMenu,
      handleSearch,
      markAsRead,
      formatTime,
      logout
    }
  }
}
</script>

<style lang="scss" scoped>
// AdminTopbar Component Styles
// Clean admin design matching AdminPortfolio/AdminNews

.admin-topbar {
  height: 70px;
  background: white;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 2rem;
  position: sticky;
  top: 0;
  z-index: 1000;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
  font-family: var(--font-family, 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif);
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 1.5rem;

  .mobile-menu-toggle {
    display: none;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    font-size: 1.1rem;
    color: #495057;
    cursor: pointer;
    padding: 0.75rem;
    transition: all 0.2s ease;

    &:hover {
      background: #e9ecef;
      color: #3498db;
    }

    @media (max-width: 768px) {
      display: flex;
    }
  }

  .breadcrumb {
    .breadcrumb-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-size: 1.25rem;
      font-weight: 700;
      color: #2c3e50;

      i {
        color: #3498db;
        background: #f8f9fa;
        padding: 0.5rem;
        border-radius: 8px;
        font-size: 1rem;
      }
    }
  }
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.search-box {
  .search-input {
    position: relative;
    width: 300px;

    .search-icon {
      position: absolute;
      left: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
      z-index: 1;
      font-size: 0.9rem;
    }

    input {
      width: 100%;
      padding: 0.75rem 1rem 0.75rem 2.5rem;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      background: #f8f9fa;
      font-size: 0.9rem;
      font-family: inherit;
      transition: all 0.2s ease;
      color: #495057;

      &:focus {
        outline: none;
        border-color: #3498db;
        background: white;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
      }

      &::placeholder {
        color: #adb5bd;
      }
    }

    @media (max-width: 768px) {
      width: 250px;
    }

    @media (max-width: 580px) {
      display: none;
    }
  }
}

.topbar-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;

  .action-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-size: 1rem;

    &:hover {
      background: #f8f9fa;
      border-color: #3498db;
      color: #3498db;
    }

    &.active {
      background: #3498db;
      border-color: #3498db;
      color: white;
    }

    .notification-badge {
      position: absolute;
      top: -6px;
      right: -6px;
      background: #e74c3c;
      color: white;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      font-size: 0.7rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid white;
    }
  }
}

.notifications-dropdown,
.user-menu {
  position: relative;
}

.user-menu-toggle {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 1rem;
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #495057;
  font-size: 0.9rem;
  font-family: inherit;

  &:hover {
    background: #f8f9fa;
    border-color: #3498db;
  }

  &.active {
    background: #f8f9fa;
    border-color: #3498db;
  }

  .user-avatar {
    width: 32px;
    height: 32px;
    background: #3498db;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    font-weight: 600;
  }

  .user-name {
    font-weight: 500;

    @media (max-width: 768px) {
      display: none;
    }
  }

  .dropdown-icon {
    font-size: 0.7rem;
    transition: transform 0.2s ease;
    color: #6c757d;
  }

  &:hover .dropdown-icon,
  &.active .dropdown-icon {
    transform: rotate(180deg);
  }
}

.dropdown {
  position: absolute;
  top: calc(100% + 0.5rem);
  right: 0;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  z-index: 1001;
  min-width: 220px;
  overflow: hidden;
  animation: slideDown 0.2s ease;

  &.user-dropdown {
    .dropdown-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 1rem 1.25rem;
      color: #2c3e50;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: background 0.2s ease;
      border-bottom: 1px solid #f1f3f4;
      font-family: inherit;

      &:last-child {
        border-bottom: none;
      }

      &:hover {
        background: #f8f9fa;
      }

      &.logout-item {
        color: #e74c3c;
      }

      i {
        width: 16px;
        color: #6c757d;
        font-size: 1rem;
      }
    }

    .dropdown-divider {
      margin: 0.5rem 0;
      border: none;
      border-top: 1px solid #e2e8f0;
    }
  }
}

.notifications-panel {
  min-width: 380px;
  max-width: 420px;

  .dropdown-header {
    padding: 1.5rem;
    background: #f8f9fa;
    color: #2c3e50;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e2e8f0;

    h3 {
      margin: 0;
      font-size: 1rem;
      font-weight: 600;
      font-family: inherit;
    }

    .badge {
      background: #3498db;
      color: white;
      padding: 0.25rem 0.5rem;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: 600;
    }
  }

  .notifications-list {
    max-height: 320px;
    overflow-y: auto;

    &::-webkit-scrollbar {
      width: 6px;
    }

    &::-webkit-scrollbar-track {
      background: #f8f9fa;
    }

    &::-webkit-scrollbar-thumb {
      background: #adb5bd;
      border-radius: 3px;
    }

    .notification-item {
      display: flex;
      gap: 1rem;
      padding: 1rem 1.5rem;
      border-bottom: 1px solid #f1f3f4;
      cursor: pointer;
      transition: background 0.2s ease;
      position: relative;

      &:last-child {
        border-bottom: none;
      }

      &:hover {
        background: #f8f9fa;
      }

      &.unread {
        background: rgba(52, 152, 219, 0.05);
        border-left: 4px solid #3498db;

        .notification-title {
          font-weight: 600;
        }
      }

      &.loading,
      &.empty {
        justify-content: center;
        color: #6c757d;
        padding: 2rem 1.5rem;
        border: none;
        font-family: inherit;

        &:hover {
          background: none;
        }

        i {
          margin-right: 0.5rem;
        }
      }

      .notification-icon {
        width: 40px;
        height: 40px;
        background: #3498db;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        flex-shrink: 0;
      }

      .notification-content {
        flex: 1;
        min-width: 0;

        .notification-title {
          margin: 0 0 0.25rem 0;
          font-weight: 500;
          font-size: 0.9rem;
          color: #2c3e50;
          line-height: 1.4;
          font-family: inherit;
        }

        .notification-text {
          margin: 0;
          color: #6c757d;
          font-size: 0.85rem;
          line-height: 1.5;
          font-family: inherit;
        }

        .notification-time {
          position: absolute;
          top: 1rem;
          right: 1.5rem;
          font-size: 0.75rem;
          color: #adb5bd;
          font-weight: 500;
          font-family: inherit;
        }
      }
    }
  }

  .dropdown-footer {
    padding: 1rem 1.5rem;
    text-align: center;
    background: #f8f9fa;
    border-top: 1px solid #e2e8f0;

    .view-all-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.75rem 1.5rem;
      background: #3498db;
      color: white;
      border-radius: 8px;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: all 0.2s ease;
      font-family: inherit;

      &:hover {
        background: #2980b9;
      }
    }
  }
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

// Responsive styles
@media (max-width: 768px) {
  .admin-topbar {
    padding: 0 1rem;
    height: 60px;
  }

  .topbar-actions .action-btn {
    width: 36px;
    height: 36px;
    font-size: 0.9rem;
  }

  .user-menu-toggle {
    padding: 0.5rem 0.75rem;

    .user-avatar {
      width: 28px;
      height: 28px;
      font-size: 0.8rem;
    }
  }

  .notifications-panel {
    min-width: 300px;
    right: -1rem;
  }
}

@media (max-width: 480px) {
  .topbar-actions .action-btn:not(.notifications-btn) {
    display: none;
  }
}
</style>
