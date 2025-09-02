<template>
  <div class="contact-page page-with-header-offset">
    <!-- Contact Form & Info Section -->
    <section class="section">
      <div class="container">
        <div class="section__header animate-fade-in">
          <h1 class="section__title">Get In Touch</h1>
          <p class="section__subtitle">
            Ready to start your project? We'd love to hear from you.
            Let's discuss your ideas and bring them to life.
          </p>
        </div>

        <div class="grid grid--2 gap-12 items-start">
          <!-- Contact Form -->
          <div class="contact-form-container animate-fade-in">
            <h2 class="contact-form__title">Send us a message</h2>
            <form @submit.prevent="submitForm" class="contact-form">
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

              <div class="form__group">
                <label for="message" class="form__label">Message *</label>
                <textarea
                  id="message"
                  v-model="form.message"
                  class="form__textarea"
                  :class="{ 'form__input--error': errors.message }"
                  placeholder="Tell us about your project, goals, and timeline..."
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
                <span>Send Message</span>
                <i class="fas fa-arrow-right btn__icon btn__icon--right"></i>
              </button>
            </form>
          </div>

          <!-- Contact Information -->
          <div class="contact-info animate-slide-up">
            <h2 class="contact-info__title">Let's start a conversation</h2>
            <p class="contact-info__subtitle">
              We're here to help you bring your ideas to life. Get in touch and let's discuss your project.
            </p>

            <div class="contact-methods">
              <div class="contact-method">
                <div class="contact-method__icon">
                  <i class="fas fa-envelope"></i>
                </div>
                <div class="contact-method__content">
                  <h3 class="contact-method__title">Email</h3>
                  <p class="contact-method__text">darkheim.studio@gmail.com</p>
                </div>
              </div>

            </div>

            <div class="response-time">
              <h3 class="response-time__title">Response Time</h3>
              <p class="response-time__text">
                We typically respond within 24 hours during business days.
                For urgent matters, please call us directly.
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

export default {
  name: 'ContactPage',
  setup() {
    const isSubmitting = ref(false)
    const showSuccess = ref(false)

    const form = reactive({
      name: '',
      email: '',
      phone: '',
      company: '',
      service: '',
      budget: '',
      message: ''
    })

    const errors = reactive({
      name: '',
      email: '',
      message: ''
    })

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
        errors.message = 'Message is required'
        isValid = false
      }

      return isValid
    }

    const submitForm = async () => {
      if (!validateForm()) return

      isSubmitting.value = true

      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 2000))

        // Reset form
        Object.keys(form).forEach(key => {
          form[key] = ''
        })

        showSuccess.value = true
      } catch (error) {
        console.error('Form submission error:', error)
        // Handle error (show error message)
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
    })

    return {
      form,
      errors,
      isSubmitting,
      showSuccess,
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
