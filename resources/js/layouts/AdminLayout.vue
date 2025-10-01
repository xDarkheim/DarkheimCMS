<template>
  <div class="admin-layout">
    <slot />
    <NotificationContainer />
  </div>
</template>

<script setup>
import NotificationContainer from '../components/NotificationContainer.vue'
</script>

<style scoped>
.admin-layout {
  position: relative;
  min-height: 100vh;
}
</style>
