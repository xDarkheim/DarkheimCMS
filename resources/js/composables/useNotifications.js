import { ref, reactive } from 'vue'

const notifications = ref([])
let notificationId = 0

export function useNotifications() {
  const showNotification = (message, type = 'info', duration = 5000) => {
    const id = ++notificationId
    const notification = {
      id,
      message,
      type, // 'success', 'error', 'warning', 'info'
      duration,
      timestamp: Date.now()
    }

    notifications.value.push(notification)

    if (duration > 0) {
      setTimeout(() => {
        removeNotification(id)
      }, duration)
    }

    return id
  }

  const removeNotification = (id) => {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      notifications.value.splice(index, 1)
    }
  }

  const clearAll = () => {
    notifications.value.splice(0)
  }

  // Convenience methods
  const showSuccess = (message, duration = 4000) => {
    return showNotification(message, 'success', duration)
  }

  const showError = (message, duration = 6000) => {
    return showNotification(message, 'error', duration)
  }

  const showWarning = (message, duration = 5000) => {
    return showNotification(message, 'warning', duration)
  }

  const showInfo = (message, duration = 4000) => {
    return showNotification(message, 'info', duration)
  }

  return {
    notifications,
    showNotification,
    removeNotification,
    clearAll,
    showSuccess,
    showError,
    showWarning,
    showInfo
  }
}
