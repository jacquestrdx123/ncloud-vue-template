<template>
  <div class="relative" ref="userMenuRef">
    <button
      @click="toggleUserMenu"
      class="flex items-center gap-2 px-3 py-2 rounded-md bg-hover-variant text-text-on-card dark:text-text-on-card-dark hover:bg-gradient-to-r hover:from-primary/10 hover:to-primary/20 dark:hover:from-primary/20 dark:hover:to-primary/30 transition-all duration-200 hover:scale-105 hover:border hover:border-primary/30"
    >
      <UserCircleIcon class="h-6 w-6" />
      <span>{{ currentUser?.name || 'Account' }}</span>
    </button>
    <div
      v-if="userMenuOpen"
      class="absolute right-0 mt-2 w-64 bg-elevated dark:bg-elevated-dark rounded-lg shadow-xl border border-primary/20 dark:border-primary/30 z-50 overflow-hidden backdrop-blur-sm"
    >
      <div class="py-1">
        <div class="px-4 py-3 border-b border-primary/20 dark:border-primary/30 bg-gradient-to-r from-primary/5 to-transparent dark:from-primary/10 dark:to-transparent">
          <p class="text-sm font-medium text-text-on-card dark:text-text-on-card-dark">{{ currentUser?.name || 'Account' }}</p>
          <p class="text-sm text-text-primary-light dark:text-text-secondary mt-0.5">{{ currentUser?.email || '' }}</p>
        </div>
        <button
          v-if="showNavigationLinks"
          @click="goToDashboard"
          class="w-full text-left px-4 py-2 text-sm text-text-on-card dark:text-text-on-card-dark hover:bg-gradient-to-r hover:from-primary/10 hover:to-primary/20 dark:hover:from-primary/20 dark:hover:to-primary/30 hover:text-primary dark:hover:text-primary-lighten-1 transition-all duration-200"
        >
          Dashboard
        </button>
        <button
          v-if="showNavigationLinks"
          @click="goToCustomers"
          class="w-full text-left px-4 py-2 text-sm text-text-on-card dark:text-text-on-card-dark hover:bg-gradient-to-r hover:from-primary/10 hover:to-primary/20 dark:hover:from-primary/20 dark:hover:to-primary/30 hover:text-primary dark:hover:text-primary-lighten-1 transition-all duration-200"
        >
          Users
        </button>
        <button
          v-if="themeRoute"
          @click="goToThemeManagement"
          class="w-full text-left px-4 py-2 text-sm text-text-on-card dark:text-text-on-card-dark hover:bg-gradient-to-r hover:from-primary/10 hover:to-primary/20 dark:hover:from-primary/20 dark:hover:to-primary/30 hover:text-primary dark:hover:text-primary-lighten-1 transition-all duration-200"
        >
          Theme
        </button>
        <div class="border-t border-primary/20 dark:border-primary/30 my-1"></div>
        <button
          @click="handleLogout"
          class="w-full text-left px-4 py-2 text-sm text-error dark:text-error-lighten-1 hover:bg-gradient-to-r hover:from-error/10 hover:to-error/20 dark:hover:from-error/20 dark:hover:to-error/30 transition-all duration-200 font-medium"
        >
          Logout
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { UserCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  themeRoute: {
    type: String,
    default: null,
  },
  logoutRoute: {
    type: String,
    default: 'web.logout',
  },
  logoutMethod: {
    type: String,
    default: 'post',
  },
  showNavigationLinks: {
    type: Boolean,
    default: false,
  },
})

const page = usePage()
const userMenuRef = ref(null)
const userMenuOpen = ref(false)

const currentUser = computed(() => {
  return page?.props?.auth?.user || null
})

function toggleUserMenu() {
  userMenuOpen.value = !userMenuOpen.value
}

function handleClickOutside(event) {
  if (userMenuRef.value && !userMenuRef.value.contains(event.target)) {
    userMenuOpen.value = false
  }
}

function goToDashboard() {
  const url = route('dashboard')
  if (url) {
    router.visit(url)
    userMenuOpen.value = false
  }
}

function goToCustomers() {
  const url = route('users.index')
  if (url) {
    router.visit(url)
    userMenuOpen.value = false
  }
}

function goToThemeManagement() {
  if (props.themeRoute) {
    const url = route(props.themeRoute)
    if (url) {
      router.visit(url)
      userMenuOpen.value = false
    }
  } else {
    userMenuOpen.value = false
  }
}

function handleLogout() {
  const url = route(props.logoutRoute)
  if (url) {
    if (props.logoutMethod === 'post') {
      router.post(url)
    } else {
      router.visit(url)
    }
    userMenuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

