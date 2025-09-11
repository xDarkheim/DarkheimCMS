<template>
  <div class="team-page page-with-header-offset">
    <!-- Hero Section -->
    <section class="team-hero">
      <div class="container">
        <div class="team-hero__content">
          <h1 class="team-hero__title">Meet Our Team</h1>
          <p class="team-hero__subtitle">
            We're a diverse group of passionate developers, designers, and innovators working together
            to create exceptional digital experiences.
          </p>
        </div>
      </div>
    </section>

    <!-- Team Stats Section -->
    <section class="team-stats" v-if="teamStats.totalMembers > 0">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ teamStats.totalMembers }}</div>
              <div class="stat-label">Team Members</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-building"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ teamStats.departments }}</div>
              <div class="stat-label">Departments</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-globe"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ teamStats.countries }}</div>
              <div class="stat-label">Countries</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-calendar"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ teamStats.avgExperience }}</div>
              <div class="stat-label">Avg. Experience</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Filters Section -->
    <section class="team-filters" v-if="teamMembers.length > 0">
      <div class="container">
        <div class="filters-wrapper">
          <div class="search-filter">
            <div class="search-box">
              <i class="fas fa-search"></i>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search team members..."
                @input="applyFilters"
              >
            </div>
          </div>

          <div class="filter-controls">
            <div class="filter-group">
              <label>Department</label>
              <select v-model="departmentFilter" @change="applyFilters">
                <option value="">All Departments</option>
                <option v-for="dept in departments" :key="dept" :value="dept">
                  {{ dept }}
                </option>
              </select>
            </div>

            <div class="filter-group">
              <label>Sort by</label>
              <select v-model="sortBy" @change="applyFilters">
                <option value="priority">Priority</option>
                <option value="name">Name</option>
                <option value="position">Position</option>
                <option value="joined_date">Join Date</option>
              </select>
            </div>

            <button v-if="hasActiveFilters" @click="clearFilters" class="btn btn--ghost btn--sm">
              <i class="fas fa-times"></i>
              Clear Filters
            </button>
          </div>
        </div>

        <div class="results-info" v-if="hasActiveFilters">
          Showing {{ filteredMembers.length }} of {{ teamMembers.length }} team members
        </div>
      </div>
    </section>

    <!-- Team Members Section -->
    <section class="team-members">
      <div class="container">
        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading team members...</p>
        </div>

        <!-- Team Grid -->
        <div v-else-if="filteredMembers.length > 0" class="team-grid">
          <div
            v-for="member in filteredMembers"
            :key="member.id"
            class="team-card"
            :class="{ 'featured': member.priority >= 80 }"
            @click="showMemberDetails(member)"
          >
            <!-- Status indicator -->
            <div class="team-card__status" :class="member.status" v-if="member.status !== 'active'">
              <i class="fas" :class="getStatusIcon(member.status)"></i>
              {{ formatStatus(member.status) }}
            </div>

            <div class="team-card__header">
              <div class="team-card__avatar">
                <img
                  :src="member.avatar || '/images/default-avatar.png'"
                  :alt="member.name"
                  loading="lazy"
                >
                <div v-if="member.status === 'active'" class="status-indicator active"></div>
                <div v-else-if="member.status === 'on-leave'" class="status-indicator warning"></div>
                <div v-else class="status-indicator inactive"></div>
              </div>

              <h3 class="team-card__name">{{ member.name }}</h3>
              <div class="team-card__position">{{ member.position }}</div>
              <div class="team-card__department">{{ member.department }}</div>

              <!-- Join date -->
              <div v-if="member.joined_date" class="team-card__joined">
                <i class="fas fa-calendar"></i>
                Joined {{ formatJoinDate(member.joined_date) }}
              </div>
            </div>

            <div class="team-card__content">
              <div class="team-card__bio">
                {{ truncateText(member.bio, 120) }}
              </div>

              <div class="team-card__skills" v-if="member.skills && member.skills.length">
                <span
                  v-for="skill in member.skills.slice(0, 3)"
                  :key="skill"
                  class="skill-tag"
                >
                  {{ skill }}
                </span>
                <span v-if="member.skills.length > 3" class="skill-more">
                  +{{ member.skills.length - 3 }} more
                </span>
              </div>

              <div class="team-card__actions">
                <button class="btn btn--ghost btn--sm">
                  View Profile
                  <i class="fas fa-user"></i>
                </button>
              </div>
            </div>

            <!-- Social Links -->
            <div v-if="hasSocialLinks(member)" class="team-card__social">
              <a
                v-for="(link, platform) in member.social_links"
                :key="platform"
                :href="link"
                target="_blank"
                rel="noopener noreferrer"
                class="social-link"
                :class="platform"
                @click.stop
                :title="`${member.name} on ${platform}`"
              >
                <i :class="getSocialIcon(platform)"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="!loading && teamMembers.length === 0" class="team-empty">
          <div class="team-empty__icon">
            <i class="fas fa-users"></i>
          </div>
          <h3 class="team-empty__title">No Team Members Found</h3>
          <p class="team-empty__text">Team information will be available soon.</p>
        </div>

        <!-- No Results State -->
        <div v-else-if="!loading && filteredMembers.length === 0" class="team-no-results">
          <div class="team-empty__icon">
            <i class="fas fa-search"></i>
          </div>
          <h3 class="team-empty__title">No Members Match Your Search</h3>
          <p class="team-empty__text">Try adjusting your filters or search terms.</p>
          <button @click="clearFilters" class="btn btn--primary">
            <i class="fas fa-times"></i>
            Clear Filters
          </button>
        </div>
      </div>
    </section>

    <!-- Member Details Modal -->
    <div v-if="selectedMember" class="member-modal" @click="closeModal">
      <div class="member-modal__content" @click.stop>
        <button class="member-modal__close" @click="closeModal">
          <i class="fas fa-times"></i>
        </button>

        <div class="member-modal__header">
          <div class="member-modal__avatar">
            <img
              :src="selectedMember.avatar || '/images/default-avatar.png'"
              :alt="selectedMember.name"
            >
            <div class="status-indicator" :class="selectedMember.status"></div>
          </div>
          <div class="member-modal__info">
            <h2 class="member-modal__name">{{ selectedMember.name }}</h2>
            <div class="member-modal__position">{{ selectedMember.position }}</div>
            <div class="member-modal__department">{{ selectedMember.department }}</div>

            <div class="member-modal__meta">
              <div v-if="selectedMember.joined_date" class="meta-item">
                <i class="fas fa-calendar"></i>
                Joined {{ formatDate(selectedMember.joined_date) }}
                <span class="experience">({{ calculateExperience(selectedMember.joined_date) }})</span>
              </div>
              <div class="meta-item">
                <i class="fas" :class="getStatusIcon(selectedMember.status)"></i>
                {{ formatStatus(selectedMember.status) }}
              </div>
            </div>
          </div>
        </div>

        <div class="member-modal__body">
          <div class="member-modal__section">
            <h4>About</h4>
            <p class="member-modal__bio">{{ selectedMember.bio }}</p>
          </div>

          <div v-if="selectedMember.skills && selectedMember.skills.length" class="member-modal__section">
            <h4>Skills & Expertise</h4>
            <div class="member-modal__skills">
              <span
                v-for="skill in selectedMember.skills"
                :key="skill"
                class="member-modal__skill-tag"
              >
                {{ skill }}
              </span>
            </div>
          </div>

          <div class="member-modal__section">
            <h4>Get In Touch</h4>
            <div class="member-modal__contact">
              <div v-if="selectedMember.email" class="contact-item">
                <i class="fas fa-envelope"></i>
                <a :href="`mailto:${selectedMember.email}`">{{ selectedMember.email }}</a>
              </div>
              <div v-if="selectedMember.phone" class="contact-item">
                <i class="fas fa-phone"></i>
                <a :href="`tel:${selectedMember.phone}`">{{ selectedMember.phone }}</a>
              </div>
              <div v-if="!selectedMember.email && !selectedMember.phone" class="contact-item">
                <i class="fas fa-info-circle"></i>
                Contact information not available
              </div>
            </div>

            <!-- Social Links in Modal -->
            <div v-if="hasSocialLinks(selectedMember)" class="member-modal__social">
              <h5>Connect Online</h5>
              <div class="social-links-grid">
                <a
                  v-for="(link, platform) in selectedMember.social_links"
                  :key="platform"
                  :href="link"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="social-link-large"
                  :class="platform"
                >
                  <i :class="getSocialIcon(platform)"></i>
                  <span>{{ formatPlatformName(platform) }}</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Join Team CTA -->
    <section class="team-cta">
      <div class="container">
        <div class="team-cta__content">
          <h2 class="team-cta__title">Want to Join Our Team?</h2>
          <p class="team-cta__text">
            We're always looking for talented individuals to join our growing team.
            Check out our open positions or get in touch.
          </p>
          <div class="team-cta__actions">
            <router-link to="/careers" class="btn btn--primary btn--xl">
              View Open Positions
              <i class="fas fa-briefcase"></i>
            </router-link>
            <router-link to="/contact" class="btn btn--ghost btn--xl">
              Get In Touch
            </router-link>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

