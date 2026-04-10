<div x-show="activeTab === 'history'" x-cloak x-data="{
        history: [],
        loading: false,
        hasMore: false,
        page: 1,

        init() {
            if (this.activeTab === 'history') {
                this.fetchHistory();
            }
            this.$watch('activeTab', (val) => {
                if (val === 'history' && this.history.length === 0 && !this.loading) {
                    this.fetchHistory();
                }
            });
        },

        async fetchHistory() {
            this.loading = true;
            try {
                const res = await fetch(`{{ route('api.profile.history') }}?page=${this.page}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.history = [...this.history, ...data.data];
                this.hasMore = data.has_more;
                this.page = data.next_page;
            } catch (e) {
                console.error(e);
            }
            this.loading = false;
        }
    }">
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Lịch sử giao dịch</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Biến động số dư và các giao dịch gần đây của bạn.</p>
        </div>

        {{-- Loading trạng thái đầu tiên --}}
        <div x-show="loading && history.length === 0" class="p-12 flex flex-col items-center">
            <span class="material-symbols-outlined text-[32px] text-primary animate-spin">progress_activity</span>
            <p class="text-sm text-slate-400 mt-3">Đang tải lịch sử giao dịch...</p>
        </div>

        {{-- Khi chưa có lịch sử nào --}}
        <div x-show="!loading && history.length === 0" x-cloak class="p-12 flex flex-col items-center text-center">
            <span class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600 mb-3">receipt_long</span>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Chưa có giao dịch hoặc biến động số dư nào.</p>
        </div>

        {{-- Danh sách giao dịch --}}
        <div x-show="history.length > 0" x-cloak class="divide-y divide-slate-100 dark:divide-slate-700/50">
            <template x-for="item in history" :key="item.id">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    
                    {{-- Thông tin lệnh --}}
                    <div class="flex items-start gap-4 flex-1">
                        <div class="h-10 w-10 shrink-0 rounded-full flex items-center justify-center shadow-sm"
                             :class="item.amount_change_raw > 0 ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30' : (item.amount_change_raw < 0 ? 'bg-rose-100 text-rose-600 dark:bg-rose-900/30' : 'bg-slate-100 text-slate-600 dark:bg-slate-700')">
                            <span class="material-symbols-outlined text-[20px]" 
                                  x-text="item.amount_change_raw > 0 ? 'arrow_downward' : (item.amount_change_raw < 0 ? 'arrow_upward' : 'sync')">
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white" x-text="item.title"></h3>
                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs">
                                <span class="text-slate-500 font-medium flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">schedule</span>
                                    <span x-text="item.created_at"></span>
                                </span>
                                <template x-if="item.details">
                                    <span class="text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded" x-text="item.details"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Biến động Balance --}}
                    <div class="flex flex-col items-end shrink-0 pl-14 sm:pl-0">
                        <template x-if="item.amount_change">
                            <span class="font-bold text-base font-mono tracking-tight" 
                                  :class="item.amount_change_raw > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                                  x-text="item.amount_change">
                            </span>
                        </template>
                        <template x-if="!item.amount_change">
                            <span class="font-bold text-base text-slate-500 font-mono tracking-tight">0đ</span>
                        </template>
                    </div>

                </div>
            </template>
        </div>

        {{-- Load more --}}
        <div x-show="hasMore" x-cloak class="p-4 flex justify-center bg-slate-50 dark:bg-slate-800/80 border-t border-slate-100 dark:border-slate-700">
            <button @click="fetchHistory()" :disabled="loading" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50">
                <span class="material-symbols-outlined text-[18px]" :class="loading ? 'animate-spin' : ''" x-text="loading ? 'progress_activity' : 'expand_more'"></span>
                Xem thêm
            </button>
        </div>
    </div>
</div>
