import { defineStore } from 'pinia'
import axios from 'axios'

export const useAppStore = defineStore('app', {
    state: () => ({
        // Menu/Navigation state
        drawerOpen: false, // false = rail mode, true = full drawer
        drawerPinned: false, // whether drawer stays open
        
        // Theme state
        themeMode: 'system', // 'light', 'dark', or 'system'
        prefersDarkMode: false, // system preference
        
        // User preferences
        preferences: {
            compactMode: false,
            fontSize: 'medium', // 'small', 'medium', 'large'
            animations: true,
        },
        
        // App state
        loading: false,
        online: true,
    }),

    getters: {
        // Menu/Navigation getters
        isDrawerOpen: (state) => state.drawerOpen,
        isDrawerPinned: (state) => state.drawerPinned,
        isRailMode: (state) => !state.drawerOpen,
        
        // Theme getters
        currentTheme: (state) => {
            if (state.themeMode === 'system') {
                return state.prefersDarkMode ? 'dark' : 'light'
            }
            return state.themeMode
        },
        
        isDarkMode: (state) => {
            if (state.themeMode === 'system') {
                return state.prefersDarkMode
            }
            return state.themeMode === 'dark'
        },
        
        isLightMode: (state) => {
            if (state.themeMode === 'system') {
                return !state.prefersDarkMode
            }
            return state.themeMode === 'light'
        },
        
        isSystemMode: (state) => state.themeMode === 'system',
        
        // Preference getters
        isCompactMode: (state) => state.preferences.compactMode,
        currentFontSize: (state) => state.preferences.fontSize,
        animationsEnabled: (state) => state.preferences.animations,
        
        // App state getters
        isLoading: (state) => state.loading,
        isOnline: (state) => state.online,
    },

    actions: {
        // Initialize store from Inertia props and localStorage
        initialize(inertiaProps = null) {
            // Track if server provided theme_mode
            let serverProvidedThemeMode = false
            
            // Load from Inertia props first (server-side data)
            if (inertiaProps) {
                serverProvidedThemeMode = this.loadFromInertiaProps(inertiaProps)
            }
            
            // Load from localStorage (fallback/client preferences)
            // Only restore themeMode from localStorage if server didn't provide it
            this.loadFromStorage(serverProvidedThemeMode)
            
            this.detectSystemTheme()
            this.watchSystemTheme()
            this.watchOnlineStatus()
            this.applyTheme()
        },
        
        // Load theme data from Inertia props
        // Returns true if theme_mode was provided by server
        loadFromInertiaProps(props) {
            let serverProvidedThemeMode = false
            
            // Load user theme mode
            if (props.auth?.user?.theme_mode && ['light', 'dark', 'system'].includes(props.auth.user.theme_mode)) {
                this.themeMode = props.auth.user.theme_mode
                serverProvidedThemeMode = true
            }
            
            return serverProvidedThemeMode
        },
        
        // Menu/Navigation actions
        toggleDrawer() {
            this.drawerOpen = !this.drawerOpen
            this.saveToStorage()
        },
        
        openDrawer() {
            this.drawerOpen = true
            this.saveToStorage()
        },
        
        closeDrawer() {
            this.drawerOpen = false
            this.saveToStorage()
        },
        
        setDrawerOpen(value) {
            this.drawerOpen = Boolean(value)
            this.saveToStorage()
        },
        
        toggleDrawerPin() {
            this.drawerPinned = !this.drawerPinned
            this.saveToStorage()
        },
        
        pinDrawer() {
            this.drawerPinned = true
            this.saveToStorage()
        },
        
        unpinDrawer() {
            this.drawerPinned = false
            this.saveToStorage()
        },
        
        // Theme actions
        async setTheme(mode) {
            if (['light', 'dark', 'system'].includes(mode)) {
                this.themeMode = mode
                this.applyTheme()
                this.saveToStorage()
                
                // Save to backend
                try {
                    await axios.put('/ajax-api/theme/user', {
                        theme_mode: mode
                    })
                } catch (error) {
                    console.warn('Could not save theme mode to backend:', error)
                }
            }
        },
        
        toggleTheme() {
            if (this.themeMode === 'system') {
                this.setTheme(this.prefersDarkMode ? 'light' : 'dark')
            } else if (this.themeMode === 'light') {
                this.setTheme('dark')
            } else {
                this.setTheme('light')
            }
        },
        
        setLightTheme() {
            this.setTheme('light')
        },
        
        setDarkTheme() {
            this.setTheme('dark')
        },
        
        setSystemTheme() {
            this.setTheme('system')
        },
        
        applyTheme() {
            // Apply theme to document
            if (typeof window !== 'undefined') {
                const targetTheme = this.currentTheme
                const root = document.documentElement
                
                // Debug logging
                console.log('[Theme] Applying theme:', {
                    themeMode: this.themeMode,
                    prefersDarkMode: this.prefersDarkMode,
                    currentTheme: targetTheme,
                    htmlHasDark: root.classList.contains('dark')
                })
                
                // Force remove dark class first to ensure it's gone
                root.classList.remove('dark')
                
                // Update document class for Tailwind dark mode
                if (targetTheme === 'dark') {
                    root.classList.add('dark')
                    console.log('[Theme] Added dark class to documentElement')
                } else {
                    // Double-check it's removed
                    root.classList.remove('dark')
                    console.log('[Theme] Removed dark class from documentElement. Current classes:', root.className)
                }
                
                // Also add theme class for CSS targeting
                root.classList.remove('light-theme', 'dark-theme')
                root.classList.add(`${targetTheme}-theme`)
                
                // Verify after a short delay
                setTimeout(() => {
                    const hasDark = root.classList.contains('dark')
                    console.log('[Theme] Verification - HTML has dark class:', hasDark, 'Expected:', targetTheme === 'dark')
                }, 100)
            }
        },
        
        // Preference actions
        setCompactMode(value) {
            this.preferences.compactMode = Boolean(value)
            this.saveToStorage()
        },
        
        toggleCompactMode() {
            this.preferences.compactMode = !this.preferences.compactMode
            this.saveToStorage()
        },
        
        setFontSize(size) {
            if (['small', 'medium', 'large'].includes(size)) {
                this.preferences.fontSize = size
                this.saveToStorage()
            }
        },
        
        setAnimations(value) {
            this.preferences.animations = Boolean(value)
            this.saveToStorage()
        },
        
        toggleAnimations() {
            this.preferences.animations = !this.preferences.animations
            this.saveToStorage()
        },
        
        // App state actions
        setLoading(value) {
            this.loading = Boolean(value)
        },
        
        setOnline(value) {
            this.online = Boolean(value)
        },
        
        // System detection and watchers
        detectSystemTheme() {
            if (typeof window !== 'undefined' && window.matchMedia) {
                this.prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches
            }
        },
        
        watchSystemTheme() {
            if (typeof window !== 'undefined' && window.matchMedia) {
                const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
                
                // Store the handler so we can remove it if needed
                const handler = (e) => {
                    this.prefersDarkMode = e.matches
                    // Only apply theme if we're in system mode
                    if (this.themeMode === 'system') {
                        this.applyTheme()
                    }
                }
                
                // Modern browsers
                if (mediaQuery.addEventListener) {
                    mediaQuery.addEventListener('change', handler)
                } else if (mediaQuery.addListener) {
                    // Legacy browsers
                    mediaQuery.addListener(handler)
                }
            }
        },
        
        watchOnlineStatus() {
            if (typeof window !== 'undefined') {
                window.addEventListener('online', () => this.setOnline(true))
                window.addEventListener('offline', () => this.setOnline(false))
                this.online = navigator.onLine
            }
        },
        
        // LocalStorage persistence
        saveToStorage() {
            if (typeof window !== 'undefined') {
                try {
                    const data = {
                        drawerOpen: this.drawerOpen,
                        drawerPinned: this.drawerPinned,
                        themeMode: this.themeMode,
                        preferences: this.preferences,
                    }
                    localStorage.setItem('app_store', JSON.stringify(data))
                } catch (error) {
                    console.warn('Could not save to localStorage:', error)
                }
            }
        },
        
        loadFromStorage(skipThemeMode = false) {
            if (typeof window !== 'undefined') {
                try {
                    const data = localStorage.getItem('app_store')
                    if (data) {
                        const parsed = JSON.parse(data)
                        
                        // Restore state
                        if (typeof parsed.drawerOpen === 'boolean') {
                            this.drawerOpen = parsed.drawerOpen
                        }
                        if (typeof parsed.drawerPinned === 'boolean') {
                            this.drawerPinned = parsed.drawerPinned
                        }
                        // Only restore themeMode from localStorage if server didn't provide it
                        if (!skipThemeMode && ['light', 'dark', 'system'].includes(parsed.themeMode)) {
                            this.themeMode = parsed.themeMode
                        }
                        if (parsed.preferences && typeof parsed.preferences === 'object') {
                            this.preferences = {
                                ...this.preferences,
                                ...parsed.preferences
                            }
                        }
                    }
                } catch (error) {
                    console.warn('Could not load from localStorage:', error)
                }
            }
        },
        
        clearStorage() {
            if (typeof window !== 'undefined') {
                try {
                    localStorage.removeItem('app_store')
                } catch (error) {
                    console.warn('Could not clear localStorage:', error)
                }
            }
        },
        
        // Reset to defaults
        reset() {
            this.drawerOpen = false
            this.drawerPinned = false
            this.themeMode = 'system'
            this.preferences = {
                compactMode: false,
                fontSize: 'medium',
                animations: true,
            }
            this.clearStorage()
            this.applyTheme()
        },
    }
})
