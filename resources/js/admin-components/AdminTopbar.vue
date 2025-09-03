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
            @keyup.enter="handleSearch"
          />
        </div>
      </div>

      <div class="topbar-actions">
        <button class="action-btn notifications-btn" title="Notifications">
          <i class="fas fa-bell"></i>
          <span class="notification-badge">3</span>
        </button>

        <button class="action-btn settings-btn" title="Settings">
          <i class="fas fa-cog"></i>
        </button>

        <div class="user-menu">
          <button class="user-menu-toggle" @click="toggleUserMenu">
            <span class="user-avatar">
              <i class="fas fa-user"></i>
            </span>
            <span class="user-name">Admin</span>
            <i class="fas fa-chevron-down dropdown-icon"></i>
          </button>

          <div v-if="isUserMenuOpen" class="user-dropdown">
            <a href="#" class="dropdown-item">
              <i class="fas fa-user"></i>
              Profile
            </a>
            <a href="#" class="dropdown-item">
              <i class="fas fa-cog"></i>
              Settings
            </a>
            <hr class="dropdown-divider">
            <a href="#" class="dropdown-item" @click="handleLogout">
              <i class="fas fa-sign-out-alt"></i>
              Logout
            </a>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const props = defineProps({
  isSidebarCollapsed: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['toggle-sidebar'])

const route = useRoute()
const router = useRouter()

const searchQuery = ref('')
const isUserMenuOpen = ref(false)

const currentRoute = computed(() => {
  return {
    title: route.meta?.title || 'Admin Panel',
    icon: route.meta?.icon || 'fas fa-home'
  }
})

const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value
}

const closeUserMenu = (event) => {
  if (!event.target.closest('.user-menu')) {
    isUserMenuOpen.value = false
  }
}

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    console.log('Searching for:', searchQuery.value)
    // Implement search functionality
  }
}

const handleLogout = () => {
  localStorage.removeItem('admin_token')
  router.push('/admin/login')
}

onMounted(() => {
  document.addEventListener('click', closeUserMenu)
})

onUnmounted(() => {
  document.removeEventListener('click', closeUserMenu)
})
</script>

<style lang="scss" scoped>
$primary-color: #3498db;
$primary-hover: #2980b9;
$text-primary: #2c3e50;
$text-secondary: #6c757d;
$text-muted: #adb5bd;
$bg-card: #ffffff;
$bg-light: #f8f9fa;
$border-color: #dee2e6;
$box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
$transition: all 0.2s ease;

.admin-topbar {
  height: 80px;
  background: $bg-card;
  border-bottom: 1px solid $border-color;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.5rem;
  box-shadow: $box-shadow;
  position: sticky;
  top: 0;
  z-index: 100;
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 1rem;

  .mobile-menu-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 1.25rem;
    color: $text-primary;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 6px;
    transition: $transition;

    &:hover {
      background: $bg-light;
    }
  }

  .breadcrumb {
    .breadcrumb-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 1.125rem;
      font-weight: 600;
      color: $text-primary;

      i {
        color: $primary-color;
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
      color: $text-muted;
      font-size: 0.875rem;
    }

    input {
      width: 100%;
      padding: 0.75rem 1rem 0.75rem 2.5rem;
      border: 1px solid $border-color;
      border-radius: 25px;
      background: $bg-light;
      font-size: 0.875rem;
      transition: $transition;

      &:focus {
        outline: none;
        border-color: $primary-color;
        background: $bg-card;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
      }

      &::placeholder {
        color: $text-muted;
      }
    }
  }
}

.topbar-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;

  .action-btn {
    position: relative;
    width: 40px;
    height: 40px;
    background: none;
    border: none;
    border-radius: 50%;
    color: $text-secondary;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: $transition;
    font-size: 1rem;

    &:hover {
      background: $bg-light;
      color: $text-primary;
    }

    .notification-badge {
      position: absolute;
      top: 8px;
      right: 8px;
      width: 18px;
      height: 18px;
      background: #e74c3c;
      color: white;
      border-radius: 50%;
      font-size: 0.7rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid $bg-card;
    }
  }
}

.user-menu {
  position: relative;

  .user-menu-toggle {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 1rem;
    background: none;
    border: 1px solid $border-color;
    border-radius: 25px;
    cursor: pointer;
    transition: $transition;
    color: $text-primary;

    &:hover {
      background: $bg-light;
      border-color: $primary-color;
    }

    .user-avatar {
      width: 32px;
      height: 32px;
      background: $primary-color;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 0.875rem;
    }

    .user-name {
      font-weight: 500;
      font-size: 0.875rem;
    }

    .dropdown-icon {
      font-size: 0.75rem;
      transition: transform 0.2s ease;
    }

    &:hover .dropdown-icon {
      transform: rotate(180deg);
    }
  }

  .user-dropdown {
    position: absolute;
    top: calc(100% + 0.5rem);
    right: 0;
    min-width: 200px;
    background: $bg-card;
    border: 1px solid $border-color;
    border-radius: 8px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    padding: 0.5rem 0;
    z-index: 200;
    animation: dropdownSlideIn 0.2s ease;

    .dropdown-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.75rem 1rem;
      color: $text-primary;
      text-decoration: none;
      font-size: 0.875rem;
      transition: $transition;

      &:hover {
        background: $bg-light;
        color: $primary-color;
      }

      i {
        width: 16px;
        color: $text-muted;
      }
    }

    .dropdown-divider {
      margin: 0.5rem 0;
      border: none;
      border-top: 1px solid $border-color;
    }
  }
}

@keyframes dropdownSlideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

// Mobile styles
@media (max-width: 768px) {
  .admin-topbar {
    padding: 0 1rem;
  }

  .topbar-left {
    .mobile-menu-toggle {
      display: flex;
    }
  }

  .search-box {
    display: none;
  }

  .topbar-actions {
    .action-btn {
      width: 36px;
      height: 36px;
      font-size: 0.875rem;
    }
  }

  .user-menu .user-menu-toggle {
    padding: 0.5rem;

    .user-name {
      display: none;
    }
  }
}

@media (max-width: 480px) {
  .topbar-actions .action-btn:not(.notifications-btn) {
    display: none;
  }
}
</style>
