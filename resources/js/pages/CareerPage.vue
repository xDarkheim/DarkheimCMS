<template>
  <div class="career-page page-with-header-offset">
    <!-- Hero Section -->
    <section class="career-hero">
      <div class="container">
        <div class="career-hero__content">
          <h1 class="career-hero__title">Join Our Team</h1>
          <p class="career-hero__subtitle">
            We're looking for talented individuals to help us build the future of web development.
            Discover exciting opportunities and grow your career with us.
          </p>
        </div>
      </div>
    </section>

    <!-- Error State -->
    <section v-if="error && !loading" class="career-error">
      <div class="container">
        <div class="error-state">
          <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <h3>Unable to Load Job Positions</h3>
          <p>{{ error }}</p>
          <button @click="retryLoad" class="btn btn--primary">
            <i class="fas fa-refresh"></i>
            Try Again
          </button>
        </div>
      </div>
    </section>

    <!-- Enhanced Stats Section -->
    <section class="career-stats" v-if="!loading && !error">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-item">
            <div class="stat-icon">
              <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ totalPositions }}</div>
              <div class="stat-label">Total Positions</div>
              <div class="stat-detail">{{ activePositions }} currently open</div>
            </div>
          </div>
          <div class="stat-item">
            <div class="stat-icon">
              <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ urgentPositions }}</div>
              <div class="stat-label">Urgent Hiring</div>
              <div class="stat-detail">High priority roles</div>
            </div>
          </div>
          <div class="stat-item">
            <div class="stat-icon">
              <i class="fas fa-wifi"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ remotePositions }}</div>
              <div class="stat-label">Remote Available</div>
              <div class="stat-detail">Work from anywhere</div>
            </div>
          </div>
          <div class="stat-item">
            <div class="stat-icon">
              <i class="fas fa-building"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ departments.length }}</div>
              <div class="stat-label">Departments</div>
              <div class="stat-detail">Diverse opportunities</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Filters Section -->
    <section class="career-filters" v-if="totalPositions > 0 && !error">
      <div class="container">
        <div class="filters-wrapper">
          <div class="filter-header">
            <h3>Find Your Perfect Role</h3>
            <p>Filter positions to match your preferences</p>
          </div>

          <div class="filter-controls">
            <div class="search-filter">
              <div class="search-box">
                <i class="fas fa-search"></i>
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search positions, skills, or departments..."
                  @input="debouncedApplyFilters"
                >
              </div>
            </div>

            <div class="filter-grid">
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
                <label>Employment Type</label>
                <select v-model="typeFilter" @change="applyFilters">
                  <option value="">All Types</option>
                  <option v-for="type in employmentTypes" :key="type.key" :value="type.key">
                    {{ type.label }}
                  </option>
                </select>
              </div>

              <div class="filter-group">
                <label>Experience Level</label>
                <select v-model="experienceFilter" @change="applyFilters">
                  <option value="">All Levels</option>
                  <option v-for="level in experienceLevels" :key="level.key" :value="level.key">
                    {{ level.label }}
                  </option>
                </select>
              </div>

              <div class="filter-group">
                <label>Location</label>
                <select v-model="locationFilter" @change="applyFilters">
                  <option value="">All Locations</option>
                  <option value="remote">Remote Only</option>
                  <option v-for="loc in locations" :key="loc" :value="loc">
                    {{ loc }}
                  </option>
                </select>
              </div>
            </div>

            <div class="filter-actions">
              <button v-if="hasActiveFilters" @click="clearFilters" class="btn btn--ghost">
                <i class="fas fa-times"></i>
                Clear All Filters
              </button>
              <div class="sort-control">
                <label>Sort by</label>
                <select v-model="sortBy" @change="applyFilters">
                  <option value="priority">Priority</option>
                  <option value="title">Job Title</option>
                  <option value="department">Department</option>
                  <option value="created_at">Newest First</option>
                  <option value="deadline">Application Deadline</option>
                </select>
              </div>
            </div>
          </div>

          <div class="filter-results" v-if="hasActiveFilters">
            <span class="results-count">
              {{ filteredCareers.length }} of {{ totalPositions }} positions match your criteria
            </span>
            <div v-if="filteredCareers.length === 0" class="no-results-hint">
              Try adjusting your filters to see more results
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Job Positions Section -->
    <section class="career-positions">
      <div class="container">
        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading job positions...</p>
        </div>

        <!-- Job Listings -->
        <div v-else-if="filteredCareers.length > 0 && !error" class="jobs-section">
          <div class="section-header">
            <h2>
              {{ hasActiveFilters ? 'Filtered Results' : 'Open Positions' }}
              <span class="position-count">({{ filteredCareers.length }})</span>
            </h2>
            <p v-if="!hasActiveFilters">Explore our current job openings and find your perfect match</p>
          </div>

          <div class="jobs-grid">
            <div
              v-for="job in filteredCareers"
              :key="job.id"
              class="job-card"
              :class="{
                'job-card--urgent': job.priority >= 75,
                'job-card--featured': job.priority >= 90
              }"
            >
              <!-- Priority Badge -->
              <div v-if="job.priority >= 75" class="job-card__priority">
                <i class="fas fa-bolt"></i>
                {{ job.priority >= 90 ? 'Featured' : 'Urgent' }}
              </div>

              <!-- Deadline Warning -->
              <div v-if="job.application_deadline && isDeadlineSoon(job.application_deadline)"
                   class="job-card__deadline-warning">
                <i class="fas fa-clock"></i>
                Deadline: {{ formatDate(job.application_deadline) }}
              </div>

              <div class="job-card__header">
                <h3 class="job-card__title">{{ job.title }}</h3>
                <div class="job-card__department">{{ job.department }}</div>
                <div v-if="job.salary_range" class="job-card__salary">{{ job.salary_range }}</div>
              </div>

              <div class="job-card__meta">
                <div class="job-meta-item">
                  <i class="fas fa-briefcase"></i>
                  <span>{{ formatEmploymentType(job.employment_type) }}</span>
                </div>
                <div class="job-meta-item">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>{{ job.location }}</span>
                  <span v-if="job.remote_available" class="remote-badge">
                    <i class="fas fa-wifi"></i>
                    Remote OK
                  </span>
                </div>
                <div class="job-meta-item">
                  <i class="fas fa-chart-line"></i>
                  <span>{{ formatExperienceLevel(job.experience_level) }}</span>
                </div>
                <div v-if="job.application_deadline" class="job-meta-item">
                  <i class="fas fa-calendar"></i>
                  <span>Apply by {{ formatDate(job.application_deadline) }}</span>
                </div>
              </div>

              <div class="job-card__description">
                <p>{{ job.short_description }}</p>
              </div>

              <div v-if="job.skills && job.skills.length" class="job-card__skills">
                <span
                  v-for="skill in job.skills.slice(0, 5)"
                  :key="skill"
                  class="skill-tag"
                >
                  {{ skill }}
                </span>
                <span v-if="job.skills.length > 5" class="skill-more">
                  +{{ job.skills.length - 5 }} more
                </span>
              </div>

              <div class="job-card__footer">
                <div class="job-card__actions">
                  <button @click="showJobDetails(job)" class="btn btn--primary">
                    View Details
                    <i class="fas fa-arrow-right"></i>
                  </button>
                  <button @click="quickApply(job)" class="btn btn--ghost">
                    <i class="fas fa-paper-plane"></i>
                    Quick Apply
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- No Results State -->
        <div v-else-if="!loading && hasActiveFilters && !error" class="no-results-state">
          <div class="no-results-icon">
            <i class="fas fa-search"></i>
          </div>
          <h3>No Positions Match Your Criteria</h3>
          <p>Try adjusting your filters or search terms to find more opportunities.</p>
          <button @click="clearFilters" class="btn btn--primary">
            <i class="fas fa-times"></i>
            Clear All Filters
          </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="!loading && totalPositions === 0 && !error" class="empty-state">
          <div class="empty-icon">
            <i class="fas fa-briefcase"></i>
          </div>
          <h3>No Open Positions</h3>
          <p>We don't have any open positions at the moment, but feel free to send us your resume for future opportunities.</p>
          <router-link to="/contact" class="btn btn--primary">
            Send Your Resume
            <i class="fas fa-envelope"></i>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Enhanced Job Details Modal -->
    <div v-if="selectedJob" class="modal-backdrop job-details-modal" @click="closeModal">
      <div class="modal-content" @click.stop>
        <button class="modal-close" @click="closeModal">
          <i class="fas fa-times"></i>
        </button>

        <div class="modal-header">
          <div class="job-title-section">
            <h2 class="modal-title">{{ selectedJob.title }}</h2>
            <div class="job-meta">
              <span class="meta-tag">{{ selectedJob.department }}</span>
              <span class="meta-tag">{{ formatEmploymentType(selectedJob.employment_type) }}</span>
              <span class="meta-tag">{{ formatExperienceLevel(selectedJob.experience_level) }}</span>
            </div>
            <div class="job-location">
              <i class="fas fa-map-marker-alt"></i>
              <span>{{ selectedJob.location }}</span>
              <span v-if="selectedJob.remote_available" class="remote-badge">
                <i class="fas fa-wifi"></i>
                Remote Available
              </span>
            </div>
            <div v-if="selectedJob.salary_range" class="job-salary">
              <i class="fas fa-dollar-sign"></i>
              {{ selectedJob.salary_range }}
            </div>
          </div>
        </div>

        <div class="modal-body">
          <div class="job-tabs">
            <button
              v-for="tab in modalTabs"
              :key="tab.id"
              @click="activeModalTab = tab.id"
              class="tab-button"
              :class="{ active: activeModalTab === tab.id }"
            >
              <i :class="tab.icon"></i>
              <span>{{ tab.label }}</span>
            </button>
          </div>

          <div class="tab-content">
            <!-- Description Tab -->
            <div v-show="activeModalTab === 'description'" class="tab-pane">
              <div class="job-section">
                <h4 class="section-title">About This Role</h4>
                <div class="section-content" style="white-space: pre-line;">{{ selectedJob.description || 'No description available' }}</div>
              </div>

              <!-- Short Description -->
              <div v-if="selectedJob.short_description" class="job-section">
                <h4 class="section-title">Summary</h4>
                <div class="section-content">{{ selectedJob.short_description }}</div>
              </div>
            </div>

            <!-- Requirements Tab -->
            <div v-show="activeModalTab === 'requirements'" class="tab-pane">
              <div class="job-section">
                <h4 class="section-title">What We're Looking For</h4>
                <div class="section-content">
                  <ul v-if="Array.isArray(selectedJob.requirements)" class="requirements-list">
                    <li v-for="requirement in selectedJob.requirements" :key="requirement">
                      {{ requirement }}
                    </li>
                  </ul>
                  <div v-else style="white-space: pre-line;">
                    {{ selectedJob.requirements || 'Requirements will be discussed during the interview process.' }}
                  </div>
                </div>
              </div>

              <!-- Skills Section -->
              <div v-if="selectedJob.skills && selectedJob.skills.length" class="job-section">
                <h4 class="section-title">Required Skills</h4>
                <div class="job-skills">
                  <span
                    v-for="skill in selectedJob.skills"
                    :key="skill"
                    class="skill-tag"
                  >
                    {{ skill }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Benefits Tab -->
            <div v-show="activeModalTab === 'benefits'" class="tab-pane">
              <div class="job-section">
                <h4 class="section-title">What We Offer</h4>
                <div class="section-content">
                  <ul v-if="Array.isArray(selectedJob.benefits)" class="benefits-list">
                    <li v-for="benefit in selectedJob.benefits" :key="benefit">
                      {{ benefit }}
                    </li>
                  </ul>
                  <div v-else-if="selectedJob.benefits" style="white-space: pre-line;">
                    {{ selectedJob.benefits }}
                  </div>
                  <div v-else>
                    <p>Benefits information will be discussed during the interview process.</p>
                  </div>
                </div>
              </div>

              <!-- Job Information Grid -->
              <div class="job-section">
                <h4 class="section-title">Position Details</h4>
                <div class="job-info-grid">
                  <div class="info-item">
                    <i class="fas fa-briefcase"></i>
                    <div class="info-content">
                      <strong>Employment Type</strong>
                      <span>{{ formatEmploymentType(selectedJob.employment_type) }}</span>
                    </div>
                  </div>
                  <div class="info-item">
                    <i class="fas fa-chart-line"></i>
                    <div class="info-content">
                      <strong>Experience Level</strong>
                      <span>{{ formatExperienceLevel(selectedJob.experience_level) }}</span>
                    </div>
                  </div>
                  <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="info-content">
                      <strong>Location</strong>
                      <span>{{ selectedJob.location }}{{ selectedJob.remote_available ? ' (Remote Available)' : '' }}</span>
                    </div>
                  </div>
                  <div v-if="selectedJob.salary_range" class="info-item">
                    <i class="fas fa-dollar-sign"></i>
                    <div class="info-content">
                      <strong>Salary Range</strong>
                      <span>{{ selectedJob.salary_range }}</span>
                    </div>
                  </div>
                  <div v-if="selectedJob.application_deadline" class="info-item">
                    <i class="fas fa-calendar"></i>
                    <div class="info-content">
                      <strong>Application Deadline</strong>
                      <span>{{ formatDate(selectedJob.application_deadline) }}</span>
                    </div>
                  </div>
                  <div class="info-item">
                    <i class="fas fa-building"></i>
                    <div class="info-content">
                      <strong>Department</strong>
                      <span>{{ selectedJob.department }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <div class="footer-info">
            <small>Posted {{ formatDate(selectedJob.created_at) }}</small>
            <span v-if="selectedJob.application_deadline" class="deadline-info">
              • Deadline: {{ formatDate(selectedJob.application_deadline) }}
            </span>
          </div>
          <div class="footer-actions">
            <button @click="shareJob(selectedJob)" class="btn btn--ghost">
              <i class="fas fa-share"></i>
              Share
            </button>
            <button @click="quickApply(selectedJob)" class="btn btn--primary">
              <i class="fas fa-paper-plane"></i>
              Apply Now
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Apply Modal -->
    <div v-if="showQuickApplyModal" class="modal-backdrop quick-apply-modal" @click="closeQuickApplyModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">Quick Apply - {{ quickApplyJob?.title }}</h3>
          <button class="modal-close" @click="closeQuickApplyModal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <p class="apply-info">This will redirect you to our contact page where you can submit your application with all necessary details.</p>
          <div class="apply-actions">
            <router-link
              :to="`/contact?position=${encodeURIComponent(quickApplyJob?.title)}&department=${encodeURIComponent(quickApplyJob?.department)}`"
              class="btn btn--primary"
              @click="closeQuickApplyModal"
            >
              Continue to Application
              <i class="fas fa-arrow-right"></i>
            </router-link>
            <button @click="closeQuickApplyModal" class="btn btn--ghost">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Why Work With Us Section -->
    <section class="career-benefits" v-if="!error">
      <div class="container">
        <div class="section-header">
          <h2>Why Work With Us?</h2>
          <p>Discover the benefits of joining our team</p>
        </div>

        <div class="benefits-grid">
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-laptop-code"></i>
            </div>
            <h4>Latest Technology</h4>
            <p>Work with cutting-edge tools and technologies in modern development environments.</p>
          </div>
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-users"></i>
            </div>
            <h4>Great Team</h4>
            <p>Join a supportive team of talented professionals who are passionate about what they do.</p>
          </div>
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <h4>Career Growth</h4>
            <p>Continuous learning opportunities and clear career advancement paths.</p>
          </div>
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-balance-scale"></i>
            </div>
            <h4>Work-Life Balance</h4>
            <p>Flexible working hours and remote work options to maintain a healthy balance.</p>
          </div>
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-heart"></i>
            </div>
            <h4>Health Benefits</h4>
            <p>Comprehensive health insurance and wellness programs for you and your family.</p>
          </div>
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-graduation-cap"></i>
            </div>
            <h4>Learning Budget</h4>
            <p>Annual budget for courses, conferences, and professional development.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="career-cta" v-if="!error">
      <div class="container">
        <div class="career-cta__content">
          <h2 class="career-cta__title">Ready to Join Our Team?</h2>
          <p class="career-cta__text">
            Don't see a perfect match? We're always looking for talented people.
            Send us your resume and we'll keep you in mind for future opportunities.
          </p>
          <div class="career-cta__actions">
            <router-link to="/contact" class="btn btn--primary btn--xl">
              Send Your Resume
              <i class="fas fa-upload"></i>
            </router-link>
            <router-link to="/team" class="btn btn--ghost btn--xl">
              Meet Our Team
              <i class="fas fa-users"></i>
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
  name: 'CareerPage',
  setup() {
    // State
    const careers = ref([])
    const selectedJob = ref(null)
    const loading = ref(false)
    const error = ref(null)
    const searchQuery = ref('')
    const departmentFilter = ref('')
    const typeFilter = ref('')
    const experienceFilter = ref('')
    const locationFilter = ref('')
    const sortBy = ref('priority')
    const activeModalTab = ref('description')
    const showQuickApplyModal = ref(false)
    const quickApplyJob = ref(null)

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
      employment_types: [
        { key: 'full-time', label: 'Full Time' },
        { key: 'part-time', label: 'Part Time' },
        { key: 'contract', label: 'Contract' },
        { key: 'internship', label: 'Internship' }
      ],
      experience_levels: [
        { key: 'entry', label: 'Entry Level' },
        { key: 'junior', label: 'Junior' },
        { key: 'mid', label: 'Mid Level' },
        { key: 'senior', label: 'Senior' },
        { key: 'lead', label: 'Lead' },
        { key: 'principal', label: 'Principal' }
      ],
      locations: [
        { key: 'remote', label: 'Remote' },
        { key: 'new-york', label: 'New York, NY' },
        { key: 'san-francisco', label: 'San Francisco, CA' },
        { key: 'london', label: 'London, UK' },
        { key: 'berlin', label: 'Berlin, Germany' }
      ]
    })

    // Modal tabs configuration
    const modalTabs = ref([
      { id: 'description', label: 'Description', icon: 'fas fa-file-text' },
      { id: 'requirements', label: 'Requirements', icon: 'fas fa-list-check' },
      { id: 'benefits', label: 'Benefits', icon: 'fas fa-heart' }
    ])

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
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const formatText = (text) => {
      if (!text || typeof text !== 'string') return ''
      return text
        .replace(/\n/g, '<br>')
        .replace(/•\s/g, '<li>')
        .replace(/^\s*-\s/gm, '<li>')
        .replace(/(<li>.*?)(?=<li>|$)/gs, '<ul>$1</ul>')
        .replace(/<li>/g, '<li>')
        .replace(/<\/ul><ul>/g, '')
    }

    const isDeadlineSoon = (deadline, daysThreshold = 7) => {
      if (!deadline) return false
      const now = new Date()
      const deadlineDate = new Date(deadline)
      const diff = deadlineDate - now
      return diff > 0 && diff < daysThreshold * 24 * 60 * 60 * 1000
    }

    const formatEmploymentType = (type) => {
      const orgType = organizationData.value.employment_types.find(t => t.key === type)
      if (orgType) return orgType.label

      const types = {
        'full-time': 'Full Time',
        'part-time': 'Part Time',
        'contract': 'Contract',
        'internship': 'Internship'
      }
      return types[type] || type
    }

    const formatExperienceLevel = (level) => {
      const orgLevel = organizationData.value.experience_levels.find(l => l.key === level)
      if (orgLevel) return orgLevel.label

      const levels = {
        'entry': 'Entry Level',
        'junior': 'Junior',
        'mid': 'Mid Level',
        'senior': 'Senior',
        'lead': 'Lead',
        'principal': 'Principal'
      }
      return levels[level] || level
    }

    // Computed properties
    const totalPositions = computed(() => careers.value.length)

    const activePositions = computed(() =>
      careers.value.filter(career => career.is_active).length
    )

    const urgentPositions = computed(() =>
      careers.value.filter(career => career.priority >= 75 && career.is_active).length
    )

    const remotePositions = computed(() =>
      careers.value.filter(career => career.remote_available && career.is_active).length
    )

    const departments = computed(() => {
      const orgDepartments = organizationData.value.departments.map(d => d.label)
      const careerDepartments = [...new Set(careers.value.map(career => career.department))]
      return [...new Set([...orgDepartments, ...careerDepartments])].sort()
    })

    const employmentTypes = computed(() => organizationData.value.employment_types)

    const experienceLevels = computed(() => organizationData.value.experience_levels)

    const locations = computed(() => {
      const orgLocations = organizationData.value.locations.map(l => l.label)
      const careerLocations = [...new Set(careers.value.map(career => career.location))]
      return [...new Set([...orgLocations, ...careerLocations])].sort()
    })

    const filteredCareers = computed(() => {
      let filtered = careers.value.filter(career => {
        if (!career.is_active) return false

        const matchesSearch = !searchQuery.value ||
          career.title?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          career.department?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          career.location?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          career.short_description?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          (career.skills && career.skills.some(skill =>
            skill.toLowerCase().includes(searchQuery.value.toLowerCase())
          ))

        const matchesDepartment = !departmentFilter.value ||
          career.department === departmentFilter.value

        const matchesType = !typeFilter.value ||
          career.employment_type === typeFilter.value

        const matchesExperience = !experienceFilter.value ||
          career.experience_level === experienceFilter.value

        const matchesLocation = !locationFilter.value ||
          (locationFilter.value === 'remote' ? career.remote_available : career.location === locationFilter.value)

        return matchesSearch && matchesDepartment && matchesType && matchesExperience && matchesLocation
      })

      return filtered.sort((a, b) => {
        switch (sortBy.value) {
          case 'title':
            return (a.title || '').localeCompare(b.title || '')
          case 'department':
            return (a.department || '').localeCompare(b.department || '')
          case 'created_at':
            return new Date(b.created_at || 0) - new Date(a.created_at || 0)
          case 'deadline':
            if (!a.application_deadline && !b.application_deadline) return 0
            if (!a.application_deadline) return 1
            if (!b.application_deadline) return -1
            return new Date(a.application_deadline) - new Date(b.application_deadline)
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
      return searchQuery.value || departmentFilter.value || typeFilter.value ||
             experienceFilter.value || locationFilter.value
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

    const loadCareers = async () => {
      try {
        loading.value = true
        error.value = null
        console.log('Loading careers...')

        const response = await axios.get('/api/careers', {
          timeout: 15000,
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        })

        console.log('Careers API response:', response.data)

        if (response.data && response.data.success !== false) {
          careers.value = response.data.data || []
          console.log('Careers loaded:', careers.value.length)
        } else {
          console.error('Invalid response format')
          error.value = 'Invalid response format from server'
          careers.value = []
        }
      } catch (err) {
        console.error('Failed to load careers:', err)
        error.value = 'Unable to load job positions. Please try again later.'
        careers.value = []
      } finally {
        loading.value = false
      }
    }

    const retryLoad = async () => {
      await loadCareers()
    }

    const showJobDetails = (job) => {
      console.log('showJobDetails called with job:', job)
      selectedJob.value = job
      activeModalTab.value = 'description'
      document.body.style.overflow = 'hidden'
      console.log('selectedJob.value set to:', selectedJob.value)
    }

    const closeModal = () => {
      console.log('closeModal called')
      selectedJob.value = null
      document.body.style.overflow = 'auto'
    }

    const quickApply = (job) => {
      quickApplyJob.value = job
      showQuickApplyModal.value = true
    }

    const closeQuickApplyModal = () => {
      showQuickApplyModal.value = false
      quickApplyJob.value = null
    }

    const shareJob = async (job) => {
      const shareData = {
        title: `${job.title} - ${job.department}`,
        text: job.short_description,
        url: window.location.href
      }

      try {
        if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
          await navigator.share(shareData)
          console.log('Job shared via native share!')
        } else if (navigator.clipboard) {
          await navigator.clipboard.writeText(shareData.url || shareData.text)
          console.log('Job shared via clipboard!')
        } else {
          // Fallback for older browsers
          const textArea = document.createElement('textarea')
          textArea.value = shareData.url || shareData.text
          document.body.appendChild(textArea)
          textArea.select()
          document.execCommand('copy')
          document.body.removeChild(textArea)
          console.log('Job shared via fallback!')
        }
      } catch (err) {
        console.error('Error sharing:', err)
      }
    }

    const applyFilters = () => {
      // Filters are applied automatically via computed property
    }

    const debouncedApplyFilters = useDebounce(applyFilters, 300)

    const clearFilters = () => {
      searchQuery.value = ''
      departmentFilter.value = ''
      typeFilter.value = ''
      experienceFilter.value = ''
      locationFilter.value = ''
    }

    // Lifecycle
    onMounted(async () => {
      console.log('CareerPage mounted')
      await loadOrganizationData()
      await loadCareers()

      // Handle escape key for modals
      const handleEscape = (e) => {
        if (e.key === 'Escape') {
          if (showQuickApplyModal.value) {
            closeQuickApplyModal()
          } else if (selectedJob.value) {
            closeModal()
          }
        }
      }
      document.addEventListener('keydown', handleEscape)
    })

    return {
      // State
      careers,
      selectedJob,
      loading,
      error,
      searchQuery,
      departmentFilter,
      typeFilter,
      experienceFilter,
      locationFilter,
      sortBy,
      activeModalTab,
      showQuickApplyModal,
      quickApplyJob,
      modalTabs,
      organizationData,

      // Computed
      totalPositions,
      activePositions,
      urgentPositions,
      remotePositions,
      departments,
      employmentTypes,
      experienceLevels,
      locations,
      filteredCareers,
      hasActiveFilters,

      // Methods
      retryLoad,
      showJobDetails,
      closeModal,
      quickApply,
      closeQuickApplyModal,
      shareJob,
      applyFilters,
      debouncedApplyFilters,
      clearFilters,
      formatEmploymentType,
      formatExperienceLevel,
      formatDate,
      formatText,
      isDeadlineSoon
    }
  }
}
</script>

<style lang="scss" scoped>
@use '../../css/pages/career-page';

.career-error {
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
