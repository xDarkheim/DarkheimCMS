<template>
  <div class="contact-page page-with-header-offset">
    <!-- Contact Form & Info Section -->
    <section class="section">
      <div class="container">
        <div class="section__header animate-fade-in">
          <h1 class="section__title">Get In Touch</h1>
          <p class="section__subtitle">
            Ready to start your project or join our team? We'd love to hear from you.
            Let's discuss your ideas and bring them to life.
          </p>
        </div>

        <div class="grid grid--2 gap-12 items-start">
          <!-- Contact Form -->
          <div class="contact-form-container animate-fade-in">
            <div class="form-type-selector">
              <label class="form-type-option" :class="{ active: form.message_type === 'general' }">
                <input type="radio" v-model="form.message_type" value="general" @change="resetConditionalFields">
                <span class="option-content">
                  <i class="fas fa-envelope"></i>
                  <strong>General Inquiry</strong>
                  <small>Project discussion, questions, or partnerships</small>
                </span>
              </label>

              <label class="form-type-option" :class="{ active: form.message_type === 'job_application' }">
                <input type="radio" v-model="form.message_type" value="job_application" @change="resetConditionalFields">
                <span class="option-content">
                  <i class="fas fa-briefcase"></i>
                  <strong>Job Application</strong>
                  <small>Apply for a position with your resume</small>
                </span>
              </label>
            </div>

            <h2 class="contact-form__title">
              {{ form.message_type === 'job_application' ? 'Submit Your Application' : 'Send us a message' }}
            </h2>

            <form @submit.prevent="submitForm" class="contact-form" enctype="multipart/form-data">
              <!-- Basic Contact Information -->
              <div class="form__group">
                <label for="name" class="form__label">Full Name *</label>
                <input
                  type="text"
                  id="name"
                  v-model="form.name"
                  class="form__input"
                  :class="{ 'form__input--error': errors.name }"
                  placeholder="Your full name"
                  required
                >
                <span v-if="errors.name" class="form__error">{{ errors.name }}</span>
              </div>

              <div class="form__group">
                <label for="email" class="form__label">Email Address *</label>
                <input
                  type="email"
                  id="email"
                  v-model="form.email"
                  class="form__input"
                  :class="{ 'form__input--error': errors.email }"
                  placeholder="your.email@example.com"
                  required
                >
                <span v-if="errors.email" class="form__error">{{ errors.email }}</span>
              </div>

              <div class="form__group">
                <label for="phone" class="form__label">Phone Number</label>
                <input
                  type="tel"
                  id="phone"
                  v-model="form.phone"
                  class="form__input"
                  placeholder="+1 (555) 123-4567"
                >
              </div>

              <div class="form__group">
                <label for="company" class="form__label">Company</label>
                <input
                  type="text"
                  id="company"
                  v-model="form.company"
                  class="form__input"
                  placeholder="Your company name"
                >
              </div>

              <!-- Job Application Specific Fields -->
              <template v-if="form.message_type === 'job_application'">
                <div class="form__group">
                  <label for="position_interest" class="form__label">Position of Interest</label>
                  <input
                    type="text"
                    id="position_interest"
                    v-model="form.position_interest"
                    class="form__input"
                    placeholder="e.g. Frontend Developer, Backend Developer"
                  >
                </div>

                <div class="form__group">
                  <label for="resume_file" class="form__label">Resume/CV *</label>
                  <div class="file-upload-area" :class="{ 'has-file': form.resume_file }">
                    <input
                      type="file"
                      id="resume_file"
                      @change="handleFileUpload"
                      class="file-input"
                      accept=".pdf,.doc,.docx"
                      required
                    >
                    <div class="file-upload-content">
                      <i class="fas fa-cloud-upload-alt"></i>
                      <span v-if="!form.resume_file">
                        Drop your resume here or click to browse
                      </span>
                      <span v-else class="file-selected">
                        <i class="fas fa-file-pdf"></i>
                        {{ form.resume_file.name }}
                      </span>
                      <small>PDF, DOC, DOCX up to 5MB</small>
                    </div>
                  </div>
                  <span v-if="errors.resume_file" class="form__error">{{ errors.resume_file }}</span>
                </div>

                <div class="form__group">
                  <label for="portfolio_url" class="form__label">Portfolio URL</label>
                  <input
                    type="url"
                    id="portfolio_url"
                    v-model="form.portfolio_url"
                    class="form__input"
                    placeholder="https://your-portfolio.com"
                  >
                </div>

                <div class="form__group">
                  <label for="experience_summary" class="form__label">Experience Summary</label>
                  <textarea
                    id="experience_summary"
                    v-model="form.experience_summary"
                    class="form__textarea"
                    placeholder="Brief summary of your relevant experience and skills..."
                    rows="3"
                  ></textarea>
                </div>

                <div class="form__group-row">
                  <div class="form__group">
                    <label for="availability" class="form__label">Availability</label>
                    <select id="availability" v-model="form.availability" class="form__select">
                      <option value="">Select availability</option>
                      <option value="immediate">Immediate</option>
                      <option value="2weeks">2 weeks</option>
                      <option value="1month">1 month</option>
                      <option value="2months">2 months</option>
                      <option value="negotiable">Negotiable</option>
                    </select>
                  </div>

                  <div class="form__group">
                    <label for="salary_expectation" class="form__label">Salary Expectation</label>
                    <input
                      type="number"
                      id="salary_expectation"
                      v-model="form.salary_expectation"
                      class="form__input"
                      placeholder="50000"
                      min="0"
                      step="1000"
                    >
                  </div>
                </div>
              </template>

              <!-- General Inquiry Fields -->
              <template v-else>
                <div class="form__group">
                  <label for="service" class="form__label">Service of Interest</label>
                  <select id="service" v-model="form.service" class="form__select">
                    <option value="">Select a service</option>
                    <option value="web-development">Web Development</option>
                    <option value="mobile-development">Mobile Development</option>
                    <option value="ui-ux-design">UI/UX Design</option>
                    <option value="ecommerce">E-commerce Solutions</option>
                    <option value="consultation">Consultation</option>
                    <option value="other">Other</option>
                  </select>
                </div>

                <div class="form__group">
                  <label for="budget" class="form__label">Project Budget</label>
                  <select id="budget" v-model="form.budget" class="form__select">
                    <option value="">Select budget range</option>
                    <option value="under-5k">Under $5,000</option>
                    <option value="5k-10k">$5,000 - $10,000</option>
                    <option value="10k-25k">$10,000 - $25,000</option>
                    <option value="25k-50k">$25,000 - $50,000</option>
                    <option value="over-50k">Over $50,000</option>
                  </select>
                </div>
              </template>

              <!-- Message Field -->
              <div class="form__group">
                <label for="message" class="form__label">
                  {{ form.message_type === 'job_application' ? 'Cover Letter *' : 'Message *' }}
                </label>
                <textarea
                  id="message"
                  v-model="form.message"
                  class="form__textarea"
                  :class="{ 'form__input--error': errors.message }"
                  :placeholder="form.message_type === 'job_application'
                    ? 'Tell us why you\'re interested in working with us and what you can bring to our team...'
                    : 'Tell us about your project, goals, and timeline...'"
                  rows="5"
                  required
                ></textarea>
                <span v-if="errors.message" class="form__error">{{ errors.message }}</span>
              </div>

              <button
                type="submit"
                class="btn btn--primary btn--xl"
                :class="{ 'btn--loading': isSubmitting }"
                :disabled="isSubmitting"
              >
                <span>{{ form.message_type === 'job_application' ? 'Submit Application' : 'Send Message' }}</span>
                <i class="fas fa-arrow-right btn__icon btn__icon--right"></i>
              </button>

              <div v-if="submitError" class="form__error form__error--submit">
                {{ submitError }}
              </div>
            </form>
          </div>

          <!-- Contact Information -->
          <div class="contact-info animate-slide-up">
            <h2 class="contact-info__title">Let's start a conversation</h2>
            <p class="contact-info__subtitle">
              We're here to help you bring your ideas to life. Get in touch and let's discuss your project.
            </p>

            <div class="contact-methods">
              <div
                v-for="contact in contactInfo"
                :key="contact.key"
                class="contact-method"
              >
                <div class="contact-method__icon">
                  <i :class="contact.icon"></i>
                </div>
                <div class="contact-method__content">
                  <h3 class="contact-method__title">{{ contact.label }}</h3>
                  <p class="contact-method__text">
                    <a
                      v-if="contact.type === 'email'"
                      :href="`mailto:${contact.value}`"
                    >
                      {{ contact.value }}
                    </a>
                    <a
                      v-else-if="contact.type === 'phone'"
                      :href="`tel:${contact.value}`"
                    >
                      {{ contact.value }}
                    </a>
                    <span v-else>{{ contact.value }}</span>
                  </p>
                </div>
              </div>
            </div>

            <div class="response-time">
              <h3 class="response-time__title">Response Time</h3>
              <p class="response-time__text">
                {{ responseTimeText || 'We typically respond within 24 hours during business days. For urgent matters, please call us directly.' }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Success Message -->
    <div v-if="showSuccess" class="success-modal" @click="closeSuccess">
      <div class="success-content" @click.stop>
        <div class="success-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <h3>Message Sent Successfully!</h3>
        <p>Thank you for reaching out. We'll get back to you within 24 hours.</p>
        <button @click="closeSuccess" class="btn btn--primary btn--base">
          Close
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { reactive, ref, onMounted } from 'vue'
import contactService from '../services/contactService.js'

export default {
  name: 'ContactPage',
  setup() {
    const isSubmitting = ref(false)
    const showSuccess = ref(false)
    const submitError = ref('')

    const form = reactive({
      name: '',
      email: '',
      phone: '',
      company: '',
      service: '',
      budget: '',
      message: '',
      message_type: 'general',
      position_interest: '',
      resume_file: null,
      portfolio_url: '',
      experience_summary: '',
      availability: '',
      salary_expectation: ''
    })

    const errors = reactive({
      name: '',
      email: '',
      message: '',
      resume_file: ''
    })

    const contactInfo = ref([])
    const responseTimeText = ref('')

    const resetConditionalFields = () => {
      // Reset fields when switching between message types
      if (form.message_type === 'general') {
        form.position_interest = ''
        form.resume_file = null
        form.portfolio_url = ''
        form.experience_summary = ''
        form.availability = ''
        form.salary_expectation = ''
      } else {
        form.service = ''
        form.budget = ''
      }
      errors.resume_file = ''
    }

    const handleFileUpload = (event) => {
      const file = event.target.files[0]
      if (file) {
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
          errors.resume_file = 'File size must be less than 5MB'
          form.resume_file = null
          event.target.value = ''
          return
        }

        // Validate file type
        const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
        if (!allowedTypes.includes(file.type)) {
          errors.resume_file = 'Please upload a PDF, DOC, or DOCX file'
          form.resume_file = null
          event.target.value = ''
          return
        }

        form.resume_file = file
        errors.resume_file = ''
      }
    }

    const validateForm = () => {
      // Reset errors
      Object.keys(errors).forEach(key => {
        errors[key] = ''
      })

      let isValid = true

      if (!form.name.trim()) {
        errors.name = 'Name is required'
        isValid = false
      }

      if (!form.email.trim()) {
        errors.email = 'Email is required'
        isValid = false
      } else if (!/\S+@\S+\.\S+/.test(form.email)) {
        errors.email = 'Please enter a valid email'
        isValid = false
      }

      if (!form.message.trim()) {
        errors.message = form.message_type === 'job_application' ? 'Cover letter is required' : 'Message is required'
        isValid = false
      }

      if (form.message_type === 'job_application' && !form.resume_file) {
        errors.resume_file = 'Resume is required for job applications'
        isValid = false
      }

      return isValid
    }

    const submitForm = async () => {
      if (!validateForm()) return

      isSubmitting.value = true
      submitError.value = ''

      try {
        const formData = new FormData()

        // Add all form fields to FormData
        Object.keys(form).forEach(key => {
          if (form[key] !== null && form[key] !== '') {
            if (key === 'resume_file' && form[key] instanceof File) {
              formData.append(key, form[key])
            } else if (key !== 'resume_file') {
              formData.append(key, form[key])
            }
          }
        })

        const response = await contactService.submitWithFile(formData)

        if (response.success) {
          // Reset form
          Object.keys(form).forEach(key => {
            if (key === 'resume_file') {
              form[key] = null
            } else if (key === 'message_type') {
              form[key] = 'general'
            } else {
              form[key] = ''
            }
          })

          // Reset file input
          const fileInput = document.getElementById('resume_file')
          if (fileInput) fileInput.value = ''

          showSuccess.value = true
        }
      } catch (error) {
        console.error('Form submission error:', error)

        if (error.errors) {
          // Handle validation errors
          Object.keys(error.errors).forEach(key => {
            if (errors.hasOwnProperty(key)) {
              errors[key] = error.errors[key][0]
            }
          })
        } else {
          submitError.value = error.message || 'An error occurred while sending your message. Please try again.'
        }
      } finally {
        isSubmitting.value = false
      }
    }

    const closeSuccess = () => {
      showSuccess.value = false
    }

    onMounted(() => {
      // Add scroll-triggered animations
      const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      }

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.animationPlayState = 'running'
          }
        })
      }, observerOptions)

      // Observe all animated elements
      document.querySelectorAll('.animate-fade-in, .animate-slide-up').forEach(el => {
        el.style.animationPlayState = 'paused'
        observer.observe(el)
      })

      // Fetch contact information from API
      const fetchContactInfo = async () => {
        try {
          const response = await contactService.getContactInfo()
          if (response.success) {
            contactInfo.value = response.data.contacts || []
            responseTimeText.value = response.data.response_time_text || ''
          }
        } catch (error) {
          console.error('Error fetching contact info:', error)
        }
      }

      fetchContactInfo()
    })

    return {
      form,
      errors,
      isSubmitting,
      showSuccess,
      submitError,
      contactInfo,
      responseTimeText,
      resetConditionalFields,
      handleFileUpload,
      validateForm,
      submitForm,
      closeSuccess
    }
  }
}
</script>

<style lang="scss" scoped>
@use '../../css/pages/contact-page';
</style>
