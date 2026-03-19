<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'NDHShop - Admin' }}</title>

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

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .dark ::-webkit-scrollbar-track {
            background: #101622;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #2d3646;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: #3b4354;
        }
    </style>

    <!-- Theme Initialization Script -->
    <script>
        (function () {
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
                    localStorage.setItem('theme', JSON.stringify({ mode, primaryColor }));
                } else {
                    let stored = localStorage.getItem('theme');
                    if (stored) {
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

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="{ sidebarOpen: false, isPanelOpen: false }"
    class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white font-display overflow-hidden flex h-screen w-full">
    <!-- Overlay backdrop khi sidebar mở trên mobile -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 z-30 lg:hidden" x-cloak></div>

    <!-- Sidebar -->
    <x-admin.nav />

    <!-- Panel Thông báo -->
    <x-admin.panel-admin-content />

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <!-- Header -->
        <x-admin.header :title="$title ?? 'Dashboard'" />

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-6 scroll-smooth">
            <div class="flex flex-col gap-6 max-w-[1400px] mx-auto">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer cố định dưới cùng (ngoài vùng scroll, giống header) -->
        <x-admin.footer />
    </main>

    {{-- Toast Notifications Component --}}
    <x-app.toast />

    {{-- Dispatch toast từ session flash --}}
    @if(session('success') || session('error'))
        <script>
            document.addEventListener('alpine:init', () => {
                @if(session('success'))
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { type: 'success', title: 'Thành công', message: @js(session('success')) }
                        }));
                    }, 100);
                @endif
                @if(session('error'))
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { type: 'error', title: 'Lỗi', message: @js(session('error')) }
                        }));
                    }, 100);
                @endif
            });
        </script>
    @endif

    {{-- Hàm tạo slug tự động từ tiêu đề (bỏ dấu tiếng Việt, viết thường, nối bằng dấu -) --}}
    <script>
        window.generateSlug = function(str) {
            if (!str) return '';
            // Bảng chuyển đổi ký tự tiếng Việt có dấu sang không dấu
            const vietnameseMap = {
                'à':'a','á':'a','ả':'a','ã':'a','ạ':'a',
                'ă':'a','ằ':'a','ắ':'a','ẳ':'a','ẵ':'a','ặ':'a',
                'â':'a','ầ':'a','ấ':'a','ẩ':'a','ẫ':'a','ậ':'a',
                'è':'e','é':'e','ẻ':'e','ẽ':'e','ẹ':'e',
                'ê':'e','ề':'e','ế':'e','ể':'e','ễ':'e','ệ':'e',
                'ì':'i','í':'i','ỉ':'i','ĩ':'i','ị':'i',
                'ò':'o','ó':'o','ỏ':'o','õ':'o','ọ':'o',
                'ô':'o','ồ':'o','ố':'o','ổ':'o','ỗ':'o','ộ':'o',
                'ơ':'o','ờ':'o','ớ':'o','ở':'o','ỡ':'o','ợ':'o',
                'ù':'u','ú':'u','ủ':'u','ũ':'u','ụ':'u',
                'ư':'u','ừ':'u','ứ':'u','ử':'u','ữ':'u','ự':'u',
                'ỳ':'y','ý':'y','ỷ':'y','ỹ':'y','ỵ':'y',
                'đ':'d',
                'À':'A','Á':'A','Ả':'A','Ã':'A','Ạ':'A',
                'Ă':'A','Ằ':'A','Ắ':'A','Ẳ':'A','Ẵ':'A','Ặ':'A',
                'Â':'A','Ầ':'A','Ấ':'A','Ẩ':'A','Ẫ':'A','Ậ':'A',
                'È':'E','É':'E','Ẻ':'E','Ẽ':'E','Ẹ':'E',
                'Ê':'E','Ề':'E','Ế':'E','Ể':'E','Ễ':'E','Ệ':'E',
                'Ì':'I','Í':'I','Ỉ':'I','Ĩ':'I','Ị':'I',
                'Ò':'O','Ó':'O','Ỏ':'O','Õ':'O','Ọ':'O',
                'Ô':'O','Ồ':'O','Ố':'O','Ổ':'O','Ỗ':'O','Ộ':'O',
                'Ơ':'O','Ờ':'O','Ớ':'O','Ở':'O','Ỡ':'O','Ợ':'O',
                'Ù':'U','Ú':'U','Ủ':'U','Ũ':'U','Ụ':'U',
                'Ư':'U','Ừ':'U','Ứ':'U','Ử':'U','Ữ':'U','Ự':'U',
                'Ỳ':'Y','Ý':'Y','Ỷ':'Y','Ỹ':'Y','Ỵ':'Y',
                'Đ':'D'
            };
            let result = '';
            for (let i = 0; i < str.length; i++) {
                result += vietnameseMap[str[i]] || str[i];
            }
            return result
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')  // Xóa ký tự đặc biệt
                .replace(/\s+/g, '-')           // Thay khoảng trắng bằng -
                .replace(/-+/g, '-')            // Xóa dấu - trùng
                .replace(/^-|-$/g, '');         // Xóa - đầu/cuối
        }
    </script>

    @stack('scripts')
</body>

</html>