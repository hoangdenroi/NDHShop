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
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <style>
        [x-cloak] {
            display: none !important;
        }

        @media (min-width: 1024px) {

            .mobile-menu-backdrop,
            .mobile-menu-panel {
                display: none !important;
            }
        }
    </style>

    <!-- Theme Initialization Script -->
    <script>
        (function () {
            try {
                // Nhận JSON từ biến DB do Blade render
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
                let primaryColor = '#0d59f2'; // Default color

                if (dbTheme) {
                    // Nếu đăng nhập và có dbTheme, ưu tiên dùng dbTheme và update storage
                    // Chú ý DB lưu string dạng json hay array
                    let config = typeof dbTheme === 'string' ? JSON.parse(dbTheme) : dbTheme;
                    mode = config.mode || mode;
                    primaryColor = config.primaryColor || primaryColor;
                    localStorage.setItem('theme', JSON.stringify({ mode, primaryColor }));
                } else {
                    // Chưa đăng nhập, dùng localStorage nếu có
                    let stored = localStorage.getItem('theme');
                    if (stored) {
                        let parsed = JSON.parse(stored);
                        mode = parsed.mode || mode;
                        primaryColor = parsed.primaryColor || primaryColor;
                    }
                }

                // Áp dụng Mode (Dark/Light)
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (mode === 'dark' || (mode === 'auto' && prefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }

                // Áp dụng Primary Color
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

<body
    class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen flex flex-col overflow-x-hidden">
    <div class="relative flex min-h-screen w-full flex-col">
        <div class="layout-container flex h-full grow flex-col">
            {{-- Header --}}
            <x-app.header />
            {{-- Spacer bù chiều cao header fixed --}}
            <div class="h-16"></div>

            {{-- Nội dung trang --}}
            <main class="flex-1 flex flex-col items-center w-full px-4 lg:px-20 py-8">
                <div class="max-w-[1400px] w-full flex flex-col gap-12">
                    @yield('content')
                </div>
            </main>

            {{-- Footer --}}
            <x-app.footer />
        </div>

        <div id="global-widgets" data-turbo-permanent>
            {{-- Floating Action Buttons (FAB) - Liên hệ nhanh + Nghe nhạc --}}
            {{-- <x-app.music-player /> --}}

            {{-- YouTube Hidden Player - nằm trong permanent zone để không bị hủy khi navigate --}}
            {{-- <div id="youtube-hidden-player" class="hidden"></div> --}}

            <div x-data="{ open: false }" class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-3">

                {{-- Desktop: Hiển thị tất cả nút theo hàng dọc --}}
                <div class="hidden md:flex flex-col gap-3">
                    {{-- Nghe nhạc --}}
                    {{-- <button @click="$dispatch('toggle-music-panel')" class="fab-btn fab-music" title="Nghe nhạc">
                        <span class="material-symbols-outlined text-2xl">music_note</span>
                    </button> --}}
                    {{-- Zalo --}}
                    <a href="https://zalo.me/0388937608" target="_blank" rel="noopener noreferrer"
                        class="fab-btn fab-zalo" title="Chat qua Zalo">
                        <span class="material-symbols-outlined text-2xl">chat</span>
                    </a>

                    {{-- Gọi điện --}}
                    <a href="tel:+84388937608" class="fab-btn fab-phone" title="Gọi điện">
                        <span class="material-symbols-outlined text-2xl">call</span>
                    </a>
                </div>

                {{-- Mobile: Toggle menu --}}
                <div class="md:hidden flex flex-col items-end gap-3">

                    {{-- Các nút khi menu mở --}}
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-4" class="flex flex-col gap-3">
                        {{-- Nghe nhạc --}}
                        {{-- <button @click="$dispatch('toggle-music-panel')" class="fab-btn fab-music"
                            title="Nghe nhạc">
                            <span class="material-symbols-outlined text-2xl">music_note</span>
                        </button> --}}
                        {{-- Zalo --}}
                        <a href="https://zalo.me/0388937608" target="_blank" rel="noopener noreferrer"
                            @click="open = false" class="fab-btn fab-zalo" title="Chat qua Zalo">
                            <span class="material-symbols-outlined text-2xl">chat</span>
                        </a>

                        {{-- Gọi điện --}}
                        <a href="tel:+84388937608" @click="open = false" class="fab-btn fab-phone" title="Gọi điện">
                            <span class="material-symbols-outlined text-2xl">call</span>
                        </a>
                    </div>

                    {{-- Nút Toggle --}}
                    <button @click="open = !open; if(!open) $dispatch('close-music-panel')"
                        class="fab-btn fab-toggle-btn" :class="{ 'fab-toggle-open': open, 'fab-pulse': !open }"
                        title="Menu liên hệ">
                        {{-- Logo khi đóng, icon X khi mở --}}
                        <img x-show="!open" src="{{ asset('NDHShop.jpg') }}" alt="Logo"
                            class="object-cover rounded-full"
                            style="width: calc(100% - 2px); height: calc(100% - 2px);">
                        <span x-show="open" x-cloak
                            class="material-symbols-outlined text-4xl font-light transition-transform duration-300"
                            style="transform: rotate(45deg);">add</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notifications Component --}}
    <x-app.toast />

    {{-- Dispatch toast từ session flash (ví dụ: khi bị redirect do chưa đăng nhập) --}}
    @if(session('toast_message'))
        <script>
            (function () {
                const initToast = () => {
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                type: '{{ session('toast_type', 'info') }}',
                                title: '{{ session('toast_type') === 'warning' ? 'Cảnh báo' : (session('toast_type') === 'error' ? 'Lỗi' : (session('toast_type') === 'success' ? 'Thành công' : 'Thông báo')) }}',
                                message: '{{ session('toast_message') }}',
                                link: '{{ session('toast_link', '') }}' || null,
                                linkText: '{{ session('toast_link_text', '') }}' || null
                            }
                        }));
                    }, 100);
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initToast);
                } else {
                    initToast(); // Khi Turbo load trang mới không có DOMContentLoaded
                }
            })();
        </script>
    @endif

    {{-- SSE Notification Client --}}
    @auth
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let lastNotificationId = 0;

                const fetchNotifications = async () => {
                    try {
                        const response = await fetch(`{{ route('api.v1.notifications.pull') }}?last_id=${lastNotificationId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!response.ok) return;

                        const data = await response.json();

                        if (data.success && data.last_id !== undefined) {
                            lastNotificationId = data.last_id;
                        }

                        if (data.success && data.notifications && data.notifications.length > 0) {
                            data.notifications.forEach(noti => {
                                // Phát sự kiện hiển thị Toast
                                window.dispatchEvent(new CustomEvent('toast', {
                                    detail: {
                                        type: noti.type || 'info',
                                        title: noti.title || 'Thông báo mới',
                                        message: noti.message
                                    }
                                }));

                                // Nếu tin nhắn yêu cầu cập nhật số dư
                                if (noti.action === 'update_balance' && noti.balance !== undefined) {
                                    window.dispatchEvent(new CustomEvent('balance-updated', { detail: { new_balance: noti.balance } }));
                                }
                            });
                        }
                    } catch (error) {
                        // console.error("Polling Error:", error);
                    }
                };

                // Chạy ngay lần đầu để lấy last_id làm mốc
                fetchNotifications();

                // Lặp lại mỗi 5 giây
                // setInterval(fetchNotifications, 5000);
            });
        </script>
    @endauth

    @stack('scripts')
</body>

</html>