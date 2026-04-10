@extends('layouts.app.app-layout')
@section('content')
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        {{-- Breadcrumb & Tiêu đề --}}
        <div class="mb-8">
            <a href="{{ route('app.gifts.my-gifts') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-primary transition-colors mb-4">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Quay lại Quà tặng của tôi
            </a>
            <div class="flex items-center gap-4">
                <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white">
                    Chỉnh sửa thiệp: <span class="text-primary">{{ $template->name }}</span>
                </h1>
                <span class="px-3 py-1 text-xs font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                    {{ $template->category_label }}
                </span>
            </div>
        </div>

        <div class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-primary/5 border-b border-primary/10 p-5">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">edit_document</span>
                    Nội dung thiệp
                </h2>
                <p class="text-xs text-slate-500 mt-1">Sửa đổi thông tin bên dưới để cập nhật món quà của bạn.</p>
            </div>

            <form method="POST" action="{{ route('app.gifts.update', $gift->unitcode) }}" class="p-5 grid grid-cols-2 gap-5">
                @csrf
                @method('PUT')
                
                {{-- OG Meta: Tiêu đề link --}}
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center gap-1">
                        Tiêu đề Link (SEO) <span class="text-xs font-normal text-slate-400 ml-auto">(Tùy chọn)</span>
                    </label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $gift->meta_title) }}" placeholder="VD: Gửi tặng Vợ yêu nhân ngày 8/3..."
                        class="w-full bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors placeholder:text-slate-400">
                    @error('meta_title') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <hr class="col-span-2 border-slate-100 dark:border-border-dark">

                {{-- Dynamic Fields từ Schema --}}
                @foreach($template->getSchemaFields() as $field)
                    @php
                        $inputName = "data[{$field['key']}]";
                        $oldValue = old("data.{$field['key']}", $gift->page_data[$field['key']] ?? '');
                        $isRequired = $field['required'] ?? false;
                        $maxLength = $field['maxLength'] ?? null;
                        $type = $field['type'] ?? 'text';
                        $limit = $field['limit'] ?? 10;
                        $width = $field['width'] ?? 'full';
                    @endphp
                    
                    <div class="{{ $width === 'half' ? 'col-span-2 sm:col-span-1' : 'col-span-2' }}">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center gap-1">
                            {{ $field['label'] ?? $field['key'] }}
                            @if($isRequired) <span class="text-rose-500">*</span> @endif
                        </label>
                        
                        @if($type === 'image')
                            {{-- ═══ Upload Ảnh Component ═══ --}}
                            <div x-data="imageUploader('{{ $field['key'] }}', {{ $limit }}, '{{ $oldValue }}')" class="space-y-3">
                                <input type="hidden" :name="'data[' + fieldKey + ']'" :value="getUrlsString()" {{ $isRequired ? 'required' : '' }}>

                                {{-- Grid ảnh (SortableJS dùng x-ref để init) --}}
                                <div x-ref="imageGrid" class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                                    <template x-for="(img, index) in images" :key="img._id">
                                        <div class="relative aspect-square rounded-xl overflow-hidden border-2 border-slate-200 dark:border-border-dark group transition-all duration-150"
                                            :data-sort-id="img._id"
                                            @click="replaceImage(index)">
                                            <span class="absolute top-1.5 left-1.5 z-10 size-6 flex items-center justify-center rounded-md bg-primary text-white text-xs font-bold shadow" x-text="index + 1"></span>
                                            {{-- Nút xóa (X) góc trên phải --}}
                                            <button type="button" @click.stop="removeImage(index)"
                                                class="absolute z-20 size-6 flex items-center justify-center rounded-full bg-black/60 hover:bg-rose-500 text-white transition-colors"
                                                style="top: 6px; right: 6px;">
                                                <span class="text-sm font-bold leading-none">&times;</span>
                                            </button>
                                            {{-- Icon kéo (handle) - chỉ hiện khi có >= 2 ảnh --}}
                                            <span x-show="images.length > 1" class="drag-handle absolute bottom-1.5 left-1.5 z-10 size-6 flex items-center justify-center rounded-md bg-black/40 text-white cursor-grab active:cursor-grabbing" @click.stop>
                                                <span class="material-symbols-outlined text-[16px]">drag_indicator</span>
                                            </span>
                                            <img :src="img.preview || img.url" class="w-full h-full object-cover pointer-events-none" alt="">
                                            <div x-show="img.uploading" class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                                <div class="size-8 border-3 border-white/30 border-t-white rounded-full animate-spin"></div>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Nút thêm ảnh (ko kéo được) --}}
                                    <div x-show="images.length < maxImages" data-sortable-ignore="true"
                                        @click="$refs.fileInput.click()"
                                        class="aspect-square rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 hover:border-primary dark:hover:border-primary flex flex-col items-center justify-center gap-1.5 cursor-pointer transition-colors bg-slate-50 dark:bg-slate-800/50 hover:bg-primary/5">
                                        <span class="material-symbols-outlined text-[28px] text-primary/60">add</span>
                                        <span class="text-xs font-medium text-slate-500">Thêm ảnh</span>
                                    </div>
                                </div>

                                <input type="file" x-ref="fileInput" accept="image/*" multiple class="hidden"
                                    @change="handleFiles($event)">

                                <p class="text-[11px] text-slate-500">
                                    Giới hạn <strong x-text="maxImages"></strong> ảnh. <strong>Kéo thả</strong> để sắp xếp thứ tự. Click ảnh để đổi.
                                    <span class="text-slate-400">(JPG, PNG, GIF, WebP — tối đa 20MB/ảnh)</span>
                                </p>
                                <p x-show="errorMsg" x-text="errorMsg" class="text-rose-500 text-xs font-medium"></p>
                            </div>

                        @elseif($type === 'asset_picker')
                            @php
                                $aType = $field['asset_type'] ?? null;
                                $assets = $aType && isset($preloadedAssets[$aType]) ? $preloadedAssets[$aType] : collect();
                            @endphp
                            <div x-data="{
                                open: false,
                                search: '',
                                selected: '{{ addslashes($oldValue) }}',
                                options: [
                                    @foreach($assets as $asset)
                                    { value: '{{ addslashes($asset->url) }}', text: '{{ addslashes($asset->name) }}' },
                                    @endforeach
                                ],
                                get filteredOptions() {
                                    if (this.search === '') return this.options;
                                    return this.options.filter(i => i.text.toLowerCase().includes(this.search.toLowerCase()));
                                },
                                get selectedText() {
                                    const item = this.options.find(i => i.value === this.selected);
                                    return item ? item.text : '-- Nhấp để chọn {{ mb_strtolower($field['label'] ?? '') }} --';
                                },
                                selectOption(val) {
                                    this.selected = val;
                                    this.open = false;
                                }
                            }" class="relative w-full" @click.outside="open = false">
                                
                                <input type="hidden" name="{{ $inputName }}" :value="selected" {{ $isRequired ? 'required' : '' }}>
                                
                                <button type="button" @click="open = !open; if(open) setTimeout(() => $refs.searchInput.focus(), 50)" 
                                        class="w-full flex items-center justify-between text-left bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors">
                                    <span x-text="selectedText" class="truncate line-clamp-1"></span>
                                    <span class="material-symbols-outlined text-[20px] text-slate-400 disabled" :class="open ? 'rotate-180' : ''">expand_more</span>
                                </button>

                                <div x-show="open" x-transition.opacity.duration.200ms
                                     class="absolute z-50 mt-1 w-full bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-xl shadow-lg outline-none overflow-hidden" style="display: none;">
                                    <div class="p-2 border-b border-slate-100 dark:border-border-dark">
                                        <input x-ref="searchInput" type="text" x-model.debounce.500ms="search" placeholder="Tìm kiếm tài nguyên..." 
                                               class="w-full bg-slate-50 dark:bg-background-dark text-slate-700 dark:text-white border border-slate-200 dark:border-border-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                                    </div>
                                    <ul class="max-h-60 overflow-y-auto w-full py-1">
                                        @if(!$isRequired)
                                        <li @click="selectOption('')"
                                            class="px-4 py-2 cursor-pointer text-sm text-slate-500 hover:bg-slate-50 dark:hover:bg-background-dark transition-colors"
                                            :class="selected === '' ? 'bg-primary/5 text-primary font-bold' : ''">
                                            -- Không chọn --
                                        </li>
                                        @endif
                                        <template x-for="option in filteredOptions" :key="option.value">
                                            <li @click="selectOption(option.value)"
                                                class="px-4 py-2 cursor-pointer text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-background-dark transition-colors"
                                                :class="selected === option.value ? 'bg-primary/5 text-primary font-bold' : ''">
                                                <span x-text="option.text"></span>
                                            </li>
                                        </template>
                                        <li x-show="filteredOptions.length === 0" class="px-4 py-3 text-center text-sm text-slate-400 italic">
                                            Không tìm thấy tài nguyên nào.
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        @elseif(in_array($type, ['textarea', 'url']))
                            <textarea name="{{ $inputName }}" rows="{{ $type === 'textarea' ? '3' : '2' }}" {{ $isRequired ? 'required' : '' }} {{ $maxLength ? "maxlength={$maxLength}" : '' }}
                                class="w-full bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors resize-none placeholder:text-slate-400"
                                placeholder="{{ $type === 'url' ? 'Nhập danh sách link URL (Mỗi link 1 dòng)' : 'Nhập ' . mb_strtolower($field['label'] ?? '') . ' (Mỗi nội dung 1 dòng)' }}">{{ $oldValue }}</textarea>
                            <p class="text-[11px] text-slate-500 mt-1">
                                <strong>Mỗi dòng</strong> sẽ được tự động lưu thành <code>{{ $field['key'] }}1</code>, <code>{{ $field['key'] }}2</code>...
                                @if(isset($field['limit'])) <span class="text-rose-500 font-bold ml-1">(Tối đa: {{ $field['limit'] }} dòng)</span> @endif
                            </p>

                        @else
                            <input type="text" name="{{ $inputName }}" value="{{ $oldValue }}" {{ $isRequired ? 'required' : '' }} {{ $maxLength ? "maxlength={$maxLength}" : '' }}
                                class="w-full bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors placeholder:text-slate-400"
                                placeholder="Nhập {{ strtolower($field['label']) }}...">
                        @endif
                        
                        @error("data.{$field['key']}") <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                @endforeach

                {{-- Submit Button + Demo --}}
                <div class="col-span-2 mt-4 pt-5 border-t border-slate-100 dark:border-border-dark flex flex-col gap-3">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-primary to-purple-600 hover:from-primary/90 hover:to-purple-600/90 text-white font-bold rounded-xl shadow-lg shadow-primary/30 transition-all transform hover:-translate-y-0.5">
                        <span class="material-symbols-outlined">save</span>
                        Lưu thay đổi
                    </button>

                    {{-- Nút xem Demo mẫu --}}
                    <button type="submit" formtarget="_blank" formaction="{{ route('app.gifts.preview', $template->slug) }}"
                        class="w-full flex items-center justify-center gap-2 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-colors">
                        <span class="material-symbols-outlined">visibility</span>
                        Xem Demo (với dữ liệu đã nhập)
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SortableJS CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>

    {{-- Alpine.js Image Uploader Component --}}
    <script>
        // Bộ đếm ID duy nhất cho mỗi ảnh (dùng làm key cho x-for)
        let _imgIdCounter = 0;

        function imageUploader(fieldKey, maxImages, initialValue) {
            return {
                fieldKey,
                maxImages,
                images: [],
                errorMsg: '',
                _sortable: null,

                init() {
                    if (initialValue && initialValue.trim()) {
                        const urls = initialValue.split('\n').filter(u => u.trim());
                        urls.forEach(url => {
                            this.images.push({ _id: ++_imgIdCounter, url: url.trim(), preview: null, uploading: false });
                        });
                    }

                    // Khởi tạo SortableJS sau khi DOM render xong
                    this.$nextTick(() => this.initSortable());
                },

                // === Khởi tạo SortableJS (hỗ trợ cả touch mobile) ===
                initSortable() {
                    const grid = this.$refs.imageGrid;
                    if (!grid || this._sortable) return;

                    const self = this;

                    // FIX: Chặn Alpine xử lý fallback clone của SortableJS
                    const origAppend = grid.appendChild.bind(grid);
                    const origInsert = grid.insertBefore.bind(grid);
                    const markIgnore = (child) => {
                        if (child?.nodeType === 1 && child.classList?.contains('sortable-fallback')) {
                            child.setAttribute('x-ignore', '');
                        }
                    };
                    grid.appendChild = function(child) {
                        markIgnore(child);
                        return origAppend(child);
                    };
                    grid.insertBefore = function(child, ref) {
                        markIgnore(child);
                        return origInsert(child, ref);
                    };

                    this._sortable = Sortable.create(grid, {
                        animation: 200,
                        handle: '.drag-handle',
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        dragClass: 'sortable-drag',
                        fallbackClass: 'sortable-fallback',
                        filter: '[data-sortable-ignore]',
                        preventOnFilter: false,
                        fallbackOnBody: false,
                        delay: 150,
                        delayOnTouchOnly: true,
                        touchStartThreshold: 5,
                        onEnd(evt) {
                            if (evt.oldIndex === evt.newIndex) return;

                            const seen = new Set();
                            const newOrder = Array.from(evt.from.children)
                                .filter(el => el.tagName !== 'TEMPLATE'
                                    && !el.hasAttribute('data-sortable-ignore')
                                    && !el.hasAttribute('x-ignore')
                                    && !el.classList.contains('sortable-fallback')
                                    && !el.classList.contains('sortable-ghost')
                                    && el.hasAttribute('data-sort-id'))
                                .map(el => Number(el.getAttribute('data-sort-id')))
                                .filter(id => {
                                    if (seen.has(id)) return false;
                                    seen.add(id);
                                    return true;
                                });

                            const lookup = new Map(self.images.map(img => [img._id, img]));
                            const reordered = newOrder.map(id => lookup.get(id)).filter(Boolean);

                            if (reordered.length === self.images.length) {
                                self.images = [];
                                self.$nextTick(() => {
                                    self.images = reordered;
                                });
                            }
                        },
                    });
                },

                getUrlsString() {
                    return this.images
                        .filter(img => img.url && !img.uploading)
                        .map(img => img.url)
                        .join('\n');
                },

                handleFiles(event) {
                    const files = Array.from(event.target.files);
                    this.errorMsg = '';

                    for (const file of files) {
                        if (this.images.length >= this.maxImages) {
                            this.errorMsg = `Đã đạt giới hạn ${this.maxImages} ảnh!`;
                            break;
                        }
                        if (file.size > 20 * 1024 * 1024) {
                            this.errorMsg = `"${file.name}" vượt quá 20MB!`;
                            continue;
                        }

                        const index = this.images.length;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.images.push({
                                _id: ++_imgIdCounter,
                                url: '',
                                preview: e.target.result,
                                uploading: true,
                            });
                            this.uploadFile(file, index);
                        };
                        reader.readAsDataURL(file);
                    }
                    event.target.value = '';
                },

                async uploadFile(file, index) {
                    const formData = new FormData();
                    formData.append('file', file);

                    try {
                        const response = await fetch('{{ route("api.v1.upload-image") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: formData,
                        });
                        const data = await response.json();
                        if (data.success && data.url) {
                            this.images[index].url = data.url;
                            this.images[index].uploading = false;
                        } else {
                            this.errorMsg = data.message || 'Upload thất bại!';
                            this.images.splice(index, 1);
                        }
                    } catch (err) {
                        this.errorMsg = 'Lỗi kết nối: ' + err.message;
                        this.images.splice(index, 1);
                    }
                },

                removeImage(index) {
                    this.images.splice(index, 1);
                    this.errorMsg = '';
                },

                replaceImage(index) {
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.accept = 'image/*';
                    input.onchange = (e) => {
                        const file = e.target.files[0];
                        if (!file) return;
                        if (file.size > 20 * 1024 * 1024) {
                            this.errorMsg = `"${file.name}" vượt quá 20MB!`;
                            return;
                        }
                        const reader = new FileReader();
                        reader.onload = (ev) => {
                            this.images[index].preview = ev.target.result;
                            this.images[index].uploading = true;
                            this.uploadFile(file, index);
                        };
                        reader.readAsDataURL(file);
                    };
                    input.click();
                },
            };
        }
    </script>

    {{-- CSS cho SortableJS --}}
    <style>
        .sortable-ghost {
            opacity: 0.3;
            border-style: dashed !important;
            border-color: var(--color-primary, #6366f1) !important;
        }
        .sortable-chosen {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            z-index: 50;
        }
    </style>
@endsection
