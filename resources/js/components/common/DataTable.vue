<template>
  <div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
      <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  v-for="column in columns"
                  :key="column.key"
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  {{ column.label }}
                </th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(item, index) in items" :key="index">
                <td
                  v-for="column in columns"
                  :key="column.key"
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                >
                  <slot :name="column.key" :item="item">
                    {{ item[column.key] }}
                  </slot>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <slot name="actions" :item="item">
                    <button
                      v-if="showView"
                      @click="$emit('view', item)"
                      class="text-indigo-600 hover:text-indigo-900 mr-4"
                    >
                      View
                    </button>
                    <button
                      v-if="showEdit"
                      @click="$emit('edit', item)"
                      class="text-yellow-600 hover:text-yellow-900 mr-4"
                    >
                      Edit
                    </button>
                    <button
                      v-if="showDelete"
                      @click="$emit('delete', item)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Delete
                    </button>
                  </slot>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DataTable',
  props: {
    columns: {
      type: Array,
      required: true,
      validator: (value) => {
        return value.every(column => 'key' in column && 'label' in column)
      }
    },
    items: {
      type: Array,
      required: true
    },
    showView: {
      type: Boolean,
      default: true
    },
    showEdit: {
      type: Boolean,
      default: true
    },
    showDelete: {
      type: Boolean,
      default: true
    }
  }
}
</script> 