<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Notifications Management</h1>
      <button
        @click="showCreateModal = true"
        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg"
      >
        Create New Notification
      </button>
    </div>

    <DataTable
      :headers="headers"
      :items="notifications"
      :loading="loading"
      @view="viewNotification"
      @edit="editNotification"
      @delete="confirmDelete"
      @send="confirmSend"
    />

    <!-- Create/Edit Modal -->
    <Modal v-model="showCreateModal" title="Create New Notification">
      <form @submit.prevent="handleSubmit">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input
              v-model="form.title"
              type="text"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Content</label>
            <textarea
              v-model="form.content"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              rows="4"
              required
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Type</label>
            <select
              v-model="form.type"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            >
              <option value="announcement">Announcement</option>
              <option value="reminder">Reminder</option>
              <option value="alert">Alert</option>
              <option value="event">Event</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Priority</label>
            <select
              v-model="form.priority"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            >
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Target Audience</label>
            <div class="mt-2 space-y-2">
              <div class="flex items-center">
                <input
                  v-model="form.target_audience"
                  type="radio"
                  value="all"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                />
                <label class="ml-2 block text-sm text-gray-700">All Users</label>
              </div>
              <div class="flex items-center">
                <input
                  v-model="form.target_audience"
                  type="radio"
                  value="students"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                />
                <label class="ml-2 block text-sm text-gray-700">Students</label>
              </div>
              <div class="flex items-center">
                <input
                  v-model="form.target_audience"
                  type="radio"
                  value="teachers"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                />
                <label class="ml-2 block text-sm text-gray-700">Teachers</label>
              </div>
              <div class="flex items-center">
                <input
                  v-model="form.target_audience"
                  type="radio"
                  value="parents"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                />
                <label class="ml-2 block text-sm text-gray-700">Parents</label>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Scheduled At (Optional)</label>
            <input
              v-model="form.scheduled_at"
              type="datetime-local"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
          </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
          <button
            type="button"
            @click="closeModal"
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
          >
            {{ isEditing ? 'Update' : 'Create' }}
          </button>
        </div>
      </form>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal v-model="showDeleteModal" title="Confirm Delete">
      <p class="text-gray-700">
        Are you sure you want to delete this notification? This action cannot be undone.
      </p>
      <div class="mt-6 flex justify-end space-x-3">
        <button
          @click="showDeleteModal = false"
          class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
        >
          Cancel
        </button>
        <button
          @click="deleteNotification"
          class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600"
        >
          Delete
        </button>
      </div>
    </Modal>

    <!-- Send Confirmation Modal -->
    <Modal v-model="showSendModal" title="Confirm Send">
      <p class="text-gray-700">
        Are you sure you want to send this notification now? This will notify all selected recipients.
      </p>
      <div class="mt-6 flex justify-end space-x-3">
        <button
          @click="showSendModal = false"
          class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
        >
          Cancel
        </button>
        <button
          @click="sendNotification"
          class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600"
        >
          Send Now
        </button>
      </div>
    </Modal>

    <!-- View Modal -->
    <Modal v-model="showViewModal" title="Notification Details">
      <div v-if="selectedNotification" class="space-y-4">
        <div class="border-b pb-4">
          <h3 class="text-lg font-semibold">{{ selectedNotification.title }}</h3>
          <p class="text-sm text-gray-600">
            Type: {{ formatType(selectedNotification.type) }}
          </p>
          <p class="text-sm text-gray-600">
            Priority: {{ formatPriority(selectedNotification.priority) }}
          </p>
          <p class="text-sm text-gray-600">
            Status: {{ formatStatus(selectedNotification.status) }}
          </p>
        </div>

        <div class="border-b pb-4">
          <h3 class="text-lg font-semibold">Content</h3>
          <p class="text-gray-700 whitespace-pre-wrap">{{ selectedNotification.content }}</p>
        </div>

        <div class="border-b pb-4">
          <h3 class="text-lg font-semibold">Details</h3>
          <p class="text-sm text-gray-600">
            Target Audience: {{ formatTargetAudience(selectedNotification.target_audience) }}
          </p>
          <p class="text-sm text-gray-600">
            Created: {{ formatDate(selectedNotification.created_at) }}
          </p>
          <p v-if="selectedNotification.scheduled_at" class="text-sm text-gray-600">
            Scheduled: {{ formatDate(selectedNotification.scheduled_at) }}
          </p>
          <p v-if="selectedNotification.sent_at" class="text-sm text-gray-600">
            Sent: {{ formatDate(selectedNotification.sent_at) }}
          </p>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import DataTable from '../common/DataTable.vue'
