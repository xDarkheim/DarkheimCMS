import { onMounted, onUnmounted, watch, ref } from 'vue'
import { useRoute } from 'vue-router'

// Глобальная переменная для отслеживания активного управления title
let activeTitleManager = null

export function usePageTitle(title, options = {}) {
  const route = useRoute()
  const {
    suffix = ' - Darkheim Development Studio',
    dynamic = false
  } = options

  const managerId = ref(Math.random().toString(36).substr(2, 9))

  const setTitle = (newTitle) => {
    // Проверяем, является ли этот экземпляр активным менеджером
    if (activeTitleManager !== managerId.value) {
      return
    }

    if (typeof newTitle === 'string' && newTitle.trim()) {
      document.title = newTitle + suffix
    } else if (route.meta && route.meta.title) {
      document.title = route.meta.title
    } else {
      document.title = 'Darkheim Development Studio'
    }
  }

  onMounted(() => {
    // Устанавливаем этот экземпляр как активный менеджер
    activeTitleManager = managerId.value

    // Устанавливаем новый title
    setTitle(title)

    // Если dynamic=true, следим за изменениями title
    if (dynamic && typeof title === 'object' && title.value !== undefined) {
      watch(title, (newTitle) => {
        setTitle(newTitle)
      }, { immediate: true })
    }
  })

  onUnmounted(() => {
    // Очищаем активный менеджер только если это наш экземпляр
    if (activeTitleManager === managerId.value) {
      activeTitleManager = null
    }
  })

  return {
    setTitle: (newTitle) => {
      if (activeTitleManager === managerId.value) {
        setTitle(newTitle)
      }
    }
  }
}

// Функция для глобального установления title (приоритетная)
export function setPageTitle(title, suffix = ' - Darkheim Development Studio') {
  if (typeof title === 'string' && title.trim()) {
    document.title = title + suffix
    // Сбрасываем активный менеджер при принудительном обновлении
    activeTitleManager = null
  }
}
