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

    <!-- Error State -->
    <section v-if="error && !loading" class="team-error">
      <div class="container">
        <div class="error-state">
          <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <h3>Unable to Load Team Members</h3>
          <p>{{ error }}</p>
          <button @click="retryLoad" class="btn btn--primary">
            <i class="fas fa-refresh"></i>
            Try Again
          </button>
        </div>
      </div>
    </section>

    <!-- Team Stats Section -->
    <section class="team-stats" v-if="teamStats.totalMembers > 0 && !error">
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
    <section class="team-filters" v-if="teamMembers.length > 0 && !error">
      <div class="container">
        <div class="filters-wrapper">
          <div class="search-filter">
            <div class="search-box">
              <i class="fas fa-search"></i>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search team members..."
                @input="debouncedApplyFilters"
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
        <div v-else-if="filteredMembers.length > 0 && !error" class="team-grid">
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
                  @error="handleImageError"
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
        <div v-else-if="!loading && teamMembers.length === 0 && !error" class="team-empty">
          <div class="team-empty__icon">
            <i class="fas fa-users"></i>
          </div>
          <h3 class="team-empty__title">No Team Members Found</h3>
          <p class="team-empty__text">Team information will be available soon.</p>
        </div>

        <!-- No Results State -->
        <div v-else-if="!loading && filteredMembers.length === 0 && !error" class="team-no-results">
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
    <div v-if="selectedMember" class="modal-backdrop team-member-modal" @click="closeModal">
      <div class="modal-content" @click.stop>
        <button class="modal-close" @click="closeModal">
          <i class="fas fa-times"></i>
        </button>

        <div class="modal-header">
          <div class="member-avatar">
            <img
              :src="selectedMember.avatar || '/images/default-avatar.png'"
              :alt="selectedMember.name"
              @error="handleImageError"
            >
            <div class="status-indicator" :class="selectedMember.status"></div>
          </div>

          <div class="member-info">
            <h2 class="modal-title">{{ selectedMember.name }}</h2>
            <div class="member-position">{{ selectedMember.position }}</div>
            <div class="member-department">{{ selectedMember.department }}</div>

            <div class="member-meta">
              <div v-if="selectedMember.joined_date" class="meta-item">
                <i class="fas fa-calendar"></i>
                <span>Joined {{ formatDate(selectedMember.joined_date) }}</span>
                <span class="experience">({{ calculateExperience(selectedMember.joined_date) }})</span>
              </div>
              <div class="meta-item">
                <i class="fas" :class="getStatusIcon(selectedMember.status)"></i>
                <span>{{ formatStatus(selectedMember.status) }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-body">
          <!-- Bio Section -->
          <div class="member-section">
            <h4 class="section-title">About</h4>
            <div class="section-content" style="white-space: pre-line;">{{ selectedMember.bio || 'No bio available' }}</div>
          </div>

          <!-- Skills Section -->
          <div v-if="selectedMember.skills && selectedMember.skills.length" class="member-section">
            <h4 class="section-title">Skills & Expertise</h4>
            <div class="member-skills">
              <span
                v-for="skill in selectedMember.skills"
                :key="skill"
                class="skill-tag"
              >
                {{ skill }}
              </span>
            </div>
          </div>

          <!-- Contact Information Section -->
          <div class="member-section">
            <h4 class="section-title">Get In Touch</h4>
            <div class="contact-info">
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
                <span>Contact information not available</span>
              </div>
            </div>

            <!-- Social Links Section -->
            <div v-if="hasSocialLinks(selectedMember)" class="social-links">
              <a
                v-for="(link, platform) in selectedMember.social_links"
                :key="platform"
                :href="link"
                target="_blank"
                rel="noopener noreferrer"
                class="social-link"
                :class="platform"
              >
                <i :class="getSocialIcon(platform)"></i>
                <span>{{ formatPlatformName(platform) }}</span>
              </a>
            </div>
          </div>

          <!-- Additional Info Section -->
          <div class="member-section">
            <h4 class="section-title">Team Information</h4>
            <div class="info-grid">
              <div class="info-item">
                <i class="fas fa-building"></i>
                <div class="info-content">
                  <strong>Department</strong>
                  <span>{{ selectedMember.department }}</span>
                </div>
              </div>
              <div class="info-item">
                <i class="fas fa-briefcase"></i>
                <div class="info-content">
                  <strong>Position</strong>
                  <span>{{ selectedMember.position }}</span>
                </div>
              </div>
              <div v-if="selectedMember.joined_date" class="info-item">
                <i class="fas fa-calendar-plus"></i>
                <div class="info-content">
                  <strong>Joined Date</strong>
                  <span>{{ formatDate(selectedMember.joined_date) }}</span>
                </div>
              </div>
              <div class="info-item">
                <i class="fas fa-chart-line"></i>
                <div class="info-content">
                  <strong>Experience</strong>
                  <span>{{ calculateExperience(selectedMember.joined_date) || 'N/A' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Join Team CTA -->
    <section class="team-cta" v-if="!error">
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
    // State
    const teamMembers = ref([])
    const selectedMember = ref(null)
    const loading = ref(false)
    const error = ref(null)
    const searchQuery = ref('')
    const departmentFilter = ref('')
    const sortBy = ref('priority')

    // Organization data with defaults
    const organizationData = ref({
      departments: [
        { key: 'engineering', label: 'Engineering' },
        { key: 'design', label: 'Design' },
        { key: 'marketing', label: 'Marketing' },
        { key: 'sales', label: 'Sales' },
        { key: 'hr', label: 'Human Resources' },
        { key: 'finance', label: 'Finance' },
        { key: 'operations', label: 'Operations' },
        { key: 'customer-support', label: 'Customer Support' }
      ],
      statuses: [
        { key: 'active', label: 'Active' },
        { key: 'inactive', label: 'Inactive' },
        { key: 'on-leave', label: 'On Leave' }
      ]
    })

    // Utility functions
    const useDebounce = (func, delay) => {
      let timeoutId
      return (...args) => {
        clearTimeout(timeoutId)
        timeoutId = setTimeout(() => func(...args), delay)
      }
    }

    const formatDate = (dateString) => {
      if (!dateString) return ''
      return new Date(dateString).toLocaleDateString('en-CA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const formatJoinDate = (dateString) => {
      if (!dateString) return ''
      return new Date(dateString).toLocaleDateString('en-CA', {
        month: 'short',
        year: 'numeric'
      })
    }

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

    const truncateText = (text, length = 120) => {
      if (!text) return ''
      return text.length > length ? text.substring(0, length) + '...' : text
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
        website: 'fas fa-globe',
        email: 'fas fa-envelope',
        phone: 'fas fa-phone'
      }
      return icons[platform.toLowerCase()] || 'fas fa-link'
    }

    const formatPlatformName = (platform) => {
      const names = {
        linkedin: 'LinkedIn',
        github: 'GitHub'
      }
      return names[platform.toLowerCase()] || platform.charAt(0).toUpperCase() + platform.slice(1)
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
      const orgStatus = organizationData.value.statuses.find(s => s.key === status)
      if (orgStatus) return orgStatus.label

      const statuses = {
        'active': 'Active',
        'inactive': 'Inactive',
        'on-leave': 'On Leave'
      }
      return statuses[status] || status
    }

    const hasSocialLinks = (entity) => {
      return entity.social_links &&
             Object.values(entity.social_links).some(link => link && link.trim() !== '')
    }

    const handleImageError = (event) => {
      event.target.src = '/images/default-avatar.png'
    }

    // Computed properties
    const departments = computed(() => {
      const orgDepartments = organizationData.value.departments.map(d => d.label)
      const teamDepartments = [...new Set(teamMembers.value.map(member => member.department))]
      return [...new Set([...orgDepartments, ...teamDepartments])].sort()
    })

    const teamStats = computed(() => {
      const members = teamMembers.value.filter(m => m.show_on_website !== false)
      const departmentsSet = new Set(members.map(m => m.department))

      // Calculate average experience based on join date
      const membersWithJoinDate = members.filter(m => m.joined_date)
      const avgExperience = membersWithJoinDate.length > 0
        ? Math.round(membersWithJoinDate.reduce((acc, member) => {
            const years = new Date().getFullYear() - new Date(member.joined_date).getFullYear()
            return acc + years
          }, 0) / membersWithJoinDate.length)
        : 0

      // Update countries for Canada-based company
      const countries = new Set()
      // Check if we have location data from team members
      members.forEach(member => {
        if (member.location) {
          countries.add(member.location)
        }
      })

      // Default to Canada if no specific locations
      if (countries.size === 0) {
        countries.add('Canada')
      }

      return {
        totalMembers: members.length,
        departments: departmentsSet.size,
        countries: countries.size,
        avgExperience: avgExperience > 0 ? `${avgExperience}+ years` : 'New team'
      }
    })

    const filteredMembers = computed(() => {
      let filtered = teamMembers.value.filter(member => {
        // Only show visible members on public page
        if (member.show_on_website === false) return false

        // Search filter
        const matchesSearch = !searchQuery.value ||
          member.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          member.position?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          member.department?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
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
            return (a.name || '').localeCompare(b.name || '')
          case 'position':
            return (a.position || '').localeCompare(b.position || '')
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
        console.log('Loading organization data...')
        // Organization data is already set with defaults above
      } catch (error) {
        console.warn('Failed to load organization data:', error)
      }
    }

    const loadTeamMembers = async () => {
      try {
        loading.value = true
        error.value = null
        console.log('Loading team members...')

        const response = await axios.get('/api/team', {
          timeout: 15000,
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        })

        console.log('Team API response:', response.data)

        if (response.data && response.data.success !== false) {
          teamMembers.value = response.data.data || []
          console.log('Team members loaded:', teamMembers.value.length)
        } else {
          console.error('Invalid response format')
          error.value = 'Invalid response format from server'
          teamMembers.value = []
        }
      } catch (err) {
        console.error('Failed to load team members:', err)
        error.value = 'Unable to load team members. Please try again later.'
        teamMembers.value = []
      } finally {
        loading.value = false
      }
    }

    const retryLoad = async () => {
      await loadTeamMembers()
    }

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

    const debouncedApplyFilters = useDebounce(applyFilters, 300)

    const clearFilters = () => {
      searchQuery.value = ''
      departmentFilter.value = ''
    }

    // Lifecycle
    onMounted(async () => {
      console.log('TeamPage mounted')
      await loadOrganizationData()
      await loadTeamMembers()

      // Handle escape key for modal
      const handleEscape = (e) => {
        if (e.key === 'Escape' && selectedMember.value) {
          closeModal()
        }
      }
      document.addEventListener('keydown', handleEscape)
    })

    return {
      // State
      teamMembers,
      selectedMember,
      loading,
      error,
      searchQuery,
      departmentFilter,
      sortBy,
      organizationData,

      // Computed
      departments,
      teamStats,
      filteredMembers,
      hasActiveFilters,

      // Methods
      retryLoad,
      showMemberDetails,
      closeModal,
      applyFilters,
      debouncedApplyFilters,
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
      handleImageError
    }
  }
}
</script>

<style lang="scss" scoped>
@use '../../css/pages/team-page';

.team-error {
  padding: 4rem 0;

  .error-state {
    text-align: center;
    max-width: 500px;
    margin: 0 auto;

    .error-icon {
      font-size: 4rem;
      color: #f56565;
      margin-bottom: 1rem;
    }

    h3 {
      color: #2d3748;
      margin-bottom: 1rem;
    }

    p {
      color: #718096;
      margin-bottom: 2rem;
    }
  }
}
</style>
