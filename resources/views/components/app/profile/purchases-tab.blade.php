<div x-show="activeTab === 'purchases'" x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak x-data="{
        orders: [],
        loading: true,
        hasMore: false,
        page: 1,
        search: '',
        searchTimeout: null,

        init() {
            this.fetchOrders();
        },

        async fetchOrders(reset = false) {
            if (reset) {
                this.orders = [];
                this.page = 1;
                this.hasMore = false;
            }
            this.loading = true;
            try {
                let url = `{{ route('api.profile.orders') }}?page=${this.page}`;
                if (this.search.trim()) {
                    url += `&search=${encodeURIComponent(this.search.trim())}`;
                }
                const res = await fetch(url, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.orders = reset ? data.data : [...this.orders, ...data.data];
                this.hasMore = data.has_more;
                this.page = data.next_page;
            } catch (e) {
                console.error(e);
            }
            this.loading = false;
        },

        onSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.fetchOrders(true), 400);
        },

        formatMoney(val) {
            return new Intl.NumberFormat('vi-VN').format(val) + 'đ';
        },

        statusLabel(status) {
            const map = { completed: 'Hoàn tất', pending: 'Chờ xử lý', cancelled: 'Đã hủy' };
            return map[status] || status;
        },

        statusColor(status) {
            const map = { completed: 'text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10', pending: 'text-amber-600 bg-amber-50 dark:bg-amber-500/10', cancelled: 'text-rose-600 bg-rose-50 dark:bg-rose-500/10' };
            return map[status] || 'text-slate-600 bg-slate-50';
        }
    }">
    <div
        class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Sản phẩm đã mua</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Danh sách các đơn hàng bạn đã thanh toán.</p>
                </div>
            </div>
            {{-- Ô tìm kiếm --}}
            <div class="relative">
                <span class="material-symbols-outlined text-[20px] text-slate-400 absolute left-3 top-1/2 -translate-y-1/2">search</span>
                <input type="text" x-model="search" @input="onSearch()" placeholder="Tìm theo mã đơn hàng hoặc tên sản phẩm..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                <button x-show="search.length > 0" x-cloak @click="search = ''; fetchOrders(true)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </div>

        {{-- Loading --}}
        <div x-show="loading && orders.length === 0" class="p-12 flex flex-col items-center">
            <span class="material-symbols-outlined text-[32px] text-primary animate-spin">progress_activity</span>
            <p class="text-sm text-slate-400 mt-3">Đang tải đơn hàng...</p>
        </div>

        {{-- Empty state --}}
        <div x-show="!loading && orders.length === 0" x-cloak class="p-12 flex flex-col items-center text-center">
            <span
                class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600 mb-3">shopping_bag</span>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Bạn chưa mua sản phẩm nào.</p>
            <a href="{{ route('app.src-app-game') }}"
                class="mt-4 text-primary text-sm font-semibold hover:underline">Khám phá ngay →</a>
        </div>

        {{-- Danh sách đơn hàng --}}
        <div x-show="orders.length > 0" x-cloak class="divide-y divide-slate-100 dark:divide-slate-700">
            <template x-for="order in orders" :key="order.id">
                <div class="p-5">
                    {{-- Order Header --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-slate-900 dark:text-white"
                                x-text="order.order_code"></span>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                                :class="statusColor(order.status)" x-text="statusLabel(order.status)"></span>
                        </div>
                        <span class="text-xs text-slate-400" x-text="order.created_at"></span>
                    </div>

                    {{-- Banner cảnh báo đơn hàng đã bị hủy --}}
                    <template x-if="order.status === 'cancelled'">
                        <div class="mb-3 p-2.5 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-lg flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-rose-500 shrink-0">cancel</span>
                            <p class="text-xs text-rose-600 dark:text-rose-400">Đơn hàng đã bị hủy. Số tiền đã được hoàn vào tài khoản của bạn.</p>
                        </div>
                    </template>

                    {{-- Items --}}
                    <div class="space-y-2">
                        <template x-for="item in order.items" :key="item.product_id">
                            <div
                                class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <a :href="'/apps/product/' + item.slug" class="shrink-0">
                                    <img :src="item.image"
                                        class="w-10 h-10 rounded-lg object-cover border border-slate-100 dark:border-slate-700"
                                        alt="">
                                </a>
                                <a :href="'/apps/product/' + item.slug"
                                    class="text-sm font-medium text-slate-700 dark:text-slate-300 truncate flex-1 hover:text-primary transition-colors"
                                    x-text="item.name"></a>
                                <span class="text-sm font-bold text-primary shrink-0"
                                    x-text="formatMoney(item.price)"></span>
                                {{-- Nút Download: chỉ hiện khi đơn hàng hoàn tất và có link tải --}}
                                <template x-if="order.status === 'completed' && item.download_url">
                                    <a :href="item.download_url" target="_blank" rel="noopener noreferrer"
                                        class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 text-xs font-bold hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-colors"
                                        title="Tải xuống">
                                        <span class="material-symbols-outlined text-[16px]">download</span>
                                        Tải
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Order Footer --}}
                    <div
                        class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <div class="flex items-center gap-2">
                            <template x-if="order.coupon_code">
                                <span
                                    class="text-xs text-green-600 bg-green-50 dark:bg-green-500/10 px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">local_offer</span>
                                    <span x-text="order.coupon_code"></span>
                                    <span x-text="'-' + formatMoney(order.discount_amount)"></span>
                                </span>
                            </template>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-slate-400">Tổng:</span>
                            <span class="text-sm font-bold text-primary ml-1"
                                x-text="formatMoney(order.total_amount)"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Load more --}}
        <div x-show="hasMore" x-cloak class="p-4 text-center border-t border-slate-100 dark:border-slate-700">
            <button @click="fetchOrders()" :disabled="loading"
                class="text-sm text-primary font-semibold hover:underline disabled:opacity-50 flex items-center gap-1 mx-auto">
                <span class="material-symbols-outlined text-[16px]" :class="loading ? 'animate-spin' : ''"
                    x-text="loading ? 'progress_activity' : 'expand_more'"></span>
                Tải thêm
            </button>
        </div>
    </div>
</div>