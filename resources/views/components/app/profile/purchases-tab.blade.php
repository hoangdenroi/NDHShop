<div x-show="activeTab === 'purchases'" x-cloak x-data="{
        orders: [],
        loading: false,
        hasMore: false,
        page: 1,
        search: '',
        searchTimeout: null,

        // Review modal
        reviewModal: false,
        reviewLoading: false,
        reviewData: { order_id: null, product_id: null, product_name: '', product_image: '', rating: 0, comment: '' },
        hoverRating: 0,

        init() {
            // Chỉ load nếu ngay từ đầu đã là tab này
            if (this.activeTab === 'purchases') {
                this.fetchOrders();
            }
            // Watch sự thay đổi của activeTab từ cha
            this.$watch('activeTab', (val) => {
                if (val === 'purchases' && this.orders.length === 0 && !this.loading) {
                    this.fetchOrders();
                }
            });
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
        },

        // Mở modal đánh giá
        openReview(order, item) {
            this.reviewData = {
                order_id: order.id,
                product_id: item.product_id,
                product_name: item.name,
                product_image: item.image,
                rating: 0,
                comment: ''
            };
            this.hoverRating = 0;
            this.reviewModal = true;
        },

        // Gửi đánh giá
        async submitReview() {
            if (this.reviewData.rating === 0) {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { type: 'warning', title: 'Cảnh báo', message: 'Vui lòng chọn số sao đánh giá.' }
                }));
                return;
            }
            this.reviewLoading = true;
            try {
                const res = await fetch('{{ route('api.reviews.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: this.reviewData.order_id,
                        product_id: this.reviewData.product_id,
                        rating: this.reviewData.rating,
                        comment: this.reviewData.comment || null
                    })
                });
                const data = await res.json();
                if (data.success) {
                    // Cập nhật review data cho item trong orders
                    this.orders.forEach(order => {
                        if (order.id === this.reviewData.order_id) {
                            order.items.forEach(item => {
                                if (item.product_id === this.reviewData.product_id) {
                                    item.review = {
                                        rating: this.reviewData.rating,
                                        comment: this.reviewData.comment,
                                        is_deleted: false
                                    };
                                }
                            });
                        }
                    });
                    this.reviewModal = false;
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'success', title: 'Thành công', message: data.message }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'error', title: 'Lỗi', message: data.message }
                    }));
                }
            } catch (e) {
                console.error(e);
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { type: 'error', title: 'Lỗi', message: 'Đã xảy ra lỗi hệ thống.' }
                }));
            }
            this.reviewLoading = false;
        },

        // Xóa đánh giá
        async deleteReview(order, item) {
            if (!confirm('Bạn có chắc muốn xóa đánh giá này? Hành động này không thể hoàn tác.')) return;
            try {
                const res = await fetch(`/apps/api/v1/reviews/${item.review.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    // Đánh dấu đã xóa — không cho đánh giá lại
                    item.review.is_deleted = true;
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'success', title: 'Thành công', message: data.message }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'error', title: 'Lỗi', message: data.message }
                    }));
                }
            } catch (e) {
                console.error(e);
            }
        },

        // Render sao dạng text
        renderStars(rating) {
            return '★'.repeat(rating) + '☆'.repeat(5 - rating);
        }
    }">
    <div
        class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Sản phẩm đã mua</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Danh sách các đơn hàng bạn đã thanh toán.
                    </p>
                </div>
            </div>
            {{-- Ô tìm kiếm --}}
            <div class="relative">
                <span
                    class="material-symbols-outlined text-[20px] text-slate-400 absolute left-3 top-1/2 -translate-y-1/2">search</span>
                <input type="text" x-model="search" @input="onSearch()"
                    placeholder="Tìm theo mã đơn hàng hoặc tên sản phẩm..."
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
                        <div
                            class="mb-3 p-2.5 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-lg flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-rose-500 shrink-0">cancel</span>
                            <p class="text-xs text-rose-600 dark:text-rose-400">Đơn hàng đã bị hủy. Số tiền đã được hoàn
                                vào tài khoản của bạn.</p>
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

                                {{-- Nút Đánh giá / Hiển thị sao --}}
                                <template x-if="order.status === 'completed'">
                                    <span class="shrink-0">
                                        {{-- Đã đánh giá và chưa xóa: hiển thị số sao + nút xóa --}}
                                        <template x-if="item.review && !item.review.is_deleted">
                                            <span class="inline-flex items-center gap-1.5">
                                                <span class="text-amber-400 text-sm tracking-tight"
                                                    x-text="renderStars(item.review.rating)"></span>
                                                <button @click="deleteReview(order, item)"
                                                    class="text-slate-400 hover:text-rose-500 transition-colors"
                                                    title="Xóa đánh giá">
                                                    <span class="material-symbols-outlined text-[14px]">close</span>
                                                </button>
                                            </span>
                                        </template>
                                        {{-- Đã xóa: hiển thị "Đã xóa" --}}
                                        <template x-if="item.review && item.review.is_deleted">
                                            <span class="text-xs text-slate-400 italic">Đã xóa</span>
                                        </template>
                                        {{-- Chưa đánh giá: hiển thị nút --}}
                                        <template x-if="!item.review">
                                            <button @click="openReview(order, item)"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-500/10 text-amber-600 text-xs font-bold hover:bg-amber-100 dark:hover:bg-amber-500/20 transition-colors"
                                                title="Đánh giá sản phẩm">
                                                <span class="material-symbols-outlined text-[16px]">star_rate</span>
                                                Đánh giá
                                            </button>
                                        </template>
                                    </span>
                                </template>

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

    {{-- Modal đánh giá sản phẩm --}}
    <template x-teleport="body">
        <div x-show="reviewModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
            @keydown.escape.window="reviewModal = false">

            <div @click.stop x-show="reviewModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

                {{-- Header --}}
                <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Đánh giá sản phẩm</h3>
                        <button @click="reviewModal = false"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    {{-- Thông tin sản phẩm --}}
                    <div class="flex items-center gap-3 mt-4">
                        <img :src="reviewData.product_image"
                            class="w-12 h-12 rounded-lg object-cover border border-slate-100 dark:border-slate-700"
                            alt="">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300 line-clamp-2"
                            x-text="reviewData.product_name"></span>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-5">
                    {{-- Chọn sao --}}
                    <div class="text-center">
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-3">Bạn đánh giá sản phẩm này như thế
                            nào?</p>
                        <div class="flex justify-center gap-1">
                            <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                <button @click="reviewData.rating = star" @mouseenter="hoverRating = star"
                                    @mouseleave="hoverRating = 0"
                                    class="text-4xl transition-all duration-150 transform hover:scale-110"
                                    :class="star <= (hoverRating || reviewData.rating) ? 'text-amber-400' : 'text-slate-300 dark:text-slate-600'">
                                    <span class="material-symbols-outlined text-[36px]"
                                        :style="star <= (hoverRating || reviewData.rating) ? 'font-variation-settings: \'FILL\' 1;' : ''">star</span>
                                </button>
                            </template>
                        </div>
                        <p class="text-xs text-slate-400 mt-2"
                            x-text="['', 'Rất tệ', 'Tệ', 'Bình thường', 'Tốt', 'Tuyệt vời'][reviewData.rating] || ''">
                        </p>
                    </div>

                    {{-- Nhận xét --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Nhận xét <span class="text-slate-400 font-normal">(không bắt buộc)</span>
                        </label>
                        <textarea x-model="reviewData.comment" rows="3" maxlength="1000"
                            placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all resize-none"></textarea>
                        <p class="text-xs text-slate-400 mt-1 text-right"
                            x-text="(reviewData.comment?.length || 0) + '/1000'"></p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-6 pt-0">
                    <button @click="submitReview()" :disabled="reviewLoading || reviewData.rating === 0"
                        class="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-primary text-white font-bold shadow-lg shadow-primary/25 hover:bg-primary/90 active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="material-symbols-outlined text-[20px]" :class="reviewLoading ? 'animate-spin' : ''"
                            x-text="reviewLoading ? 'progress_activity' : 'send'"></span>
                        <span x-text="reviewLoading ? 'Đang gửi...' : 'Gửi đánh giá'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>