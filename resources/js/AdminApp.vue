<template>
  <div id="admin-app" class="admin-application">
    <router-view />
  </div>
</template>

<script setup>
// Admin app root component
</script>

<style>
.admin-application {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  min-height: 100vh;
  background: #f5f6fa;
}

/* Reset any conflicting styles from main site */
.admin-application * {
  box-sizing: border-box;
}

.admin-application input,
.admin-application textarea,
.admin-application select,
.admin-application button {
  font-family: inherit;
}
</style>
