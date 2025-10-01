<template>
  <i :class="iconClasses" :style="customStyle"></i>
</template>

<script>
export default {
  name: 'FontAwesomeIcon',
  props: {
    // Имя иконки (например: 'home', 'user', 'envelope')
    icon: {
      type: String,
      required: true
    },
    // Тип иконки: 'solid' (fas), 'regular' (far), 'brands' (fab)
    type: {
      type: String,
      default: 'solid',
      validator: (value) => ['solid', 'regular', 'brands'].includes(value)
    },
    // Размер иконки: 'xs', 'sm', 'lg', 'xl', '2x', '3x', '4x', '5x'
    size: {
      type: String,
      default: null
    },
    // Вращение иконки
    spin: {
      type: Boolean,
      default: false
    },
    // Фиксированная ширина
    fixedWidth: {
      type: Boolean,
      default: false
    },
    // Цвет иконки
    color: {
      type: String,
      default: null
    }
  },
  computed: {
    iconClasses() {
      const classes = []

      // Добавляем базовый класс в зависимости от типа
      switch (this.type) {
        case 'solid':
          classes.push('fas')
          break
        case 'regular':
          classes.push('far')
          break
        case 'brands':
          classes.push('fab')
          break
        default:
          classes.push('fas')
      }

      // Добавляем класс иконки
      classes.push(`fa-${this.icon}`)

      // Добавляем размер если указан
      if (this.size) {
        if (['xs', 'sm', 'lg', 'xl'].includes(this.size)) {
          classes.push(`fa-${this.size}`)
        } else if (['2x', '3x', '4x', '5x'].includes(this.size)) {
          classes.push(`fa-${this.size}`)
        }
      }

      // Добавляем анимацию вращения
      if (this.spin) {
        classes.push('fa-spin')
      }

      // Добавляем фиксированную ширину
      if (this.fixedWidth) {
        classes.push('fa-fw')
      }

      return classes.join(' ')
    },
    customStyle() {
      const style = {}

      if (this.color) {
        style.color = this.color
      }

      return style
    }
  }
}
</script>

<style scoped>
/* Дополнительные стили для иконок, если нужно */
i {
  display: inline-block;
}
</style>
