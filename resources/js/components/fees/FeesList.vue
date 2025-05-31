<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Fees Management</h1>
      <button
        @click="showCreateModal = true"
        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg"
      >
        Add New Fee
      </button>
    </div>

    <DataTable
      :headers="headers"
      :items="fees"
      :loading="loading"
      @view="viewFee"
      @edit="editFee"
      @delete="confirmDelete"
      @toggle-status="toggleStatus"
    />

    <!-- Create/Edit Modal -->
    <Modal v-model="showCreateModal" title="Add New Fee">
      <form @submit.prevent="handleSubmit">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input
              v-model="form.name"
              type="text"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea
              v-model="form.description"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              rows="3"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Amount</label>
            <input
              v-model="form.amount"
              type="number"
              step="0.01"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Type</label>
            <select
              v-model="form.type"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            >
              <option value="tuition">Tuition</option>
              <option value="transportation">Transportation</option>
              <option value="library">Library</option>
              <option value="laboratory">Laboratory</option>
              <option value="sports">Sports</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Frequency</label>
            <select
              v-model="form.frequency"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            >
              <option value="one_time">One Time</option>
              <option value="monthly">Monthly</option>
              <option value="quarterly">Quarterly</option>
              <option value="yearly">Yearly</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">School</label>
            <select
              v-model="form.school_id"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            >
              <option v-for="school in schools" :key="school.id" :value="school.id">
                {{ school.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Class Room (Optional)</label>
            <select
              v-model="form.class_room_id"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="">Select Class Room</option>
              <option v-for="classRoom in classRooms" :key="classRoom.id" :value="classRoom.id">
                {{ classRoom.name }}
              </option>
            </select>
          </div>

          <div class="flex items-center">
            <input
              v-model="form.is_mandatory"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label class="ml-2 block text-sm text-gray-700">Mandatory Fee</label>
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
        Are you sure you want to delete this fee? This action cannot be undone.
      </p>
      <div class="mt-6 flex justify-end space-x-3">
        <button
          @click="showDeleteModal = false"
          class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
        >
          Cancel
        </button>
        <button
          @click="deleteFee"
          class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600"
        >
          Delete
        </button>
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
    const fees = ref([])
    const schools = ref([])
    const classRooms = ref([])
    const loading = ref(false)
    const showCreateModal = ref(false)
    const showDeleteModal = ref(false)
    const selectedFee = ref(null)
    const isEditing = ref(false)

    const form = ref({
      name: '',
      description: '',
      amount: '',
      type: 'tuition',
      frequency: 'monthly',
      school_id: '',
      class_room_id: '',
      is_mandatory: false
    })

    const headers = [
      { key: 'name', label: 'Name' },
      { key: 'type', label: 'Type' },
      { key: 'amount', label: 'Amount' },
      { key: 'frequency', label: 'Frequency' },
      { key: 'status', label: 'Status' },
      { key: 'actions', label: 'Actions' }
    ]

    const fetchFees = async () => {
      loading.value = true
      try {
        const response = await axios.get('/api/fees')
        fees.value = response.data
      } catch (error) {
        console.error('Error fetching fees:', error)
      }
      loading.value = false
    }

    const fetchSchools = async () => {
      try {
        const response = await axios.get('/api/schools')
        schools.value = response.data
      } catch (error) {
        console.error('Error fetching schools:', error)
      }
    }

    const fetchClassRooms = async () => {
      try {
        const response = await axios.get('/api/class-rooms')
        classRooms.value = response.data
      } catch (error) {
        console.error('Error fetching class rooms:', error)
      }
    }

    const resetForm = () => {
      form.value = {
        name: '',
        description: '',
        amount: '',
        type: 'tuition',
        frequency: 'monthly',
        school_id: '',
        class_room_id: '',
        is_mandatory: false
      }
    }

    const viewFee = (fee) => {
      // Implement view functionality
    }

    const editFee = (fee) => {
      isEditing.value = true
      selectedFee.value = fee
      form.value = { ...fee }
      showCreateModal.value = true
    }

    const confirmDelete = (fee) => {
      selectedFee.value = fee
      showDeleteModal.value = true
    }

    const deleteFee = async () => {
      try {
        await axios.delete(`/api/fees/${selectedFee.value.id}`)
        await fetchFees()
        showDeleteModal.value = false
      } catch (error) {
        console.error('Error deleting fee:', error)
      }
    }

    const toggleStatus = async (fee) => {
      try {
        await axios.post(`/api/fees/${fee.id}/toggle-status`)
        await fetchFees()
      } catch (error) {
        console.error('Error toggling fee status:', error)
      }
    }

    const handleSubmit = async () => {
      try {
        if (isEditing.value) {
          await axios.put(`/api/fees/${selectedFee.value.id}`, form.value)
        } else {
          await axios.post('/api/fees', form.value)
        }
        await fetchFees()
        closeModal()
      } catch (error) {
        console.error('Error saving fee:', error)
      }
    }

    const closeModal = () => {
      showCreateModal.value = false
      isEditing.value = false
      selectedFee.value = null
      resetForm()
    }

    onMounted(() => {
      fetchFees()
      fetchSchools()
      fetchClassRooms()
    })

    return {
      fees,
      schools,
      classRooms,
      loading,
      showCreateModal,
      showDeleteModal,
      selectedFee,
      isEditing,
      form,
      headers,
      viewFee,
      editFee,
      confirmDelete,
      deleteFee,
      toggleStatus,
      handleSubmit,
      closeModal
    }
  }
}
</script> 