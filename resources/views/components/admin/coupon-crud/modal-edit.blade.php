<div x-data="{ 
        show: false,
        coupon: null,
        form: {
            id: '',
            code: '',
            type: 'percent',
            value: '',
            min_order: '0',
            max_uses: '',
            expires_at: '',
            is_active: false
        },
        actionUrl: ''
    }" @open-edit-coupon.window="
        coupon = $event.detail;
        form.id = coupon.id;
        form.code = coupon.code;
        form.type = coupon.type;
        form.value = coupon.value;
        form.min_order = coupon.min_order;
        form.max_uses = coupon.max_uses || '';
        form.expires_at = coupon.expires_at ? coupon.expires_at.substring(0, 16) : '';
        form.is_active = coupon.is_active ? true : false;
        actionUrl = '{{ route('admin.coupons.update', ':id') }}'.replace(':id', coupon.id);
        show = true;
    " @close-modal.window="if ($event.detail === 'edit-coupon') show = false">

    <div x-cloak x-show="show" class="fixed inset-0 z-50 flex items-center justify-center pointer-events-none">
        <!-- Backdrop -->
        <div x-show="show" x-transition.opacity.duration.300ms
            class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm pointer-events-auto" @click="show = false"></div>

        <!-- Modal -->
        <div x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="relative w-full max-w-2xl bg-white dark:bg-surface-dark rounded-2xl shadow-xl border border-slate-200 dark:border-border-dark overflow-hidden pointer-events-auto mx-4 flex flex-col max-h-[90vh]">

            <!-- Header -->
            <div
                class="flex items-center justify-between p-4 sm:p-6 border-b border-slate-100 dark:border-border-dark shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-500/10 text-blue-600 rounded-lg">
                        <span class="material-symbols-outlined text-[24px]">edit</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Cập nhật mã giảm giá</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Thay đổi thông tin cho mã: <span
                                class="font-bold text-slate-700 dark:text-slate-300" x-text="form.code"></span></p>
                    </div>
                </div>
                <button @click="show = false"
                    class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Body -->
            <form :action="actionUrl" method="POST" class="flex flex-col overflow-hidden h-full">
                @csrf
                @method('PUT')
                <div class="p-4 sm:p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Code -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Mã giảm
                                giá <span class="text-rose-500">*</span></label>
                            <input type="text" name="code" x-model="form.code" required placeholder="VD: TET2024"
                                style="text-transform: uppercase;"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Loại giảm
                                giá <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                                <select name="type" x-model="form.type" required
                                    class="w-full pl-4 pr-10 py-2.5 bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all appearance-none">
                                    <option value="percent">Theo phần trăm (%)</option>
                                    <option value="fixed">Số tiền cố định (đ)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Value -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Giá trị
                                giảm <span class="text-rose-500">*</span></label>
                            <input type="number" name="value" x-model="form.value" required min="0" step="0.01"
                                placeholder="Nhập giá trị..."
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>

                        <!-- Min Order -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Đơn hàng
                                tối thiểu (đ) <span class="text-rose-500">*</span></label>
                            <input type="number" name="min_order" x-model="form.min_order" required min="0"
                                placeholder="0"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>

                        <!-- Max Uses -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Số lượng
                                tối đa</label>
                            <input type="number" name="max_uses" x-model="form.max_uses" min="1"
                                placeholder="Để trống nếu không giới hạn"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>

                        <!-- Expires at -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Ngày hết
                                hạn</label>
                            <input type="datetime-local" name="expires_at" x-model="form.expires_at"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="pt-4 border-t border-slate-100 dark:border-border-dark">
                        <label class="flex items-center gap-3 cursor-pointer group w-fit">
                            <div class="relative">
                                <input type="checkbox" name="is_active" class="peer sr-only" x-model="form.is_active"
                                    value="1">
                                <div
                                    class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                </div>
                            </div>
                            <span
                                class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-primary transition-colors">Trạng
                                thái kích hoạt</span>
                        </label>
                    </div>

                </div>

                <!-- Footer -->
                <div
                    class="p-4 sm:p-6 border-t border-slate-100 dark:border-border-dark bg-slate-50/50 dark:bg-surface-dark shrink-0 flex items-center justify-end gap-3">
                    <button type="button" @click="show = false"
                        class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                        Hủy bỏ
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-primary/25">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>