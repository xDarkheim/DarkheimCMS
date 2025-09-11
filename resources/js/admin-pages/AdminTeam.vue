<template>
  <div class="admin-team">
    <div class="admin-header">
      <div class="header-left">
        <h1>Team Management</h1>
        <p class="header-subtitle">Manage team members and their public profiles</p>
      </div>
      <button @click="showCreateModal" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Add Team Member
      </button>
    </div>

    <!-- Enhanced Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon primary">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
          <div class="stat-number">{{ teamMembers.length }}</div>
          <div class="stat-label">Total Members</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon success">
          <i class="fas fa-eye"></i>
        </div>
        <div class="stat-content">
          <div class="stat-number">{{ visibleMembers }}</div>
          <div class="stat-label">Visible on Website</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon warning">
          <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-content">
          <div class="stat-number">{{ activeMembers }}</div>
          <div class="stat-label">Active Members</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon info">
          <i class="fas fa-building"></i>
        </div>
        <div class="stat-content">
          <div class="stat-number">{{ uniqueDepartments }}</div>
          <div class="stat-label">Departments</div>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="filters-section">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search team members..."
          @input="handleSearch"
        >
      </div>
      <div class="filter-controls">
        <select v-model="statusFilter" @change="applyFilters">
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
          <option value="on-leave">On Leave</option>
        </select>
        <select v-model="departmentFilter" @change="applyFilters">
          <option value="">All Departments</option>
          <option v-for="dept in departmentsList" :key="dept" :value="dept">
            {{ getDepartmentLabel(dept) }}
          </option>
        </select>
        <select v-model="visibilityFilter" @change="applyFilters">
          <option value="">All Visibility</option>
          <option value="visible">Visible on Website</option>
          <option value="hidden">Hidden from Website</option>
        </select>
        <button @click="clearFilters" class="btn btn-outline">
          <i class="fas fa-times"></i>
          Clear
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading && !teamMembers.length" class="loading-state">
      <div class="loading-spinner"></div>
      <p>Loading team members...</p>
    </div>

    <!-- Error State -->
    <div v-if="error" class="error-state">
      <div class="error-icon">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <h3>Error Loading Team</h3>
      <p>{{ error }}</p>
      <button @click="loadTeamMembers" class="btn btn-primary">
        <i class="fas fa-retry"></i>
        Try Again
      </button>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && !error && !teamMembers.length" class="empty-state">
      <div class="empty-icon">
        <i class="fas fa-users"></i>
      </div>
      <h3>No Team Members Yet</h3>
      <p>Start building your team by adding your first member</p>
      <button @click="showCreateModal" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Add First Member
      </button>
    </div>

    <!-- Enhanced Team Members Table -->
    <div v-if="filteredMembers.length" class="table-container">
      <div class="table-header">
        <h3>Team Members ({{ filteredMembers.length }})</h3>
        <div class="table-actions">
          <button @click="exportMembers" class="btn btn-outline btn-sm">
            <i class="fas fa-download"></i>
            Export
          </button>
        </div>
      </div>

      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Photo</th>
              <th @click="sortBy('name')" class="sortable">
                Name
                <i class="fas fa-sort" :class="getSortIcon('name')"></i>
              </th>
              <th @click="sortBy('position')" class="sortable">
                Position
                <i class="fas fa-sort" :class="getSortIcon('position')"></i>
              </th>
              <th @click="sortBy('department')" class="sortable">
                Department
                <i class="fas fa-sort" :class="getSortIcon('department')"></i>
              </th>
              <th @click="sortBy('status')" class="sortable">
                Status
                <i class="fas fa-sort" :class="getSortIcon('status')"></i>
              </th>
              <th>Skills</th>
              <th @click="sortBy('show_on_website')" class="sortable">
                Visible
                <i class="fas fa-sort" :class="getSortIcon('show_on_website')"></i>
              </th>
              <th @click="sortBy('priority')" class="sortable">
                Priority
                <i class="fas fa-sort" :class="getSortIcon('priority')"></i>
              </th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="member in paginatedMembers" :key="member.id" class="member-row">
              <td class="avatar-cell">
                <div class="avatar-container">
                  <img :src="member.avatar || '/images/default-avatar.png'" :alt="member.name" class="avatar-small">
                  <div v-if="member.status === 'active'" class="status-indicator active"></div>
                  <div v-else-if="member.status === 'on-leave'" class="status-indicator warning"></div>
                  <div v-else class="status-indicator inactive"></div>
                </div>
              </td>
              <td class="member-name">
                <div class="name-content">
                  <strong>{{ member.name }}</strong>
                  <small v-if="member.email">{{ member.email }}</small>
                  <span v-if="member.joined_date" class="join-date">
                    <i class="fas fa-calendar"></i>
                    Joined {{ formatDate(member.joined_date) }}
                  </span>
                </div>
              </td>
              <td>
                <div class="position-content">
                  <span class="position-title">{{ getPositionLabel(member.position) }}</span>
                </div>
              </td>
              <td>
                <span class="department-badge">{{ getDepartmentLabel(member.department) }}</span>
              </td>
              <td>
                <span class="status-badge" :class="member.status">
                  <i class="fas" :class="getStatusIcon(member.status)"></i>
                  {{ getStatusLabel(member.status) }}
                </span>
              </td>
              <td class="skills-cell">
                <div v-if="member.skills && member.skills.length" class="skills-preview">
                  <span v-for="skill in member.skills.slice(0, 3)" :key="skill" class="skill-tag">
                    {{ getSkillLabel(skill) }}
                  </span>
                  <span v-if="member.skills.length > 3" class="skills-more">
                    +{{ member.skills.length - 3 }}
                  </span>
                </div>
                <span v-else class="no-skills">No skills listed</span>
              </td>
              <td>
                <span class="visibility-badge" :class="member.show_on_website ? 'visible' : 'hidden'">
                  <i class="fas" :class="member.show_on_website ? 'fa-eye' : 'fa-eye-slash'"></i>
                  {{ member.show_on_website ? 'Visible' : 'Hidden' }}
                </span>
              </td>
              <td>
                <div class="priority-indicator" :class="getPriorityClass(member.priority)">
                  {{ member.priority || 0 }}
                </div>
              </td>
              <td class="actions">
                <div class="action-buttons">
                  <button @click="viewMember(member)" class="btn-icon info" title="View Details">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button @click="editMember(member)" class="btn-icon" title="Edit">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button @click="duplicateMember(member)" class="btn-icon info" title="Duplicate">
                    <i class="fas fa-copy"></i>
                  </button>
                  <button @click="toggleVisibility(member)" class="btn-icon" :class="member.show_on_website ? 'warning' : 'success'" :title="member.show_on_website ? 'Hide from website' : 'Show on website'">
                    <i :class="member.show_on_website ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                  </button>
                  <button @click="deleteMember(member)" class="btn-icon danger" title="Delete">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="pagination-container">
        <div class="pagination-info">
          Showing {{ startItem }} to {{ endItem }} of {{ filteredMembers.length }} members
        </div>
        <div class="pagination">
          <button
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="btn btn-outline btn-sm"
          >
            <i class="fas fa-chevron-left"></i>
            Previous
          </button>

          <span class="page-numbers">
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="currentPage = page"
              class="btn btn-sm"
              :class="page === currentPage ? 'btn-primary' : 'btn-outline'"
            >
              {{ page }}
            </button>
          </span>

          <button
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            class="btn btn-outline btn-sm"
          >
            Next
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Enhanced Create/Edit Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content large" @click.stop>
        <div class="modal-header">
          <div class="modal-title">
            <h2>{{ isEditing ? 'Edit Team Member' : 'Add Team Member' }}</h2>
            <p class="modal-subtitle">{{ isEditing ? 'Update team member information' : 'Add a new team member to your organization' }}</p>
          </div>
          <button @click="closeModal" class="btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form @submit.prevent="saveMember" class="modal-body">
          <div class="form-tabs">
            <button
              type="button"
              v-for="tab in formTabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              class="tab-button"
              :class="{ active: activeTab === tab.id }"
            >
              <i :class="tab.icon"></i>
              {{ tab.label }}
            </button>
          </div>

          <!-- Basic Information Tab -->
          <div v-show="activeTab === 'basic'" class="tab-content">
            <div class="form-grid">
              <div class="form-group">
                <label>Full Name *</label>
                <input v-model="form.name" type="text" required placeholder="e.g., John Doe">
                <small class="form-hint">The full name of the team member</small>
              </div>

              <div class="form-group">
                <label>Position/Title *</label>
                <select v-model="form.position" required>
                  <option value="">Select position</option>
                  <option v-for="(label, key) in organizationData.positions" :key="key" :value="key">
                    {{ label }}
                  </option>
                </select>
                <small class="form-hint">Job title or role in the company</small>
              </div>

              <div class="form-group">
                <label>Department *</label>
                <select v-model="form.department" required>
                  <option value="">Select department</option>
                  <option v-for="(label, key) in organizationData.departments" :key="key" :value="key">
                    {{ label }}
                  </option>
                </select>
                <small class="form-hint">Department or team they belong to</small>
              </div>

              <div class="form-group">
                <label>Employment Status *</label>
                <select v-model="form.status" required>
                  <option value="">Select status</option>
                  <option v-for="(label, key) in organizationData.statuses" :key="key" :value="key">
                    {{ label }}
                  </option>
                </select>
                <small class="form-hint">Current employment status</small>
              </div>

              <div class="form-group">
                <label>Email Address</label>
                <input v-model="form.email" type="email" placeholder="john@example.com">
                <small class="form-hint">Professional email address (optional)</small>
              </div>

              <div class="form-group">
                <label>Joined Date</label>
                <input v-model="form.joined_date" type="date">
                <small class="form-hint">Date when they joined the company</small>
              </div>

              <div class="form-group">
                <label>Priority Level</label>
                <select v-model.number="form.priority">
                  <option value="0">Normal (0)</option>
                  <option value="25">Low Priority (25)</option>
                  <option value="50">Medium Priority (50)</option>
                  <option value="75">High Priority (75)</option>
                  <option value="100">Featured (100)</option>
                </select>
                <small class="form-hint">Higher priority members appear first on team page</small>
              </div>

              <div class="form-group checkbox-group">
                <label class="checkbox-label">
                  <input v-model="form.show_on_website" type="checkbox">
                  <span class="checkmark"></span>
                  Show on Public Website
                </label>
                <small class="form-hint">Check to display this member on the public team page</small>
              </div>
            </div>
          </div>

          <!-- Profile Tab -->
          <div v-show="activeTab === 'profile'" class="tab-content">
            <div class="form-group">
              <label>Profile Photo</label>
              <div class="avatar-upload">
                <div class="avatar-current">
                  <img :src="avatarPreview || currentAvatarUrl || '/images/default-avatar.png'" alt="Profile photo">
                </div>
                <div class="avatar-controls">
                  <input @change="handleAvatarChange" type="file" accept="image/*" ref="avatarInput" style="display: none;">
                  <button type="button" @click="$refs.avatarInput.click()" class="btn btn-outline">
                    <i class="fas fa-camera"></i>
                    Choose Photo
                  </button>
                  <button type="button" @click="removeAvatar" class="btn btn-outline" v-if="avatarPreview || currentAvatarUrl">
                    <i class="fas fa-trash"></i>
                    Remove
                  </button>
                </div>
              </div>
              <small class="form-hint">Upload a professional profile photo (recommended: 400x400px)</small>
            </div>

            <div class="form-group">
              <label>Bio/Description *</label>
              <textarea
                v-model="form.bio"
                rows="6"
                required
                placeholder="Write a brief description about this team member, their role, experience, and what they bring to the team..."
                maxlength="500"
              ></textarea>
              <small class="form-hint">{{ form.bio?.length || 0 }}/500 characters - Brief description for the team page</small>
            </div>

            <div class="form-group">
              <label>Skills & Expertise</label>
              <div class="skills-input">
                <input
                  v-model="newSkill"
                  type="text"
                  placeholder="Type a skill and press Enter"
                  @keypress.enter.prevent="addSkill"
                >
                <button type="button" @click="addSkill" class="btn btn-outline btn-sm">
                  <i class="fas fa-plus"></i>
                  Add
                </button>
              </div>
              <div v-if="form.skills && form.skills.length" class="skills-list">
                <span
                  v-for="(skill, index) in form.skills"
                  :key="index"
                  class="skill-tag"
                >
                  {{ skill }}
                  <button type="button" @click="removeSkill(index)" class="remove-skill">
                    <i class="fas fa-times"></i>
                  </button>
                </span>
              </div>
              <small class="form-hint">Add relevant skills, technologies, and areas of expertise</small>
            </div>

            <div class="form-group">
              <label>Suggested Skills</label>
              <div class="suggested-skills">
                <button
                  type="button"
                  v-for="skill in suggestedSkills"
                  :key="skill"
                  @click="addSuggestedSkill(skill)"
                  class="skill-suggestion"
                  :disabled="form.skills && form.skills.includes(skill)"
                >
                  {{ skill }}
                </button>
              </div>
            </div>
          </div>

          <!-- Social Links Tab -->
          <div v-show="activeTab === 'social'" class="tab-content">
            <div class="form-group">
              <label>Social Media & Professional Links</label>
              <div class="social-inputs">
                <div class="social-input">
                  <div class="social-icon linkedin">
                    <i class="fab fa-linkedin"></i>
                  </div>
                  <input v-model="socialLinks.linkedin" type="url" placeholder="https://linkedin.com/in/username">
                  <label>LinkedIn Profile</label>
                </div>
                <div class="social-input">
                  <div class="social-icon twitter">
                    <i class="fab fa-twitter"></i>
                  </div>
                  <input v-model="socialLinks.twitter" type="url" placeholder="https://twitter.com/username">
                  <label>Twitter Profile</label>
                </div>
                <div class="social-input">
                  <div class="social-icon github">
                    <i class="fab fa-github"></i>
                  </div>
                  <input v-model="socialLinks.github" type="url" placeholder="https://github.com/username">
                  <label>GitHub Profile</label>
                </div>
                <div class="social-input">
                  <div class="social-icon dribbble">
                    <i class="fab fa-dribbble"></i>
                  </div>
                  <input v-model="socialLinks.dribbble" type="url" placeholder="https://dribbble.com/username">
                  <label>Dribbble Portfolio</label>
                </div>
                <div class="social-input">
                  <div class="social-icon website">
                    <i class="fas fa-globe"></i>
                  </div>
                  <input v-model="socialLinks.website" type="url" placeholder="https://yourwebsite.com">
                  <label>Personal Website</label>
                </div>
              </div>
              <small class="form-hint">Add professional social media and portfolio links (all optional)</small>
            </div>
          </div>

          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn btn-secondary">
              <i class="fas fa-times"></i>
              Cancel
            </button>
            <button type="submit" class="btn btn-primary" :disabled="modalLoading">
              <i class="fas" :class="modalLoading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
              {{ modalLoading ? 'Saving...' : (isEditing ? 'Update Member' : 'Add Member') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Member Details Modal -->
    <div v-if="showDetailsModal && selectedMember" class="modal-overlay" @click="closeDetailsModal">
      <div class="modal-content medium" @click.stop>
        <div class="modal-header">
          <div class="modal-title">
            <h2>{{ selectedMember.name }}</h2>
            <p class="modal-subtitle">{{ getPositionLabel(selectedMember.position) }} • {{ getDepartmentLabel(selectedMember.department) }}</p>
          </div>
          <button @click="closeDetailsModal" class="btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="member-details">
          <div class="member-profile">
            <div class="member-avatar">
              <img :src="selectedMember.avatar || '/images/default-avatar.png'" :alt="selectedMember.name">
              <div class="status-indicator" :class="selectedMember.status"></div>
            </div>
            <div class="member-info">
              <h3>{{ selectedMember.name }}</h3>
              <p class="position">{{ getPositionLabel(selectedMember.position) }}</p>
              <p class="department">{{ getDepartmentLabel(selectedMember.department) }}</p>
              <div class="member-meta">
                <span v-if="selectedMember.email" class="meta-item">
                  <i class="fas fa-envelope"></i>
                  {{ selectedMember.email }}
                </span>
                <span v-if="selectedMember.joined_date" class="meta-item">
                  <i class="fas fa-calendar"></i>
                  Joined {{ formatDate(selectedMember.joined_date) }}
                </span>
                <span class="meta-item">
                  <i class="fas fa-eye"></i>
                  {{ selectedMember.show_on_website ? 'Visible on website' : 'Hidden from website' }}
                </span>
              </div>
            </div>
          </div>

          <div class="member-bio">
            <h4>About</h4>
            <p>{{ selectedMember.bio || 'No biography available.' }}</p>
          </div>

          <div v-if="selectedMember.skills && selectedMember.skills.length" class="member-skills">
            <h4>Skills & Expertise</h4>
            <div class="skills-list">
              <span v-for="skill in selectedMember.skills" :key="skill" class="skill-tag">
                {{ getSkillLabel(skill) }}
              </span>
            </div>
          </div>

          <div v-if="hasSocialLinks(selectedMember)" class="member-social">
            <h4>Professional Links</h4>
            <div class="social-links">
              <a v-if="selectedMember.social_links?.linkedin" :href="selectedMember.social_links.linkedin" target="_blank" class="social-link linkedin">
                <i class="fab fa-linkedin"></i>
                LinkedIn
              </a>
              <a v-if="selectedMember.social_links?.twitter" :href="selectedMember.social_links.twitter" target="_blank" class="social-link twitter">
                <i class="fab fa-twitter"></i>
                Twitter
              </a>
              <a v-if="selectedMember.social_links?.github" :href="selectedMember.social_links.github" target="_blank" class="social-link github">
                <i class="fab fa-github"></i>
                GitHub
              </a>
              <a v-if="selectedMember.social_links?.dribbble" :href="selectedMember.social_links.dribbble" target="_blank" class="social-link dribbble">
                <i class="fab fa-dribbble"></i>
                Dribbble
              </a>
              <a v-if="selectedMember.social_links?.website" :href="selectedMember.social_links.website" target="_blank" class="social-link website">
                <i class="fas fa-globe"></i>
                Website
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notifications -->
    <div v-if="toast.show" class="toast" :class="toast.type">
      <div class="toast-content">
        <i class="fas" :class="getToastIcon(toast.type)"></i>
        <span>{{ toast.message }}</span>
      </div>
      <button @click="hideToast" class="toast-close">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useNotifications } from '../composables/useNotifications'
import axios from 'axios'

const { showSuccess, showError, showWarning, showInfo } = useNotifications()

export default {
  name: 'AdminTeam',
  setup() {
    const teamMembers = ref([])
    const showModal = ref(false)
    const showDetailsModal = ref(false)
    const selectedMember = ref(null)
    const isEditing = ref(false)
    const loading = ref(false)
    const modalLoading = ref(false)
    const error = ref('')
    const newSkill = ref('')
    const avatarFile = ref(null)
    const avatarPreview = ref(null)
    const currentAvatarUrl = ref('')
    const activeTab = ref('basic')
    const searchQuery = ref('')
    const statusFilter = ref('')
    const departmentFilter = ref('')
    const visibilityFilter = ref('')
    const currentPage = ref(1)
    const itemsPerPage = 10
    const sortKey = ref('id')
    const sortOrder = ref('desc')
    const toast = ref({ show: false, message: '', type: '' })

    // Organization data with fallback values
    const organizationData = ref({
      departments: {
        'engineering': 'Engineering',
        'design': 'Design',
        'marketing': 'Marketing',
        'sales': 'Sales',
        'hr': 'Human Resources',
        'finance': 'Finance',
        'operations': 'Operations',
        'customer-support': 'Customer Support'
      },
      positions: {
        'frontend-developer': 'Frontend Developer',
        'backend-developer': 'Backend Developer',
        'fullstack-developer': 'Full Stack Developer',
        'ui-ux-designer': 'UI/UX Designer',
        'product-manager': 'Product Manager',
        'marketing-manager': 'Marketing Manager',
        'sales-representative': 'Sales Representative',
        'hr-specialist': 'HR Specialist',
        'devops-engineer': 'DevOps Engineer',
        'data-analyst': 'Data Analyst'
      },
      skills: {
        'javascript': 'JavaScript',
        'typescript': 'TypeScript',
        'php': 'PHP',
        'laravel': 'Laravel',
        'vuejs': 'Vue.js',
        'react': 'React',
        'nodejs': 'Node.js',
        'python': 'Python',
        'mysql': 'MySQL',
        'postgresql': 'PostgreSQL',
        'mongodb': 'MongoDB',
        'git': 'Git',
        'docker': 'Docker',
        'aws': 'AWS',
        'html': 'HTML',
        'css': 'CSS',
        'figma': 'Figma',
        'photoshop': 'Photoshop',
        'communication': 'Communication',
        'teamwork': 'Team Leadership',
        'problem-solving': 'Problem Solving',
        'project-management': 'Project Management'
      },
      statuses: {
        'active': 'Active',
        'inactive': 'Inactive',
        'on-leave': 'On Leave'
      }
    })

    const form = ref({
      name: '',
      position: '',
      department: '',
      bio: '',
      email: '',
      skills: [],
      social_links: {},
      status: 'active',
      joined_date: null,
      priority: 0,
      show_on_website: true
    })

    const socialLinks = ref({
      linkedin: '',
      twitter: '',
      github: '',
      dribbble: '',
      website: ''
    })

    const formTabs = ref([
      { id: 'basic', label: 'Basic Info', icon: 'fas fa-user' },
      { id: 'profile', label: 'Profile', icon: 'fas fa-id-card' },
      { id: 'social', label: 'Social Links', icon: 'fas fa-share-alt' }
    ])

    // Dynamic suggested skills from organization data
    const suggestedSkills = computed(() => {
      return Object.values(organizationData.value.skills)
    })

    // Computed properties
    const visibleMembers = computed(() =>
      teamMembers.value.filter(member => member.show_on_website).length
    )

    const activeMembers = computed(() =>
      teamMembers.value.filter(member => member.status === 'active').length
    )

    const uniqueDepartments = computed(() => {
      const departments = new Set(teamMembers.value.map(member => member.department))
      return departments.size
    })

    const departmentsList = computed(() => {
      const departments = new Set(teamMembers.value.map(member => member.department))
      return Array.from(departments).sort()
    })

    const filteredMembers = computed(() => {
      let filtered = teamMembers.value.filter(member => {
        const matchesStatus = statusFilter.value ?
          member.status === statusFilter.value : true
        const matchesDepartment = departmentFilter.value ?
          member.department === departmentFilter.value : true
        const matchesVisibility = visibilityFilter.value ?
          (visibilityFilter.value === 'visible' ? member.show_on_website : !member.show_on_website) : true
        const matchesSearch = searchQuery.value ?
          member.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          member.email?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          member.position.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          member.department.toLowerCase().includes(searchQuery.value.toLowerCase()) : true

        return matchesStatus && matchesDepartment && matchesVisibility && matchesSearch
      })

      // Apply sorting
      return filtered.sort((a, b) => {
        const modifier = sortOrder.value === 'asc' ? 1 : -1
        let aVal = a[sortKey.value]
        let bVal = b[sortKey.value]

        // Handle priority sorting (prioritize higher values)
        if (sortKey.value === 'priority') {
          aVal = a[sortKey.value] || 0
          bVal = b[sortKey.value] || 0
          return (bVal - aVal) * (sortOrder.value === 'asc' ? -1 : 1)
        }

        if (aVal < bVal) return -1 * modifier
        if (aVal > bVal) return 1 * modifier
        return 0
      })
    })

    const totalPages = computed(() =>
      Math.ceil(filteredMembers.value.length / itemsPerPage)
    )

    const paginatedMembers = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage
      const end = start + itemsPerPage
      return filteredMembers.value.slice(start, end)
    })

    const startItem = computed(() =>
      (currentPage.value - 1) * itemsPerPage + 1
    )

    const endItem = computed(() =>
      Math.min(currentPage.value * itemsPerPage, filteredMembers.value.length)
    )

    const visiblePages = computed(() => {
      const pages = []
      const maxVisible = 5
      let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
      let end = Math.min(totalPages.value, start + maxVisible - 1)

      if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1)
      }

      for (let i = start; i <= end; i++) {
        pages.push(i)
      }
      return pages
    })

    // Methods
    const getAuthHeaders = () => {
      const token = localStorage.getItem('admin_token')
      return token ? { Authorization: `Bearer ${token}` } : {}
    }

    const loadOrganizationData = async () => {
      try {
        // Try to load organization data, but use defaults if it fails
        const [departments, positions, skills, statuses] = await Promise.all([
          axios.get('/api/organization/departments', { headers: getAuthHeaders() }).catch(() => ({ data: { data: organizationData.value.departments } })),
          axios.get('/api/organization/positions', { headers: getAuthHeaders() }).catch(() => ({ data: { data: organizationData.value.positions } })),
          axios.get('/api/organization/skills', { headers: getAuthHeaders() }).catch(() => ({ data: { data: organizationData.value.skills } })),
          axios.get('/api/organization/statuses', { headers: getAuthHeaders() }).catch(() => ({ data: { data: organizationData.value.statuses } }))
        ])

        organizationData.value = {
          departments: departments.data?.data || organizationData.value.departments,
          positions: positions.data?.data || organizationData.value.positions,
          skills: skills.data?.data || organizationData.value.skills,
          statuses: statuses.data?.data || organizationData.value.statuses
        }
      } catch (err) {
        console.error('Failed to load organization data, using defaults:', err)
      }
    }

    const loadTeamMembers = async () => {
      loading.value = true
      error.value = ''
      try {
        const response = await axios.get('/api/admin/team', {
          headers: getAuthHeaders()
        })
        teamMembers.value = response.data?.data || []
      } catch (err) {
        console.error('Failed to load team members:', err)
        showError('Failed to load team members. Please try again.')
        teamMembers.value = []
      } finally {
        loading.value = false
      }
    }

    const showCreateModal = () => {
      resetForm()
      isEditing.value = false
      activeTab.value = 'basic'
      currentAvatarUrl.value = ''
      showModal.value = true
    }

    const viewMember = (member) => {
      selectedMember.value = member
      showDetailsModal.value = true
    }

    const editMember = (member) => {
      try {
        resetForm()

        // Copy member data safely
        form.value = {
          id: member.id,
          name: member.name || '',
          position: member.position || '',
          department: member.department || '',
          bio: member.bio || '',
          email: member.email || '',
          skills: Array.isArray(member.skills) ? [...member.skills] : [],
          social_links: member.social_links && typeof member.social_links === 'object' ? {...member.social_links} : {},
          status: member.status || 'active',
          joined_date: formatDateForInput(member.joined_date),
          priority: member.priority || 0,
          show_on_website: Boolean(member.show_on_website)
        }

        // Handle social links safely
        if (member.social_links && typeof member.social_links === 'object') {
          Object.keys(socialLinks.value).forEach(platform => {
            socialLinks.value[platform] = member.social_links[platform] || ''
          })
        }

        currentAvatarUrl.value = member.avatar || ''
        avatarPreview.value = null

        isEditing.value = true
        activeTab.value = 'basic'
        showModal.value = true
      } catch (err) {
        console.error('Error editing member:', err)
        showToast('Failed to load member data for editing', 'error')
      }
    }

    const duplicateMember = (member) => {
      try {
        resetForm()

        // Copy member data for duplication
        form.value = {
          name: `Copy of ${member.name || 'Unknown'}`,
          position: member.position || '',
          department: member.department || '',
          bio: member.bio || '',
          email: '', // Clear email for duplicate
          skills: Array.isArray(member.skills) ? [...member.skills] : [],
          social_links: {},
          status: member.status || 'active',
          joined_date: null,
          priority: member.priority || 0,
          show_on_website: false // Hide duplicate by default
        }

        // Clear social links for duplicate
        Object.keys(socialLinks.value).forEach(platform => {
          socialLinks.value[platform] = ''
        })

        isEditing.value = false
        activeTab.value = 'basic'
        showModal.value = true
      } catch (err) {
        console.error('Error duplicating member:', err)
        showToast('Failed to duplicate member', 'error')
      }
    }

    const formatDateForInput = (value) => {
      if (!value) return null
      try {
        if (typeof value === 'string') {
          if (/^\d{4}-\d{2}-\d{2}$/.test(value)) return value
          const d = new Date(value)
          if (!isNaN(d.getTime())) {
            return d.toISOString().split('T')[0]
          }
          return null
        }
        if (value instanceof Date) {
          return value.toISOString().split('T')[0]
        }
      } catch (_) { return null }
      return null
    }

    const formatDate = (dateString) => {
      if (!dateString) return ''
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }

    const handleAvatarChange = (event) => {
      const file = event.target.files[0]
      if (file) {
        avatarFile.value = file
        const reader = new FileReader()
        reader.onload = (e) => {
          avatarPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
      }
    }

    const removeAvatar = () => {
      avatarFile.value = null
      avatarPreview.value = null
      currentAvatarUrl.value = ''
    }

    const addSkill = () => {
      const skill = newSkill.value.trim()
      if (skill && !form.value.skills.includes(skill)) {
        form.value.skills.push(skill)
        newSkill.value = ''
      }
    }

    const removeSkill = (index) => {
      form.value.skills.splice(index, 1)
    }

    const addSuggestedSkill = (skill) => {
      if (!form.value.skills.includes(skill)) {
        form.value.skills.push(skill)
      }
    }

    const saveMember = async () => {
      try {
        modalLoading.value = true

        // Prepare form data
        const memberData = {
          ...form.value,
          social_links: socialLinks.value
        }

        let response
        if (isEditing.value) {
          response = await axios.put(`/api/admin/team/${form.value.id}`, memberData, {
            headers: getAuthHeaders()
          })
          showSuccess('Team member updated successfully!')
        } else {
          response = await axios.post('/api/admin/team', memberData, {
            headers: getAuthHeaders()
          })
          showSuccess('Team member created successfully!')
        }

        closeModal()
        await loadTeamMembers()
      } catch (err) {
        console.error('Failed to save team member:', err)
        showError(err.response?.data?.message || 'Failed to save team member. Please try again.')
      } finally {
        modalLoading.value = false
      }
    }

    const deleteMember = async (member) => {
      if (!confirm(`Are you sure you want to delete "${member.name}"? This action cannot be undone.`)) {
        return
      }

      try {
        await axios.delete(`/api/admin/team/${member.id}`, {
          headers: getAuthHeaders()
        })
        showSuccess(`Team member "${member.name}" deleted successfully`)
        await loadTeamMembers()
      } catch (err) {
        console.error('Failed to delete team member:', err)
        showError('Failed to delete team member. Please try again.')
      }
    }

    const toggleVisibility = async (member) => {
      try {
        const newVisibility = !member.show_on_website
        await axios.patch(`/api/admin/team/${member.id}/visibility`, {
          show_on_website: newVisibility
        }, {
          headers: getAuthHeaders()
        })

        member.show_on_website = newVisibility
        showSuccess(`Team member is now ${newVisibility ? 'visible' : 'hidden'} on the website`)
      } catch (err) {
        console.error('Failed to toggle visibility:', err)
        showError('Failed to update visibility. Please try again.')
      }
    }

    const exportMembers = () => {
      const csvContent = [
        'Name,Position,Department,Status,Email,Visible,Joined Date',
        ...filteredMembers.value.map(member =>
          `"${member.name}","${getPositionLabel(member.position)}","${getDepartmentLabel(member.department)}","${getStatusLabel(member.status)}","${member.email || ''}","${member.show_on_website ? 'Yes' : 'No'}","${member.joined_date || ''}"`
        )
      ].join('\n')

      const blob = new Blob([csvContent], { type: 'text/csv' })
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `team-members-${new Date().toISOString().split('T')[0]}.csv`
      a.click()
      window.URL.revokeObjectURL(url)
    }

    const closeModal = () => {
      showModal.value = false
      resetForm()
    }

    const closeDetailsModal = () => {
      showDetailsModal.value = false
      selectedMember.value = null
    }

    const resetForm = () => {
      Object.assign(form.value, {
        name: '',
        position: '',
        department: '',
        bio: '',
        email: '',
        skills: [],
        social_links: {},
        status: 'active',
        joined_date: null,
        priority: 0,
        show_on_website: true
      })
      newSkill.value = ''
      avatarFile.value = null
      avatarPreview.value = null
      currentAvatarUrl.value = ''
      Object.keys(socialLinks.value).forEach(key => {
        socialLinks.value[key] = ''
      })
    }

    // Utility methods for organization data labels
    const getPositionLabel = (position) => {
      return organizationData.value.positions[position] || position
    }

    const getDepartmentLabel = (department) => {
      return organizationData.value.departments[department] || department
    }

    const getStatusLabel = (status) => {
      return organizationData.value.statuses[status] || status
    }

    const getSkillLabel = (skill) => {
      return organizationData.value.skills[skill] || skill
    }

    const getStatusIcon = (status) => {
      const icons = {
        'active': 'fa-check-circle',
        'inactive': 'fa-pause-circle',
        'on-leave': 'fa-clock'
      }
      return icons[status] || 'fa-question-circle'
    }

    const getPriorityClass = (priority) => {
      const p = priority || 0
      if (p === 0) return 'normal'
      if (p <= 25) return 'low'
      if (p <= 50) return 'medium'
      if (p <= 75) return 'high'
      return 'urgent'
    }

    const getSortIcon = (key) => {
      if (sortKey.value !== key) return ''
      return sortOrder.value === 'asc' ? 'fa-sort-up' : 'fa-sort-down'
    }

    const sortBy = (key) => {
      if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
      } else {
        sortKey.value = key
        sortOrder.value = 'asc'
      }
    }

    const handleSearch = () => {
      currentPage.value = 1
    }

    const applyFilters = () => {
      currentPage.value = 1
    }

    const clearFilters = () => {
      searchQuery.value = ''
      statusFilter.value = ''
      departmentFilter.value = ''
      visibilityFilter.value = ''
      currentPage.value = 1
    }

    const hasSocialLinks = (member) => {
      return member.social_links && Object.values(member.social_links).some(link => link && link.trim() !== '')
    }

    const showToast = (message, type = 'success') => {
      toast.value = { show: true, message, type }
      setTimeout(() => {
        toast.value.show = false
      }, 4000)
    }

    const hideToast = () => {
      toast.value.show = false
    }

    const getToastIcon = (type) => {
      const icons = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
      }
      return icons[type] || 'fa-info-circle'
    }

    // Lifecycle
    onMounted(() => {
      loadTeamMembers()
      loadOrganizationData()

      // Handle escape key for modals
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          if (showModal.value) closeModal()
          if (showDetailsModal.value) closeDetailsModal()
        }
      })
    })

    return {
      teamMembers,
      showModal,
      showDetailsModal,
      selectedMember,
      isEditing,
      loading,
      modalLoading,
      error,
      form,
      newSkill,
      socialLinks,
      avatarPreview,
      currentAvatarUrl,
      activeTab,
      searchQuery,
      statusFilter,
      departmentFilter,
      visibilityFilter,
      currentPage,
      itemsPerPage,
      sortKey,
      sortOrder,
      toast,
      formTabs,
      organizationData,
      suggestedSkills,
      visibleMembers,
      activeMembers,
      uniqueDepartments,
      departmentsList,
      filteredMembers,
      totalPages,
      paginatedMembers,
      startItem,
      endItem,
      visiblePages,
      showCreateModal,
      viewMember,
      editMember,
      duplicateMember,
      saveMember,
      deleteMember,
      toggleVisibility,
      exportMembers,
      closeModal,
      closeDetailsModal,
      handleAvatarChange,
      removeAvatar,
      addSkill,
      removeSkill,
      addSuggestedSkill,
      formatDate,
      getPositionLabel,
      getDepartmentLabel,
      getStatusLabel,
      getSkillLabel,
      getStatusIcon,
      getPriorityClass,
      getSortIcon,
      sortBy,
      handleSearch,
      applyFilters,
      clearFilters,
      hasSocialLinks,
      showToast,
      hideToast,
      getToastIcon
    }
  }
}
</script>

