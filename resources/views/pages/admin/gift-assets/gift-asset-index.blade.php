<x-admin-layout title="NDHShop - Admin - Gift Assets">
    <div class="flex flex-col gap-6">

        {{-- Thanh lọc & công cụ --}}
        <div
            class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 bg-white/80 dark:bg-surface-dark border border-slate-200 dark:border-border-dark p-4 rounded-xl backdrop-blur-sm">
            <div class="flex flex-col sm:flex-row flex-wrap items-center gap-2 w-full xl:w-auto flex-1">
                {{-- Lọc theo Category --}}
                <div class="relative flex-1 sm:flex-none w-full sm:w-44">
                    <span
                        class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">category</span>
                    <select id="filterCategory"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-9 pr-8 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer appearance-none transition-colors"
                        onchange="applyFilters()">
                        <option value="" {{ request('category') == '' ? 'selected' : '' }}>Tất cả danh mục</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <span
                        class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                </div>

                {{-- Lọc theo Type --}}
                <div class="relative flex-1 sm:flex-none w-full sm:w-44">
                    <span
                        class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">perm_media</span>
                    <select id="filterType"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-9 pr-8 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer appearance-none transition-colors"
                        onchange="applyFilters()">
                        <option value="" {{ request('type') == '' ? 'selected' : '' }}>Tất cả loại</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span
                        class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                </div>

                {{-- Lọc trạng thái --}}
                <div class="relative flex-1 sm:flex-none w-full sm:w-36">
                    <span
                        class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">filter_list</span>
                    <select id="filterStatus"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-9 pr-8 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer appearance-none transition-colors"
                        onchange="applyFilters()">
                        <option value="" {{ request('status') == '' ? 'selected' : '' }}>Tất cả</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Đang ẩn</option>
                    </select>
                    <span
                        class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                </div>

                {{-- Tìm kiếm --}}
                <div class="relative flex-1 w-full sm:min-w-[180px]">
                    <span
                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">search</span>
                    <input type="text" id="searchInput" placeholder="Tìm kiếm..."
                        value="{{ request('search') }}"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-10 pr-4 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                        oninput="debounceSearch()">
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button x-data x-on:click="$dispatch('open-modal', 'bulk-create-gift-asset')"
                    class="flex items-center gap-2 px-3 py-2 bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 text-sm font-medium rounded-lg transition-colors whitespace-nowrap">
                    <span class="material-symbols-outlined text-[18px]">playlist_add</span>
                    Thêm hàng loạt
                </button>
                <button x-data x-on:click="$dispatch('open-modal', 'create-gift-asset')"
                    class="flex items-center gap-2 px-3 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-primary/25 whitespace-nowrap">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Thêm mới
                </button>
            </div>
        </div>

        {{-- Grid Assets --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse($assets as $asset)
                <div class="group bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-xl overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all duration-200 {{ !$asset->is_active ? 'opacity-60' : '' }}">
                    {{-- Preview --}}
                    <div class="relative aspect-square bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        @if(in_array($asset->type, ['image', 'gif']))
                            <img src="{{ $asset->thumbnail ?? $asset->url }}" alt="{{ $asset->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                loading="lazy">
                        @elseif($asset->type === 'video')
                            <div class="w-full h-full flex items-center justify-center bg-slate-900/10">
                                @if($asset->thumbnail)
                                    <img src="{{ $asset->thumbnail }}" alt="{{ $asset->name }}" class="w-full h-full object-cover absolute inset-0">
                                @endif
                                <span class="material-symbols-outlined text-[48px] text-white/80 relative z-10 drop-shadow-lg">play_circle</span>
                            </div>
                        @elseif($asset->type === 'audio')
                            <div class="w-full h-full flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[48px] text-primary/60">music_note</span>
                                <span class="text-xs text-slate-500 font-medium">Âm thanh</span>
                            </div>
                        @elseif($asset->type === 'lottie')
                            <div class="w-full h-full flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[48px] text-purple-400">animation</span>
                                <span class="text-xs text-slate-500 font-medium">Lottie</span>
                            </div>
                        @endif

                        {{-- Badges --}}
                        <div class="absolute top-2 left-2">
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase
                                @if($asset->type === 'gif') bg-emerald-500 text-white
                                @elseif($asset->type === 'video') bg-rose-500 text-white
                                @elseif($asset->type === 'audio') bg-blue-500 text-white
                                @elseif($asset->type === 'lottie') bg-purple-500 text-white
                                @else bg-slate-600 text-white
                                @endif">{{ $asset->type_label }}</span>
                        </div>

                        {{-- Actions overlay --}}
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center gap-2">
                            <button x-data
                                x-on:click="$dispatch('open-edit-gift-asset', {{ json_encode([
                                    'id' => $asset->id,
                                    'name' => $asset->name,
                                    'type' => $asset->type,
                                    'url' => $asset->url,
                                    'category_id' => $asset->category_id,
                                    'thumbnail' => $asset->thumbnail,
                                    'file_size' => $asset->file_size,
                                    'description' => $asset->description,
                                    'tags' => $asset->tags ? implode(', ', $asset->tags) : '',
                                    'sort_order' => $asset->sort_order,
                                    'is_active' => $asset->is_active,
                                ]) }})"
                                class="p-2 bg-white/90 rounded-lg text-slate-700 hover:bg-white transition-colors" title="Sửa">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <a href="{{ $asset->url }}" target="_blank"
                                class="p-2 bg-white/90 rounded-lg text-slate-700 hover:bg-white transition-colors" title="Xem URL">
                                <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                            </a>
                            <button x-data
                                x-on:click="$dispatch('open-delete-gift-asset', {{ json_encode(['id' => $asset->id, 'name' => $asset->name]) }})"
                                class="p-2 bg-rose-500/90 rounded-lg text-white hover:bg-rose-500 transition-colors" title="Xóa">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="p-3">
                        <p class="text-sm font-bold text-slate-900 dark:text-white truncate" title="{{ $asset->name }}">{{ $asset->name }}</p>
                        <div class="flex items-center justify-between mt-1.5">
                            <span class="text-xs text-slate-500 truncate max-w-[60%]">{{ $asset->category_label }}</span>
                            @if($asset->file_size)
                                <span class="text-xs text-slate-400">{{ $asset->file_size }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <span class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600">perm_media</span>
                        <p class="text-slate-500 text-sm">Chưa có tài nguyên nào.</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($assets->hasPages())
            <div class="flex justify-center mt-4">
                {{ $assets->links('pagination::tailwind') }}
            </div>
        @endif
    </div>

    {{-- Script filter --}}
    <script>
        let searchTimeout = null;

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            if (searchInput && searchInput.value) {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }
        });

        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => applyFilters(), 500);
        }

        function applyFilters() {
            const category = document.getElementById('filterCategory').value;
            const type = document.getElementById('filterType').value;
            const status = document.getElementById('filterStatus').value;
            const search = document.getElementById('searchInput')?.value || '';
            const params = new URLSearchParams(window.location.search);

            if (category) params.set('category', category); else params.delete('category');
            if (type) params.set('type', type); else params.delete('type');
            if (status) params.set('status', status); else params.delete('status');
            if (search) params.set('search', search); else params.delete('search');
            params.delete('page');

            window.location.href = '{{ route("admin.gift-assets.index") }}?' + params.toString();
        }
    </script>

    {{-- Modals --}}
    <x-admin.gift-asset-crud.modal-create :categories="$categories" :types="$types" />
    <x-admin.gift-asset-crud.modal-bulk-create :categories="$categories" :types="$types" />
    <x-admin.gift-asset-crud.modal-edit :categories="$categories" :types="$types" />
    <x-admin.gift-asset-crud.modal-delete />
</x-admin-layout>
