<template>
  <nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
      <li v-for="(item, index) in items" :key="index" class="flex items-center">
        <template v-if="index > 0">
          <ChevronRightIcon class="h-5 w-5 text-text-secondary-variant mx-2" />
        </template>
        <a
          v-if="!item.disabled && item.href"
          :href="item.href"
          class="text-sm font-medium text-text-secondary-variant hover:text-text-primary dark:text-text-secondary dark:hover:text-text-on-card-dark transition-colors"
        >
          {{ item.title }}
        </a>
        <span
          v-else
          class="text-sm font-medium text-text-on-card dark:text-text-on-card-dark"
          :aria-current="item.disabled ? 'page' : undefined"
        >
          {{ item.title }}
        </span>
      </li>
    </ol>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { ChevronRightIcon } from '@heroicons/vue/24/outline'

const page = usePage()

const items = computed(() => {
  const crumbs = page?.props?.breadcrumbs ? page.props.breadcrumbs : []
  return crumbs.map((crumb) => {
    const url = crumb.href ? crumb.href : undefined
    return {
      title: crumb.title,
      disabled: (!!crumb.active) || !url,
      href: url,
      to: url,
    }
  })
})
</script>

