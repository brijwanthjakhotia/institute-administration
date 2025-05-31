<template>
  <div>
    <div class="mb-6 flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-900">Schools</h2>
      <button
        @click="showCreateModal = true"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        Add School
      </button>
    </div>

    <DataTable
      :columns="columns"
      :items="schools"
      @view="viewSchool"
      @edit="editSchool"
      @delete="confirmDelete"
    />

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || showEditModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              {{ showEditModal ? 'Edit School' : 'Add New School' }}
            </h3>
            <Form
              :fields="formFields"
              :initial-data="selectedSchool"
              :submit-label="showEditModal ? 'Update' : 'Create'"
              @submit="handleSubmit"
              @cancel="closeModal"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Delete School
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Are you sure you want to delete this school? This action cannot be undone.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="deleteSchool"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Delete
            </button>
            <button
              type="button"
              @click="showDeleteModal = false"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import DataTable from '../common/DataTable.vue'
import Form from '../common/Form.vue'

export default {
  name: 'SchoolsList',
  components: {
    DataTable,
    Form
  },
  data() {
    return {
      schools: [],
      showCreateModal: false,
      showEditModal: false,
      showDeleteModal: false,
      selectedSchool: null,
      columns: [
        { key: 'name', label: 'Name' },
        { key: 'address', label: 'Address' },
        { key: 'phone', label: 'Phone' },
        { key: 'email', label: 'Email' },
        { key: 'principal_name', label: 'Principal' }
      ],
      formFields: [
        { name: 'name', label: 'School Name', type: 'text', required: true },
        { name: 'address', label: 'Address', type: 'textarea', required: true },
        { name: 'phone', label: 'Phone', type: 'text', required: true },
        { name: 'email', label: 'Email', type: 'email', required: true },
        { name: 'website', label: 'Website', type: 'text' },
        { name: 'principal_name', label: 'Principal Name', type: 'text', required: true },
        { name: 'established_year', label: 'Established Year', type: 'number', required: true }
      ]
    }
  },
  created() {
    this.fetchSchools()
  },
  methods: {
    async fetchSchools() {
      try {
        const response = await axios.get('/api/schools')
        this.schools = response.data
      } catch (error) {
        console.error('Error fetching schools:', error)
      }
    },
    viewSchool(school) {
      this.$router.push(`/schools/${school.id}`)
    },
    editSchool(school) {
      this.selectedSchool = { ...school }
      this.showEditModal = true
    },
    confirmDelete(school) {
      this.selectedSchool = school
      this.showDeleteModal = true
    },
    async deleteSchool() {
      try {
        await axios.delete(`/api/schools/${this.selectedSchool.id}`)
        this.schools = this.schools.filter(s => s.id !== this.selectedSchool.id)
        this.showDeleteModal = false
      } catch (error) {
        console.error('Error deleting school:', error)
      }
    },
    async handleSubmit(formData) {
      try {
        if (this.showEditModal) {
          await axios.put(`/api/schools/${this.selectedSchool.id}`, formData)
          const index = this.schools.findIndex(s => s.id === this.selectedSchool.id)
          this.schools[index] = { ...this.schools[index], ...formData }
        } else {
          const response = await axios.post('/api/schools', formData)
          this.schools.push(response.data)
        }
        this.closeModal()
      } catch (error) {
        console.error('Error saving school:', error)
      }
    },
    closeModal() {
      this.showCreateModal = false
      this.showEditModal = false
      this.selectedSchool = null
    }
  }
}
</script> 