<template>
  <div class="admin-dashboard">
    <div class="dashboard-header">
      <h1>Dashboard</h1>
      <p>Welcome back, {{ user?.name }}!</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon users">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
          <h3>{{ stats.users_count }}</h3>
          <p>Total Users</p>
          <div class="stat-trend">
            <i class="fas fa-arrow-up"></i>
            <span>+12% from last month</span>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon portfolio">
          <i class="fas fa-briefcase"></i>
        </div>
        <div class="stat-content">
          <h3>{{ stats.portfolios_count }}</h3>
          <p>Portfolio Items</p>
          <div class="stat-trend">
            <i class="fas fa-arrow-up"></i>
            <span>{{ stats.featured_portfolios_count }} featured</span>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon news">
          <i class="fas fa-newspaper"></i>
        </div>
        <div class="stat-content">
          <h3>{{ stats.news_count }}</h3>
          <p>News Articles</p>
          <div class="stat-trend">
            <i class="fas fa-check-circle"></i>
            <span>{{ stats.published_news_count }} published</span>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon activity">
          <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-content">
          <h3>94.5%</h3>
          <p>System Health</p>
          <div class="stat-trend positive">
            <i class="fas fa-arrow-up"></i>
            <span>All systems operational</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-sections">
      <div class="section">
        <h2>Quick Actions</h2>
        <div class="quick-actions">
          <router-link to="/admin/users" class="action-card">
            <div class="action-icon users">
              <i class="fas fa-user-plus"></i>
            </div>
            <div class="action-content">
              <h4>Add New User</h4>
              <p>Create a new user account</p>
            </div>
          </router-link>

          <router-link to="/admin/portfolio" class="action-card">
            <div class="action-icon portfolio">
              <i class="fas fa-plus"></i>
            </div>
            <div class="action-content">
              <h4>Add Portfolio Item</h4>
              <p>Showcase your latest work</p>
            </div>
          </router-link>

          <router-link to="/admin/news" class="action-card">
            <div class="action-icon news">
              <i class="fas fa-edit"></i>
            </div>
            <div class="action-content">
              <h4>Write Article</h4>
              <p>Share news and updates</p>
            </div>
          </router-link>
        </div>
      </div>

      <div class="section">
        <h2>Recent Activity</h2>
        <div class="activity-list">
          <div class="activity-item">
            <div class="activity-icon">
              <i class="fas fa-user"></i>
            </div>
            <div class="activity-content">
              <p><strong>New user registered</strong></p>
              <span>2 hours ago</span>
            </div>
          </div>

          <div class="activity-item">
            <div class="activity-icon">
              <i class="fas fa-briefcase"></i>
            </div>
            <div class="activity-content">
              <p><strong>Portfolio item updated</strong></p>
              <span>5 hours ago</span>
            </div>
          </div>

          <div class="activity-item">
            <div class="activity-icon">
              <i class="fas fa-newspaper"></i>
            </div>
            <div class="activity-content">
              <p><strong>News article published</strong></p>
              <span>1 day ago</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAdminAuth } from '../admin-composables/useAdminAuth'
import { adminApiService } from '../admin-services/adminApi'

const { user } = useAdminAuth()

const stats = ref({
  users_count: 0,
  portfolios_count: 0,
  news_count: 0,
  published_news_count: 0,
  featured_portfolios_count: 0
})

onMounted(async () => {
  try {
    const response = await adminApiService.getStats()
    stats.value = response.data
  } catch (error) {
    console.error('Failed to load stats:', error)
  }
})
</script>

<style scoped>
.admin-dashboard {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.dashboard-header {
  margin-bottom: 2rem;
}

.dashboard-header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  color: #2c3e50;
  margin: 0 0 0.5rem 0;
}

.dashboard-header p {
  color: #7f8c8d;
  font-size: 1.1rem;
  margin: 0;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.stat-card {
  background: white;
  padding: 2rem;
  border-radius: 16px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  display: flex;
  align-items: center;
  transition: all 0.3s ease;
  border: 1px solid #e2e8f0;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  margin-right: 1.5rem;
  color: white;
}

.stat-icon.users { background: linear-gradient(135deg, #667eea, #764ba2); }
.stat-icon.portfolio { background: linear-gradient(135deg, #f093fb, #f5576c); }
.stat-icon.news { background: linear-gradient(135deg, #4facfe, #00f2fe); }
.stat-icon.activity { background: linear-gradient(135deg, #43e97b, #38f9d7); }

.stat-content h3 {
  font-size: 2rem;
  font-weight: 700;
  color: #2c3e50;
  margin: 0 0 0.25rem 0;
}

.stat-content p {
  color: #7f8c8d;
  font-size: 0.9rem;
  margin: 0 0 0.5rem 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 500;
}

.stat-trend {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.8rem;
  color: #27ae60;
  font-weight: 500;
}

.stat-trend.positive {
  color: #27ae60;
}

.stat-trend i {
  font-size: 0.7rem;
}

.dashboard-sections {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
}

.section {
  background: white;
  padding: 2rem;
  border-radius: 16px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
}

.section h2 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2c3e50;
  margin: 0 0 1.5rem 0;
}

.quick-actions {
  display: grid;
  gap: 1rem;
}

.action-card {
  display: flex;
  align-items: center;
  padding: 1.5rem;
  background: #f8f9fa;
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.2s ease;
  border: 1px solid #e9ecef;
}

.action-card:hover {
  background: #e9ecef;
  transform: translateX(4px);
}

.action-icon {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  margin-right: 1rem;
  color: white;
}

.action-icon.users { background: #667eea; }
.action-icon.portfolio { background: #f5576c; }
.action-icon.news { background: #4facfe; }

.action-content h4 {
  font-size: 1rem;
  font-weight: 600;
  color: #2c3e50;
  margin: 0 0 0.25rem 0;
}

.action-content p {
  font-size: 0.85rem;
  color: #7f8c8d;
  margin: 0;
}

.activity-list {
  space-y: 1rem;
}

.activity-item {
  display: flex;
  align-items: center;
  padding: 1rem 0;
  border-bottom: 1px solid #f1f3f4;
}

.activity-item:last-child {
  border-bottom: none;
}

.activity-item .activity-icon {
  width: 40px;
  height: 40px;
  background: #e9ecef;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  color: #6c757d;
}

.activity-content p {
  margin: 0 0 0.25rem 0;
  font-size: 0.9rem;
  color: #2c3e50;
}

.activity-content span {
  font-size: 0.8rem;
  color: #7f8c8d;
}

@media (max-width: 1024px) {
  .dashboard-sections {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .admin-dashboard {
    padding: 1rem;
  }

  .dashboard-header h1 {
    font-size: 2rem;
  }

  .stats-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .stat-card {
    padding: 1.5rem;
  }
}
</style>
