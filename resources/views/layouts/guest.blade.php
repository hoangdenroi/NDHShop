<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

        <!-- Theme Initialization Script -->
        <script>
            (function() {
                try {
                    // Nhận JSON từ biến DB do Blade render
                    const dbThemeJson = '{!! addslashes(json_encode(auth()->check() ? \App\Models\Setting::getForUser(auth()->id(), "theme") : null)) !!}';
                    const dbTheme = dbThemeJson && dbThemeJson !== 'null' ? JSON.parse(dbThemeJson) : null;
                    
                    let mode = 'auto';
                    let primaryColor = '#0d59f2'; // Default color

                    if (dbTheme) {
                        let config = typeof dbTheme === 'string' ? JSON.parse(dbTheme) : dbTheme;
                        mode = config.mode || mode;
                        primaryColor = config.primaryColor || primaryColor;
                        localStorage.setItem('theme', JSON.stringify({mode, primaryColor}));
                    } else {
                        let stored = localStorage.getItem('theme');
                        if(stored) {
                            let parsed = JSON.parse(stored);
                            mode = parsed.mode || mode;
                            primaryColor = parsed.primaryColor || primaryColor;
                        }
                    }

                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (mode === 'dark' || (mode === 'auto' && prefersDark)) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                    
                    if (primaryColor) {
                        document.documentElement.style.setProperty('--color-primary', primaryColor);
                    }
                } catch (e) {
                    console.error("Theme init error:", e);
                }
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-background-light dark:bg-background-dark font-display antialiased">
        <div class="relative flex min-h-screen flex-col overflow-x-hidden">
            <!-- Main Content -->
            <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-10">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
