<template>
  <div>
    <button
      v-if="item.children || (!hasRoute && !hasValidUrl)"
      @click="toggleExpanded"
      :class="[
        'w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-all duration-200',
        isCollapsed ? 'justify-center' : 'justify-between',
        item.active
          ? 'menu-item-active text-white shadow-md'
          : 'text-gray-900 dark:text-gray-100 hover:bg-primary/10 dark:hover:bg-primary/20 hover:text-primary dark:hover:text-primary-lighten-1',
      ]"
    >
      <div class="flex items-center gap-3 flex-1 min-w-0">
        <component 
          v-if="item.icon" 
          :is="getIconComponent(item.icon)" 
          class="h-5 w-5 flex-shrink-0"
        />
        <span v-if="!isCollapsed" class="truncate">{{ item.label }}</span>
      </div>
      <ChevronDownIcon
        v-if="!isCollapsed && item.children"
        class="h-4 w-4 flex-shrink-0 transition-transform"
        :class="{ 'rotate-180': isExpanded }"
      />
    </button>

    <a
      v-else
      @click="handleClick"
      :href="itemUrl"
      :class="[
        'w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-all duration-200',
        isCollapsed ? 'justify-center' : '',
        item.active
          ? 'menu-item-active text-white shadow-md'
          : 'text-gray-900 dark:text-gray-100 hover:bg-primary/10 dark:hover:bg-primary/20 hover:text-primary dark:hover:text-primary-lighten-1',
      ]"
    >
      <component 
        v-if="item.icon" 
        :is="getIconComponent(item.icon)" 
        class="h-5 w-5 flex-shrink-0"
      />
      <span v-if="!isCollapsed" class="truncate">{{ item.label }}</span>
    </a>

    <Transition name="slide">
      <ul
        v-if="item.children && isExpanded && !isCollapsed"
        class="ml-4 mt-1 space-y-1 border-l border-gray-200 dark:border-gray-700 pl-2"
      >
        <li v-for="(child, childKey) in item.children" :key="childKey">
          <MenuItem
            :item="{ ...child, key: childKey }"
            :level="level + 1"
            :is-collapsed="isCollapsed"
            @select="$emit('select', $event)"
          />
        </li>
      </ul>
    </Transition>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { ChevronDownIcon } from '@heroicons/vue/24/outline'
import { getIconComponent } from '@/utils/iconMapper'

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
  level: {
    type: Number,
    default: 1,
  },
  isCollapsed: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['select'])

const isExpanded = ref(props.item.active || false)

// Check if item has a route property (don't validate if it exists - that would throw)
const hasRoute = computed(() => {
  const result = props.item.route && props.item.route !== '#' && typeof route === 'function'
  // console.log('[MenuItem Debug] hasRoute check:', {
  //   'item.route': props.item.route,
  //   'route !== #': props.item.route !== '#',
  //   'typeof route': typeof route,
  //   'route is function': typeof route === 'function',
  //   'result': result
  // })
  return result
})

// Check if item has a valid URL
const hasValidUrl = computed(() => {
  const result = props.item.url && props.item.url !== '#'
  // console.log('[MenuItem Debug] hasValidUrl check:', {
  //   'item.url': props.item.url,
  //   'url !== #': props.item.url !== '#',
  //   'result': result
  // })
  return result
})

// Get the URL to use - prioritize route over url
// This safely handles routes that might not exist
const itemUrl = computed(() => {
  // console.log('[MenuItem Debug] itemUrl computed - starting:', {
  //   'item.route': props.item.route,
  //   'item.url': props.item.url,
  //   'hasRoute.value': hasRoute.value,
  //   'hasValidUrl.value': hasValidUrl.value
  // })
  
  // Try route first if it exists
  if (hasRoute.value) {
    try {
      // console.log('[MenuItem Debug] Attempting to resolve route:', props.item.route)
      const routeUrl = route(props.item.route)
      // console.log('[MenuItem Debug] Route resolved successfully:', routeUrl)
      if (routeUrl) {
        return routeUrl
      }
    } catch (e) {
      // Route doesn't exist in Ziggy, fall back to URL
      console.log('[MenuItem Debug] Route resolution failed:', {
        'route': props.item.route,
        'error': e.message,
        'falling back to URL': props.item.url
      })
    }
  } else {
    console.log('[MenuItem Debug] hasRoute is false, skipping route resolution')
  }
  
  // Fall back to URL if route doesn't work
  const finalUrl = props.item.url || '#'
  console.log('[MenuItem Debug] Final URL:', finalUrl)
  return finalUrl
})

// Note: We don't validate if route actually exists in Ziggy here to avoid
// throwing errors during render. The itemUrl computed will handle that safely.

// Auto-expand if item has active child or is active
watch(
  () => props.item.active,
  (active) => {
    if (active) {
      isExpanded.value = true
    }
  },
  { immediate: true }
)

// Auto-expand during search if forceExpanded is set
watch(
  () => props.item.forceExpanded,
  (forceExpanded) => {
    if (forceExpanded) {
      isExpanded.value = true
    }
  }
)

const toggleExpanded = () => {
  if (props.item.children) {
    isExpanded.value = !isExpanded.value
  } else if (hasRoute.value || hasValidUrl.value) {
    handleClick()
  }
}

const handleClick = () => {
  console.log('[MenuItem Debug] handleClick called:', {
    'item': props.item,
    'item.route': props.item.route,
    'item.url': props.item.url,
    'itemUrl.value': itemUrl.value
  })
  emit('select', props.item)
}
</script>

<style scoped>
.menu-item-active {
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-lighten-1) 50%, var(--color-primary-darken-1) 100%);
  position: relative;
}

.menu-item-active::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 3px;
  background: var(--color-primary-lighten-2);
  border-radius: 0 3px 3px 0;
}

.menu-item-active:hover {
  background: linear-gradient(135deg, var(--color-primary-darken-1) 0%, var(--color-primary) 50%, var(--color-primary-lighten-1) 100%);
  transform: translateX(2px);
}

.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
  max-height: 1000px;
  overflow: hidden;
}

.slide-enter-from,
.slide-leave-to {
  max-height: 0;
  opacity: 0;
}

</style>
