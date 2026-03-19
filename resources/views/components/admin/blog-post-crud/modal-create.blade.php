<x-ui.modal name="create-blog-post" maxWidth="2xl">
    <form method="POST" action="{{ route('admin.blogs-posts.store') }}" class="p-6 flex flex-col max-h-[90vh]"
        x-data="{ slug: '' }">
        @csrf

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6 shrink-0">
            <span class="material-symbols-outlined text-primary text-[24px]">post_add</span>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Thêm bài viết mới</h3>
        </div>

        <div class="overflow-y-auto overflow-x-hidden pr-2 -mr-2 flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Cột trái --}}
            <div class="space-y-4">
                {{-- Tiêu đề --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        Tiêu đề <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" required placeholder="Nhập tiêu đề..."
                        x-on:input="slug = generateSlug($event.target.value)"
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                </div>

                {{-- Slug --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        Slug (Đường dẫn gốc) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="slug" required placeholder="tieu-de-bai-viet" x-model="slug"
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                </div>

                {{-- Danh mục --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        Danh mục <span class="text-rose-500">*</span>
                    </label>
                    <select name="category_id" required
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors cursor-pointer">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach(\App\Models\PostCategory::all() as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Ảnh đại diện (URL) --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        Ảnh đại diện (URL)
                    </label>
                    <input type="url" name="thumbnail" placeholder="https://example.com/image.jpg"
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                </div>

                {{-- Tóm tắt --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        Tóm tắt
                    </label>
                    <textarea name="summary" rows="3" placeholder="Nhập tóm tắt..."
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"></textarea>
                </div>

                {{-- Trạng thái --}}
                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" checked
                            class="checkbox-custom rounded bg-slate-100 dark:bg-background-dark border-slate-300 dark:border-border-dark text-primary focus:ring-0 w-4 h-4 cursor-pointer" />
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Xuất bản bài viết ngay</span>
                    </label>
                </div>
            </div>

            {{-- Cột phải --}}
            <div class="space-y-4">
                {{-- Nội dung --}}
                <div class="flex flex-col">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        Nội dung <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="content" required rows="10" placeholder="Nội dung bài viết..."
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors resize-y"></textarea>
                </div>

                {{-- SEO --}}
                <div
                    class="p-4 bg-slate-50 dark:bg-background-dark rounded-lg border border-slate-200 dark:border-border-dark space-y-3">
                    <h4 class="font-semibold text-sm text-slate-900 dark:text-white">SEO Meta Data</h4>

                    <input type="text" name="meta_title" placeholder="Meta Title"
                        class="w-full px-3 py-2 bg-white dark:bg-surface-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />

                    <textarea name="meta_description" rows="2" placeholder="Meta Description"
                        class="w-full px-3 py-2 bg-white dark:bg-surface-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"></textarea>

                    <input type="text" name="meta_keywords" placeholder="Meta Keywords (cách nhau dấu phẩy)"
                        class="w-full px-3 py-2 bg-white dark:bg-surface-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                </div>
            </div>
        </div>
        </div>

        {{-- Actions --}}
        <div
            class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-slate-200 dark:border-border-dark shrink-0">
            <button type="button" x-on:click="$dispatch('close-modal', 'create-blog-post')"
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