@props(['categories'])

<div x-data="{
    images: [{url: ''}],
    files: [{url: ''}],
    primaryIndex: 0,
    slug: '',
    
    addImage() { if(this.images.length < 5) this.images.push({url: ''}); },
    removeImage(index) { 
        this.images.splice(index, 1); 
        if(this.primaryIndex === index) this.primaryIndex = 0;
        else if (this.primaryIndex > index) this.primaryIndex--;
    },
    addFile() { if(this.files.length < 2) this.files.push({url: ''}); },
    removeFile(index) { this.files.splice(index, 1); },
    resetForm() {
        this.images = [{url: ''}];
        this.files = [{url: ''}];
        this.primaryIndex = 0;
        this.slug = '';
        document.getElementById('form-create-product').reset();
    }
}" x-on:close-modal.window="if($event.detail === 'create-product') resetForm()">

    <x-ui.modal name="create-product" maxWidth="2xl">
        <form id="form-create-product" method="POST" action="{{ route('admin.products.store') }}" class="p-6">
            @csrf

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-6 border-b border-slate-200 dark:border-border-dark pb-4">
                <span class="material-symbols-outlined text-primary text-[24px]">inventory_2</span>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Thêm sản phẩm mới</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Cột trái: Thông tin cơ bản --}}
                <div class="space-y-4">
                    {{-- Tên sản phẩm --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            Tên sản phẩm <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" required placeholder="Nhập tên sản phẩm..."
                            x-on:input="slug = generateSlug($event.target.value)"
                            class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                    </div>

                    {{-- Slug --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            Slug <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="slug" required placeholder="ten-san-pham" x-model="slug"
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
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Giá và Giá khuyến mãi --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                Giá gốc <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="price" required min="0" step="1000" placeholder="0"
                                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                Giá KM
                            </label>
                            <input type="number" name="sale_price" min="0" step="1000" placeholder="0"
                                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>
                    </div>

                    {{-- Thông số kỹ thuật metadata --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Platform</label>
                            <input type="text" name="platform" placeholder="vd: PC, PS5..."
                                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary transition-colors" />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Version</label>
                            <input type="text" name="version" placeholder="vd: Standard..."
                                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary transition-colors" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nhà phát
                            triển</label>
                        <input type="text" name="developer"
                            class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary transition-colors" />
                    </div>
                </div>

                {{-- Cột phải: Hình ảnh và Cấu hình phụ --}}
                <div class="space-y-4">
                    {{-- Ảnh sản phẩm --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Link URL ảnh sản phẩm (Tối đa 5)
                        </label>
                        <div class="space-y-3">
                            <template x-for="(image, index) in images" :key="index">
                                <div class="flex items-center gap-2">
                                    <label class="cursor-pointer flex items-center gap-1 shrink-0"
                                        title="Đặt làm ảnh chính">
                                        <input type="radio" name="primary_image_index" :value="index"
                                            x-model="primaryIndex" class="text-amber-500 focus:ring-amber-500 w-4 h-4">
                                    </label>
                                    <input type="text" name="image_urls[]" x-model="image.url" placeholder="https://..."
                                        class="flex-1 min-w-0 px-3 py-2 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm transition-colors">
                                    <template x-if="image.url">
                                        <img :src="image.url"
                                            class="w-16 h-10 min-w-[64px] object-contain bg-slate-50 dark:bg-slate-800 rounded shadow-sm border border-slate-200 dark:border-border-dark shrink-0"
                                            x-on:error="$event.target.style.display='none'"
                                            onload="this.style.display='block'">
                                    </template>
                                    <button type="button" x-on:click="removeImage(index)"
                                        class="text-rose-500 hover:bg-rose-50 p-1.5 rounded shrink-0" title="Xóa">
                                        <span class="material-symbols-outlined text-[18px]">close</span>
                                    </button>
                                </div>
                            </template>
                            <button type="button" x-show="images.length < 5" x-on:click="addImage"
                                class="text-sm font-medium text-primary hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-[18px]">add</span> Thêm link ảnh
                            </button>
                        </div>
                    </div>

                    {{-- File tài nguyên (sản phẩm số) --}}
                    <div class="pt-4 border-t border-slate-200">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2 mt-2">
                            Link File sản phẩm số trực tiếp (Tối đa 2)
                        </label>
                        <div class="space-y-3">
                            <template x-for="(file, index) in files" :key="index">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-slate-400 shrink-0">link</span>
                                    <input type="text" name="file_urls[]" x-model="file.url"
                                        placeholder="https://drive.google.com/..."
                                        class="flex-1 min-w-0 px-3 py-2 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm transition-colors">
                                    <button type="button" x-on:click="removeFile(index)"
                                        class="text-rose-500 hover:bg-rose-50 p-1.5 rounded shrink-0" title="Xóa">
                                        <span class="material-symbols-outlined text-[18px]">close</span>
                                    </button>
                                </div>
                            </template>
                            <button type="button" x-show="files.length < 2" x-on:click="addFile"
                                class="text-sm font-medium text-primary hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-[18px]">add</span> Thêm link file
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mô tả đầy đủ --}}
            <div class="mt-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mô tả sản phẩm</label>
                <textarea name="description" rows="3" placeholder="Nhập mô tả..."
                    class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"></textarea>
            </div>

            {{-- Trạng thái --}}
            <div class="mt-4 mb-6">
                <label class="flex items-center gap-2 cursor-pointer w-fit">
                    <input type="checkbox" name="is_active" value="1" checked
                        class="checkbox-custom rounded bg-slate-100 dark:bg-background-dark border-slate-300 dark:border-border-dark text-primary focus:ring-0 w-4 h-4 cursor-pointer" />
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Hoạt động (Hiển thị)</span>
                </label>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 border-t border-slate-200 dark:border-border-dark pt-4">
                <button type="button" x-on:click="$dispatch('close-modal', 'create-product')"
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
</div>