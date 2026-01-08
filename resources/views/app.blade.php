<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead

    <script>
        // Apply theme immediately to prevent flash of wrong theme
        (function() {
            try {
                const stored = localStorage.getItem('app_store');
                let targetTheme = 'light'; // Default to light

                if (stored) {
                    const data = JSON.parse(stored);
                    if (data.themeMode && ['light', 'dark', 'system'].includes(data.themeMode)) {
                        const themeMode = data.themeMode;

                        if (themeMode === 'system') {
                            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)')
                                .matches;
                            targetTheme = prefersDark ? 'dark' : 'light';
                        } else {
                            targetTheme = themeMode;
                        }
                    }
                }

                // Always explicitly set the class
                if (targetTheme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }

                console.log('[Theme Init] Applied theme from localStorage:', targetTheme);
            } catch (e) {
                console.error('[Theme Init] Error:', e);
                // Default to light on error
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>

<body class="antialiased bg-page dark:bg-page-dark">
    @inertia
    <noscript>
        <strong>We're sorry but this app requires JavaScript to work properly.</strong>
    </noscript>
</body>

</html>
