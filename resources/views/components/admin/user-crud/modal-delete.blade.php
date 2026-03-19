{{-- Modal Xác nhận xóa người dùng --}}
<div x-data="{
    user: {},
    open(userData) {
        this.user = userData;
        $dispatch('open-modal', 'delete-user');
    }
}" x-on:open-delete-user.window="open($event.detail)">

    <x-ui.modal name="delete-user" maxWidth="sm">
        <form method="POST" x-bind:action="'/admin/users/' + user.id + '/delete'" class="p-6">
            @csrf
            @method('PATCH')

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-4">
                <span class="material-symbols-outlined text-rose-500 text-[24px]">person_remove</span>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Xóa người dùng</h3>
            </div>

            {{-- Message --}}
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                Bạn có chắc chắn muốn xóa người dùng "<span class="font-semibold text-slate-900 dark:text-white" x-text="user.name"></span>" không?
                Hành động này không thể hoàn tác.
            </p>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'delete-user')"
                    class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    Hủy
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    Xóa
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>
