@props(['categories'])

<div x-data="{
    product: {},
    images: [],
    files: [{url: ''}],
    primaryIndex: 0,

    open(productData) {
        this.product = productData;
        const allAssets = productData.assets || [];

        // Load ảnh cũ từ server
        const imgAssets = allAssets.filter(a => a.type === 'image');
        this.images = imgAssets.map(a => ({
            id: a.id,
            url: a.url,
            preview: a.url,
            isExisting: true,
            name: 'Ảnh hiện tại',
            file: null
        }));

        let pIndex = imgAssets.findIndex(a => a.is_primary);
        this.primaryIndex = pIndex >= 0 ? pIndex : 0;

        // Load file sản phẩm số
        this.files = allAssets.filter(a => a.type === 'file').map(a => ({ url: a.url }));

        if(this.images.length === 0) this.images = [];
        if(this.files.length === 0) this.files.push({url: ''});

        $dispatch('open-modal', 'edit-product');
    },

    addImage() {
        if(this.images.length < 5) {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png,image/webp,image/gif';
            input.onchange = (e) => {
                const file = e.target.files[0];
                if (!file) return;
                if (file.size > 20 * 1024 * 1024) {
                    alert('Ảnh quá lớn! Tối đa 20MB mỗi ảnh.');
                    return;
                }
                this.images.push({
                    id: null,
                    url: '',
                    preview: URL.createObjectURL(file),
                    isExisting: false,
                    name: file.name,
                    file: file
                });
            };
            input.click();
        }
    },
    removeImage(index) {
        const img = this.images[index];
        if (!img.isExisting && img.preview) {
            URL.revokeObjectURL(img.preview);
        }
        this.images.splice(index, 1);
        if(this.primaryIndex === index) this.primaryIndex = 0;
        else if (this.primaryIndex > index) this.primaryIndex--;
    },
    addFile() { if(this.files.length < 2) this.files.push({url: ''}); },
    removeFile(index) { this.files.splice(index, 1); },

    handleDrop(e) {
        e.preventDefault();
        const files = e.dataTransfer.files;
        for (let i = 0; i < files.length && this.images.length < 5; i++) {
            const file = files[i];
            if (!file.type.startsWith('image/')) continue;
            if (file.size > 20 * 1024 * 1024) continue;
            this.images.push({
                id: null,
                url: '',
                preview: URL.createObjectURL(file),
                isExisting: false,
                name: file.name,
                file: file
            });
        }
    }
}" x-on:open-edit-product.window="open($event.detail)">

    <x-ui.modal name="edit-product" maxWidth="2xl">
        <form method="POST" x-bind:action="'/admin/products/' + product.id" enctype="multipart/form-data" class="p-6"
            x-on:submit="
                const form = $event.target;
                // Gắn file ảnh mới vào form
                images.filter(img => !img.isExisting && img.file).forEach((img) => {
                    const dt = new DataTransfer();
                    dt.items.add(img.file);
                    let input = document.createElement('input');
                    input.type = 'file';
                    input.name = 'new_images[]';
                    input.files = dt.files;
                    input.style.display = 'none';
                    form.appendChild(input);
                });
            ">
            @csrf
            @method('PUT')

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-6 border-b border-slate-200 dark:border-border-dark pb-4">
                <span class="material-symbols-outlined text-primary text-[24px]">edit_note</span>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Cập nhật sản phẩm</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Cột trái: Thông tin cơ bản --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tên sản phẩm
                            <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="product.name" required
                            class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary transition-colors" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Slug <span
                                class="text-rose-500">*</span></label>
                        <input type="text" name="slug" x-model="product.slug" required
                            class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary transition-colors" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Danh mục <span
                                class="text-rose-500">*</span></label>
                        <select name="category_id" x-model="product.category_id" required
                            class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary transition-colors cursor-pointer">
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Giá gốc
                                <span class="text-rose-500">*</span></label>
                            <input type="number" name="price" x-model="product.price" required min="0" step="1000"
                                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary transition-colors" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Giá
                                KM</label>
                            <input type="number" name="sale_price" x-model="product.sale_price" min="0" step="1000"
                                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary transition-colors" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Platform</label>
                            <input type="text" name="platform" x-model="product.platform"
                                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary transition-colors" />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Version</label>
                            <input type="text" name="version" x-model="product.version"
                                class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary transition-colors" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nhà phát
                            triển</label>
                        <input type="text" name="developer" x-model="product.developer"
                            class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary transition-colors" />
                    </div>
                </div>

                {{-- Cột phải --}}
                <div class="space-y-4">
                    {{-- QUẢN LÝ ẢNH --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Ảnh sản phẩm (Tối đa 5 ảnh, mỗi ảnh tối đa 20MB)
                        </label>

                        {{-- Hidden inputs: ID ảnh cũ giữ lại --}}
                        <template x-for="(image, index) in images.filter(i => i.isExisting)" :key="'existing-' + index">
                            <input type="hidden" name="existing_image_ids[]" :value="image.id">
                        </template>

                        {{-- Khu vực Drag & Drop --}}
                        <div x-show="images.length < 5"
                            class="border-2 border-dashed border-slate-300 dark:border-border-dark rounded-lg p-4 text-center cursor-pointer hover:border-primary hover:bg-primary/5 transition-all"
                            x-on:click="addImage()"
                            x-on:dragover.prevent="$el.classList.add('border-primary', 'bg-primary/5')"
                            x-on:dragleave.prevent="$el.classList.remove('border-primary', 'bg-primary/5')"
                            x-on:drop.prevent="$el.classList.remove('border-primary', 'bg-primary/5'); handleDrop($event)">
                            <span class="material-symbols-outlined text-slate-400 text-[32px]">cloud_upload</span>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kéo thả ảnh vào đây hoặc <span class="text-primary font-medium">nhấn để chọn</span></p>
                            <p class="text-xs text-slate-400 mt-0.5">JPG, PNG, WebP, GIF • Tối đa 20MB</p>
                        </div>

                        {{-- Danh sách ảnh --}}
                        <div class="space-y-2 mt-3" x-show="images.length > 0">
                            <template x-for="(image, index) in images" :key="index">
                                <div class="flex items-center gap-2 p-2 bg-slate-50 dark:bg-background-dark rounded-lg border border-slate-200 dark:border-border-dark">
                                    {{-- Radio chọn ảnh chính --}}
                                    <label class="cursor-pointer flex items-center shrink-0" title="Đặt làm ảnh chính">
                                        <input type="radio" name="primary_image_index" :value="index"
                                            x-model="primaryIndex" class="text-amber-500 focus:ring-amber-500 w-4 h-4">
                                    </label>

                                    {{-- Preview ảnh --}}
                                    <img :src="image.preview"
                                        class="w-16 h-12 min-w-[64px] object-contain bg-white dark:bg-slate-800 rounded shadow-sm border border-slate-200 dark:border-border-dark shrink-0"
                                        x-on:error="$event.target.src='{{ asset('images/placeholder.png') }}'">

                                    {{-- Tên file / Trạng thái --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-slate-700 dark:text-slate-300 truncate" x-text="image.name"></p>
                                        <div class="flex items-center gap-2">
                                            <span x-show="image.isExisting" class="text-xs text-emerald-500 font-medium">✓ Đã lưu</span>
                                            <span x-show="!image.isExisting" class="text-xs text-blue-500 font-medium">● Ảnh mới</span>
                                            <span x-show="primaryIndex == index" class="text-xs text-amber-500 font-medium">★ Ảnh chính</span>
                                        </div>
                                    </div>

                                    {{-- Nút xóa --}}
                                    <button type="button" x-on:click="removeImage(index)"
                                        class="text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 p-1.5 rounded shrink-0" title="Xóa">
                                        <span class="material-symbols-outlined text-[18px]">close</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- QUẢN LÝ FILE --}}
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

            {{-- Mô tả --}}
            <div class="mt-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mô tả sản phẩm</label>
                <textarea name="description" rows="3" x-model="product.description"
                    class="w-full px-3 py-2.5 bg-white dark:bg-background-dark border border-slate-300 dark:border-border-dark rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:border-primary transition-colors"></textarea>
            </div>

            {{-- Trạng thái --}}
            <div class="mt-4 mb-6">
                <label class="flex items-center gap-2 cursor-pointer w-fit">
                    <input type="checkbox" name="is_active" value="1" x-bind:checked="product.is_active"
                        class="checkbox-custom rounded bg-slate-100 dark:bg-background-dark border-slate-300 dark:border-border-dark text-primary focus:ring-0 w-4 h-4 cursor-pointer" />
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Hoạt động</span>
                </label>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 border-t border-slate-200 dark:border-border-dark pt-4">
                <button type="button" x-on:click="$dispatch('close-modal', 'edit-product')"
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