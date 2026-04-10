<div x-show="activeTab === 'favorites'" x-cloak x-data="{
        favorites: [],
        loading: false,
        hasMore: false,
        page: 1,

        init() {
            if (this.activeTab === 'favorites') {
                this.fetchFavorites();
            }
            this.$watch('activeTab', (val) => {
                if (val === 'favorites' && this.favorites.length === 0 && !this.loading) {
                    this.fetchFavorites();
                }
            });
        },

        async fetchFavorites() {
            this.loading = true;
            try {
                const res = await fetch(`{{ route('api.profile.favorites') }}?page=${this.page}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.favorites = [...this.favorites, ...data.data];
                this.hasMore = data.has_more;
                this.page = data.next_page;
            } catch (e) {
                console.error(e);
            }
            this.loading = false;
        },

        formatMoney(val) {
            return new Intl.NumberFormat('vi-VN').format(val) + 'đ';
        },

        async removeFromWishlist(productId, index) {
            try {
                const res = await fetch('{{ route('app.wishlist.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: productId })
                });
                const data = await res.json();
                if (data.success && !data.is_favorited) {
                    this.favorites.splice(index, 1);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'success', title: 'Thành công', message: 'Đã bỏ yêu thích.' }
                    }));
                }
            } catch (e) {
                console.error(e);
            }
        }
    }">
    <div
        class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Sản phẩm yêu thích</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Các sản phẩm bạn đã lưu vào danh sách yêu thích.
            </p>
        </div>

        {{-- Loading --}}
        <div x-show="loading && favorites.length === 0" class="p-12 flex flex-col items-center">
            <span class="material-symbols-outlined text-[32px] text-primary animate-spin">progress_activity</span>
            <p class="text-sm text-slate-400 mt-3">Đang tải danh sách yêu thích...</p>
        </div>

        {{-- Empty state --}}
        <div x-show="!loading && favorites.length === 0" x-cloak class="p-12 flex flex-col items-center text-center">
            <span class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600 mb-3">favorite</span>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Chưa có sản phẩm yêu thích nào.</p>
            <a href="{{ route('app.src-app-game') }}"
                class="mt-4 text-primary text-sm font-semibold hover:underline">Khám phá ngay →</a>
        </div>

        {{-- Danh sách sản phẩm yêu thích --}}
        <div x-show="favorites.length > 0" x-cloak class="divide-y divide-slate-100 dark:divide-slate-700">
            <template x-for="(item, index) in favorites" :key="item.id">
                <div
                    class="flex items-center gap-4 p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                    {{-- Ảnh --}}
                    <a :href="'/apps/product/' + item.slug" class="shrink-0">
                        <img :src="item.image"
                            class="w-14 h-14 rounded-xl object-cover border border-slate-100 dark:border-slate-700"
                            alt="">
                    </a>

                    {{-- Thông tin --}}
                    <div class="flex-1 min-w-0">
                        <a :href="'/apps/product/' + item.slug" class="block">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate group-hover:text-primary transition-colors"
                                x-text="item.name"></h3>
                        </a>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            <template x-if="item.is_purchased">
                                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 px-1.5 py-0.5 rounded flex items-center gap-0.5">
                                    <span class="material-symbols-outlined text-[12px]">check_circle</span>
                                    Đã mua
                                </span>
                            </template>
                            <template x-if="item.platform">
                                <span class="text-xs text-blue-600 bg-blue-50 dark:bg-blue-500/10 px-1.5 py-0.5 rounded"
                                    x-text="item.platform"></span>
                            </template>
                            <template x-if="item.category">
                                <span class="text-xs text-slate-400" x-text="item.category"></span>
                            </template>
                        </div>
                    </div>

                    {{-- Giá --}}
                    <div class="text-right shrink-0">
                        <template x-if="item.sale_price !== null && item.sale_price < item.price">
                            <div>
                                <div class="text-sm font-bold text-primary" x-text="formatMoney(item.sale_price)"></div>
                                <div class="text-xs text-slate-400 line-through" x-text="formatMoney(item.price)"></div>
                            </div>
                        </template>
                        <template x-if="item.sale_price === null || item.sale_price >= item.price">
                            <div class="text-sm font-bold text-primary" x-text="formatMoney(item.price)"></div>
                        </template>
                    </div>

                    {{-- Nút xóa yêu thích --}}
                    <button @click="removeFromWishlist(item.product_id, index)" title="Bỏ yêu thích"
                        class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-red-500 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                        <span class="material-symbols-outlined text-[16px]">heart_minus</span>
                        Bỏ thích
                    </button>
                </div>
            </template>
        </div>

        {{-- Load more --}}
        <div x-show="hasMore" x-cloak class="p-4 text-center border-t border-slate-100 dark:border-slate-700">
            <button @click="fetchFavorites()" :disabled="loading"
                class="text-sm text-primary font-semibold hover:underline disabled:opacity-50 flex items-center gap-1 mx-auto">
                <span class="material-symbols-outlined text-[16px]" :class="loading ? 'animate-spin' : ''"
                    x-text="loading ? 'progress_activity' : 'expand_more'"></span>
                Tải thêm
            </button>
        </div>
    </div>
</div>