{{-- Modal thêm 1 Gift Asset --}}
@props(['categories', 'types'])

<x-ui.modal name="create-gift-asset" maxWidth="md">
    <form method="POST" action="{{ route('admin.gift-assets.store') }}" class="p-6"
        x-data="{ slug: '' }">
        @csrf

        <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-primary text-[24px]">add_photo_alternate</span>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Thêm tài nguyên mới</h3>
        </div>

        <div class="space-y-4">
            {{-- Tên --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    Tên hiển thị <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" required placeholder="Pháo hoa vàng..."
                    class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
            </div>

            {{-- URL --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    URL tài nguyên <span class="text-rose-500">*</span>
                </label>
                <input type="url" name="url" required placeholder="https://cdn.example.com/asset.gif"
                    class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
            </div>

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

            {{-- Thumbnail --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">URL ảnh preview</label>
                <input type="url" name="thumbnail" placeholder="https://..."
                    class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Tags --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tags</label>
                    <input type="text" name="tags" placeholder="tet, phao-hoa, vang"
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                    <p class="text-xs text-slate-400 mt-1">Phân cách bằng dấu phẩy</p>
                </div>

                {{-- Dung lượng --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Dung lượng</label>
                    <input type="text" name="file_size" placeholder="2.5 MB"
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                </div>
            </div>

            {{-- Mô tả --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mô tả</label>
                <textarea name="description" rows="2" placeholder="Mô tả ngắn..."
                    class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"></textarea>
            </div>
        </div>

        {{-- Trạng thái --}}
        <div class="mt-5">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked
                    class="checkbox-custom rounded bg-slate-100 dark:bg-background-dark border-slate-300 dark:border-border-dark text-primary focus:ring-0 w-4 h-4 cursor-pointer" />
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Hoạt động</span>
            </label>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-slate-200 dark:border-border-dark">
            <button type="button" x-on:click="$dispatch('close-modal', 'create-gift-asset')"
                class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                Hủy
            </button>
            <button type="submit"
                class="px-4 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                Thêm mới
            </button>
        </div>
    </form>
</x-ui.modal>
