<div x-data="toastComponent()" @toast.window="add($event.detail)"
    class="fixed top-[30px] right-4 z-[9999] flex flex-col gap-3 w-full max-w-sm pointer-events-none">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-[120%] opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-300 transform"
            x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-[120%] opacity-0"
            class="pointer-events-auto relative w-full max-w-sm overflow-hidden rounded-xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700">
            <div class="p-4 flex items-start">
                <div class="shrink-0 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[24px]" :class="{
                              'text-green-500': toast.type === 'success',
                              'text-red-500': toast.type === 'error',
                              'text-amber-500': toast.type === 'warning',
                              'text-blue-500': toast.type === 'info' || !toast.type
                          }" x-text="toast.icon"></span>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-bold text-slate-900 dark:text-white" x-text="toast.title"></p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400" x-text="toast.message"></p>
                    {{-- Link tùy chọn (ví dụ: "Nạp tiền ngay") --}}
                    <template x-if="toast.link">
                        <a :href="toast.link" class="inline-flex items-center gap-1 mt-2 text-xs font-bold text-primary hover:underline">
                            <span x-text="toast.linkText || 'Xem thêm'"></span>
                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                        </a>
                    </template>
                </div>
                <!-- Nút close nằm đây với z-index, pointer-events-auto để bấm được -->
                <div class="ml-4 flex shrink-0">
                    <button @click="remove(toast.id)" type="button"
                        class="inline-flex relative z-10 rounded-md bg-white dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                        <span class="sr-only">Đóng</span>
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('toastComponent', () => ({
                toasts: [],
                add(toast) {
                    const storedSettings = JSON.parse(localStorage.getItem('notifications') || '{"push":true}');
                    if (storedSettings.push === false) return; // Không hiển thị nếu user đã tắt Push Noti

                    toast.id = Date.now() + Math.random().toString(36).substr(2, 9);
                    toast.visible = true;

                    // Xử lý type mặc định và icon dựa trên 4 loại: success, error, warning, info
                    if (!toast.type) toast.type = 'info';
                    if (!toast.icon) {
                        if (toast.type === 'success') toast.icon = 'check_circle';
                        else if (toast.type === 'error') toast.icon = 'error';
                        else if (toast.type === 'warning') toast.icon = 'warning';
                        else toast.icon = 'info';
                    }

                    this.toasts.push(toast);

                    // Nếu có link, tăng timeout để user kịp đọc và bấm
                    const defaultTimeout = toast.link ? 8000 : 5000;
                    setTimeout(() => {
                        this.remove(toast.id);
                    }, toast.timeout || defaultTimeout);
                },
                remove(id) {
                    // Sửa lại hàm remove để nút đóng hoạt động mượt mà
                    let index = this.toasts.findIndex(t => t.id === id);
                    if (index !== -1) {
                        this.toasts[index].visible = false;
                        setTimeout(() => {
                            this.toasts.splice(index, 1);
                        }, 350); // Chờ hiệu ứng slide-out hoàn tất (300ms + buffer 50ms)
                    }
                }
            }));
        });
    </script>
@endpush