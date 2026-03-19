{{-- Modal Thêm người dùng mới --}}
<x-ui.modal name="create-category" maxWidth="md">
    <form method="POST" action="{{ route('admin.categories.store') }}" class="p-6"
        x-data="{ slug: '' }">
        @csrf

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-primary text-[24px]">person_add</span>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Thêm danh mục mới</h3>
        </div>

        {{-- Tên danh mục --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                Tên danh mục <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="name" required placeholder="Nhập tên danh mục..."
                x-on:input="slug = generateSlug($event.target.value)"
                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
        </div>

        {{-- Slug Danh mục --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                Đường dẫn tĩnh (Slug) <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="slug" required placeholder="danh-muc-vi-du" x-model="slug"
                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
        </div>

        {{-- Danh mục cha --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                Danh mục cha
            </label>
            <select name="parent_id"
                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors cursor-pointer">
                <option value="">-- Không có (Danh mục gốc) --</option>
                @foreach(\App\Models\Category::all() as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Vị trí hiển thị (Metadata) --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                Vị trí hiển thị
            </label>
            <select name="metadata[position]"
                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors cursor-pointer">
                <option value="">-Hiển thị mặc định --</option>
                <option value="1">Hiển thị ở vị trí 1</option>
                <option value="2">Hiển thị ở vị trí 2</option>
                <option value="3">Hiển thị ở vị trí 3</option>
                <option value="4">Hiển thị ở vị trí 4</option>
                <option value="5">Hiển thị ở vị trí 5</option>
                <option value="6">Hiển thị ở vị trí 6</option>
                <option value="7">Hiển thị ở vị trí 7</option>
                <option value="8">Hiển thị ở vị trí 8</option>
                <option value="9">Hiển thị ở vị trí 9</option>
                <option value="10">Hiển thị ở vị trí 10</option>
            </select>
        </div>

        {{-- Mô tả --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                Mô tả
            </label>
            <textarea name="description" rows="3" placeholder="Nhập mô tả danh mục..."
                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"></textarea>
        </div>

        {{-- Trạng thái --}}
        <div class="mb-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked
                    class="checkbox-custom rounded bg-slate-100 dark:bg-background-dark border-slate-300 dark:border-border-dark text-primary focus:ring-0 w-4 h-4 cursor-pointer" />
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Hoạt động</span>
            </label>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <button type="button" x-on:click="$dispatch('close-modal', 'create-category')"
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