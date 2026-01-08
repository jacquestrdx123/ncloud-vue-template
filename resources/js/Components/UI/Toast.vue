<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="opacity-100 translate-y-0 sm:translate-x-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100 translate-y-0 sm:translate-x-0"
      leave-to-class="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2"
    >
      <div
        v-if="show"
        :class="[
          'fixed top-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg pointer-events-auto',
          colorClasses
        ]"
      >
        <div class="p-4">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <component v-if="iconComponent" :is="iconComponent" class="h-6 w-6" />
            </div>
            <div class="ml-3 w-0 flex-1">
              <p :class="textClasses">
                {{ message }}
              </p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
              <button
                @click="close"
                class="inline-flex text-text-secondary-variant hover:text-text-secondary focus:outline-none"
              >
                <XMarkIcon class="h-5 w-5" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue'
import { XMarkIcon, CheckCircleIcon, ExclamationTriangleIcon, InformationCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  message: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'info', // success, error, warning, info
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  duration: {
    type: Number,
    default: 5000 // milliseconds, 0 = don't auto-close
  }
})

const emit = defineEmits(['close', 'update:show'])

const iconComponent = computed(() => {
  const icons = {
    success: CheckCircleIcon,
    error: XCircleIcon,
    warning: ExclamationTriangleIcon,
    info: InformationCircleIcon
  }
  return icons[props.type] || InformationCircleIcon
})

const colorClasses = computed(() => {
  const classes = {
    success: 'bg-success-lighten-2 dark:bg-success-dark-darken-2/20 border border-success-lighten-1 dark:border-success-dark-darken-1',
    error: 'bg-error-lighten-2 dark:bg-error-dark-darken-2/20 border border-error-lighten-1 dark:border-error-dark-darken-1',
    warning: 'bg-warning-lighten-2 dark:bg-warning-dark-darken-2/20 border border-warning-lighten-1 dark:border-warning-dark-darken-1',
    info: 'bg-info-lighten-2 dark:bg-info-dark-darken-2/20 border border-info-lighten-1 dark:border-info-dark-darken-1'
  }
  return classes[props.type] || classes.info
})

const textClasses = computed(() => {
  const classes = {
    success: 'text-sm font-medium text-success-darken-2 dark:text-success-dark',
    error: 'text-sm font-medium text-error-darken-2 dark:text-error-dark',
    warning: 'text-sm font-medium text-warning-darken-2 dark:text-warning-dark',
    info: 'text-sm font-medium text-info-darken-2 dark:text-info-dark'
  }
  return classes[props.type] || classes.info
})

let timeoutId = null

const close = () => {
  emit('close')
  emit('update:show', false)
}

onMounted(() => {
  if (props.duration > 0) {
    timeoutId = setTimeout(() => {
      close()
    }, props.duration)
  }
})

onUnmounted(() => {
  if (timeoutId) {
    clearTimeout(timeoutId)
  }
})
</script>

