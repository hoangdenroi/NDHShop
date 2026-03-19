{{-- Modal xác nhận xóa Gift Template --}}
<div x-data="{
    templateData: {},
}" @open-delete-gift-template.window="templateData = $event.detail; $dispatch('open-modal', 'delete-gift-template')">

    <x-ui.modal name="delete-gift-template" maxWidth="md">
        <form method="POST" :action="'{{ route('admin.gift-templates.destroy', '__ID__') }}'.replace('__ID__', templateData.id)" class="p-6">
            @csrf
            @method('DELETE')

            <div class="flex flex-col items-center text-center gap-4">
                <div class="size-12 rounded-full bg-rose-100 dark:bg-rose-500/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-rose-500 text-[24px]">delete</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Xóa mẫu template?</h2>
                    <p class="text-sm text-slate-500 mt-2">Bạn có chắc chắn muốn xóa mẫu
                        "<span class="font-bold text-slate-900 dark:text-white" x-text="templateData.name"></span>"?
                    </p>
                    <p class="text-xs text-slate-400 mt-1">Thao tác này sẽ xóa mềm mẫu template.</p>
                </div>
            </div>

            <div class="flex items-center justify-center gap-3 mt-6">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    Hủy
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    Xóa mẫu
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>
