{{-- Modal chi tiết đơn hàng (fetch API khi mở) --}}
<div x-data="{
    show: false,
    loading: true,
    order: null,
    newStatus: '',
    showCancelWarning: false,
    updating: false,

    async fetchOrder(id) {
        this.loading = true;
        this.show = true;
        this.showCancelWarning = false;
        this.updating = false;
        try {
            const res = await fetch(`/admin/orders/${id}`, {
                headers: { 'Accept': 'application/json' }
            });
            this.order = await res.json();
            this.newStatus = this.order.status;
        } catch (e) {
            console.error(e);
        }
        this.loading = false;
    },

    formatMoney(val) {
        return new Intl.NumberFormat('vi-VN').format(val) + 'đ';
    },

    statusLabel(status) {
        const map = { completed: 'Hoàn tất', pending: 'Chờ xử lý', cancelled: 'Đã hủy' };
        return map[status] || status;
    },

    statusColor(status) {
        const map = {
            completed: 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
            pending: 'bg-amber-500/10 text-amber-600 border-amber-500/20',
            cancelled: 'bg-rose-500/10 text-rose-500 border-rose-500/20'
        };
        return map[status] || 'bg-slate-500/10 text-slate-500 border-slate-500/20';
    }
}" @open-order-detail.window="fetchOrder($event.detail.id)">

    <div x-cloak x-show="show" class="fixed inset-0 z-50 flex items-center justify-center pointer-events-none">
        {{-- Backdrop --}}
        <div x-show="show" x-transition.opacity.duration.300ms
            class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm pointer-events-auto" @click="show = false">
        </div>

        {{-- Modal --}}
        <div x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="relative w-full max-w-3xl bg-white dark:bg-surface-dark rounded-2xl shadow-xl border border-slate-200 dark:border-border-dark overflow-hidden pointer-events-auto mx-4 flex flex-col max-h-[90vh]">

            {{-- Header --}}
            <div
                class="flex items-center justify-between p-4 sm:p-6 border-b border-slate-100 dark:border-border-dark shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-primary/10 text-primary rounded-lg">
                        <span class="material-symbols-outlined text-[24px]">receipt_long</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Chi tiết đơn hàng</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400" x-show="order"
                            x-text="'#' + order?.order_code"></p>
                    </div>
                </div>
                <button @click="show = false"
                    class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-4 sm:p-6 overflow-y-auto custom-scrollbar flex-1">

                {{-- Loading --}}
                <div x-show="loading" class="flex flex-col items-center justify-center py-12">
                    <span
                        class="material-symbols-outlined text-[32px] text-primary animate-spin">progress_activity</span>
                    <p class="text-sm text-slate-400 mt-3">Đang tải thông tin...</p>
                </div>

                {{-- Content --}}
                <div x-show="!loading && order" x-cloak class="space-y-6">

                    {{-- Thông tin đơn hàng + Khách hàng --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Đơn hàng --}}
                        <div
                            class="bg-slate-50 dark:bg-background-dark rounded-xl p-4 border border-slate-100 dark:border-border-dark">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-primary">info</span>
                                Thông tin đơn hàng
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Mã đơn:</span>
                                    <span class="font-bold text-primary" x-text="order?.order_code"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Ngày tạo:</span>
                                    <span class="text-slate-700 dark:text-slate-300" x-text="order?.created_at"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Thanh toán:</span>
                                    <span class="text-slate-700 dark:text-slate-300"
                                        x-text="order?.payment_method || 'Số dư'"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500">Trạng thái:</span>
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                        :class="statusColor(order?.status)" x-text="statusLabel(order?.status)"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Khách hàng --}}
                        <div
                            class="bg-slate-50 dark:bg-background-dark rounded-xl p-4 border border-slate-100 dark:border-border-dark">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-primary">person</span>
                                Khách hàng
                            </h3>
                            <template x-if="order?.user">
                                <div class="flex items-center gap-3">
                                    <template x-if="order.user.avatar_url">
                                        <img :src="order.user.avatar_url"
                                            class="w-10 h-10 rounded-full object-cover border border-slate-200" alt="">
                                    </template>
                                    <template x-if="!order.user.avatar_url">
                                        <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold"
                                            x-text="order.user.name?.charAt(0).toUpperCase()"></div>
                                    </template>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-900 dark:text-white truncate"
                                            x-text="order.user.name"></p>
                                        <p class="text-xs text-slate-400 truncate" x-text="order.user.email"></p>
                                        <p class="text-xs text-slate-500 mt-0.5">Số dư: <span
                                                class="font-bold text-primary"
                                                x-text="formatMoney(order.user.balance)"></span></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Bảng sản phẩm --}}
                    <div
                        class="bg-slate-50 dark:bg-background-dark rounded-xl border border-slate-100 dark:border-border-dark overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-border-dark">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-primary">inventory_2</span>
                                Sản phẩm
                            </h3>
                        </div>
                        <div class="divide-y divide-slate-100 dark:divide-border-dark">
                            <template x-for="item in order?.items" :key="item.product_id">
                                <div class="flex items-center gap-3 p-3">
                                    <template x-if="item.image">
                                        <img :src="item.image"
                                            class="w-10 h-10 rounded-lg object-cover border border-slate-200 dark:border-slate-700 shrink-0"
                                            alt="">
                                    </template>
                                    <template x-if="!item.image">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                            <span
                                                class="material-symbols-outlined text-[18px] text-slate-400">image</span>
                                        </div>
                                    </template>
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300 flex-1 truncate"
                                        x-text="item.name"></span>
                                    <span class="text-sm font-bold text-primary shrink-0"
                                        x-text="formatMoney(item.price)"></span>
                                </div>
                            </template>
                        </div>
                        {{-- Tổng tiền --}}
                        <div
                            class="px-4 py-3 border-t border-slate-200 dark:border-border-dark bg-white/50 dark:bg-slate-800/50">
                            <template x-if="order?.coupon_code">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-green-600 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">local_offer</span>
                                        <span x-text="order.coupon_code"></span>
                                    </span>
                                    <span class="text-green-600 font-medium"
                                        x-text="'-' + formatMoney(order.discount_amount)"></span>
                                </div>
                            </template>
                            <div class="flex justify-between">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">Tổng cộng:</span>
                                <span class="text-base font-bold text-primary"
                                    x-text="formatMoney(order?.total_amount)"></span>
                            </div>
                        </div>
                    </div>

                    {{-- License Keys --}}
                    <template x-if="order?.licenses && order.licenses.length > 0">
                        <div
                            class="bg-slate-50 dark:bg-background-dark rounded-xl border border-slate-100 dark:border-border-dark overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-100 dark:border-border-dark">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-primary">vpn_key</span>
                                    License Keys
                                </h3>
                            </div>
                            <div class="divide-y divide-slate-100 dark:divide-border-dark">
                                <template x-for="lic in order.licenses" :key="lic.license_key">
                                    <div class="flex items-center justify-between p-3">
                                        <div class="min-w-0">
                                            <code
                                                class="text-xs bg-slate-200 dark:bg-slate-700 px-2 py-1 rounded font-mono text-slate-700 dark:text-slate-300"
                                                x-text="lic.license_key"></code>
                                            <p class="text-xs text-slate-400 mt-1" x-text="lic.product_name"></p>
                                        </div>
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                                            :class="lic.is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400'"
                                            x-text="lic.is_active ? 'Active' : 'Inactive'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Cập nhật trạng thái --}}
                    {{-- Nếu đã hủy: hiện thông báo khóa --}}
                    <template x-if="order?.status === 'cancelled'">
                        <div
                            class="bg-rose-50 dark:bg-rose-500/10 rounded-xl border border-rose-200 dark:border-rose-500/20 p-4">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[24px] text-rose-500">lock</span>
                                <div>
                                    <p class="text-sm font-bold text-rose-600 dark:text-rose-400">Đơn hàng đã bị hủy</p>
                                    <p class="text-xs text-rose-500 dark:text-rose-400/80 mt-0.5">Không thể thay đổi
                                        trạng thái. Số tiền đã được hoàn cho khách hàng.</p>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Nếu chưa hủy: hiện form cập nhật --}}
                    <div x-show="order?.status !== 'cancelled'"
                        class="bg-slate-50 dark:bg-background-dark rounded-xl border border-slate-100 dark:border-border-dark p-4">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-primary">sync</span>
                            Cập nhật trạng thái
                        </h3>
                        <form :action="`/admin/orders/${order?.id}/status`" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
                                <div class="flex-1 w-full">
                                    <div class="relative">
                                        <span
                                            class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
                                        <select name="status" x-model="newStatus"
                                            @change="showCancelWarning = (newStatus === 'cancelled' && order?.status !== 'cancelled')"
                                            class="w-full pl-4 pr-10 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-border-dark rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all appearance-none">
                                            <option value="pending">Chờ xử lý</option>
                                            <option value="completed">Hoàn tất</option>
                                            <option value="cancelled">Đã hủy</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" :disabled="updating || newStatus === order?.status"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm shadow-primary/25 disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[18px]">save</span>
                                    Cập nhật
                                </button>
                            </div>

                            {{-- Cảnh báo hủy đơn --}}
                            <div x-show="showCancelWarning" x-cloak x-transition
                                class="mt-3 p-3 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-xl">
                                <p class="text-sm text-rose-600 dark:text-rose-400 flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">warning</span>
                                    <span>
                                        Hủy đơn hàng sẽ <strong>hoàn lại <span
                                                x-text="formatMoney(order?.total_amount)"></span></strong> vào tài khoản
                                        khách hàng
                                        <strong x-text="order?.user?.name"></strong> và gửi thông báo cho họ.
                                    </span>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>