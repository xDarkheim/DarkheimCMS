import './bootstrap';
import { createApp } from 'vue';
import router from './router';

// Main App component
import App from './App.vue';

// Global components
import FontAwesomeIcon from './components/FontAwesomeIcon.vue';

// Create Vue app instance
const app = createApp(App);

// Register global components
app.component('FontAwesomeIcon', FontAwesomeIcon);

// Use router
app.use(router);

// Mount the app
app.mount('#app');

console.log('Modern Vue.js + Sass application loaded successfully!');