export default {
  name: 'TeamPage',
  setup() {
    const teamMembers = ref([])
    const selectedMember = ref(null)
    const loading = ref(false)
    const searchQuery = ref('')
    const departmentFilter = ref('')
    const sortBy = ref('priority')

    // Организационные данные
    const organizationData = ref({
      departments: [],
      positions: [],
      skills: [],
      statuses: []
    })

    // Computed properties
    const departments = computed(() => {
      // Используем данные из OrganizationData, но также добавляем уникальные департаменты из команды
      const orgDepartments = organizationData.value.departments.map(d => d.label)
      const teamDepartments = [...new Set(teamMembers.value.map(member => member.department))]
      return [...new Set([...orgDepartments, ...teamDepartments])].sort()
    })

    const teamStats = computed(() => {
      const members = teamMembers.value
      const departmentsSet = new Set(members.map(m => m.department))

      // Подсчитываем средний опыт на основе даты присоединения
      const membersWithJoinDate = members.filter(m => m.joined_date)
      const avgExperience = membersWithJoinDate.length > 0
        ? Math.round(membersWithJoinDate.reduce((acc, member) => {
            const years = new Date().getFullYear() - new Date(member.joined_date).getFullYear()
            return acc + years
          }, 0) / membersWithJoinDate.length)
        : 0

      // Подсчитываем страны на основе локаций (можно расширить)
      const countries = new Set()
      countries.add('Global') // По умолчанию - можно расширить логику в будущем

      return {
        totalMembers: members.filter(m => m.show_on_website).length,
        departments: departmentsSet.size,
        countries: countries.size,
        avgExperience: avgExperience > 0 ? `${avgExperience}+ years` : 'New team'
      }
    })

    const filteredMembers = computed(() => {
      let filtered = teamMembers.value.filter(member => {
        // Search filter
        const matchesSearch = !searchQuery.value ||
          member.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          member.position.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          member.department.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          (member.skills && member.skills.some(skill =>
            skill.toLowerCase().includes(searchQuery.value.toLowerCase())
          ))

        // Department filter
        const matchesDepartment = !departmentFilter.value ||
          member.department === departmentFilter.value

        return matchesSearch && matchesDepartment
      })

      // Apply sorting
      return filtered.sort((a, b) => {
        switch (sortBy.value) {
          case 'name':
            return a.name.localeCompare(b.name)
          case 'position':
            return a.position.localeCompare(b.position)
          case 'joined_date':
            if (!a.joined_date && !b.joined_date) return 0
            if (!a.joined_date) return 1
            if (!b.joined_date) return -1
            return new Date(b.joined_date) - new Date(a.joined_date)
          case 'priority':
          default:
            const aPriority = a.priority || 0
            const bPriority = b.priority || 0
            if (aPriority !== bPriority) {
              return bPriority - aPriority
            }
            return new Date(b.created_at || 0) - new Date(a.created_at || 0)
        }
      })
    })

    const hasActiveFilters = computed(() => {
      return searchQuery.value || departmentFilter.value
    })

    // Methods
    const loadOrganizationData = async () => {
      try {
        // Загружаем все организационные данные параллельно
        const [departments, positions, skills, statuses] = await Promise.all([
          axios.get('/api/organization/departments').catch(() => ({ data: { data: [] } })),
          axios.get('/api/organization/positions').catch(() => ({ data: { data: [] } })),
          axios.get('/api/organization/skills').catch(() => ({ data: { data: [] } })),
          axios.get('/api/organization/statuses').catch(() => ({ data: { data: [] } }))
        ])

        organizationData.value = {
          departments: departments.data.data || getDefaultDepartments(),
          positions: positions.data.data || getDefaultPositions(),
          skills: skills.data.data || getDefaultSkills(),
          statuses: statuses.data.data || getDefaultStatuses()
        }
      } catch (error) {
        console.error('Failed to load organization data:', error)
        // Используем fallback значения
        organizationData.value = {
          departments: getDefaultDepartments(),
          positions: getDefaultPositions(),
          skills: getDefaultSkills(),
          statuses: getDefaultStatuses()
        }
      }
    }

    const loadTeamMembers = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/team')
        // Только видимые участники на публичной странице
        teamMembers.value = (response.data.data || []).filter(member => member.show_on_website)
      } catch (error) {
        console.error('Failed to load team members:', error)
        teamMembers.value = []
      } finally {
        loading.value = false
      }
    }

    // Методы для работы с организационными данными
    const getDepartmentLabel = (departmentKey) => {
      const dept = organizationData.value.departments.find(d => d.key === departmentKey || d.label === departmentKey)
      return dept ? dept.label : departmentKey
    }

    const getPositionLabel = (positionKey) => {
      const position = organizationData.value.positions.find(p => p.key === positionKey || p.label === positionKey)
      return position ? position.label : positionKey
    }

    const getSkillLabel = (skillKey) => {
      const skill = organizationData.value.skills.find(s => s.key === skillKey || s.label === skillKey)
      return skill ? skill.label : skillKey
    }

    const getStatusInfo = (statusKey) => {
      const status = organizationData.value.statuses.find(s => s.key === statusKey)
      return status || { key: statusKey, label: formatStatus(statusKey), description: '' }
    }

    // Methods
    const showMemberDetails = (member) => {
      selectedMember.value = member
      document.body.style.overflow = 'hidden'
    }

    const closeModal = () => {
      selectedMember.value = null
      document.body.style.overflow = 'auto'
    }

    const applyFilters = () => {
      // Filters are applied automatically via computed property
    }

    const clearFilters = () => {
      searchQuery.value = ''
      departmentFilter.value = ''
    }

    const getSocialIcon = (platform) => {
      const icons = {
        linkedin: 'fab fa-linkedin',
        twitter: 'fab fa-twitter',
        github: 'fab fa-github',
        instagram: 'fab fa-instagram',
        facebook: 'fab fa-facebook',
        dribbble: 'fab fa-dribbble',
        behance: 'fab fa-behance',
        website: 'fas fa-globe'
      }
      return icons[platform.toLowerCase()] || 'fas fa-link'
    }

    const getStatusIcon = (status) => {
      const icons = {
        'active': 'fa-check-circle',
        'inactive': 'fa-pause-circle',
        'on-leave': 'fa-clock'
      }
      return icons[status] || 'fa-question-circle'
    }

    const formatStatus = (status) => {
      const statuses = {
        'active': 'Active',
        'inactive': 'Inactive',
        'on-leave': 'On Leave'
      }
      return statuses[status] || status
    }

    const formatPlatformName = (platform) => {
      return platform.charAt(0).toUpperCase() + platform.slice(1)
    }

    const formatDate = (dateString) => {
      if (!dateString) return ''
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const formatJoinDate = (dateString) => {
      if (!dateString) return ''
      return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        year: 'numeric'
      })
    }

    const calculateExperience = (joinDate) => {
      if (!joinDate) return ''
      const years = new Date().getFullYear() - new Date(joinDate).getFullYear()
      const months = new Date().getMonth() - new Date(joinDate).getMonth()

      if (years > 0) {
        return `${years}+ year${years > 1 ? 's' : ''} experience`
      } else if (months > 0) {
        return `${months} month${months > 1 ? 's' : ''} experience`
      } else {
        return 'Recent addition'
      }
    }

    const truncateText = (text, length) => {
      if (!text) return ''
      return text.length > length ? text.substring(0, length) + '...' : text
    }

    const hasSocialLinks = (member) => {
      return member.social_links && Object.values(member.social_links).some(link => link && link.trim() !== '')
    }

    const getDefaultDepartments = () => [
      { key: 'engineering', label: 'Engineering' },
      { key: 'design', label: 'Design' },
      { key: 'marketing', label: 'Marketing' },
      { key: 'sales', label: 'Sales' },
      { key: 'hr', label: 'Human Resources' },
      { key: 'finance', label: 'Finance' },
      { key: 'operations', label: 'Operations' },
      { key: 'customer-support', label: 'Customer Support' }
    ]

    const getDefaultPositions = () => [
      { key: 'frontend-developer', label: 'Frontend Developer' },
      { key: 'backend-developer', label: 'Backend Developer' },
      { key: 'fullstack-developer', label: 'Full Stack Developer' },
      { key: 'ui-ux-designer', label: 'UI/UX Designer' },
      { key: 'product-manager', label: 'Product Manager' },
      { key: 'marketing-manager', label: 'Marketing Manager' },
      { key: 'sales-representative', label: 'Sales Representative' },
      { key: 'hr-specialist', label: 'HR Specialist' },
      { key: 'devops-engineer', label: 'DevOps Engineer' },
      { key: 'data-analyst', label: 'Data Analyst' }
    ]

    const getDefaultSkills = () => [
      { key: 'javascript', label: 'JavaScript' },
      { key: 'php', label: 'PHP' },
      { key: 'laravel', label: 'Laravel' },
      { key: 'vuejs', label: 'Vue.js' },
      { key: 'react', label: 'React' }
    ]

    const getDefaultStatuses = () => [
      { key: 'active', label: 'Active' },
      { key: 'inactive', label: 'Inactive' },
      { key: 'on-leave', label: 'On Leave' }
    ]

    onMounted(async () => {
      // Загружаем организационные данные и команду параллельно
      await Promise.all([
        loadOrganizationData(),
        loadTeamMembers()
      ])

      // Handle escape key for modal
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && selectedMember.value) {
          closeModal()
        }
      })
    })

    return {
      teamMembers,
      selectedMember,
      loading,
      searchQuery,
      departmentFilter,
      sortBy,
      organizationData,
      departments,
      teamStats,
      filteredMembers,
      hasActiveFilters,
      showMemberDetails,
      closeModal,
      applyFilters,
      clearFilters,
      getSocialIcon,
      getStatusIcon,
      formatStatus,
      formatPlatformName,
      formatDate,
      formatJoinDate,
      calculateExperience,
      truncateText,
      hasSocialLinks,
      // Новые методы для работы с организационными данными
      getDepartmentLabel,
      getPositionLabel,
      getSkillLabel,
      getStatusInfo
    }
  }
}
</script>

<style lang="scss" scoped>
@use '../../css/pages/team-page';
</style>
