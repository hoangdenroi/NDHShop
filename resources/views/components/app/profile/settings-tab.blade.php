<div x-show="activeTab === 'settings'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak
    data-theme="{{ json_encode(auth()->user()->theme) }}"
    data-notification="{{ json_encode(auth()->user()->notification) }}"
    data-language="{{ auth()->user()->language }}"
    x-data="{ 
        settingsView: '{{ session('status') == 'password-updated' || session('status') == 'profile-updated' ? 'security' : 'menu' }}', 
        securityView: '{{ $errors->updatePassword->isNotEmpty() ? 'password' : ($errors->has('email') ? 'email' : 'list') }}', // list | password | email
        theme: 'auto', 
        accentColor: 'blue', 
        lang: 'vi', 
        notiEmail: true, 
        notiPush: true,
        colorMap: {
            'blue': '#3b82f6',
            'indigo': '#6366f1',
            'violet': '#8b5cf6',
            'pink': '#ec4899',
            'rose': '#f43f5e',
            'red': '#ef4444',
            'orange': '#f97316',
            'amber': '#f59e0b',
            'yellow': '#eab308',
            'lime': '#84cc16',
            'green': '#22c55e',
            'emerald': '#10b981',
            'teal': '#14b8a6',
            'cyan': '#06b6d4',
            'sky': '#0ea5e9',
            'slate': '#64748b',
            'gray': '#6b7280',
            'zinc': '#71717a',
            'neutral': '#737373',
            'stone': '#78716c'
        },
        init() {
            let themeData = this.$el.dataset.theme;
            let serverTheme = themeData && themeData !== 'null' ? JSON.parse(themeData) : null;
            if (serverTheme) {
                this.theme = serverTheme.mode || 'auto';
                let hexVal = serverTheme.primaryColor || '#0d59f2';
                let foundKey = Object.keys(this.colorMap).find(key => this.colorMap[key] === hexVal);
                if(foundKey) this.accentColor = foundKey;
            } else {
                let stored = localStorage.getItem('theme');
                if(stored) {
                    let parsed = JSON.parse(stored);
                    this.theme = parsed.mode || 'auto';
                    let hexVal = parsed.primaryColor || '#0d59f2';
                    let foundKey = Object.keys(this.colorMap).find(key => this.colorMap[key] === hexVal);
                    if(foundKey) this.accentColor = foundKey;
                }
            }

            let notiData = this.$el.dataset.notification;
            let serverNoti = notiData && notiData !== 'null' ? JSON.parse(notiData) : null;
            if (serverNoti) {
                this.notiPush = serverNoti.push !== false;
                this.notiEmail = serverNoti.email !== false;
            } else {
                let storedNoti = localStorage.getItem('notifications');
                if(storedNoti) {
                    let parsed = JSON.parse(storedNoti);
                    this.notiPush = parsed.push !== false;
                    this.notiEmail = parsed.email !== false;
                }
            }

            let serverLang = this.$el.dataset.language;
            if (serverLang) {
                this.lang = serverLang;
            }

            // Theo dõi thay đổi theme (chế độ sáng tối)
            this.$watch('theme', value => {
                this.updateThemeSettings();
                if (value === 'dark' || (value === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            });

            // Theo dõi thay đổi màu
            this.$watch('accentColor', value => {
                if(this.colorMap[value]) {
                    document.documentElement.style.setProperty('--color-primary', this.colorMap[value]);
                    this.updateThemeSettings();
                }
            });

            // Theo dõi phần Bật tắt thông báo
            this.$watch('notiPush', value => this.updateNotiSettings());
            this.$watch('notiEmail', value => this.updateNotiSettings());

            // Theo dõi thay đổi ngôn ngữ
            this.$watch('lang', value => this.updateLangSettings());
        },
        updateNotiSettings() {
            localStorage.setItem('notifications', JSON.stringify({ push: this.notiPush, email: this.notiEmail }));
            
            fetch('{{ route("api.v1.settings.notification") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    push: this.notiPush,
                    email: this.notiEmail
                })
            }).catch(e => console.error('Lỗi lưu cấu hình thông báo', e));
        },
        updateLangSettings() {
            fetch('{{ route("api.v1.settings.language") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    lang: this.lang
                })
            }).catch(e => console.error('Lỗi lưu ngôn ngữ', e));
        },
        updateThemeSettings() {
            let hexColor = this.colorMap[this.accentColor] || '#0d59f2';
            // Lưu local
            localStorage.setItem('theme', JSON.stringify({ mode: this.theme, primaryColor: hexColor }));
            
            // Call API lưu DB
            fetch('{{ route("api.v1.settings.theme") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    mode: this.theme,
                    primaryColor: hexColor
                })
            }).catch(e => console.error('Lỗi lưu cấu hình', e));
        }
    }">

    {{-- Menu chính cài đặt --}}
    <div x-show="settingsView === 'menu'">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Cài đặt</h2>
            </div>
            <div class="flex flex-col divide-y divide-slate-100 dark:divide-slate-700">
                <button @click="settingsView = 'theme'" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group w-full text-left">
                    <span class="material-symbols-outlined text-slate-500 dark:text-slate-400 group-hover:text-primary transition-colors">dark_mode</span>
                    <span class="flex-1 text-sm font-medium text-slate-700 dark:text-slate-300">Giao diện</span>
                    <span class="material-symbols-outlined text-slate-400 text-[20px]">chevron_right</span>
                </button>
                <button @click="settingsView = 'language'" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group w-full text-left">
                    <span class="material-symbols-outlined text-slate-500 dark:text-slate-400 group-hover:text-primary transition-colors">language</span>
                    <span class="flex-1 text-sm font-medium text-slate-700 dark:text-slate-300">Ngôn ngữ</span>
                    <span class="material-symbols-outlined text-slate-400 text-[20px]">chevron_right</span>
                </button>
                <button @click="settingsView = 'notifications'" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group w-full text-left">
                    <span class="material-symbols-outlined text-slate-500 dark:text-slate-400 group-hover:text-primary transition-colors">notifications</span>
                    <span class="flex-1 text-sm font-medium text-slate-700 dark:text-slate-300">Thông báo</span>
                    <span class="material-symbols-outlined text-slate-400 text-[20px]">chevron_right</span>
                </button>
                <button @click="settingsView = 'security'" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group w-full text-left">
                    <span class="material-symbols-outlined text-slate-500 dark:text-slate-400 group-hover:text-primary transition-colors">shield</span>
                    <span class="flex-1 text-sm font-medium text-slate-700 dark:text-slate-300">Bảo mật</span>
                    <span class="material-symbols-outlined text-slate-400 text-[20px]">chevron_right</span>
                </button>
                <button @click="settingsView = 'help'" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group w-full text-left">
                    <span class="material-symbols-outlined text-slate-500 dark:text-slate-400 group-hover:text-primary transition-colors">help</span>
                    <span class="flex-1 text-sm font-medium text-slate-700 dark:text-slate-300">Trợ giúp</span>
                    <span class="material-symbols-outlined text-slate-400 text-[20px]">chevron_right</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Sub: Giao diện --}}
    <div x-show="settingsView === 'theme'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <button @click="settingsView = 'menu'" class="flex size-8 items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </button>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Giao diện</h2>
            </div>
            <div class="p-6">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">Chọn giao diện hiển thị cho ứng dụng.</p>
                <div class="grid grid-cols-3 gap-3">
                    <button @click="theme = 'light'" :class="theme === 'light' ? 'ring-2 ring-primary border-primary' : 'border-slate-200 dark:border-slate-700'"
                        class="flex flex-col items-center gap-3 p-4 rounded-xl border transition-all hover:shadow-md">
                        <div class="size-12 rounded-full bg-amber-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-amber-500">light_mode</span>
                        </div>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Sáng</span>
                    </button>
                    <button @click="theme = 'dark'" :class="theme === 'dark' ? 'ring-2 ring-primary border-primary' : 'border-slate-200 dark:border-slate-700'"
                        class="flex flex-col items-center gap-3 p-4 rounded-xl border transition-all hover:shadow-md">
                        <div class="size-12 rounded-full bg-slate-700 flex items-center justify-center">
                            <span class="material-symbols-outlined text-slate-300">dark_mode</span>
                        </div>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Tối</span>
                    </button>
                    <button @click="theme = 'auto'" :class="theme === 'auto' ? 'ring-2 ring-primary border-primary' : 'border-slate-200 dark:border-slate-700'"
                        class="flex flex-col items-center gap-3 p-4 rounded-xl border transition-all hover:shadow-md">
                        <div class="size-12 rounded-full bg-gradient-to-br from-amber-100 to-slate-700 flex items-center justify-center">
                            <span class="material-symbols-outlined text-white">contrast</span>
                        </div>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Tự động</span>
                    </button>
                </div>

                {{-- Màu chủ đạo --}}
                <div class="mt-8">
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-4">Màu chủ đạo</p>
                    <div class="flex flex-wrap gap-3">
                        <button @click="accentColor = 'blue'" class="relative size-10 rounded-full bg-blue-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'blue' ? 'ring-2 ring-blue-500' : ''">
                            <span x-show="accentColor === 'blue'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'indigo'" class="relative size-10 rounded-full bg-indigo-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'indigo' ? 'ring-2 ring-indigo-500' : ''">
                            <span x-show="accentColor === 'indigo'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'violet'" class="relative size-10 rounded-full bg-violet-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'violet' ? 'ring-2 ring-violet-500' : ''">
                            <span x-show="accentColor === 'violet'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'pink'" class="relative size-10 rounded-full bg-pink-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'pink' ? 'ring-2 ring-pink-500' : ''">
                            <span x-show="accentColor === 'pink'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'rose'" class="relative size-10 rounded-full bg-rose-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'rose' ? 'ring-2 ring-rose-500' : ''">
                            <span x-show="accentColor === 'rose'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'red'" class="relative size-10 rounded-full bg-red-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'red' ? 'ring-2 ring-red-500' : ''">
                            <span x-show="accentColor === 'red'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'orange'" class="relative size-10 rounded-full bg-orange-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'orange' ? 'ring-2 ring-orange-500' : ''">
                            <span x-show="accentColor === 'orange'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'amber'" class="relative size-10 rounded-full bg-amber-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'amber' ? 'ring-2 ring-amber-500' : ''">
                            <span x-show="accentColor === 'amber'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'emerald'" class="relative size-10 rounded-full bg-emerald-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'emerald' ? 'ring-2 ring-emerald-500' : ''">
                            <span x-show="accentColor === 'emerald'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'teal'" class="relative size-10 rounded-full bg-teal-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'teal' ? 'ring-2 ring-teal-500' : ''">
                            <span x-show="accentColor === 'teal'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'cyan'" class="relative size-10 rounded-full bg-cyan-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'cyan' ? 'ring-2 ring-cyan-500' : ''">
                            <span x-show="accentColor === 'cyan'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'sky'" class="relative size-10 rounded-full bg-sky-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'sky' ? 'ring-2 ring-sky-500' : ''">
                            <span x-show="accentColor === 'sky'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'lime'" class="relative size-10 rounded-full bg-lime-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'lime' ? 'ring-2 ring-lime-500' : ''">
                            <span x-show="accentColor === 'lime'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'green'" class="relative size-10 rounded-full bg-green-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'green' ? 'ring-2 ring-green-500' : ''">
                            <span x-show="accentColor === 'green'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'yellow'" class="relative size-10 rounded-full bg-yellow-400 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'yellow' ? 'ring-2 ring-yellow-400' : ''">
                            <span x-show="accentColor === 'yellow'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'fuchsia'" class="relative size-10 rounded-full bg-fuchsia-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'fuchsia' ? 'ring-2 ring-fuchsia-500' : ''">
                            <span x-show="accentColor === 'fuchsia'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'purple'" class="relative size-10 rounded-full bg-purple-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'purple' ? 'ring-2 ring-purple-500' : ''">
                            <span x-show="accentColor === 'purple'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'slate'" class="relative size-10 rounded-full bg-slate-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'slate' ? 'ring-2 ring-slate-500' : ''">
                            <span x-show="accentColor === 'slate'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'gray'" class="relative size-10 rounded-full bg-gray-500 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'gray' ? 'ring-2 ring-gray-500' : ''">
                            <span x-show="accentColor === 'gray'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                        <button @click="accentColor = 'zinc'" class="relative size-10 rounded-full bg-zinc-600 hover:scale-110 transition-transform ring-offset-2 dark:ring-offset-slate-800"
                            :class="accentColor === 'zinc' ? 'ring-2 ring-zinc-600' : ''">
                            <span x-show="accentColor === 'zinc'" class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-white text-[18px]">check</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sub: Ngôn ngữ --}}
    <div x-show="settingsView === 'language'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <button @click="settingsView = 'menu'" class="flex size-8 items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </button>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Ngôn ngữ</h2>
            </div>
            <div class="flex flex-col divide-y divide-slate-100 dark:divide-slate-700">
                <button @click="lang = 'vi'" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors w-full text-left">
                    <span class="text-xl">🇻🇳</span>
                    <span class="flex-1 text-sm font-medium text-slate-700 dark:text-slate-300">Tiếng Việt</span>
                    <span x-show="lang === 'vi'" class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                </button>
                <button @click="lang = 'en'" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors w-full text-left">
                    <span class="text-xl">🇺🇸</span>
                    <span class="flex-1 text-sm font-medium text-slate-700 dark:text-slate-300">Tiếng Anh</span>
                    <span x-show="lang === 'en'" class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                </button>
                <button @click="lang = 'zh'" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors w-full text-left">
                    <span class="text-xl">🇨🇳</span>
                    <span class="flex-1 text-sm font-medium text-slate-700 dark:text-slate-300">Tiếng Trung</span>
                    <span x-show="lang === 'zh'" class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                </button>
                <button @click="lang = 'ja'" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors w-full text-left">
                    <span class="text-xl">🇯🇵</span>
                    <span class="flex-1 text-sm font-medium text-slate-700 dark:text-slate-300">Tiếng Nhật</span>
                    <span x-show="lang === 'ja'" class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Sub: Thông báo --}}
    <div x-show="settingsView === 'notifications'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <button @click="settingsView = 'menu'" class="flex size-8 items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </button>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Thông báo</h2>
            </div>
            <div class="flex flex-col divide-y divide-slate-100 dark:divide-slate-700">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-4">
                        <span class="material-symbols-outlined text-slate-500 dark:text-slate-400">mail</span>
                        <div>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Thông báo qua Email</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Nhận thông báo về đơn hàng, khuyến mãi</p>
                        </div>
                    </div>
                    {{-- Toggle switch --}}
                    <button @click="notiEmail = !notiEmail" :class="notiEmail ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600'" class="relative shrink-0 w-11 h-6 rounded-full transition-colors">
                        <span :class="notiEmail ? 'translate-x-5' : 'translate-x-0.5'" class="absolute top-0.5 left-0 size-5 bg-white rounded-full shadow transform transition-transform"></span>
                    </button>
                </div>
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-4">
                        <span class="material-symbols-outlined text-slate-500 dark:text-slate-400">notifications</span>
                        <div>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Thông báo đẩy</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Nhận thông báo trên trình duyệt</p>
                        </div>
                    </div>
                    <button @click="notiPush = !notiPush" :class="notiPush ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600'" class="relative shrink-0 w-11 h-6 rounded-full transition-colors">
                        <span :class="notiPush ? 'translate-x-5' : 'translate-x-0.5'" class="absolute top-0.5 left-0 size-5 bg-white rounded-full shadow transform transition-transform"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Sub: Bảo mật --}}
    <div x-show="settingsView === 'security'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
        
        {{-- Màn hình danh sách Bảo mật --}}
        <div x-show="securityView === 'list'" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden" x-cloak>
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <button @click="settingsView = 'menu'" class="flex size-8 items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </button>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Bảo mật</h2>
            </div>
            <div class="p-6 flex flex-col gap-4">
                
                {{-- Mật khẩu --}}
                <div class="flex items-center justify-between p-3 sm:p-4 gap-2 sm:gap-3 rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 overflow-hidden">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="size-10 sm:size-12 rounded-xl bg-slate-200/50 dark:bg-slate-700 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-slate-600 dark:text-slate-300">lock</span>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-white text-sm sm:text-base truncate">Mật khẩu</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate w-full">
                                Thay đổi: 
                                @if (Auth::user()->last_change_password_at)
                                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ Auth::user()->last_change_password_at->diffForHumans() }}</span>
                                @else
                                    <span class="font-medium text-emerald-600 dark:text-emerald-400">Chưa đổi</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <button @click="securityView = 'password'" class="px-3 sm:px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors shadow-sm shrink-0">
                        Đổi mật khẩu
                    </button>
                </div>

                {{-- Email xác thực --}}
                <div class="flex items-center justify-between p-3 sm:p-4 gap-2 sm:gap-3 rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 overflow-hidden">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="size-10 sm:size-12 rounded-xl bg-slate-200/50 dark:bg-slate-700 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-slate-600 dark:text-slate-300">mail</span>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-white text-sm sm:text-base truncate">Email xác thực</p>
                            <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <button @click="securityView = 'email'" class="px-3 sm:px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors shadow-sm shrink-0">
                        Thay đổi
                    </button>
                </div>

                <hr class="border-slate-100 dark:border-slate-700 my-2">

                {{-- Xóa tài khoản --}}
                <div class="flex items-center justify-between p-3 sm:p-4 gap-2 sm:gap-3 rounded-xl border border-red-100 dark:border-red-900/30 bg-red-50/50 dark:bg-red-900/10 overflow-hidden">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="size-10 sm:size-12 rounded-xl bg-red-100 dark:bg-red-900/50 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-red-600 dark:text-red-400">warning</span>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-red-600 dark:text-red-400 text-sm sm:text-base truncate">Xóa tài khoản</p>
                            <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate" title="Sau khi xóa, tất cả dữ liệu bị mất vĩnh viễn.">Sau khi xóa, tất cả dữ liệu bị mất vĩnh viễn.</p>
                        </div>
                    </div>
                    <button class="px-3 sm:px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs sm:text-sm font-bold transition-colors shadow-sm shrink-0">
                        Xóa
                    </button>
                </div>

            </div>
        </div>

        {{-- Form: Đổi mật khẩu --}}
        <form method="post" action="{{ route('password.update') }}" x-show="securityView === 'password'" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden" x-cloak>
            @csrf
            @method('put')
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <button type="button" @click="securityView = 'list'" class="flex size-8 items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </button>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Đổi mật khẩu</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-3">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Mật khẩu hiện tại</label>
                        <input name="current_password" class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary h-11 sm:text-sm"
                            type="password" placeholder="Nhập mật khẩu hiện tại" />
                        <x-ui.input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Mật khẩu mới</label>
                        <input name="password" class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary h-11 sm:text-sm"
                            type="password" placeholder="Nhập mật khẩu mới" />
                        <x-ui.input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Xác nhận mật khẩu</label>
                        <input name="password_confirmation" class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary h-11 sm:text-sm"
                            type="password" placeholder="Nhập lại mật khẩu mới" />
                        <x-ui.input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button type="submit" class="h-10 px-6 bg-primary hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition-all shadow-sm shadow-primary/20 active:scale-[0.98]">
                        Cập nhật mật khẩu
                    </button>
                    @if (session('status') === 'password-updated')
                        <p class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">Đã cập nhật mật khẩu.</p>
                    @endif
                </div>
            </div>
        </form>

        {{-- Form: Đổi email --}}
        <form method="post" action="{{ route('app.profile.update') }}" x-show="securityView === 'email'" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden" x-cloak>
            @csrf
            @method('patch')
            <input type="hidden" name="name" value="{{ Auth::user()->name }}">
            <input type="hidden" name="phone" value="{{ Auth::user()->phone }}">
            <input type="hidden" name="avatar_url" value="{{ Auth::user()->avatar_url }}">
            
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <button type="button" @click="securityView = 'list'" class="flex size-8 items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </button>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Đổi email</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-3">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email hiện tại</label>
                        <input class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 h-11 sm:text-sm"
                            type="email" value="{{ Auth::user()->email }}" disabled />
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email bổ sung</label>
                        <input name="email" class="form-input block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary h-11 sm:text-sm"
                            type="email" value="{{ old('email') }}" placeholder="Nhập email mới" />
                        <x-ui.input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>
                    
                    {{-- Ghi chú: Yêu cầu mật khẩu trong update profile là bắt buộc hay không thì tùy logic Breeze --}}
                </div>
                <div class="flex items-center gap-4">
                    <button type="submit" class="h-10 px-6 bg-primary hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition-all shadow-sm shadow-primary/20 active:scale-[0.98]">
                        Lưu email
                    </button>
                    @if (session('status') === 'profile-updated' && !session('password-updated'))
                        <p class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">Đã cập nhật email.</p>
                    @endif
                </div>
            </div>
        </form>

    </div>

    {{-- Sub: Trợ giúp --}}
    <div x-show="settingsView === 'help'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <button @click="settingsView = 'menu'" class="flex size-8 items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </button>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Trợ giúp</h2>
            </div>
            <div class="flex flex-col divide-y divide-slate-100 dark:divide-slate-700">
                <a href="#" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <span class="material-symbols-outlined text-slate-500 dark:text-slate-400 group-hover:text-primary transition-colors">menu_book</span>
                    <span class="flex-1 text-sm font-medium text-slate-700 dark:text-slate-300">Hướng dẫn sử dụng</span>
                    <span class="material-symbols-outlined text-slate-400 text-[20px]">chevron_right</span>
                </a>
                <a href="#" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <span class="material-symbols-outlined text-slate-500 dark:text-slate-400 group-hover:text-primary transition-colors">chat</span>
                    <span class="flex-1 text-sm font-medium text-slate-700 dark:text-slate-300">Liên hệ hỗ trợ</span>
                    <span class="material-symbols-outlined text-slate-400 text-[20px]">chevron_right</span>
                </a>
                <a href="#" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <span class="material-symbols-outlined text-slate-500 dark:text-slate-400 group-hover:text-primary transition-colors">policy</span>
                    <span class="flex-1 text-sm font-medium text-slate-700 dark:text-slate-300">Chính sách bảo mật</span>
                    <span class="material-symbols-outlined text-slate-400 text-[20px]">chevron_right</span>
                </a>
                <a href="#" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <span class="material-symbols-outlined text-slate-500 dark:text-slate-400 group-hover:text-primary transition-colors">description</span>
                    <span class="flex-1 text-sm font-medium text-slate-700 dark:text-slate-300">Điều khoản sử dụng</span>
                    <span class="material-symbols-outlined text-slate-400 text-[20px]">chevron_right</span>
                </a>
            </div>
        </div>
    </div>

</div>
