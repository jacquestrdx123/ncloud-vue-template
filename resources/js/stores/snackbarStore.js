import { defineStore } from 'pinia'

export const useSnackbarStore = defineStore('snackbar', {
    state: () => ({
        show: false,
        text: '',
        color: 'success',
        timeout: 4000
    }),

    getters: {
        isVisible: (state) => state.show,
        message: (state) => state.text,
        variant: (state) => state.color
    },

    actions: {
        showSnackbar(text, color = 'success', timeout = 4000) {
            this.text = text
            this.color = color
            this.timeout = timeout
            this.show = true
        },

        success(text, timeout = 4000) {
            this.showSnackbar(text, 'success', timeout)
        },

        error(text, timeout = 4000) {
            this.showSnackbar(text, 'error', timeout)
        },

        warning(text, timeout = 4000) {
            this.showSnackbar(text, 'warning', timeout)
        },

        info(text, timeout = 4000) {
            this.showSnackbar(text, 'info', timeout)
        },

        hide() {
            this.show = false
        },

        clear() {
            this.show = false
            this.text = ''
            this.color = 'success'
            this.timeout = 4000
        }
    }
})
