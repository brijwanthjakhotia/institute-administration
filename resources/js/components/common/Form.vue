<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <div v-for="field in fields" :key="field.name">
      <label :for="field.name" class="block text-sm font-medium text-gray-700">
        {{ field.label }}
      </label>
      <div class="mt-1">
        <input
          v-if="field.type === 'text' || field.type === 'email' || field.type === 'number'"
          :type="field.type"
          :name="field.name"
          :id="field.name"
          v-model="formData[field.name]"
          :required="field.required"
          :placeholder="field.placeholder"
          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
          :class="{ 'border-red-300': errors[field.name] }"
        />
        <select
          v-else-if="field.type === 'select'"
          :name="field.name"
          :id="field.name"
          v-model="formData[field.name]"
          :required="field.required"
          class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          :class="{ 'border-red-300': errors[field.name] }"
        >
          <option value="">Select {{ field.label }}</option>
          <option
            v-for="option in field.options"
            :key="option.value"
            :value="option.value"
          >
            {{ option.label }}
          </option>
        </select>
        <textarea
          v-else-if="field.type === 'textarea'"
          :name="field.name"
          :id="field.name"
          v-model="formData[field.name]"
          :required="field.required"
          :placeholder="field.placeholder"
          rows="3"
          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
          :class="{ 'border-red-300': errors[field.name] }"
        ></textarea>
      </div>
      <p v-if="errors[field.name]" class="mt-2 text-sm text-red-600">
        {{ errors[field.name] }}
      </p>
    </div>

    <div class="flex justify-end space-x-3">
      <button
        type="button"
        @click="$emit('cancel')"
        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        Cancel
      </button>
      <button
        type="submit"
        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        {{ submitLabel }}
      </button>
    </div>
  </form>
</template>

<script>
export default {
  name: 'Form',
  props: {
    fields: {
      type: Array,
      required: true,
      validator: (value) => {
        return value.every(field => 
          'name' in field && 
          'label' in field && 
          'type' in field
        )
      }
    },
    initialData: {
      type: Object,
      default: () => ({})
    },
    submitLabel: {
      type: String,
      default: 'Submit'
    }
  },
  data() {
    return {
      formData: { ...this.initialData },
      errors: {}
    }
  },
  methods: {
    validate() {
      this.errors = {}
      let isValid = true

      this.fields.forEach(field => {
        if (field.required && !this.formData[field.name]) {
          this.errors[field.name] = `${field.label} is required`
          isValid = false
        }

        if (field.type === 'email' && this.formData[field.name]) {
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
          if (!emailRegex.test(this.formData[field.name])) {
            this.errors[field.name] = 'Please enter a valid email address'
            isValid = false
          }
        }

        if (field.type === 'number' && this.formData[field.name]) {
          if (isNaN(this.formData[field.name])) {
            this.errors[field.name] = 'Please enter a valid number'
            isValid = false
          }
        }
      })

      return isValid
    },
    handleSubmit() {
      if (this.validate()) {
        this.$emit('submit', this.formData)
      }
    }
  }
}
</script> 