<style scoped>
.admin-team {
  padding: 24px;
  background: #f8fafc;
  min-height: 100vh;
}

.admin-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 32px;
  background: white;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.header-left h1 {
  font-size: 28px;
  font-weight: 700;
  color: #1a202c;
  margin: 0 0 8px 0;
}

.header-subtitle {
  font-size: 16px;
  color: #718096;
  margin: 0;
  line-height: 1.5;
}

.btn {
  border-radius: 8px;
  border: none;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 12px 20px;
  box-shadow: 0 4px 14px 0 rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px 0 rgba(102, 126, 234, 0.4);
}

.btn-secondary {
  background: #e2e8f0;
  color: #4a5568;
  padding: 12px 20px;
}

.btn-secondary:hover {
  background: #cbd5e0;
}

.btn-outline {
  background: transparent;
  color: #4a5568;
  border: 2px solid #e2e8f0;
  padding: 10px 18px;
}

.btn-outline:hover {
  background: #f7fafc;
  border-color: #cbd5e0;
}

.btn-sm {
  padding: 8px 16px;
  font-size: 13px;
}

.btn-icon {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  border: none;
  background: #f7fafc;
  color: #4a5568;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-icon:hover {
  background: #edf2f7;
  transform: translateY(-1px);
}

.btn-icon.info { color: #3182ce; }
.btn-icon.info:hover { background: #bee3f8; }
.btn-icon.success { color: #38a169; }
.btn-icon.success:hover { background: #c6f6d5; }
.btn-icon.warning { color: #d69e2e; }
.btn-icon.warning:hover { background: #faf089; }
.btn-icon.danger { color: #e53e3e; }
.btn-icon.danger:hover { background: #fed7d7; }

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 16px;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}

.stat-icon.primary { background: #e6fffa; color: #319795; }
.stat-icon.success { background: #f0fff4; color: #38a169; }
.stat-icon.warning { background: #fffbeb; color: #d69e2e; }
.stat-icon.info { background: #ebf8ff; color: #3182ce; }

.stat-content {
  flex: 1;
}

.stat-number {
  font-size: 28px;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 14px;
  color: #718096;
  font-weight: 500;
}

.filters-section {
  background: white;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
}

.search-box {
  position: relative;
  margin-bottom: 16px;
}

.search-box i {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #a0aec0;
  font-size: 16px;
}

.search-box input {
  width: 100%;
  padding: 12px 16px 12px 48px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 16px;
  background: #f7fafc;
  transition: all 0.2s ease;
}

.search-box input:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filter-controls {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  align-items: center;
}

.filter-controls select {
  padding: 10px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  font-size: 14px;
  color: #4a5568;
  min-width: 150px;
}

.filter-controls select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.loading-state, .error-state, .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 64px 24px;
  background: white;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.loading-spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #e2e8f0;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

.error-icon, .empty-icon {
  font-size: 64px;
  margin-bottom: 16px;
  opacity: 0.5;
}

.error-state .error-icon { color: #e53e3e; }
.empty-state .empty-icon { color: #a0aec0; }

.table-container {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e2e8f0;
  background: #f7fafc;
}

.table-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1a202c;
  margin: 0;
}

.table-wrapper {
  overflow-x: auto;
}

.admin-table {
  width: 100%;
  border-collapse: collapse;
}

.admin-table th {
  background: #f7fafc;
  padding: 16px 20px;
  text-align: left;
  font-weight: 600;
  color: #4a5568;
  font-size: 14px;
  border-bottom: 1px solid #e2e8f0;
  white-space: nowrap;
}

.admin-table th.sortable {
  cursor: pointer;
  user-select: none;
  transition: all 0.2s ease;
}

.admin-table th.sortable:hover {
  background: #edf2f7;
  color: #2d3748;
}

.admin-table th i {
  margin-left: 8px;
  opacity: 0.5;
  transition: opacity 0.2s ease;
}

.admin-table th.sortable:hover i {
  opacity: 1;
}

.admin-table td {
  padding: 16px 20px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.member-row {
  transition: background-color 0.2s ease;
}

.member-row:hover {
  background: #f8fafc;
}

.avatar-container {
  position: relative;
  display: inline-block;
}

.avatar-small {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  object-fit: cover;
  border: 2px solid #e2e8f0;
  transition: transform 0.2s ease, border-color 0.2s ease;
}

.avatar-small:hover {
  transform: scale(1.05);
  border-color: #667eea;
}

.status-indicator {
  position: absolute;
  bottom: -2px;
  right: -2px;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  border: 2px solid white;
}

.status-indicator.active { background: #38a169; }
.status-indicator.warning { background: #d69e2e; }
.status-indicator.inactive { background: #e53e3e; }

.name-content {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.name-content strong {
  font-size: 16px;
  font-weight: 600;
  color: #1a202c;
}

.name-content small {
  font-size: 12px;
  color: #718096;
}

.join-date {
  font-size: 11px;
  color: #a0aec0;
  display: flex;
  align-items: center;
  gap: 4px;
}

.position-title {
  font-weight: 500;
  color: #2d3748;
}

.department-badge {
  display: inline-block;
  padding: 6px 12px;
  background: #ebf8ff;
  color: #3182ce;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.status-badge.active {
  background: #f0fff4;
  color: #38a169;
}

.status-badge.inactive {
  background: #fed7d7;
  color: #e53e3e;
}

.status-badge.on-leave {
  background: #fffbeb;
  color: #d69e2e;
}

.skills-preview {
  display: flex;
  gap: 4px;
  flex-wrap: wrap;
}

.skill-tag {
  display: inline-block;
  padding: 4px 8px;
  background: #edf2f7;
  color: #4a5568;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 500;
}

.skills-more {
  display: inline-block;
  padding: 4px 8px;
  background: #e2e8f0;
  color: #718096;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 500;
}

.no-skills {
  font-size: 12px;
  color: #a0aec0;
  font-style: italic;
}

.visibility-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
}

.visibility-badge.visible {
  background: #f0fff4;
  color: #38a169;
}

.visibility-badge.hidden {
  background: #fed7d7;
  color: #e53e3e;
}

.priority-indicator {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  color: white;
  margin-left: 8px;
}

.priority-indicator.normal { background: #a0aec0; }
.priority-indicator.low { background: #68d391; }
.priority-indicator.medium { background: #f6e05e; }
.priority-indicator.high { background: #fbd38d; }
.priority-indicator.urgent { background: #fc8181; }

.action-buttons {
  display: flex;
  gap: 6px;
  justify-content: flex-end;
}

.pagination-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-top: 1px solid #e2e8f0;
  background: #f7fafc;
}

.pagination-info {
  font-size: 14px;
  color: #718096;
}

.pagination {
  display: flex;
  align-items: center;
  gap: 8px;
}

.page-numbers {
  display: flex;
  gap: 4px;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.modal-content {
  background: white;
  border-radius: 16px;
  width: 90%;
  max-width: 900px;
  max-height: 90vh;
  overflow: hidden;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
  position: relative;
}

.modal-content.large {
  max-width: 1000px;
}

.modal-content.medium {
  max-width: 600px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 24px;
  border-bottom: 1px solid #e2e8f0;
  background: #f7fafc;
}

.modal-title h2 {
  font-size: 24px;
  font-weight: 700;
  color: #1a202c;
  margin: 0 0 8px 0;
}

.modal-subtitle {
  font-size: 16px;
  color: #718096;
  margin: 0;
}

.btn-close {
  background: #edf2f7;
  border: none;
  border-radius: 8px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #4a5568;
}

.btn-close:hover {
  background: #e2e8f0;
  color: #2d3748;
}

.modal-body {
  padding: 24px;
  max-height: 60vh;
  overflow-y: auto;
}

.modal-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 64px 24px;
  text-align: center;
}

.modal-loading .loading-spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #e2e8f0;
  border-top: 3px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

.modal-loading p {
  color: #718096;
  font-size: 14px;
  margin: 0;
}

.form-tabs {
  display: flex;
  border-bottom: 2px solid #e2e8f0;
  margin-bottom: 24px;
  background: #f7fafc;
  border-radius: 8px 8px 0 0;
  overflow: hidden;
}

.tab-button {
  flex: 1;
  padding: 16px 20px;
  text-align: center;
  cursor: pointer;
  font-weight: 600;
  color: #718096;
  background: transparent;
  border: none;
  transition: all 0.2s ease;
  position: relative;
}

.tab-button:hover {
  background: #edf2f7;
  color: #4a5568;
}

.tab-button.active {
  background: white;
  color: #667eea;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.tab-button.active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: #667eea;
}

.tab-button i {
  margin-right: 8px;
  font-size: 16px;
}

.tab-content {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 8px;
  font-weight: 600;
  color: #2d3748;
  font-size: 14px;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 12px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.2s ease;
  background: #f7fafc;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 80px;
}

.form-hint {
  margin-top: 6px;
  font-size: 12px;
  color: #718096;
  line-height: 1.4;
}

.checkbox-group {
  flex-direction: row;
  align-items: center;
  gap: 12px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  font-weight: 500;
  color: #4a5568;
}

.checkbox-label input[type="checkbox"] {
  width: 18px;
  height: 18px;
  accent-color: #667eea;
}

.avatar-upload {
  display: flex;
  gap: 16px;
  align-items: flex-start;
}

.avatar-current {
  width: 120px;
  height: 120px;
  border-radius: 12px;
  overflow: hidden;
  background: #f7fafc;
  border: 2px solid #e2e8f0;
  flex-shrink: 0;
}

.avatar-current img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-controls {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.skills-input {
  display: flex;
  gap: 12px;
  align-items: flex-end;
}

.skills-input input {
  flex: 1;
}

.skills-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 12px;
}

.skill-tag {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  background: #667eea;
  color: white;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 500;
}

.remove-skill {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  padding: 0;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.remove-skill:hover {
  background: rgba(255, 255, 255, 0.2);
}

.suggested-skills {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 12px;
}

.skill-suggestion {
  padding: 8px 12px;
  background: #f7fafc;
  color: #4a5568;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.skill-suggestion:hover:not(:disabled) {
  background: #edf2f7;
  border-color: #cbd5e0;
}

.skill-suggestion:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background: #e2e8f0;
}

.social-inputs {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.social-input {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: #f7fafc;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.social-input:hover {
  background: #edf2f7;
  border-color: #cbd5e0;
}

.social-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  color: white;
  flex-shrink: 0;
}

.social-icon.linkedin { background: #0077b5; }
.social-icon.twitter { background: #1da1f2; }
.social-icon.github { background: #333; }
.social-icon.dribbble { background: #ea4c89; }
.social-icon.website { background: #667eea; }

.social-input input {
  flex: 1;
  border: none;
  background: transparent;
  outline: none;
  font-size: 14px;
}

.social-input label {
  font-weight: 600;
  color: #4a5568;
  min-width: 120px;
  margin: 0;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 24px;
  border-top: 1px solid #e2e8f0;
  background: #f7fafc;
}

.member-details {
  padding: 24px;
}

.member-profile {
  display: flex;
  gap: 20px;
  margin-bottom: 32px;
  align-items: flex-start;
}

.member-avatar {
  position: relative;
  width: 120px;
  height: 120px;
  border-radius: 16px;
  overflow: hidden;
  border: 3px solid #e2e8f0;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
  flex-shrink: 0;
}

.member-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.member-info {
  flex: 1;
}

.member-info h3 {
  font-size: 24px;
  font-weight: 700;
  color: #1a202c;
  margin: 0 0 8px 0;
}

.member-info .position {
  font-size: 18px;
  font-weight: 600;
  color: #667eea;
  margin: 0 0 4px 0;
}

.member-info .department {
  font-size: 16px;
  color: #718096;
  margin: 0 0 16px 0;
}

.member-meta {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #718096;
}

.member-bio {
  margin-bottom: 32px;
}

.member-bio h4 {
  font-size: 18px;
  font-weight: 600;
  color: #1a202c;
  margin: 0 0 12px 0;
}

.member-bio p {
  color: #4a5568;
  line-height: 1.6;
  margin: 0;
}

.member-skills {
  margin-bottom: 32px;
}

.member-skills h4 {
  font-size: 18px;
  font-weight: 600;
  color: #1a202c;
  margin: 0 0 12px 0;
}

.member-social h4 {
  font-size: 18px;
  font-weight: 600;
  color: #1a202c;
  margin: 0 0 12px 0;
}

.social-links {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.social-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  background: #f7fafc;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  text-decoration: none;
  color: #4a5568;
  transition: all 0.2s ease;
}

.social-link:hover {
  background: #edf2f7;
  border-color: #cbd5e0;
  transform: translateY(-1px);
}

.social-link.linkedin { border-color: #0077b5; }
.social-link.linkedin:hover { background: #e6f7ff; color: #0077b5; }
.social-link.twitter { border-color: #1da1f2; }
.social-link.twitter:hover { background: #e6f7ff; color: #1da1f2; }
.social-link.github { border-color: #333; }
.social-link.github:hover { background: #f5f5f5; color: #333; }
.social-link.dribbble { border-color: #ea4c89; }
.social-link.dribbble:hover { background: #fef0f5; color: #ea4c89; }
.social-link.website { border-color: #667eea; }
.social-link.website:hover { background: #f0f9ff; color: #667eea; }

.toast {
  position: fixed;
  top: 24px;
  right: 24px;
  background: white;
  border-radius: 12px;
  padding: 16px 20px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  z-index: 1001;
  display: flex;
  align-items: center;
  gap: 12px;
  max-width: 400px;
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

.toast.success { border-left: 4px solid #38a169; }
.toast.error { border-left: 4px solid #e53e3e; }
.toast.warning { border-left: 4px solid #d69e2e; }
.toast.info { border-left: 4px solid #3182ce; }

.toast-content {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  font-weight: 500;
  color: #2d3748;
}

.toast-content i {
  font-size: 16px;
}

.toast.success .toast-content i { color: #38a169; }
.toast.error .toast-content i { color: #e53e3e; }
.toast.warning .toast-content i { color: #d69e2e; }
.toast.info .toast-content i { color: #3182ce; }

.toast-close {
  background: none;
  border: none;
  cursor: pointer;
  color: #a0aec0;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.toast-close:hover {
  background: #f7fafc;
  color: #4a5568;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
  .admin-team { padding: 16px; }

  .admin-header {
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .filter-controls {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-controls select {
    min-width: auto;
  }

  .table-wrapper {
    overflow-x: scroll;
  }

  .action-buttons {
    flex-direction: column;
    gap: 4px;
  }

  .pagination-container {
    flex-direction: column;
    gap: 16px;
    text-align: center;
  }

  .modal-content {
    width: 95%;
    margin: 20px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .modal-actions {
    flex-direction: column-reverse;
  }

  .toast {
    right: 16px;
    left: 16px;
    max-width: none;
  }

  .avatar-upload {
    flex-direction: column;
  }

  .member-profile {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
}

@media (max-width: 480px) {
  .form-tabs {
    flex-direction: column;
  }

  .tab-button {
    text-align: left;
    padding: 12px 16px;
  }

  .skills-input {
    flex-direction: column;
    align-items: stretch;
  }

  .social-input {
    flex-direction: column;
    align-items: stretch;
    gap: 8px;
  }

  .social-input label {
    min-width: auto;
    text-align: center;
  }
}
</style>
