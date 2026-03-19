@props(['title' => 'Dashboard'])
<header class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-border-dark bg-white dark:bg-background-dark z-20">
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-900 dark:text-white">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <h2 class="text-slate-900 dark:text-white text-lg font-bold">{{ $title }}</h2>
    </div>
    <div class="flex items-center gap-4">
        <!-- Search -->
        <div class="relative hidden md:block">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[20px]">search</span>
            <input class="bg-slate-100 dark:bg-surface-dark border border-slate-200 dark:border-border-dark text-slate-900 dark:text-white text-sm rounded-lg pl-10 pr-4 py-2 w-64 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary placeholder-slate-400 dark:placeholder-slate-500"
                placeholder="Search data..." type="text" />
        </div>

        <!-- Mobile Search (Alpine.js) -->
        <div class="relative md:hidden" x-data="{ isSearchOpen: false }">
            <button @click="isSearchOpen = !isSearchOpen"
                :class="isSearchOpen ? 'text-primary' : 'text-gray-500 dark:text-slate-400'"
                class="flex items-center justify-center size-9 hover:text-gray-900 dark:hover:text-white transition-colors">
                <span class="material-symbols-outlined text-[20px]">search</span>
            </button>

            <!-- Search Dropdown -->
            <div x-show="isSearchOpen" x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-[-50px] top-full mt-3 w-[280px] sm:w-[320px] bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg shadow-xl p-3 z-50 origin-top-right">
                <!-- Arrow -->
                <div class="absolute -top-[5px] right-[58px] size-2.5 bg-white dark:bg-surface-dark border-t border-l border-gray-200 dark:border-border-dark transform rotate-45"></div>

                <!-- Search Input -->
                <div class="relative">
                    <input type="text" placeholder="Tìm kiếm..." x-ref="mobileSearchInput" @keydown.escape="isSearchOpen = false"
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-background-dark border border-gray-200 dark:border-border-dark rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all shadow-sm">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 dark:text-slate-500 text-[20px]">search</span>
                </div>
            </div>

            <!-- Backdrop để đóng khi click ra ngoài -->
            <div x-show="isSearchOpen" @click="isSearchOpen = false" x-cloak class="fixed inset-0 z-40 cursor-default"></div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2">
            <button @click="isPanelOpen = !isPanelOpen"
                class="flex items-center justify-center size-9 rounded-lg bg-slate-100 dark:bg-surface-dark border border-slate-200 dark:border-border-dark text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:border-slate-300 dark:hover:border-slate-500 transition-colors">
                <span class="material-symbols-outlined text-[20px]">notifications</span>
            </button>
            <button 
                x-data="{ isDark: document.documentElement.classList.contains('dark') }"
                @click="
                    isDark = !isDark;
                    document.documentElement.classList.toggle('dark', isDark);
                    let theme = JSON.parse(localStorage.getItem('theme') || '{}');
                    theme.mode = isDark ? 'dark' : 'light';
                    localStorage.setItem('theme', JSON.stringify(theme));
                    fetch('{{ route('api.v1.settings.theme') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                        body: JSON.stringify({ mode: theme.mode, primaryColor: theme.primaryColor || '#0d59f2' })
                    }).catch(() => {});
                "
                class="flex items-center justify-center size-9 rounded-lg bg-slate-100 dark:bg-surface-dark border border-slate-200 dark:border-border-dark text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:border-slate-300 dark:hover:border-slate-500 transition-colors">
                <span x-show="isDark" class="material-symbols-outlined text-[20px]">light_mode</span>
                <span x-show="!isDark" class="material-symbols-outlined text-[20px]">dark_mode</span>
            </button>
        </div>
        <div class="relative" x-data="{ openProfile: false }">
            <div @click="openProfile = !openProfile" class="flex items-center gap-3 rounded-lg hover:bg-slate-100 dark:hover:bg-surface-dark cursor-pointer transition-colors p-1">
                <div class="bg-center bg-no-repeat bg-cover rounded-full size-9 relative"
                    data-alt="User avatar profile picture"
                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAFIVJGrseRQphdphToGMmrAxagUQu-7mGL_65zAh_GJKy6dV8HcFohGCRcF0Org4sUipu_aTpkgNj6FUuY2ueTWQ_ld986bOgRdiigMPpbSx-PmqM8WIjzklAbLXpjpkQf2Dsti6-M0kpgaILOej3cLib-E_iEWL0blGd0DxyACN5suFf5rG1Yl9lAuepnrVXpL10EKSei96O8oCvdwnkUwqD7NqT0XQuagaCYEvSDaCljvMAhO1E9_R9pdbYQh7MKhPhmV1G_3tI");'>
                    <div class="absolute bottom-0 right-0 size-2.5 bg-green-500 border-2 border-white dark:border-[#0b0f17] rounded-full"></div>
                </div>
            </div>

            <!-- Dropdown Menu -->
            <div x-show="openProfile" @click.away="openProfile = false"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                x-cloak
                class="absolute right-0 top-full mt-2 w-56 bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-xl shadow-xl z-50 overflow-hidden">
                
                {{-- Thông tin user --}}
                @auth
                <div class="px-4 py-3 border-b border-slate-100 dark:border-border-dark">
                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                </div>
                @endauth

                <div class="py-1">
                    <a target="_blank" href="{{ route('app.home') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <span class="material-symbols-outlined text-[18px] text-slate-400">home</span>
                        Trang chủ
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <span class="material-symbols-outlined text-[18px] text-slate-400">settings</span>
                        Cài đặt
                    </a>
                </div>

                <div class="border-t border-slate-100 dark:border-border-dark py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors w-full text-left">
                            <span class="material-symbols-outlined text-[18px]">logout</span>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
