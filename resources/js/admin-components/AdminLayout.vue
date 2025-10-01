<template>
  <div class="admin-layout">
    <AdminSidebar
      :is-collapsed="isSidebarCollapsed"
      @toggle="toggleSidebar"
    />

    <div class="admin-main" :class="{ 'sidebar-collapsed': isSidebarCollapsed }">
      <main class="admin-content">
        <router-view />
      </main>
    </div>

    <!-- Mobile overlay -->
    <div
      v-if="isMobileMenuOpen"
      class="mobile-overlay"
      @click="closeMobileMenu"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import AdminSidebar from './AdminSidebar.vue'

const isSidebarCollapsed = ref(false)
const isMobileMenuOpen = ref(false)
const isMobile = ref(false)

const toggleSidebar = () => {
  if (isMobile.value) {
    isMobileMenuOpen.value = !isMobileMenuOpen.value
  } else {
    isSidebarCollapsed.value = !isSidebarCollapsed.value
    localStorage.setItem('admin-sidebar-collapsed', isSidebarCollapsed.value)
  }
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
  if (!isMobile.value) {
    isMobileMenuOpen.value = false
  }
}

onMounted(() => {
  // Restore sidebar state
  const savedState = localStorage.getItem('admin-sidebar-collapsed')
  if (savedState !== null) {
    isSidebarCollapsed.value = savedState === 'true'
  }

  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})
</script>

<style lang="scss" scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background: #f8f9fa;
}

.admin-main {
  flex: 1;
  margin-left: 280px;
  transition: margin-left 0.3s ease;
  min-width: 0;

  &.sidebar-collapsed {
    margin-left: 80px;
  }
}

.admin-content {
  padding: 1.5rem;
  min-height: 100vh;
}

.mobile-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 999;
  backdrop-filter: blur(2px);
}

@media (max-width: 768px) {
  .admin-main {
    margin-left: 0;

    &.sidebar-collapsed {
      margin-left: 0;
    }
  }

  .admin-content {
    padding: 1rem;
  }
}
</style>
