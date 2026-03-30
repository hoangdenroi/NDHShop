{{-- Modal thêm hàng loạt Gift Assets --}}
@props(['categories', 'types'])

<x-ui.modal name="bulk-create-gift-asset" maxWidth="lg">
    <form method="POST" action="{{ route('admin.gift-assets.store-bulk') }}" class="p-6">
        @csrf

        <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-emerald-500 text-[24px]">playlist_add</span>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Thêm hàng loạt tài nguyên</h3>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                {{-- Loại --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Loại <span class="text-rose-500">*</span></label>
                    <select name="type" required
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors">
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Danh mục --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Danh mục</label>
                    <select name="category_id"
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors">
                        <option value="">Không phân loại</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Danh sách URLs --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    Danh sách URL <span class="text-rose-500">*</span>
                </label>
                <textarea name="urls" rows="10" required
                    placeholder="Paste mỗi URL trên 1 dòng:&#10;https://cdn.example.com/asset1.gif&#10;https://cdn.example.com/asset2.gif&#10;https://cdn.example.com/asset3.gif"
                    class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors font-mono"></textarea>
                <p class="text-xs text-slate-400 mt-1">Mỗi URL trên 1 dòng. Tên sẽ tự lấy từ file name trong URL. URL không hợp lệ sẽ bị bỏ qua.</p>
            </div>
        </div>

        {{-- Trạng thái --}}
        <div class="mt-5">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked
                    class="checkbox-custom rounded bg-slate-100 dark:bg-background-dark border-slate-300 dark:border-border-dark text-primary focus:ring-0 w-4 h-4 cursor-pointer" />
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Hoạt động ngay</span>
            </label>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-slate-200 dark:border-border-dark">
            <button type="button" x-on:click="$dispatch('close-modal', 'bulk-create-gift-asset')"
                class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                Hủy
            </button>
            <button type="submit"
                class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                Thêm tất cả
            </button>
        </div>
    </form>
</x-ui.modal>