import Modal from '../common/Modal.vue'
import axios from 'axios'

export default {
  components: {
    DataTable,
    Modal
  },

  setup() {
    const notifications = ref([])
    const loading = ref(false)
    const showCreateModal = ref(false)
    const showDeleteModal = ref(false)
    const showSendModal = ref(false)
    const showViewModal = ref(false)
    const selectedNotification = ref(null)
    const isEditing = ref(false)

    const form = ref({
      title: '',
      content: '',
      type: 'announcement',
      priority: 'medium',
      target_audience: 'all',
      scheduled_at: ''
    })

    const headers = [
      { key: 'title', label: 'Title' },
      { key: 'type', label: 'Type' },
      { key: 'priority', label: 'Priority' },
      { key: 'status', label: 'Status' },
      { key: 'scheduled_at', label: 'Scheduled' },
      { key: 'actions', label: 'Actions' }
    ]

    const fetchNotifications = async () => {
      loading.value = true
      try {
        const response = await axios.get('/api/notifications')
        notifications.value = response.data
      } catch (error) {
        console.error('Error fetching notifications:', error)
      }
      loading.value = false
    }

    const resetForm = () => {
      form.value = {
        title: '',
        content: '',
        type: 'announcement',
        priority: 'medium',
        target_audience: 'all',
        scheduled_at: ''
      }
    }

    const viewNotification = (notification) => {
      selectedNotification.value = notification
      showViewModal.value = true
    }

    const editNotification = (notification) => {
      isEditing.value = true
      selectedNotification.value = notification
      form.value = { ...notification }
      showCreateModal.value = true
    }

    const confirmDelete = (notification) => {
      selectedNotification.value = notification
      showDeleteModal.value = true
    }

    const deleteNotification = async () => {
      try {
        await axios.delete(`/api/notifications/${selectedNotification.value.id}`)
        await fetchNotifications()
        showDeleteModal.value = false
      } catch (error) {
        console.error('Error deleting notification:', error)
      }
    }

    const confirmSend = (notification) => {
      selectedNotification.value = notification
      showSendModal.value = true
    }

    const sendNotification = async () => {
      try {
        await axios.post(`/api/notifications/${selectedNotification.value.id}/send`)
        await fetchNotifications()
        showSendModal.value = false
      } catch (error) {
        console.error('Error sending notification:', error)
      }
    }

    const handleSubmit = async () => {
      try {
        if (isEditing.value) {
          await axios.put(`/api/notifications/${selectedNotification.value.id}`, form.value)
        } else {
          await axios.post('/api/notifications', form.value)
        }
        await fetchNotifications()
        closeModal()
      } catch (error) {
        console.error('Error saving notification:', error)
      }
    }

    const closeModal = () => {
      showCreateModal.value = false
      showViewModal.value = false
      isEditing.value = false
      selectedNotification.value = null
      resetForm()
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleString()
    }

    const formatType = (type) => {
      return type.charAt(0).toUpperCase() + type.slice(1)
    }

    const formatPriority = (priority) => {
      return priority.charAt(0).toUpperCase() + priority.slice(1)
    }

    const formatStatus = (status) => {
      return status.charAt(0).toUpperCase() + status.slice(1)
    }

    const formatTargetAudience = (audience) => {
      return audience.charAt(0).toUpperCase() + audience.slice(1)
    }

    onMounted(() => {
      fetchNotifications()
    })

    return {
      notifications,
      loading,
      showCreateModal,
      showDeleteModal,
      showSendModal,
      showViewModal,
      selectedNotification,
      isEditing,
      form,
      headers,
      viewNotification,
      editNotification,
      confirmDelete,
      deleteNotification,
      confirmSend,
      sendNotification,
      handleSubmit,
      closeModal,
      formatDate,
      formatType,
      formatPriority,
      formatStatus,
      formatTargetAudience
    }
  }
}
</script> 