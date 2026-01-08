<template>
  <div v-if="favoriteItems.length > 0 && !isCollapsed" class="p-2 border-b border-gray-200 dark:border-gray-700">
    <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
      Favorites
    </div>
    <ul class="space-y-1">
      <li v-for="(item, index) in favoriteItems" :key="index">
        <a
          @click="handleClick(item)"
          :href="getItemUrl(item)"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-all duration-200',
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
          <span class="truncate">{{ item.label }}</span>
        </a>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { getIconComponent } from '@/utils/iconMapper'

const props = defineProps({
  isCollapsed: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['select'])

const page = usePage()

const favoriteItems = computed(() => {
  const favorites = page?.props?.favoriteMenuItems
  if (!favorites || !Array.isArray(favorites)) {
    return []
  }
  return favorites.sort((a, b) => (a.order || 0) - (b.order || 0))
})

const getItemUrl = (item) => {
  // Prioritize route over URL
  if (item.route && item.route !== '#' && typeof route === 'function') {
    try {
      return route(item.route)
    } catch (e) {
      // If route doesn't exist, fall back to URL
      return item.url || '#'
    }
  }
  return item.url || '#'
}

const handleClick = (item) => {
  emit('select', item)
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
</style>
