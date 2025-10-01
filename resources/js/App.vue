<template>
  <div id="app">
    <!-- Modern Header -->
    <AppHeader />

    <!-- Main Content with Page Transitions -->
    <main>
      <router-view v-slot="{ Component }">
        <transition name="page" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- Modern Footer -->
    <AppFooter />
  </div>
</template>

<script>
import AppHeader from './components/AppHeader.vue'
import AppFooter from './components/AppFooter.vue'

export default {
  name: 'App',
  components: {
    AppHeader,
    AppFooter
  },
  mounted() {
    console.log('Modern Design System loaded successfully!')
  }
}
</script>

<style>
/* Global styles are imported from app.scss */
</style>
