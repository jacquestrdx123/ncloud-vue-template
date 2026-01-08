# Layout and Sidebar Implementation

This document outlines the implementation of the layout system and sidebar menu copied from `ncloudrad` and adapted for the current project.

## Overview

The layout system includes an Authenticated layout with sidebar menu, breadcrumbs, session notifications, and supporting components. All components have been updated to use CSS custom properties for consistent theming in both light and dark modes.

## Files Created

### Layout Files

1. **`resources/js/Layouts/Authenticated.vue`**

    - Main authenticated layout with sidebar, header, breadcrumbs, and content area
    - Includes dark mode toggle button
    - Mobile-responsive with hamburger menu button
    - Uses CSS custom properties for theming

2. **`resources/js/Layouts/Guest.vue`**
    - Simple guest layout for unauthenticated pages
    - Centered content layout

### Supporting Components

3. **`resources/js/Components/Breadcrumb.vue`**

    - Breadcrumb navigation component
    - Reads breadcrumbs from Inertia shared props
    - Supports active/inactive states

4. **`resources/js/Components/SessionNotification.vue`**

    - Flash message notification handler
    - Integrates with snackbar store
    - Displays success, error, warning, and info messages

5. **`resources/js/Components/UI/Toast.vue`**

    - Toast notification component
    - Supports multiple types (success, error, warning, info)
    - Auto-dismiss with configurable duration
    - Teleported to body for proper z-index

6. **`resources/js/Components/UI/TopRightMenu.vue`**
    - User menu dropdown in header
    - Shows user name and email
    - Navigation links (Dashboard, Users)
    - Theme management link (optional)
    - Logout functionality
    - Click-outside-to-close behavior

## Files Updated

### Sidebar Components

1. **`resources/js/Components/SidebarMenu/SidebarMenu.vue`**

    - Updated with route support (prioritizes named routes over URLs)
    - Mobile-responsive with drawer animation
    - Search functionality
    - Scroll position persistence
    - Uses CSS custom properties for theming
    - Body scroll lock when mobile menu is open
    - Auto-closes on navigation

2. **`resources/js/Components/SidebarMenu/MenuItem.vue`**

    - Updated with route support
    - Handles both route names and URLs
    - Safe route resolution with fallback
    - Auto-expands active items
    - Supports nested menu items

3. **`resources/js/Components/SidebarMenu/ExpandedFavoriteChildren.vue`**
    - Updated with route support
    - Displays favorite menu items
    - Sorted by order property

### CSS Updates

4. **`resources/css/app.css`**
    - Added utility classes for backgrounds:
        - `.bg-header` / `.dark .bg-header-dark`
        - `.bg-card` / `.dark .bg-card-dark`
        - `.bg-card-dark-light`
        - `.bg-hover` / `.dark .bg-hover-dark`
        - `.bg-hover-variant`
        - `.bg-elevated` / `.dark .bg-elevated-dark`
        - `.bg-sidebar` / `.dark .bg-sidebar-dark`
        - `.bg-input` / `.dark .bg-input-dark`
    - Added utility classes for text colors:
        - `.text-text-on-card` / `.dark .text-text-on-card-dark`
        - `.text-text-primary-light`
        - `.text-text-secondary-variant`
    - Added border color utilities

### Utility Files

5. **`resources/js/utils/iconMapper.js`**
    - Fixed `ThumbUpIcon` import error
    - Replaced with `HandThumbUpIcon` (correct Heroicons v2 export)

## Key Features

### Dark Mode Support

-   All components use CSS custom properties for theming
-   Dark mode toggle button in header (sun/moon icons)
-   Consistent color scheme across all components
-   Professional appearance in both light and dark modes

### Mobile Responsiveness

-   Hamburger menu button in header (mobile only)
-   Slide-in drawer menu from left
-   Backdrop overlay with blur
-   Body scroll lock when menu is open
-   Auto-closes on navigation or item selection
-   Responsive breakpoint: 1024px (lg)

### Route Support

-   Sidebar menu supports both named routes and URLs
-   Route names take priority over URLs
-   Safe route resolution with fallback
-   Works with Ziggy route helper

### Menu Features

-   Search functionality
-   Favorite menu items section
-   Nested menu items support
-   Active state highlighting
-   Scroll position persistence
-   Collapsible/expandable groups

## Dependencies

### Required Inertia Shared Props

The layout expects the following props to be shared via Inertia:

-   `menu` - Menu structure (object or array)
-   `breadcrumbs` - Breadcrumb data (array)
-   `favoriteMenuItems` - Favorite menu items (array)
-   `auth.user` - Authenticated user object

### Route Names

The TopRightMenu component uses these route names (can be customized via props):

-   `dashboard` - Dashboard route
-   `users.index` - Users index route
-   `web.logout` - Logout route (POST)

## CSS Custom Properties

All colors are defined in `resources/css/app.css` using CSS custom properties:

-   Background colors: `--bg-page-1`, `--bg-header-1`, `--bg-card-1`, `--bg-sidebar-1`, etc.
-   Text colors: `--text-primary`, `--text-secondary`, `--text-muted`
-   Theme colors: `--color-primary`, `--color-secondary`, `--color-error`, etc.

Each has light and dark mode variants defined in `:root` and `.dark` selectors.

## Mobile Menu Behavior

1. **Opening**: Click hamburger button in header
2. **Closing**:
    - Click overlay/backdrop
    - Click close button (X icon)
    - Select a menu item
    - Navigate to a new page
3. **Scroll Lock**: Body scrolling is disabled when menu is open
4. **Animation**: Smooth slide-in from left with backdrop fade

## Browser Compatibility

-   Modern browsers with CSS custom properties support
-   Tailwind CSS v4
-   Vue 3 with Inertia.js v2
-   Heroicons v2

## Notes

-   The `MenuItem.vue` component includes debug `console.log` statements from ncloudrad that can be removed if desired
-   All linter warnings are minor (Tailwind v4 class preferences) and don't affect functionality
-   The theme toggle uses the appStore's `toggleTheme()` method which saves to backend via `/ajax-api/theme/user`

## Testing Checklist

-   [x] Layout renders correctly in light mode
-   [x] Layout renders correctly in dark mode
-   [x] Dark mode toggle works
-   [x] Sidebar menu opens/closes on desktop
-   [x] Mobile menu opens/closes on mobile
-   [x] Menu items navigate correctly (routes and URLs)
-   [x] Breadcrumbs display correctly
-   [x] Session notifications work
-   [x] User menu dropdown works
-   [x] Search functionality works
-   [x] Favorite items display
-   [x] Body scroll lock on mobile menu
-   [x] Menu closes on navigation

## Future Enhancements

Potential improvements for future consideration:

-   Remove debug console.log statements from MenuItem
-   Add keyboard navigation support
-   Add menu item animations
-   Add menu item badges/notifications
-   Add menu item tooltips when collapsed
-   Add menu customization options
-   Add menu item reordering (drag & drop)
