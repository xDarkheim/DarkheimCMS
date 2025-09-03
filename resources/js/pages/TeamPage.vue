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

    <!-- Team Members Section -->
    <section class="team-members">
      <div class="container">
        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading team members...</p>
        </div>

        <!-- Team Grid -->
        <div v-else-if="teamMembers.length > 0" class="team-grid">
          <div
            v-for="member in teamMembers"
            :key="member.id"
            class="team-card"
            @click="showMemberDetails(member)"
          >
            <div class="team-card__header">
              <div class="team-card__avatar">
                <img
                  :src="member.avatar"
                  :alt="member.name"
                  loading="lazy"
                >
              </div>

              <h3 class="team-card__name">{{ member.name }}</h3>
              <div class="team-card__position">{{ member.position }}</div>
              <div class="team-card__department">{{ member.department }}</div>
            </div>

            <div class="team-card__content">
              <div class="team-card__bio">
                {{ member.bio || 'Passionate team member dedicated to delivering excellent results.' }}
              </div>

              <div class="team-card__skills">
                <span
                  v-for="skill in member.skills?.slice(0, 3) || []"
                  :key="skill"
                  class="skill-tag"
                >
                  {{ skill }}
                </span>
                <span v-if="member.skills && member.skills.length > 3" class="skill-more">
                  +{{ member.skills.length - 3 }}
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
            <div v-if="member.social_links" class="team-card__social">
              <a
                v-for="(link, platform) in member.social_links"
                :key="platform"
                :href="link"
                target="_blank"
                rel="noopener noreferrer"
                class="social-link"
                @click.stop
              >
                <i :class="getSocialIcon(platform)"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="team-empty">
          <div class="team-empty__icon">
            <i class="fas fa-users"></i>
          </div>
          <h3 class="team-empty__title">No Team Members Found</h3>
          <p class="team-empty__text">Team information will be available soon.</p>
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
              :src="selectedMember.avatar"
              :alt="selectedMember.name"
            >
          </div>
          <div class="member-modal__info">
            <h2 class="member-modal__name">{{ selectedMember.name }}</h2>
            <div class="member-modal__position">{{ selectedMember.position }}</div>
            <div class="member-modal__department">{{ selectedMember.department }}</div>
            <div v-if="selectedMember.formatted_joined_date" class="member-modal__joined">
              Joined {{ selectedMember.formatted_joined_date }}
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
            <h4>Contact</h4>
            <div class="member-modal__contact">
              <div v-if="selectedMember.email" class="contact-item">
                <i class="fas fa-envelope"></i>
                <a :href="`mailto:${selectedMember.email}`">{{ selectedMember.email }}</a>
              </div>
              <div v-if="selectedMember.phone" class="contact-item">
                <i class="fas fa-phone"></i>
                <a :href="`tel:${selectedMember.phone}`">{{ selectedMember.phone }}</a>
              </div>
            </div>

            <!-- Social Links in Modal -->
            <div v-if="selectedMember.social_links" class="member-modal__social">
              <a
                v-for="(link, platform) in selectedMember.social_links"
                :key="platform"
                :href="link"
                target="_blank"
                rel="noopener noreferrer"
                class="social-link-large"
              >
                <i :class="getSocialIcon(platform)"></i>
                {{ platform.charAt(0).toUpperCase() + platform.slice(1) }}
              </a>
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
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'TeamPage',
  setup() {
    const teamMembers = ref([])
    const selectedMember = ref(null)
    const loading = ref(false)

    const loadTeamMembers = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/team')
        teamMembers.value = response.data.data || []
      } catch (error) {
        console.error('Failed to load team members:', error)
        teamMembers.value = []
      } finally {
        loading.value = false
      }
    }

    const showMemberDetails = (member) => {
      selectedMember.value = member
      document.body.style.overflow = 'hidden'
    }

    const closeModal = () => {
      selectedMember.value = null
      document.body.style.overflow = 'auto'
    }

    const getSocialIcon = (platform) => {
      const icons = {
        linkedin: 'fab fa-linkedin',
        twitter: 'fab fa-twitter',
        github: 'fab fa-github',
        instagram: 'fab fa-instagram',
        facebook: 'fab fa-facebook',
        dribbble: 'fab fa-dribbble',
        behance: 'fab fa-behance'
      }
      return icons[platform.toLowerCase()] || 'fas fa-link'
    }

    onMounted(() => {
      loadTeamMembers()

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
      showMemberDetails,
      closeModal,
      getSocialIcon
    }
  }
}
</script>

<style lang="scss" scoped>
@use '../../css/pages/team-page';
</style>
