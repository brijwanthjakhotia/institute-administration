<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Payments Management</h1>
      <button
        @click="showCreateModal = true"
        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg"
      >
        Record New Payment
      </button>
    </div>

    <DataTable
      :headers="headers"
      :items="payments"
      :loading="loading"
      @view="viewPayment"
      @edit="editPayment"
      @delete="confirmDelete"
      @generate-receipt="generateReceipt"
    />

    <!-- Create/Edit Modal -->
    <Modal v-model="showCreateModal" title="Record New Payment">
      <form @submit.prevent="handleSubmit">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Student</label>
            <select
              v-model="form.student_id"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            >
              <option v-for="student in students" :key="student.id" :value="student.id">
                {{ student.first_name }} {{ student.last_name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Fee</label>
            <select
              v-model="form.fee_id"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            >
              <option v-for="fee in activeFees" :key="fee.id" :value="fee.id">
                {{ fee.name }} - {{ fee.amount }}
              </option>
            </select>
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
            <label class="block text-sm font-medium text-gray-700">Payment Date</label>
            <input
              v-model="form.payment_date"
              type="date"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Payment Method</label>
            <select
              v-model="form.payment_method"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            >
              <option value="cash">Cash</option>
              <option value="card">Card</option>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="check">Check</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Transaction ID</label>
            <input
              v-model="form.transaction_id"
              type="text"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Notes</label>
            <textarea
              v-model="form.notes"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              rows="3"
            ></textarea>
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
            {{ isEditing ? 'Update' : 'Record' }}
          </button>
        </div>
      </form>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal v-model="showDeleteModal" title="Confirm Delete">
      <p class="text-gray-700">
        Are you sure you want to delete this payment record? This action cannot be undone.
      </p>
      <div class="mt-6 flex justify-end space-x-3">
        <button
          @click="showDeleteModal = false"
          class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
        >
          Cancel
        </button>
        <button
          @click="deletePayment"
          class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600"
        >
          Delete
        </button>
      </div>
    </Modal>

    <!-- Receipt Modal -->
    <Modal v-model="showReceiptModal" title="Payment Receipt">
      <div v-if="selectedPayment" class="space-y-4">
        <div class="border-b pb-4">
          <h3 class="text-lg font-semibold">Payment Details</h3>
          <p class="text-sm text-gray-600">Receipt #: {{ selectedPayment.id }}</p>
          <p class="text-sm text-gray-600">Date: {{ formatDate(selectedPayment.payment_date) }}</p>
        </div>

        <div class="border-b pb-4">
          <h3 class="text-lg font-semibold">Student Information</h3>
          <p class="text-sm text-gray-600">
            Name: {{ selectedPayment.student?.first_name }} {{ selectedPayment.student?.last_name }}
          </p>
          <p class="text-sm text-gray-600">
            Class: {{ selectedPayment.student?.class_room?.name }}
          </p>
        </div>

        <div class="border-b pb-4">
          <h3 class="text-lg font-semibold">Fee Information</h3>
          <p class="text-sm text-gray-600">Fee: {{ selectedPayment.fee?.name }}</p>
          <p class="text-sm text-gray-600">Amount: {{ formatCurrency(selectedPayment.amount) }}</p>
          <p class="text-sm text-gray-600">Payment Method: {{ formatPaymentMethod(selectedPayment.payment_method) }}</p>
          <p v-if="selectedPayment.transaction_id" class="text-sm text-gray-600">
            Transaction ID: {{ selectedPayment.transaction_id }}
          </p>
        </div>

        <div class="flex justify-end">
          <button
            @click="printReceipt"
            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
          >
            Print Receipt
          </button>
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
    const payments = ref([])
    const students = ref([])
    const activeFees = ref([])
    const loading = ref(false)
    const showCreateModal = ref(false)
    const showDeleteModal = ref(false)
    const showReceiptModal = ref(false)
    const selectedPayment = ref(null)
    const isEditing = ref(false)

    const form = ref({
      student_id: '',
      fee_id: '',
      amount: '',
      payment_date: '',
      payment_method: 'cash',
      transaction_id: '',
      notes: ''
    })

    const headers = [
      { key: 'student_name', label: 'Student' },
      { key: 'fee_name', label: 'Fee' },
      { key: 'amount', label: 'Amount' },
      { key: 'payment_date', label: 'Date' },
      { key: 'payment_method', label: 'Method' },
      { key: 'actions', label: 'Actions' }
    ]

    const fetchPayments = async () => {
      loading.value = true
      try {
        const response = await axios.get('/api/payments')
        payments.value = response.data
      } catch (error) {
        console.error('Error fetching payments:', error)
      }
      loading.value = false
    }

    const fetchStudents = async () => {
      try {
        const response = await axios.get('/api/students')
        students.value = response.data
      } catch (error) {
        console.error('Error fetching students:', error)
      }
    }

    const fetchActiveFees = async () => {
      try {
        const response = await axios.get('/api/fees?status=active')
        activeFees.value = response.data
      } catch (error) {
        console.error('Error fetching active fees:', error)
      }
    }

    const resetForm = () => {
      form.value = {
        student_id: '',
        fee_id: '',
        amount: '',
        payment_date: '',
        payment_method: 'cash',
        transaction_id: '',
        notes: ''
      }
    }

    const viewPayment = (payment) => {
      selectedPayment.value = payment
      showReceiptModal.value = true
    }

    const editPayment = (payment) => {
      isEditing.value = true
      selectedPayment.value = payment
      form.value = { ...payment }
      showCreateModal.value = true
    }

    const confirmDelete = (payment) => {
      selectedPayment.value = payment
      showDeleteModal.value = true
    }

    const deletePayment = async () => {
      try {
        await axios.delete(`/api/payments/${selectedPayment.value.id}`)
        await fetchPayments()
        showDeleteModal.value = false
      } catch (error) {
        console.error('Error deleting payment:', error)
      }
    }

    const generateReceipt = (payment) => {
      selectedPayment.value = payment
      showReceiptModal.value = true
    }

    const handleSubmit = async () => {
      try {
        if (isEditing.value) {
          await axios.put(`/api/payments/${selectedPayment.value.id}`, form.value)
        } else {
          await axios.post('/api/payments', form.value)
        }
        await fetchPayments()
        closeModal()
      } catch (error) {
        console.error('Error saving payment:', error)
      }
    }

    const closeModal = () => {
      showCreateModal.value = false
      showReceiptModal.value = false
      isEditing.value = false
      selectedPayment.value = null
      resetForm()
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString()
    }

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
      }).format(amount)
    }

    const formatPaymentMethod = (method) => {
      return method.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
    }

    const printReceipt = () => {
      window.print()
    }

    onMounted(() => {
      fetchPayments()
      fetchStudents()
      fetchActiveFees()
    })

    return {
      payments,
      students,
      activeFees,
      loading,
      showCreateModal,
      showDeleteModal,
      showReceiptModal,
      selectedPayment,
      isEditing,
      form,
      headers,
      viewPayment,
      editPayment,
      confirmDelete,
      deletePayment,
      generateReceipt,
      handleSubmit,
      closeModal,
      formatDate,
      formatCurrency,
      formatPaymentMethod,
      printReceipt
    }
  }
}
</script>

<style>
@media print {
  body * {
    visibility: hidden;
  }
  .modal-content,
  .modal-content * {
    visibility: visible;
  }
  .modal-content {
    position: absolute;
    left: 0;
    top: 0;
  }
}
</style> 