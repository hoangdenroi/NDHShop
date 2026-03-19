{{-- Modal Cập nhật người dùng --}}
<div x-data="{
    user: {},
    open(userData) {
        this.user = userData;
        $dispatch('open-modal', 'edit-user');
    }
}" x-on:open-edit-user.window="open($event.detail)">

    <x-ui.modal name="edit-user" maxWidth="lg">
        <form method="POST" x-bind:action="'/admin/users/' + user.id" class="p-6">
            @csrf
            @method('PUT')

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-6">
                <span class="material-symbols-outlined text-primary text-[24px]">edit</span>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Cập nhật người dùng</h3>
            </div>

            {{-- Họ tên + Email (chỉ xem, do user tự sửa) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Họ tên</label>
                    <input type="text" x-model="user.name" readonly disabled
                        class="w-full px-3 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-border-dark rounded-lg text-sm text-slate-500 dark:text-slate-400 cursor-not-allowed" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                    <input type="email" x-model="user.email" readonly disabled
                        class="w-full px-3 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-border-dark rounded-lg text-sm text-slate-500 dark:text-slate-400 cursor-not-allowed" />
                </div>
            </div>

            {{-- SĐT (chỉ xem) --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Số điện thoại</label>
                <input type="text" x-model="user.phone" readonly disabled placeholder="Chưa cập nhật"
                    class="w-full px-3 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-border-dark rounded-lg text-sm text-slate-500 dark:text-slate-400 cursor-not-allowed" />
            </div>

            {{-- Divider --}}
            <div class="flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-slate-500 text-[20px]">tune</span>
                <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Quản lý tài khoản</h4>
            </div>

            {{-- Trạng thái + Số dư --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Trạng thái</label>
                    <select name="status" x-model="user.status" required
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer transition-colors">
                        <option value="active">Hoạt động</option>
                        <option value="unactive">Chưa kích hoạt</option>
                        <option value="locked">Khóa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Số dư (VND)</label>
                    <input type="number" name="balance" x-model="user.balance" min="0" step="1000"
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                </div>
            </div>

            {{-- Role --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Vai trò</label>
                    <select name="role" x-model="user.role" required
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer transition-colors">
                        <option value="user">Người dùng</option>
                        <option value="admin">Quản trị viên</option>
                    </select>
                </div>
            </div>

            {{-- Avatar URL --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Avatar URL</label>
                <input type="url" name="avatar_url" x-model="user.avatar_url" placeholder="https://..." readonly disabled
                    class="w-full px-3 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-border-dark rounded-lg text-sm text-slate-500 dark:text-slate-400 cursor-not-allowed" />
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'edit-user')"
                    class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    Hủy
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    Cập nhật
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>
