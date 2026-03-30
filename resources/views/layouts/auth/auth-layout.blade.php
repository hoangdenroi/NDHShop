<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NDHShop') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="shortcut icon" href="{{ asset('NDHShop.jpg') }}" type="image/x-icon">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

        <!-- Theme Initialization Script -->
        <script>
            (function() {
                try {
                    const dbThemeJson = '{!! addslashes(json_encode(auth()->check() ? auth()->user()->theme : null)) !!}';
                    const dbTheme = dbThemeJson && dbThemeJson !== 'null' ? JSON.parse(dbThemeJson) : null;
                    
                    const dbNotiJson = '{!! addslashes(json_encode(auth()->check() ? auth()->user()->notification : null)) !!}';
                    if (dbNotiJson && dbNotiJson !== 'null') {
                        let notiObj = JSON.parse(dbNotiJson);
                        localStorage.setItem('notifications', JSON.stringify(typeof notiObj === 'string' ? JSON.parse(notiObj) : notiObj));
                    }

                    const dbLang = '{!! addslashes(auth()->check() ? auth()->user()->language : "") !!}';
                    if (dbLang) {
                        localStorage.setItem('language', dbLang);
                    }

                    let mode = 'auto';
                    let primaryColor = '#0d59f2';

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

        {{-- Toast Notifications Component --}}
        <x-app.toast />

        @stack('scripts')

        {{-- Dispatch toast từ session flash (ví dụ: khi bị redirect do chưa đăng nhập) --}}
        @if(session('toast_message'))
        <script>
            // Dùng setTimeout để đảm bảo Alpine.js đã khởi tạo xong toast component
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('toast', { detail: {
                        type: '{{ session('toast_type', 'info') }}',
                        title: '{{ session('toast_type') === 'warning' ? 'Cảnh báo' : (session('toast_type') === 'error' ? 'Lỗi' : (session('toast_type') === 'success' ? 'Thành công' : 'Thông báo')) }}',
                        message: '{!! addslashes(session('toast_message')) !!}',
                        link: '{{ session('toast_link', '') }}' || null,
                        linkText: '{{ session('toast_link_text', '') }}' || null
                    }}));
                }, 100);
            });
        </script>
        @endif

        {{-- Validation Errors Toast --}}
        @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    @foreach ($errors->all() as $error)
                    window.dispatchEvent(new CustomEvent('toast', { detail: {
                        type: 'error',
                        title: 'Thất bại',
                        message: '{!! addslashes($error) !!}'
                    }}));
                    @endforeach
                }, 100);
            });
        </script>
        @endif

        {{-- Auth Status Toast --}}
        @if (session('status'))
        @php
            $statusMsg = session('status');
            if ($statusMsg === 'verification-link-sent') {
                $statusMsg = 'Liên kết xác minh mới đã được gửi đến email của bạn.';
            } else {
                $statusMsg = __($statusMsg);
            }
        @endphp
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('toast', { detail: {
                        type: 'success',
                        title: 'Thành công',
                        message: '{!! addslashes($statusMsg) !!}'
                    }}));
                }, 100);
            });
        </script>
        @endif
    </body>
</html>
