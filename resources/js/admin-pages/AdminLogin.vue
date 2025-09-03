<template>
  <div class="admin-login-page">
    <div class="login-container">
      <div class="login-card">
        <div class="login-header">
          <div class="logo">
            <i class="fas fa-shield-alt"></i>
          </div>
          <h1>Admin Panel</h1>
          <p>Sign in to manage your application</p>
        </div>

        <form @submit.prevent="handleLogin" class="login-form">
          <div class="form-group">
            <label for="email">
              <i class="fas fa-envelope"></i>
              Email Address
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              :disabled="isLoading"
              placeholder="admin@example.com"
              autocomplete="username"
            >
          </div>

          <div class="form-group">
            <label for="password">
              <i class="fas fa-lock"></i>
              Password
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              :disabled="isLoading"
              placeholder="Enter your password"
              autocomplete="current-password"
            >
          </div>

          <div v-if="error" class="error-message">
            <i class="fas fa-exclamation-triangle"></i>
            {{ error }}
          </div>

          <button type="submit" :disabled="isLoading" class="login-btn">
            <div v-if="isLoading" class="loading-spinner"></div>
            <i v-else class="fas fa-sign-in-alt"></i>
            {{ isLoading ? 'Signing in...' : 'Sign In' }}
          </button>
        </form>

        <div class="login-footer">
          <p>
            <i class="fas fa-info-circle"></i>
            Admin access required
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAdminAuth } from '../admin-composables/useAdminAuth'

const router = useRouter()
const { login, isLoading, isAuthenticated } = useAdminAuth()

const form = reactive({
  email: '',
  password: ''
})

const error = ref('')

const handleLogin = async () => {
  error.value = ''

  const result = await login(form)

  if (result.success) {
    router.push('/admin')
  } else {
    error.value = result.error
  }
}

onMounted(() => {
  if (isAuthenticated.value) {
    router.push('/admin')
  }
})
</script>

<style scoped>
.admin-login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem;
  font-family: 'Inter', sans-serif;
}

.login-container {
  width: 100%;
  max-width: 420px;
}

.login-card {
  background: white;
  border-radius: 16px;
  padding: 2.5rem;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
  backdrop-filter: blur(10px);
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.logo {
  width: 64px;
  height: 64px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  color: white;
  font-size: 1.5rem;
}

.login-header h1 {
  margin: 0 0 0.5rem 0;
  font-size: 1.75rem;
  font-weight: 700;
  color: #1a202c;
}

.login-header p {
  margin: 0;
  color: #718096;
  font-size: 0.95rem;
}

.login-form {
  space-y: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #2d3748;
  font-size: 0.9rem;
}

.form-group label i {
  width: 16px;
  color: #667eea;
}

.form-group input {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.2s ease;
  box-sizing: border-box;
  background: #f7fafc;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group input:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.error-message {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: #fed7d7;
  color: #c53030;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
  border: 1px solid #feb2b2;
}

.login-btn {
  width: 100%;
  padding: 0.875rem 1rem;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.login-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.login-footer {
  text-align: center;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e2e8f0;
}

.login-footer p {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  color: #718096;
  font-size: 0.85rem;
  margin: 0;
}

.login-footer i {
  color: #667eea;
}

@media (max-width: 480px) {
  .admin-login-page {
    padding: 1rem;
  }

  .login-card {
    padding: 2rem;
  }
}
</style>
