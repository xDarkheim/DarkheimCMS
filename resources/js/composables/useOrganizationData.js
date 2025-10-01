import { ref } from 'vue'
import axios from 'axios'

// Shared composable for organization data
export function useOrganizationData() {
  const organizationData = ref({
    departments: [],
    employment_types: [],
    experience_levels: [],
    locations: [],
    skills: [],
    positions: [],
    statuses: []
  })

  const loading = ref(false)
  const error = ref(null)

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

  const getDefaultEmploymentTypes = () => [
    { key: 'full-time', label: 'Full Time' },
    { key: 'part-time', label: 'Part Time' },
    { key: 'contract', label: 'Contract' },
    { key: 'internship', label: 'Internship' }
  ]

  const getDefaultExperienceLevels = () => [
    { key: 'entry', label: 'Entry Level' },
    { key: 'junior', label: 'Junior' },
    { key: 'mid', label: 'Mid Level' },
    { key: 'senior', label: 'Senior' },
    { key: 'lead', label: 'Lead' },
    { key: 'principal', label: 'Principal' }
  ]

  const getDefaultLocations = () => [
    { key: 'remote', label: 'Remote' },
    { key: 'new-york', label: 'New York, NY' },
    { key: 'san-francisco', label: 'San Francisco, CA' },
    { key: 'london', label: 'London, UK' },
    { key: 'berlin', label: 'Berlin, Germany' }
  ]

  const getDefaultSkills = () => [
    { key: 'javascript', label: 'JavaScript' },
    { key: 'php', label: 'PHP' },
    { key: 'laravel', label: 'Laravel' },
    { key: 'vuejs', label: 'Vue.js' },
    { key: 'react', label: 'React' },
    { key: 'nodejs', label: 'Node.js' },
    { key: 'python', label: 'Python' },
    { key: 'mysql', label: 'MySQL' },
    { key: 'docker', label: 'Docker' },
    { key: 'aws', label: 'AWS' }
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

  const getDefaultStatuses = () => [
    { key: 'active', label: 'Active' },
    { key: 'inactive', label: 'Inactive' },
    { key: 'on-leave', label: 'On Leave' }
  ]

  const loadOrganizationData = async () => {
    try {
      loading.value = true
      error.value = null

      // Set defaults first for immediate UI response
      organizationData.value = {
        departments: getDefaultDepartments(),
        employment_types: getDefaultEmploymentTypes(),
        experience_levels: getDefaultExperienceLevels(),
        locations: getDefaultLocations(),
        skills: getDefaultSkills(),
        positions: getDefaultPositions(),
        statuses: getDefaultStatuses()
      }

      // Try to load from API with timeout
      const promises = [
        axios.get('/api/organization/departments', { timeout: 5000 }).catch(() => null),
        axios.get('/api/organization/employment-types', { timeout: 5000 }).catch(() => null),
        axios.get('/api/organization/experience-levels', { timeout: 5000 }).catch(() => null),
        axios.get('/api/organization/locations', { timeout: 5000 }).catch(() => null),
        axios.get('/api/organization/skills', { timeout: 5000 }).catch(() => null),
        axios.get('/api/organization/positions', { timeout: 5000 }).catch(() => null),
        axios.get('/api/organization/statuses', { timeout: 5000 }).catch(() => null)
      ]

      const results = await Promise.allSettled(promises)

      // Update with API data if available
      if (results[0].status === 'fulfilled' && results[0].value?.data?.data) {
        organizationData.value.departments = results[0].value.data.data
      }
      if (results[1].status === 'fulfilled' && results[1].value?.data?.data) {
        organizationData.value.employment_types = results[1].value.data.data
      }
      if (results[2].status === 'fulfilled' && results[2].value?.data?.data) {
        organizationData.value.experience_levels = results[2].value.data.data
      }
      if (results[3].status === 'fulfilled' && results[3].value?.data?.data) {
        organizationData.value.locations = results[3].value.data.data
      }
      if (results[4].status === 'fulfilled' && results[4].value?.data?.data) {
        organizationData.value.skills = results[4].value.data.data
      }
      if (results[5].status === 'fulfilled' && results[5].value?.data?.data) {
        organizationData.value.positions = results[5].value.data.data
      }
      if (results[6].status === 'fulfilled' && results[6].value?.data?.data) {
        organizationData.value.statuses = results[6].value.data.data
      }

    } catch (err) {
      console.warn('Failed to load organization data:', err)
      error.value = 'Failed to load organization data'
    } finally {
      loading.value = false
    }
  }

  // Helper methods for formatting
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

  const formatDepartment = (dept) => {
    const orgDept = organizationData.value.departments.find(d => d.key === dept || d.label === dept)
    return orgDept ? orgDept.label : dept
  }

  const formatPosition = (pos) => {
    const orgPos = organizationData.value.positions.find(p => p.key === pos || p.label === pos)
    return orgPos ? orgPos.label : pos
  }

  const formatSkill = (skill) => {
    const orgSkill = organizationData.value.skills.find(s => s.key === skill || s.label === skill)
    return orgSkill ? orgSkill.label : skill
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

  return {
    organizationData,
    loading,
    error,
    loadOrganizationData,
    formatEmploymentType,
    formatExperienceLevel,
    formatDepartment,
    formatPosition,
    formatSkill,
    formatStatus,
    // Expose default getters for direct usage
    getDefaultDepartments,
    getDefaultEmploymentTypes,
    getDefaultExperienceLevels,
    getDefaultLocations,
    getDefaultSkills,
    getDefaultPositions,
    getDefaultStatuses
  }
}
