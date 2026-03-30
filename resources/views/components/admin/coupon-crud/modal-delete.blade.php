<div x-data="{ 
        show: false,
        coupon: null,
        actionUrl: ''
    }" 
    @open-delete-coupon.window="
        coupon = $event.detail;
        actionUrl = '{{ route('admin.coupons.destroy', ':id') }}'.replace(':id', coupon.id);
        show = true;
    "
    @close-modal.window="if ($event.detail === 'delete-coupon') show = false">
    
    <div x-cloak x-show="show" class="fixed inset-0 z-50 flex items-center justify-center pointer-events-none">
        <!-- Backdrop -->
        <div x-show="show" x-transition.opacity.duration.300ms class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm pointer-events-auto" @click="show = false"></div>

        <!-- Modal -->
        <div x-show="show" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-md bg-white dark:bg-surface-dark rounded-2xl shadow-xl border border-slate-200 dark:border-border-dark overflow-hidden pointer-events-auto mx-4 p-6 sm:p-8 text-center">
            
            <div class="w-16 h-16 rounded-full bg-rose-100 dark:bg-rose-500/20 text-rose-500 flex items-center justify-center mx-auto mb-5">
                <span class="material-symbols-outlined text-[32px]">warning</span>
            </div>
            
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Xác nhận xóa?</h3>
            <p class="text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
                Bạn có chắc chắn muốn xóa mã giảm giá <span class="font-bold text-slate-700 dark:text-slate-300" x-text="coupon?.code"></span> không?<br>
                Hành động này không thể hoàn tác.
            </p>

            <form :action="actionUrl" method="POST" class="flex items-center justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="show = false" 
                    class="px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-background-dark hover:bg-slate-200 dark:hover:bg-slate-800 rounded-xl transition-colors">
                    Hủy bỏ
                </button>
                <button type="submit" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm shadow-rose-500/25">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                    Xóa ngay
                </button>
            </form>
        </div>
    </div>
</div>
