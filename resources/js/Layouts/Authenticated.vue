<template>
  <div class="min-h-screen bg-page dark:bg-page-dark">
    <!-- Sidebar Menu -->
    <SidebarMenu ref="sidebarMenuRef" />
    
    <!-- Main Layout -->
    <div :class="[
      'bg-white dark:bg-gray-900',
      'transition-all duration-300',
      isMobile ? 'ml-0' : (isSidebarCollapsed ? 'ml-16' : 'ml-[250px]')
    ]">
      <!-- Top App Bar -->
      <header class="header-gradient sticky top-0 z-40 shadow-lg border-b border-primary/20 dark:border-primary/30">
        <div class="flex items-center justify-between h-14 px-4">
          <div class="flex items-center gap-4">
            <!-- Mobile Menu Button -->
            <button
              v-if="isMobile"
              @click="openMobileMenu"
              class="p-2 rounded-md text-white hover:bg-primary-darken-1/30 transition-all duration-200 lg:hidden"
              aria-label="Open menu"
            >
              <Bars3Icon class="h-6 w-6" />
            </button>
            <h1 class="text-lg font-semibold text-white drop-shadow-sm">
              {{ title }}
            </h1>
          </div>

          <div class="flex items-center gap-2">
            <!-- Dark Mode Toggle -->
            <button
              @click="toggleDarkMode"
              class="p-2 rounded-md text-white hover:bg-primary-darken-1/30 transition-all duration-200 hover:scale-105"
              :aria-label="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
              :title="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
            >
              <SunIcon v-if="isDarkMode" class="h-6 w-6" />
              <MoonIcon v-else class="h-6 w-6" />
            </button>

            <!-- Notifications Button -->
            <button
              @click="onNotifications"
              class="relative p-2 rounded-md text-white hover:bg-primary-darken-1/30 transition-all duration-200 hover:scale-105"
              aria-label="Notifications"
            >
              <BellIcon class="h-6 w-6" />
              <span
                v-if="notificationCount > 0"
                class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full text-xs font-semibold bg-error text-white shadow-lg animate-pulse"
              >
                {{ notificationCount }}
              </span>
            </button>

            <!-- User Menu -->
            <TopRightMenu :show-navigation-links="true" />
          </div>

          <slot name="actions" />
        </div>
      </header>

      <!-- Main Content Area -->
      <main class="flex-1">
        <!-- Breadcrumb Bar -->
        <div class="px-4 py-2 bg-header dark:bg-header-dark border-b border-gray-200 dark:border-gray-700">
          <Breadcrumb />
        </div>

        <!-- Session Notifications -->
        <SessionNotification />
        
        <!-- Page Content -->
        <div class="p-4 bg-page dark:bg-page-dark">
          <slot/>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed, ref, onBeforeUnmount } from 'vue'
import { useAppStore } from '@/stores/appStore'
import { usePage } from '@inertiajs/vue3'
import { BellIcon, SunIcon, MoonIcon, Bars3Icon } from '@heroicons/vue/24/outline'
import SessionNotification from '@/Components/SessionNotification.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import SidebarMenu from '@/Components/SidebarMenu/SidebarMenu.vue'
import { useSidebar } from '@/Components/SidebarMenu/composables/useSidebar.js'
import TopRightMenu from '@/Components/UI/TopRightMenu.vue'

const props = defineProps({
  title: {
    type: String,
    default: 'Dashboard',
  },
})

const appStore = useAppStore()
const page = usePage()
const { isCollapsed: isSidebarCollapsed } = useSidebar()
const sidebarMenuRef = ref(null)
const isMobile = ref(false)

// Store is already initialized in app.js, but ensure theme is applied
// in case of navigation
onMounted(() => {
  // Just reapply theme to ensure it's correct after navigation
  // The store is already initialized in app.js
  appStore.applyTheme()
  
  // Check if mobile on mount
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', checkMobile)
})

const checkMobile = () => {
  isMobile.value = window.innerWidth < 1024
}

const isDarkMode = computed(() => appStore.isDarkMode)

const notificationCount = computed(() => {
  return 0
})

function toggleDarkMode() {
  appStore.toggleTheme()
}

function openMobileMenu() {
  if (sidebarMenuRef.value) {
    sidebarMenuRef.value.openMobileMenu()
  }
}

function onNotifications() {
  // Hook up when notifications page is available
}
</script>

<style scoped>
.header-gradient {
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-lighten-1) 50%, var(--color-primary) 100%);
  background-size: 200% 200%;
  animation: gradient-shift 8s ease infinite;
}

@keyframes gradient-shift {
  0%, 100% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
}
</style>

