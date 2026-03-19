<x-ui.modal name="create-post-category" maxWidth="md">
    <form method="POST" action="{{ route('admin.post-categories.store') }}" class="p-6"
        x-data="{ slug: '' }">
        @csrf

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-primary text-[24px]">category</span>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Thêm danh mục bài viết</h3>
        </div>

        <div class="space-y-4">
            {{-- Tên danh mục --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    Tên danh mục <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" required placeholder="Nhập tên danh mục..."
                    x-on:input="slug = generateSlug($event.target.value)"
                    class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
            </div>

            {{-- Slug --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    Slug (Đường dẫn gốc) <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="slug" required placeholder="ten-danh-muc" x-model="slug"
                    class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
            </div>

            {{-- Mô tả --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    Mô tả
                </label>
                <textarea name="description" rows="3" placeholder="Nhập mô tả danh mục..."
                    class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"></textarea>
            </div>
        </div>

        {{-- Trạng thái --}}
        <div class="mt-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked
                    class="checkbox-custom rounded bg-slate-100 dark:bg-background-dark border-slate-300 dark:border-border-dark text-primary focus:ring-0 w-4 h-4 cursor-pointer" />
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Hoạt động</span>
            </label>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-slate-200 dark:border-border-dark">
            <button type="button" x-on:click="$dispatch('close-modal', 'create-post-category')"
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
