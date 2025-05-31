<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <h1 class="text-xl font-bold text-gray-800">School Management</h1>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <router-link
                v-for="item in navigation"
                :key="item.name"
                :to="item.href"
                :class="[
                  $route.path === item.href
                    ? 'border-indigo-500 text-gray-900'
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                  'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                ]"
              >
                {{ item.name }}
              </router-link>
            </div>
          </div>
          <div class="hidden sm:ml-6 sm:flex sm:items-center">
            <div class="ml-3 relative">
              <div>
                <button
                  @click="isProfileMenuOpen = !isProfileMenuOpen"
                  class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <span class="sr-only">Open user menu</span>
                  <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white">
                    {{ userInitials }}
                  </div>
                </button>
              </div>
              <div
                v-if="isProfileMenuOpen"
                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
              >
                <a
                  href="#"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click="logout"
                >
                  Sign out
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <div class="py-10">
      <header>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h1 class="text-3xl font-bold leading-tight text-gray-900">
            {{ title }}
          </h1>
        </div>
      </header>
      <main>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <slot></slot>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AppLayout',
  props: {
    title: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      isProfileMenuOpen: false,
      navigation: [
        { name: 'Dashboard', href: '/dashboard' },
        { name: 'Schools', href: '/schools' },
        { name: 'Classes', href: '/classrooms' },
        { name: 'Students', href: '/students' },
        { name: 'Teachers', href: '/teachers' },
        { name: 'Subjects', href: '/subjects' },
        { name: 'Attendance', href: '/attendance' },
        { name: 'Grades', href: '/grades' },
        { name: 'Parents', href: '/parents' }
      ]
    }
  },
  computed: {
    userInitials() {
      // Replace with actual user initials
      return 'AD'
    }
  },
  methods: {
    logout() {
      // Implement logout logic
      this.$router.push('/login')
    }
  }
}
</script> 