<template>
  <Toast
    v-if="snackbarStore.show"
    :show="snackbarStore.show"
    :message="snackbarStore.text"
    :type="snackbarStore.color"
    :duration="snackbarStore.timeout"
    @update:show="snackbarStore.hide()"
    @close="snackbarStore.hide()"
  />
</template>

<script setup>
import { watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useSnackbarStore } from '@/stores/snackbarStore'
import Toast from '@/Components/UI/Toast.vue'

const snackbarStore = useSnackbarStore()
const page = usePage()

watch(() => page.props?.flash, (flash) => {
  if (flash?.success) {
    snackbarStore.success(flash.success)
  }
  if (flash?.error) {
    snackbarStore.error(flash.error)
  }
  if (flash?.warning) {
    snackbarStore.warning(flash.warning)
  }
  if (flash?.info) {
    snackbarStore.info(flash.info)
  }
}, { immediate: true })
</script>

