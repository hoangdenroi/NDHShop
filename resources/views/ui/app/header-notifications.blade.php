{{-- Icon thông báo + Dropdown (Dynamic với Alpine.js) --}}
{{-- Bảo mật: API filter theo auth()->id(), user khác không đọc được --}}
<div class="relative" x-data="{
    notiOpen: false,
    notifications: [],
    unreadCount: 0,
    loading: false,
    fetched: false,

    async fetchNotifications() {
        if (this.fetched && !this.loading) return;
        this.loading = true;
        try {
            const res = await fetch('{{ route('api.notifications') }}', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            this.notifications = data.notifications || [];
            this.unreadCount = data.unread_count || 0;
            this.fetched = true;
        } catch (e) {
            console.error(e);
        }
        this.loading = false;
    },

    async markAllRead() {
        try {
            await fetch('{{ route('api.notifications.read-all') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            });
            this.unreadCount = 0;
            this.notifications = this.notifications.map(n => ({ ...n, is_read: true }));
        } catch (e) {
            console.error(e);
        }
    },

    toggleOpen() {
        this.notiOpen = !this.notiOpen;
        if (this.notiOpen) {
            this.fetchNotifications();
        }
    },

    notiIcon(type) {
        const map = {
            order_cancelled: 'cancel',
            order_completed: 'check_circle',
            order_refund: 'currency_exchange',
        };
        return map[type] || 'notifications';
    },

    notiColor(type) {
        const map = {
            order_cancelled: 'text-rose-500 bg-rose-50 dark:bg-rose-500/10',
            order_completed: 'text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10',
            order_refund: 'text-blue-500 bg-blue-50 dark:bg-blue-500/10',
        };
        return map[type] || 'text-primary bg-primary/10';
    }
}" x-init="
    {{-- Fetch unread count on load --}}
    fetch('{{ route('api.notifications') }}', { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(d => { unreadCount = d.unread_count || 0; })
        .catch(() => {});
">
    <button @click="toggleOpen()"
        class="relative flex size-10 cursor-pointer items-center justify-center rounded-full text-slate-900 dark:text-white transition-colors"
        :class="notiOpen ? 'bg-primary text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800'">
        <span class="material-symbols-outlined">notifications</span>
        {{-- Badge chưa đọc --}}
        <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount" x-cloak
            class="absolute -top-1 -right-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white px-1"></span>
    </button>

    {{-- Dropdown thông báo --}}
    <div x-show="notiOpen" @click.outside="notiOpen = false"
        @keydown.escape.window="notiOpen = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
        class="absolute top-[100%] right-[-88px] sm:right-0 mt-3 w-[calc(100vw-24px)] sm:w-[380px] min-w-[280px] max-w-[380px] bg-white dark:bg-slate-800 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.3)] border border-slate-200 dark:border-slate-700 z-[200] origin-top-right transform"
        x-cloak>

        {{-- Mũi tên --}}
        <div
            class="absolute -top-[9px] right-[99px] sm:right-[11px] w-0 h-0 border-l-[9px] border-l-transparent border-r-[9px] border-r-transparent border-b-[9px] border-b-slate-200 dark:border-b-slate-700">
        </div>
        <div
            class="absolute -top-[7px] right-[101px] sm:right-[13px] w-0 h-0 border-l-[7px] border-l-transparent border-r-[7px] border-r-transparent border-b-[7px] border-b-white dark:border-b-slate-800">
        </div>

        {{-- Header --}}
        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700/50 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 dark:text-white text-[15px]">Thông báo</h3>
            <button @click="markAllRead()" x-show="unreadCount > 0"
                class="text-xs text-primary font-medium hover:underline cursor-pointer">
                Đã đọc tất cả
            </button>
        </div>

        {{-- Loading --}}
        <div x-show="loading && notifications.length === 0" class="flex items-center justify-center py-8">
            <span class="material-symbols-outlined text-[24px] text-primary animate-spin">progress_activity</span>
        </div>

        {{-- Danh sách thông báo --}}
        <div x-show="!loading && notifications.length > 0" class="max-h-[400px] overflow-y-auto scrollbar-hide">
            <template x-for="noti in notifications" :key="noti.id">
                <a :href="noti.action_url || '#'" @click="notiOpen = false"
                    class="flex gap-3 px-4 py-3 border-b border-slate-50 dark:border-slate-700/30 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"
                    :class="!noti.is_read ? 'bg-primary/[0.03] dark:bg-primary/[0.05]' : ''">
                    {{-- Icon --}}
                    <div class="shrink-0 w-9 h-9 rounded-full flex items-center justify-center"
                        :class="notiColor(noti.type)">
                        <span class="material-symbols-outlined text-[18px]" x-text="notiIcon(noti.type)"></span>
                    </div>
                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-900 dark:text-white leading-snug"
                            :class="!noti.is_read ? 'font-bold' : 'font-medium'" x-text="noti.title"></p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed line-clamp-2" x-text="noti.message"></p>
                        <p class="text-[11px] text-slate-400 mt-1" x-text="noti.created_at"></p>
                    </div>
                    {{-- Unread dot --}}
                    <div class="shrink-0 flex items-start pt-2">
                        <span x-show="!noti.is_read" class="w-2 h-2 rounded-full bg-primary"></span>
                    </div>
                </a>
            </template>
        </div>

        {{-- Rỗng --}}
        <div x-show="!loading && notifications.length === 0 && fetched" x-cloak
            class="flex flex-col items-center justify-center py-8 px-4 text-slate-500 dark:text-slate-400">
            <span class="material-symbols-outlined text-[40px] mb-2 opacity-50">notifications_off</span>
            <p class="text-sm font-medium">Bạn chưa có thông báo mới nào!</p>
        </div>
    </div>
</div>
