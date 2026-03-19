<!-- Panel Thông báo / Cài đặt - Alpine.js (Slide from right, cả desktop + mobile) -->
<div x-show="isPanelOpen" x-cloak x-data="{ activeTab: 'notifications' }">
    <!-- Backdrop overlay -->
    <div @click="isPanelOpen = false"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/20 dark:bg-black/50 z-40"></div>

    <!-- Panel slide from right -->
    <div class="fixed inset-y-0 right-0 w-[90%] sm:w-[60%] lg:w-[30%] bg-white dark:bg-surface-dark shadow-2xl border-l border-slate-200 dark:border-border-dark flex flex-col z-50"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full">

        {{-- Header --}}
        <div class="flex items-center gap-4 px-5 py-4 border-b border-slate-200 dark:border-border-dark">
            <button @click="isPanelOpen = false" class="p-1 hover:bg-slate-100 dark:hover:bg-background-dark rounded transition-colors">
                <span class="material-symbols-outlined text-slate-500 text-[24px]">close</span>
            </button>
            <h3 class="text-slate-900 dark:text-white text-lg font-semibold"
                x-text="activeTab === 'notifications' ? 'Thông báo' : 'Cài đặt'"></h3>
        </div>

        {{-- Tabs Navigation --}}
        <div class="flex border-b border-slate-200 dark:border-border-dark">
            <button @click="activeTab = 'notifications'"
                :class="activeTab === 'notifications' ? 'bg-slate-50 dark:bg-background-dark text-slate-900 dark:text-white border-b-2 border-primary' : 'text-slate-500 dark:text-slate-400'"
                class="flex-1 px-4 py-3 text-sm font-medium transition-colors hover:bg-slate-50 dark:hover:bg-background-dark">
                Thông báo
            </button>
            <button @click="activeTab = 'settings'"
                :class="activeTab === 'settings' ? 'bg-slate-50 dark:bg-background-dark text-slate-900 dark:text-white border-b-2 border-primary' : 'text-slate-500 dark:text-slate-400'"
                class="flex items-center justify-center px-4 py-3 transition-colors hover:bg-slate-50 dark:hover:bg-background-dark">
                <span class="material-symbols-outlined text-[20px]">settings</span>
            </button>
        </div>

        {{-- Tab Content (Scrollable) --}}
        <div class="flex-1 overflow-y-auto">
            {{-- Notifications Tab --}}
            <div x-show="activeTab === 'notifications'" class="p-4">
                <div class="text-center text-slate-400 py-8">
                    <span class="material-symbols-outlined text-5xl mb-2 block">notifications_off</span>
                    <p class="text-sm">Chưa có thông báo mới</p>
                </div>
            </div>

            {{-- Settings Tab --}}
            <div x-show="activeTab === 'settings'" class="p-2">
                <button class="flex items-center gap-4 w-full px-4 py-3 text-left text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-background-dark rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[22px]">light_mode</span>
                    <span class="text-sm font-medium">Giao diện</span>
                </button>
                <button class="flex items-center gap-4 w-full px-4 py-3 text-left text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-background-dark rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[22px]">language</span>
                    <span class="text-sm font-medium">Ngôn ngữ</span>
                </button>
                <button class="flex items-center gap-4 w-full px-4 py-3 text-left text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-background-dark rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[22px]">notifications</span>
                    <span class="text-sm font-medium">Thông báo</span>
                </button>
                <button class="flex items-center gap-4 w-full px-4 py-3 text-left text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-background-dark rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[22px]">shield</span>
                    <span class="text-sm font-medium">Bảo mật</span>
                </button>
                <button class="flex items-center gap-4 w-full px-4 py-3 text-left text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-background-dark rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[22px]">help</span>
                    <span class="text-sm font-medium">Trợ giúp</span>
                </button>
            </div>
        </div>
    </div>
</div>