// Utility functions composable
export function useUtilities() {
  // Debounce function
  function useDebounce(func, delay) {
    let timeoutId
    return (...args) => {
      clearTimeout(timeoutId)
      timeoutId = setTimeout(() => func(...args), delay)
    }
  }

  // Date formatting functions
  const formatDate = (dateString, options = {}) => {
    if (!dateString) return ''

    const defaultOptions = {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    }

    return new Date(dateString).toLocaleDateString('en-US', { ...defaultOptions, ...options })
  }

  const formatJoinDate = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleDateString('en-US', {
      month: 'short',
      year: 'numeric'
    })
  }

  const formatRelativeDate = (dateString) => {
    if (!dateString) return ''

    const date = new Date(dateString)
    const now = new Date()
    const diffTime = now - date
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))

    if (diffDays < 1) return 'Today'
    if (diffDays === 1) return 'Yesterday'
    if (diffDays < 7) return `${diffDays} days ago`
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`
    if (diffDays < 365) return `${Math.floor(diffDays / 30)} months ago`

    return `${Math.floor(diffDays / 365)} years ago`
  }

  // Text formatting functions
  const formatText = (text) => {
    if (!text) return ''
    return text
      .replace(/\n/g, '<br>')
      .replace(/•\s/g, '<li>')
      .replace(/^\s*-\s/gm, '<li>')
      .replace(/(<li>.*?)(?=<li>|$)/gs, '<ul>$1</ul>')
      .replace(/<li>/g, '<li>')
      .replace(/<\/ul><ul>/g, '')
  }

  const truncateText = (text, length = 120) => {
    if (!text) return ''
    return text.length > length ? text.substring(0, length) + '...' : text
  }

  const slugify = (text) => {
    return text
      .toString()
      .toLowerCase()
      .trim()
      .replace(/\s+/g, '-')
      .replace(/[^\w-]+/g, '')
      .replace(/--+/g, '-')
      .replace(/^-+/, '')
      .replace(/-+$/, '')
  }

  // Experience calculation
  const calculateExperience = (joinDate) => {
    if (!joinDate) return ''

    const start = new Date(joinDate)
    const now = new Date()
    const years = now.getFullYear() - start.getFullYear()
    const months = now.getMonth() - start.getMonth()

    let totalMonths = years * 12 + months
    if (now.getDate() < start.getDate()) {
      totalMonths--
    }

    const finalYears = Math.floor(totalMonths / 12)
    const finalMonths = totalMonths % 12

    if (finalYears > 0) {
      return `${finalYears}+ year${finalYears > 1 ? 's' : ''} experience`
    } else if (finalMonths > 0) {
      return `${finalMonths} month${finalMonths > 1 ? 's' : ''} experience`
    } else {
      return 'Recent addition'
    }
  }

  // Date checking functions
  const isDeadlineSoon = (deadline, daysThreshold = 7) => {
    if (!deadline) return false
    const now = new Date()
    const deadlineDate = new Date(deadline)
    const diff = deadlineDate - now
    return diff > 0 && diff < daysThreshold * 24 * 60 * 60 * 1000
  }

  const isDateInPast = (dateString) => {
    if (!dateString) return false
    return new Date(dateString) < new Date()
  }

  // Social media utilities
  const getSocialIcon = (platform) => {
    const icons = {
      linkedin: 'fab fa-linkedin',
      twitter: 'fab fa-twitter',
      github: 'fab fa-github',
      instagram: 'fab fa-instagram',
      facebook: 'fab fa-facebook',
      dribbble: 'fab fa-dribbble',
      behance: 'fab fa-behance',
      website: 'fas fa-globe',
      email: 'fas fa-envelope',
      phone: 'fas fa-phone',
      youtube: 'fab fa-youtube',
      tiktok: 'fab fa-tiktok',
      discord: 'fab fa-discord'
    }
    return icons[platform.toLowerCase()] || 'fas fa-link'
  }

  const formatPlatformName = (platform) => {
    const names = {
      linkedin: 'LinkedIn',
      github: 'GitHub',
      youtube: 'YouTube',
      tiktok: 'TikTok'
    }
    return names[platform.toLowerCase()] || platform.charAt(0).toUpperCase() + platform.slice(1)
  }

  const hasSocialLinks = (entity) => {
    return entity.social_links &&
           Object.values(entity.social_links).some(link => link && link.trim() !== '')
  }

  // Image utilities
  const handleImageError = (event, fallbackSrc = '/images/default-avatar.png') => {
    event.target.src = fallbackSrc
  }

  const getImageSrc = (src, fallback = '/images/default-avatar.png') => {
    return src || fallback
  }

  // Status utilities
  const getStatusIcon = (status) => {
    const icons = {
      'active': 'fa-check-circle',
      'inactive': 'fa-pause-circle',
      'on-leave': 'fa-clock',
      'pending': 'fa-hourglass-half',
      'draft': 'fa-edit',
      'published': 'fa-eye',
      'archived': 'fa-archive'
    }
    return icons[status] || 'fa-question-circle'
  }

  const getStatusColor = (status) => {
    const colors = {
      'active': 'success',
      'inactive': 'secondary',
      'on-leave': 'warning',
      'pending': 'info',
      'draft': 'secondary',
      'published': 'success',
      'archived': 'dark'
    }
    return colors[status] || 'secondary'
  }

  // Modal utilities
  const handleModalEscape = (callback) => {
    const handleEscape = (e) => {
      if (e.key === 'Escape') {
        callback()
      }
    }
    document.addEventListener('keydown', handleEscape)
    return () => document.removeEventListener('keydown', handleEscape)
  }

  const lockBodyScroll = () => {
    document.body.style.overflow = 'hidden'
  }

  const unlockBodyScroll = () => {
    document.body.style.overflow = 'auto'
  }

  // URL and sharing utilities
  const shareContent = async (shareData) => {
    try {
      if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
        await navigator.share(shareData)
        return { success: true, method: 'native' }
      } else if (navigator.clipboard) {
        await navigator.clipboard.writeText(shareData.url || shareData.text)
        return { success: true, method: 'clipboard' }
      } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea')
        textArea.value = shareData.url || shareData.text
        document.body.appendChild(textArea)
        textArea.select()
        document.execCommand('copy')
        document.body.removeChild(textArea)
        return { success: true, method: 'fallback' }
      }
    } catch (err) {
      console.error('Error sharing:', err)
      return { success: false, error: err.message }
    }
  }

  const buildQueryString = (params) => {
    const searchParams = new URLSearchParams()
    Object.keys(params).forEach(key => {
      if (params[key] !== null && params[key] !== undefined && params[key] !== '') {
        searchParams.append(key, params[key])
      }
    })
    return searchParams.toString()
  }

  // Array utilities
  const chunkArray = (array, size) => {
    const chunks = []
    for (let i = 0; i < array.length; i += size) {
      chunks.push(array.slice(i, i + size))
    }
    return chunks
  }

  const uniqueBy = (array, key) => {
    const seen = new Set()
    return array.filter(item => {
      const value = typeof key === 'function' ? key(item) : item[key]
      if (seen.has(value)) {
        return false
      }
      seen.add(value)
      return true
    })
  }

  // Validation utilities
  const isValidEmail = (email) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailRegex.test(email)
  }

  const isValidUrl = (url) => {
    try {
      new URL(url)
      return true
    } catch {
      return false
    }
  }

  const isValidPhone = (phone) => {
    const phoneRegex = /^[+]?[\d\s-()]{10,}$/
    return phoneRegex.test(phone)
  }

  return {
    useDebounce,
    formatDate,
    formatJoinDate,
    formatRelativeDate,
    formatText,
    truncateText,
    slugify,
    calculateExperience,
    isDeadlineSoon,
    isDateInPast,
    getSocialIcon,
    formatPlatformName,
    hasSocialLinks,
    handleImageError,
    getImageSrc,
    getStatusIcon,
    getStatusColor,
    handleModalEscape,
    lockBodyScroll,
    unlockBodyScroll,
    shareContent,
    buildQueryString,
    chunkArray,
    uniqueBy,
    isValidEmail,
    isValidUrl,
    isValidPhone
  }
}
