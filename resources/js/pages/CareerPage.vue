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

    <!-- Stats Section -->
    <section class="career-stats">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-item">
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ teamSize }}+</div>
              <div class="stat-label">Team Members</div>
            </div>
          </div>
          <div class="stat-item">
            <div class="stat-icon">
              <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ activePositions }}</div>
              <div class="stat-label">Open Positions</div>
            </div>
          </div>
          <div class="stat-item">
            <div class="stat-icon">
              <i class="fas fa-globe"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">100%</div>
              <div class="stat-label">Remote Friendly</div>
            </div>
          </div>
          <div class="stat-item">
            <div class="stat-icon">
              <i class="fas fa-heart"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">4.9/5</div>
              <div class="stat-label">Employee Rating</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Job Positions Section -->
    <section class="career-positions">
      <div class="container">
        <div class="section-header">
          <h2>Open Positions</h2>
          <p>Explore our current job openings and find your perfect match</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading job positions...</p>
        </div>

        <!-- Job Listings -->
        <div v-else-if="careers.length > 0" class="jobs-grid">
          <div
            v-for="job in careers"
            :key="job.id"
            class="job-card"
            @click="showJobDetails(job)"
          >
            <div class="job-header">
              <h3 class="job-title">{{ job.title }}</h3>
              <div class="job-department">{{ job.department }}</div>
            </div>

            <div class="job-meta">
              <div class="job-meta-item">
                <i class="fas fa-briefcase"></i>
                <span>{{ job.formatted_employment_type }}</span>
              </div>
              <div class="job-meta-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ job.location }}</span>
                <span v-if="job.remote_available" class="remote-badge">Remote OK</span>
              </div>
              <div class="job-meta-item">
                <i class="fas fa-chart-line"></i>
                <span>{{ job.formatted_experience_level }}</span>
              </div>
            </div>

            <p class="job-description">{{ job.short_description }}</p>

            <div v-if="job.skills && job.skills.length" class="job-skills">
              <span
                v-for="skill in job.skills.slice(0, 4)"
                :key="skill"
                class="skill-tag"
              >
                {{ skill }}
              </span>
              <span v-if="job.skills.length > 4" class="skill-more">
                +{{ job.skills.length - 4 }}
              </span>
            </div>

            <div class="job-footer">
              <div class="job-salary">{{ job.formatted_salary }}</div>
              <button class="btn btn--primary btn--sm">
                View Details
                <i class="fas fa-arrow-right"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="empty-state">
          <div class="empty-icon">
            <i class="fas fa-briefcase"></i>
          </div>
          <h3>No Open Positions</h3>
          <p>We don't have any open positions at the moment, but feel free to send us your resume for future opportunities.</p>
          <router-link to="/contact" class="btn btn--primary">
            Get In Touch
            <i class="fas fa-envelope"></i>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Job Details Modal -->
    <div v-if="selectedJob" class="job-modal" @click="closeModal">
      <div class="job-modal__content" @click.stop>
        <button class="job-modal__close" @click="closeModal">
          <i class="fas fa-times"></i>
        </button>

        <div class="job-modal__header">
          <h2 class="job-modal__title">{{ selectedJob.title }}</h2>
          <div class="job-modal__meta">
            <span class="job-modal__department">{{ selectedJob.department }}</span>
            <span class="job-modal__location">{{ selectedJob.location }}</span>
            <span v-if="selectedJob.remote_available" class="job-modal__remote">Remote OK</span>
          </div>
        </div>

        <div class="job-modal__body">
          <div class="job-modal__section">
            <h4>Job Description</h4>
            <div class="job-modal__content-text" v-html="selectedJob.description"></div>
          </div>

          <div class="job-modal__section">
            <h4>Requirements</h4>
            <div class="job-modal__content-text" v-html="selectedJob.requirements"></div>
          </div>

          <div v-if="selectedJob.benefits" class="job-modal__section">
            <h4>Benefits</h4>
            <div class="job-modal__content-text" v-html="selectedJob.benefits"></div>
          </div>

          <div v-if="selectedJob.skills && selectedJob.skills.length" class="job-modal__section">
            <h4>Required Skills</h4>
            <div class="job-modal__skills">
              <span
                v-for="skill in selectedJob.skills"
                :key="skill"
                class="job-modal__skill-tag"
              >
                {{ skill }}
              </span>
            </div>
          </div>

          <div class="job-modal__actions">
            <router-link
              :to="{ path: '/contact', query: { subject: `Application for ${selectedJob.title}` } }"
              class="btn btn--primary btn--lg"
            >
              Apply Now
              <i class="fas fa-paper-plane"></i>
            </router-link>
            <button class="btn btn--ghost btn--lg" @click="closeModal">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <section class="career-cta">
      <div class="container">
        <div class="career-cta__content">
          <h2 class="career-cta__title">Don't See a Perfect Match?</h2>
          <p class="career-cta__text">
            We're always looking for talented people. Send us your resume and we'll keep you in mind for future opportunities.
          </p>
          <router-link to="/contact" class="btn btn--primary btn--xl">
            Send Your Resume
            <i class="fas fa-upload"></i>
          </router-link>
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
    const careers = ref([])
    const selectedJob = ref(null)
    const loading = ref(false)

    const loadCareers = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/careers')
        careers.value = response.data.data || []
      } catch (error) {
        console.error('Failed to load careers:', error)
        careers.value = []
      } finally {
        loading.value = false
      }
    }

    const showJobDetails = (job) => {
      selectedJob.value = job
      document.body.style.overflow = 'hidden'
    }

    const closeModal = () => {
      selectedJob.value = null
      document.body.style.overflow = 'auto'
    }

    const activePositions = computed(() => careers.value.length)
    const teamSize = computed(() => 15) // This could come from API

    onMounted(() => {
      loadCareers()

      // Handle escape key for modal
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && selectedJob.value) {
          closeModal()
        }
      })
    })

    return {
      careers,
      selectedJob,
      loading,
      activePositions,
      teamSize,
      showJobDetails,
      closeModal
    }
  }
}
</script>

<style lang="scss" scoped>
@use '../../css/pages/career-page';
</style>
