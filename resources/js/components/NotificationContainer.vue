<template>
  <div class="notifications-container">
    <transition-group name="notification" tag="div" class="notifications-list">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        :class="['notification', `notification-${notification.type}`]"
        @click="removeNotification(notification.id)"
      >
        <div class="notification-content">
          <div class="notification-icon">
            <i :class="getIcon(notification.type)"></i>
          </div>
          <div class="notification-message">
            {{ notification.message }}
          </div>
          <button class="notification-close" @click.stop="removeNotification(notification.id)">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { useNotifications } from '../composables/useNotifications'

const { notifications, removeNotification } = useNotifications()

const getIcon = (type) => {
  const icons = {
    success: 'fas fa-check-circle',
    error: 'fas fa-exclamation-circle',
    warning: 'fas fa-exclamation-triangle',
    info: 'fas fa-info-circle'
  }
  return icons[type] || icons.info
}
</script>

<style scoped>
.notifications-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  max-width: 400px;
  width: 100%;
}

.notifications-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.notification {
  background: white;
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
  border: 1px solid #e2e8f0;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  min-height: 60px;
}

.notification:hover {
  transform: translateX(-4px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.notification-content {
  display: flex;
  align-items: center;
  padding: 16px;
  gap: 12px;
}

.notification-icon {
  flex-shrink: 0;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
}

.notification-message {
  flex: 1;
  font-size: 14px;
  line-height: 1.4;
  color: #2c3e50;
  font-weight: 500;
}

.notification-close {
  flex-shrink: 0;
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s ease;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.notification-close:hover {
  background: #f1f5f9;
  color: #64748b;
}

/* Type-specific styles */
.notification-success {
  border-left: 4px solid #10b981;
}

.notification-success .notification-icon {
  color: #10b981;
}

.notification-error {
  border-left: 4px solid #ef4444;
}

.notification-error .notification-icon {
  color: #ef4444;
}

.notification-warning {
  border-left: 4px solid #f59e0b;
}

.notification-warning .notification-icon {
  color: #f59e0b;
}

.notification-info {
  border-left: 4px solid #3b82f6;
}

.notification-info .notification-icon {
  color: #3b82f6;
}

/* Animations */
.notification-enter-active {
  transition: all 0.3s ease;
}

.notification-leave-active {
  transition: all 0.3s ease;
}

.notification-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.notification-leave-to {
  transform: translateX(100%);
  opacity: 0;
}

.notification-move {
  transition: transform 0.3s ease;
}

/* Responsive */
@media (max-width: 768px) {
  .notifications-container {
    top: 10px;
    right: 10px;
    left: 10px;
    max-width: none;
  }

  .notification-content {
    padding: 12px;
  }

  .notification-message {
    font-size: 13px;
  }
}
</style>
