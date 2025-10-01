<template>
  <div class="admin-file-manager">
    <div class="page-header">
      <div class="header-content">
        <h1><i class="fas fa-folder-open"></i> File Manager</h1>
        <p>Manage your files and media assets</p>
      </div>
      <div class="header-actions">
        <button @click="showUploadModal = true" class="btn btn-primary">
          <i class="fas fa-upload"></i> Upload Files
        </button>
        <button @click="showCreateFolderModal = true" class="btn btn-secondary">
          <i class="fas fa-folder-plus"></i> New Folder
        </button>
      </div>
    </div>

    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
      <nav class="breadcrumb">
        <button @click="navigateToPath('')" class="breadcrumb-item">
          <i class="fas fa-home"></i> Root
        </button>
        <span v-for="(part, index) in pathParts" :key="index" class="breadcrumb-item">
          <i class="fas fa-chevron-right"></i>
          <button @click="navigateToPath(getPathUpTo(index + 1))">{{ part }}</button>
        </span>
      </nav>
    </div>

    <!-- File Type Filter -->
    <div class="filter-bar">
      <div class="filter-group">
        <label>Filter by type:</label>
        <select v-model="currentFilter" @change="loadFiles">
          <option value="all">All Files</option>
          <option value="images">Images</option>
          <option value="documents">Documents</option>
          <option value="videos">Videos</option>
          <option value="archives">Archives</option>
        </select>
      </div>
      <div class="view-toggle">
        <button
          @click="viewMode = 'grid'"
          :class="['btn', { active: viewMode === 'grid' }]"
        >
          <i class="fas fa-th"></i>
        </button>
        <button
          @click="viewMode = 'list'"
          :class="['btn', { active: viewMode === 'list' }]"
        >
          <i class="fas fa-list"></i>
        </button>
      </div>
    </div>

    <!-- File Explorer -->
    <div class="file-explorer" :class="viewMode">
      <div v-if="loading" class="loading-state">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Loading files...</p>
      </div>

      <div v-else-if="!files.directories.length && !files.files.length" class="empty-state">
        <i class="fas fa-folder-open"></i>
        <h3>This folder is empty</h3>
        <p>Upload files or create folders to get started</p>
      </div>

      <div v-else class="file-grid">
        <!-- Directories -->
        <div
          v-for="directory in files.directories"
          :key="directory.path"
          @click="navigateToPath(directory.path)"
          @contextmenu.prevent="showContextMenu($event, directory)"
          class="file-item directory"
        >
          <div class="file-icon">
            <i class="fas fa-folder"></i>
          </div>
          <div class="file-info">
            <span class="file-name">{{ directory.name }}</span>
            <span class="file-meta">Folder</span>
          </div>
        </div>

        <!-- Files -->
        <div
          v-for="file in files.files"
          :key="file.path"
          @click="selectFile(file)"
          @dblclick="openFile(file)"
          @contextmenu.prevent="showContextMenu($event, file)"
          :class="['file-item', { selected: selectedFiles.includes(file.path) }]"
        >
          <div class="file-icon">
            <img v-if="file.is_image && file.extension !== 'svg'"
                 :src="file.url"
                 :alt="file.name"
                 @error="$event.target.style.display='none'"
            >
            <i v-else :class="getFileIcon(file.type, file.extension)"></i>
          </div>
          <div class="file-info">
            <span class="file-name" :title="file.name">{{ file.name }}</span>
            <span class="file-meta">
              {{ formatFileSize(file.size) }} • {{ formatDate(file.modified) }}
            </span>
            <span v-if="file.width && file.height" class="file-dimensions">
              {{ file.width }} × {{ file.height }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Upload Modal -->
    <div v-if="showUploadModal" class="modal-overlay" @click.self="showUploadModal = false">
      <div class="modal">
        <div class="modal-header">
          <h3>Upload Files</h3>
          <button @click="showUploadModal = false" class="btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="upload-area"
               @drop.prevent="handleDrop"
               @dragover.prevent
               @dragenter.prevent>
            <input
              ref="fileInput"
              type="file"
              multiple
              @change="handleFileSelect"
              style="display: none"
            >
            <div class="upload-content">
              <i class="fas fa-cloud-upload-alt"></i>
              <p>Drag files here or <button @click="$refs.fileInput.click()" class="link-btn">browse</button></p>
              <small>Maximum file size: 10MB</small>
            </div>
          </div>

          <div v-if="uploadQueue.length" class="upload-queue">
            <h4>Upload Queue</h4>
            <div v-for="(file, index) in uploadQueue" :key="index" class="upload-item">
              <span>{{ file.name }}</span>
              <span>{{ formatFileSize(file.size) }}</span>
              <button @click="removeFromQueue(index)" class="btn-remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showUploadModal = false" class="btn btn-secondary">Cancel</button>
          <button @click="uploadFiles" :disabled="!uploadQueue.length || uploading" class="btn btn-primary">
            <i v-if="uploading" class="fas fa-spinner fa-spin"></i>
            <i v-else class="fas fa-upload"></i>
            {{ uploading ? 'Uploading...' : 'Upload' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Create Folder Modal -->
    <div v-if="showCreateFolderModal" class="modal-overlay" @click.self="showCreateFolderModal = false">
      <div class="modal">
        <div class="modal-header">
          <h3>Create New Folder</h3>
          <button @click="showCreateFolderModal = false" class="btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="folderName">Folder Name</label>
            <input
              id="folderName"
              v-model="newFolderName"
              type="text"
              placeholder="Enter folder name"
              @keyup.enter="createFolder"
            >
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showCreateFolderModal = false" class="btn btn-secondary">Cancel</button>
          <button @click="createFolder" :disabled="!newFolderName.trim()" class="btn btn-primary">
            <i class="fas fa-folder-plus"></i> Create
          </button>
        </div>
      </div>
    </div>

    <!-- Context Menu -->
    <div v-if="contextMenu.show"
         :style="{ top: contextMenu.y + 'px', left: contextMenu.x + 'px' }"
         class="context-menu">
      <button v-if="contextMenu.item.type === 'directory'" @click="navigateToPath(contextMenu.item.path)">
        <i class="fas fa-folder-open"></i> Open
      </button>
      <button v-else @click="openFile(contextMenu.item)">
        <i class="fas fa-external-link-alt"></i> Open
      </button>
      <button @click="copyUrl(contextMenu.item)" v-if="contextMenu.item.url">
        <i class="fas fa-copy"></i> Copy URL
      </button>
      <hr>
      <button @click="deleteItem(contextMenu.item)" class="danger">
        <i class="fas fa-trash"></i> Delete
      </button>
    </div>
  </div>
</template>

<script>
import { adminApiService } from '../admin-services/adminApi.js'

export default {
  name: 'AdminFileManager',
  data() {
    return {
      files: {
        current_path: '',
        directories: [],
        files: []
      },
      currentPath: '',
      currentFilter: 'all',
      viewMode: 'grid',
      loading: false,
      selectedFiles: [],

      // Upload
      showUploadModal: false,
      uploadQueue: [],
      uploading: false,

      // Create folder
      showCreateFolderModal: false,
      newFolderName: '',

      // Context menu
      contextMenu: {
        show: false,
        x: 0,
        y: 0,
        item: null
      }
    }
  },
  computed: {
    pathParts() {
      return this.currentPath ? this.currentPath.split('/').filter(Boolean) : []
    }
  },
  mounted() {
    this.loadFiles()
    document.addEventListener('click', this.hideContextMenu)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.hideContextMenu)
  },
  methods: {
    async loadFiles() {
      this.loading = true
      try {
        const response = await adminApiService.getFiles(this.currentPath, this.currentFilter)
        this.files = response.data.data
        this.selectedFiles = []
      } catch (error) {
        console.error('Failed to load files:', error)
        this.$toast?.error('Failed to load files')
      } finally {
        this.loading = false
      }
    },

    navigateToPath(path) {
      this.currentPath = path
      this.loadFiles()
    },

    getPathUpTo(index) {
      return this.pathParts.slice(0, index).join('/')
    },

    selectFile(file) {
      const index = this.selectedFiles.indexOf(file.path)
      if (index > -1) {
        this.selectedFiles.splice(index, 1)
      } else {
        this.selectedFiles.push(file.path)
      }
    },

    openFile(file) {
      if (file.url) {
        window.open(file.url, '_blank')
      }
    },

    getFileIcon(type, extension) {
      const icons = {
        images: 'fas fa-image',
        documents: 'fas fa-file-alt',
        videos: 'fas fa-video',
        archives: 'fas fa-file-archive'
      }
      return icons[type] || 'fas fa-file'
    },

    formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes'
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    },

    formatDate(timestamp) {
      return new Date(timestamp * 1000).toLocaleDateString()
    },

    // Upload methods
    handleFileSelect(event) {
      this.addFilesToQueue(Array.from(event.target.files))
    },

    handleDrop(event) {
      this.addFilesToQueue(Array.from(event.dataTransfer.files))
    },

    addFilesToQueue(files) {
      this.uploadQueue.push(...files)
    },

    removeFromQueue(index) {
      this.uploadQueue.splice(index, 1)
    },

    async uploadFiles() {
      if (!this.uploadQueue.length) return

      this.uploading = true
      try {
        await adminApiService.uploadFiles(this.uploadQueue, this.currentPath)
        this.uploadQueue = []
        this.showUploadModal = false
        this.loadFiles()
        this.$toast?.success('Files uploaded successfully')
      } catch (error) {
        console.error('Upload failed:', error)
        this.$toast?.error('Upload failed')
      } finally {
        this.uploading = false
      }
    },

    // Folder methods
    async createFolder() {
      if (!this.newFolderName.trim()) return

      try {
        await adminApiService.createDirectory(this.newFolderName, this.currentPath)
        this.newFolderName = ''
        this.showCreateFolderModal = false
        this.loadFiles()
        this.$toast?.success('Folder created successfully')
      } catch (error) {
        console.error('Failed to create folder:', error)
        this.$toast?.error('Failed to create folder')
      }
    },

    // Context menu methods
    showContextMenu(event, item) {
      this.contextMenu = {
        show: true,
        x: event.clientX,
        y: event.clientY,
        item
      }
    },

    hideContextMenu() {
      this.contextMenu.show = false
    },

    copyUrl(item) {
      if (item.url) {
        navigator.clipboard.writeText(item.url)
        this.$toast?.success('URL copied to clipboard')
      }
      this.hideContextMenu()
    },

    async deleteItem(item) {
      if (confirm(`Are you sure you want to delete "${item.name}"?`)) {
        try {
          await adminApiService.deleteFiles([item.path])
          this.loadFiles()
          this.$toast?.success('Item deleted successfully')
        } catch (error) {
          console.error('Failed to delete item:', error)
          this.$toast?.error('Failed to delete item')
        }
      }
      this.hideContextMenu()
    }
  }
}
</script>

<style scoped>
.admin-file-manager {
  padding: 20px;
  background: #f8fafc;
  min-height: 100vh;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  background: white;
  padding: 25px 30px;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.header-content h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 8px 0;
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-content h1 i {
  color: #3b82f6;
  font-size: 1.8rem;
}

.header-content p {
  color: #6b7280;
  margin: 0;
  font-size: 1rem;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.breadcrumb-nav {
  margin-bottom: 20px;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 16px 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.breadcrumb-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.breadcrumb-item button {
  background: none;
  border: none;
  color: #3b82f6;
  cursor: pointer;
  padding: 6px 12px;
  border-radius: 6px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.breadcrumb-item button:hover {
  background: linear-gradient(135deg, #dbeafe, #bfdbfe);
  color: #1e40af;
}

.breadcrumb-item i {
  color: #6b7280;
  font-size: 0.875rem;
}

.filter-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 20px 25px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 12px;
}

.filter-group label {
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
}

.filter-group select {
  padding: 8px 12px;
  border: 2px solid #e5e7eb;
  border-radius: 6px;
  background: white;
  color: #374151;
  font-size: 0.875rem;
  transition: all 0.2s ease;
}

.filter-group select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.view-toggle {
  display: flex;
  gap: 4px;
  background: #f3f4f6;
  padding: 4px;
  border-radius: 8px;
}

.view-toggle .btn {
  padding: 8px 12px;
  border: none;
  background: transparent;
  color: #6b7280;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
}

.view-toggle .btn.active {
  background: white;
  color: #3b82f6;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  font-weight: 600;
}

.file-explorer {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  overflow: hidden;
  min-height: 400px;
}

.file-explorer.grid .file-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  padding: 25px;
}

.file-explorer.list .file-grid {
  display: flex;
  flex-direction: column;
  gap: 1px;
  padding: 0;
}

.file-item {
  padding: 20px;
  border: 2px solid transparent;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
  background: white;
  position: relative;
  overflow: hidden;
}

.file-explorer.grid .file-item {
  text-align: center;
  background: linear-gradient(135deg, #f8fafc, #f1f5f9);
  border: 1px solid #e5e7eb;
}

.file-explorer.list .file-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px 25px;
  border-radius: 0;
  border-bottom: 1px solid #f3f4f6;
  text-align: left;
}

.file-item:hover {
  border-color: #3b82f6;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
  transform: translateY(-2px);
}

.file-explorer.list .file-item:hover {
  background: linear-gradient(135deg, #fafbff, #f0f4ff);
  transform: none;
}

.file-item.selected {
  border-color: #3b82f6;
  background: linear-gradient(135deg, #dbeafe, #bfdbfe);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.file-item.directory {
  background: linear-gradient(135deg, #fef3c7, #fde68a);
  border-color: #f59e0b;
}

.file-item.directory:hover {
  border-color: #d97706;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.file-icon {
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 80px;
}

.file-explorer.list .file-icon {
  margin-bottom: 0;
  height: auto;
  width: 40px;
  justify-content: flex-start;
}

.file-icon i {
  font-size: 3rem;
  color: #6b7280;
}

.file-explorer.list .file-icon i {
  font-size: 1.5rem;
}

.file-icon img {
  max-width: 80px;
  max-height: 80px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  object-fit: cover;
}

.file-explorer.list .file-icon img {
  max-width: 40px;
  max-height: 40px;
  border-radius: 4px;
}

.file-info {
  flex: 1;
}

.file-name {
  display: block;
  font-weight: 600;
  margin-bottom: 6px;
  word-break: break-word;
  color: #1f2937;
  font-size: 0.875rem;
}

.file-explorer.grid .file-name {
  text-align: center;
}

.file-meta {
  display: block;
  font-size: 0.75rem;
  color: #6b7280;
  margin-bottom: 4px;
}

.file-dimensions {
  display: block;
  font-size: 0.75rem;
  color: #8b5cf6;
  font-weight: 500;
}

.loading-state, .empty-state {
  text-align: center;
  padding: 80px 20px;
  color: #6b7280;
}

.loading-state i {
  font-size: 48px;
  margin-bottom: 20px;
  color: #3b82f6;
  animation: spin 1s linear infinite;
}

.empty-state i {
  font-size: 64px;
  margin-bottom: 20px;
  color: #d1d5db;
}

.empty-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
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

.modal {
  background: white;
  border-radius: 16px;
  width: 90%;
  max-width: 600px;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  border: 1px solid #e5e7eb;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 30px;
  border-bottom: 1px solid #e5e7eb;
  background: linear-gradient(135deg, #f8fafc, #f1f5f9);
}

.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
}

.btn-close {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.btn-close:hover {
  background: #f3f4f6;
  color: #374151;
}

.modal-body {
  padding: 30px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 20px 30px;
  border-top: 1px solid #e5e7eb;
  background: #f8fafc;
}

.upload-area {
  border: 3px dashed #3b82f6;
  border-radius: 12px;
  padding: 40px;
  text-align: center;
  margin-bottom: 20px;
  background: linear-gradient(135deg, #fafbff, #f0f4ff);
  transition: all 0.2s ease;
}

.upload-area:hover {
  border-color: #1d4ed8;
  background: linear-gradient(135deg, #f0f4ff, #e0f2fe);
  transform: scale(1.02);
}

.upload-content i {
  font-size: 3rem;
  color: #3b82f6;
  margin-bottom: 16px;
  display: block;
}

.upload-content p {
  font-size: 1.125rem;
  color: #374151;
  margin: 0 0 8px 0;
}

.upload-content small {
  color: #6b7280;
  font-size: 0.875rem;
}

.link-btn {
  background: none;
  border: none;
  color: #3b82f6;
  cursor: pointer;
  text-decoration: underline;
  font-weight: 600;
}

.link-btn:hover {
  color: #1d4ed8;
}

.upload-queue {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 20px;
  background: #f8fafc;
}

.upload-queue h4 {
  margin: 0 0 16px 0;
  font-size: 1rem;
  font-weight: 600;
  color: #374151;
}

.upload-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid #e5e7eb;
  background: white;
  border-radius: 6px;
  margin-bottom: 8px;
  transition: all 0.2s ease;
}

.upload-item:hover {
  background: #f3f4f6;
}

.upload-item:last-child {
  margin-bottom: 0;
}

.btn-remove {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #dc2626;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.btn-remove:hover {
  background: #dc2626;
  color: white;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-weight: 600;
  margin-bottom: 8px;
  color: #374151;
}

.form-group input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  background: white;
}

.form-group input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.context-menu {
  position: fixed;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
  z-index: 1001;
  min-width: 180px;
  overflow: hidden;
}

.context-menu button {
  display: block;
  width: 100%;
  padding: 12px 16px;
  border: none;
  background: none;
  text-align: left;
  cursor: pointer;
  color: #374151;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.context-menu button:hover {
  background: #f3f4f6;
  color: #1f2937;
}

.context-menu button.danger {
  color: #dc2626;
}

.context-menu button.danger:hover {
  background: #fef2f2;
  color: #b91c1c;
}

.context-menu hr {
  margin: 4px 0;
  border: none;
  border-top: 1px solid #e5e7eb;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
  text-decoration: none;
  position: relative;
  overflow: hidden;
}

.btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: left 0.5s;
}

.btn:hover::before {
  left: 100%;
}

.btn-primary {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.3);
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #2563eb, #1e40af);
  transform: translateY(-1px);
  box-shadow: 0 6px 20px 0 rgba(59, 130, 246, 0.4);
}

.btn-secondary {
  background: linear-gradient(135deg, #6b7280, #4b5563);
  color: white;
  box-shadow: 0 4px 14px 0 rgba(107, 114, 128, 0.3);
}

.btn-secondary:hover {
  background: linear-gradient(135deg, #4b5563, #374151);
  transform: translateY(-1px);
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .file-explorer.grid .file-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
  }
}

@media (max-width: 768px) {
  .admin-file-manager {
    padding: 15px;
  }

  .page-header {
    flex-direction: column;
    gap: 20px;
    align-items: stretch;
  }

  .header-actions {
    justify-content: center;
  }

  .filter-bar {
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
  }

  .file-explorer.grid .file-grid {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    padding: 15px;
  }

  .file-item {
    padding: 15px;
  }

  .file-icon {
    height: 60px;
  }

  .file-icon i {
    font-size: 2rem;
  }

  .file-icon img {
    max-width: 60px;
    max-height: 60px;
  }

  .modal {
    width: 95%;
    margin: 20px;
  }

  .modal-header,
  .modal-body,
  .modal-footer {
    padding: 20px;
  }

  .upload-area {
    padding: 30px 20px;
  }

  .upload-content i {
    font-size: 2.5rem;
  }
}

@media (max-width: 480px) {
  .breadcrumb {
    flex-wrap: wrap;
    gap: 4px;
  }

  .file-explorer.grid .file-grid {
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 8px;
    padding: 10px;
  }

  .file-item {
    padding: 10px;
  }

  .file-explorer.list .file-item {
    padding: 12px 15px;
    gap: 12px;
  }

  .file-explorer.list .file-icon {
    width: 32px;
  }

  .file-explorer.list .file-icon i {
    font-size: 1.25rem;
  }

  .file-explorer.list .file-icon img {
    max-width: 32px;
    max-height: 32px;
  }
}
</style>
