{{-- Modal Thêm người dùng mới --}}
<x-ui.modal name="create-user" maxWidth="md">
    <form method="POST" action="{{ route('admin.users.store') }}" class="p-6">
        @csrf

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-primary text-[24px]">person_add</span>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Thêm người dùng mới</h3>
        </div>

        {{-- Họ tên --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                Họ tên <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="name" required placeholder="Nhập họ tên..."
                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                Email <span class="text-rose-500">*</span>
            </label>
            <input type="email" name="email" required placeholder="example@email.com"
                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
        </div>

        {{-- Mật khẩu --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                Mật khẩu <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="password" required placeholder="Nhập mật khẩu..." minlength="6"
                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <button type="button" x-on:click="$dispatch('close-modal', 'create-user')"
                class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                Hủy
            </button>
            <button type="submit"
                class="px-4 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                Tạo mới
            </button>
        </div>
    </form>
</x-ui.modal>
