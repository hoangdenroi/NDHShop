{{-- Modal chỉnh sửa Gift Asset --}}
@props(['categories', 'types'])

<x-ui.modal name="edit-gift-asset" maxWidth="md">
    <div x-data="{
            asset: { name: '', type: '', url: '', category_id: '', thumbnail: '', file_size: '', description: '', tags: '', sort_order: 0, is_active: false }
        }"
        @open-edit-gift-asset.window="asset = $event.detail; $dispatch('open-modal', 'edit-gift-asset')">
        <form method="POST" x-bind:action="`{{ url('admin/gift-assets') }}/${asset?.id}`" class="p-6">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-3 mb-6">
                <span class="material-symbols-outlined text-amber-500 text-[24px]">edit_square</span>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Chỉnh sửa tài nguyên</h3>
            </div>

            <div class="space-y-4">
                {{-- Tên --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        Tên hiển thị <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" x-model="asset.name" required
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                </div>

                {{-- URL --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        URL tài nguyên <span class="text-rose-500">*</span>
                    </label>
                    <input type="url" name="url" x-model="asset.url" required
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Loại --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Loại <span class="text-rose-500">*</span></label>
                        <select name="type" x-model="asset.type" required
                            class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors">
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Danh mục --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Danh mục</label>
                        <select name="category_id" x-model="asset.category_id"
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
                    <input type="url" name="thumbnail" x-model="asset.thumbnail"
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Tags --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tags</label>
                        <input type="text" name="tags" x-model="asset.tags" placeholder="tet, phao-hoa"
                            class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                    </div>

                    {{-- Dung lượng --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Dung lượng</label>
                        <input type="text" name="file_size" x-model="asset.file_size"
                            class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Thứ tự --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Thứ tự</label>
                        <input type="number" name="sort_order" x-model="asset.sort_order" min="0"
                            class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                    </div>
                    <div></div>
                </div>

                {{-- Mô tả --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mô tả</label>
                    <textarea name="description" x-model="asset.description" rows="2"
                        class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"></textarea>
                </div>
            </div>

            {{-- Trạng thái --}}
            <div class="mt-5">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" x-model="asset.is_active"
                        class="checkbox-custom rounded bg-slate-100 dark:bg-background-dark border-slate-300 dark:border-border-dark text-primary focus:ring-0 w-4 h-4 cursor-pointer" />
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Hoạt động</span>
                </label>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-slate-200 dark:border-border-dark">
                <button type="button" x-on:click="$dispatch('close-modal', 'edit-gift-asset')"
                    class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    Hủy
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</x-ui.modal>
