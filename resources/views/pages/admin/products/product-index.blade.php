<x-admin-layout title="NDHShop - Admin - Sản phẩm">
    <div class="flex flex-col gap-6">

        {{-- Thanh lọc & công cụ --}}
        <div
            class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 bg-white/80 dark:bg-surface-dark border border-slate-200 dark:border-border-dark p-4 rounded-xl backdrop-blur-sm">
            <div class="flex flex-col sm:flex-row flex-wrap items-center gap-2 w-full xl:w-auto flex-1">
                {{-- Lọc theo Status --}}
                <div class="relative flex-1 sm:flex-none w-full sm:w-44">
                    <span
                        class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">filter_list</span>
                    <select id="filterStatus"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-9 pr-8 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer appearance-none transition-colors"
                        onchange="applyFilters()">
                        <option value="" {{ request('status') == '' ? 'selected' : '' }}>Tất cả</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Dừng hoạt động</option>
                    </select>
                    <span
                        class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                </div>

                {{-- Lọc theo Category. Bạn có thể thêm vào view --}}
                {{-- search --}}
                <div class="relative flex-1 w-full sm:min-w-[200px]">
                    <span
                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">search</span>
                    <input type="text" id="searchInput" placeholder="Tìm kiếm sản phẩm..."
                        value="{{ request('search') }}"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-10 pr-4 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                        oninput="debounceSearch()">
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button x-data x-on:click="$dispatch('open-modal', 'create-product')"
                    class="flex items-center gap-2 px-3 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-primary/25 whitespace-nowrap">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Thêm mới
                </button>
                <button
                    class="flex items-center gap-2 px-3 py-2 bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 text-sm font-medium rounded-lg transition-colors whitespace-nowrap">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Xuất dữ liệu
                </button>
            </div>
        </div>

        {{-- Bảng danh sách product --}}
        <div
            class="rounded-lg border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark overflow-hidden flex flex-col">
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-background-dark/50 border-b border-slate-200 dark:border-border-dark">
                            <th class="p-4 w-12 text-center">STT</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Hình ảnh</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Sản phẩm</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Danh mục</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Giá</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                            <th class="p-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Hành
                                động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-border-dark">
                        @forelse($products as $product)
                                                <tr class="hover:bg-slate-50 dark:hover:bg-background-dark/30 transition-colors group">
                                                    <td class="p-4 text-center text-sm">{{ $loop->iteration }}</td>
                                                    <td class="p-4">
                                                        @php
                                                            $primaryAsset = $product->assets->firstWhere('is_primary', true) ?? $product->assets->first();
                                                        @endphp
                                                        @if($primaryAsset)
                                                            <img src="{{ $primaryAsset->url_or_path }}" alt="{{ $product->name }}"
                                                                class="w-16 h-12 object-contain rounded-lg border border-slate-200 bg-slate-50 dark:bg-slate-800 dark:border-border-dark">
                                                        @else
                                                            <div
                                                                class="w-16 h-12 flex items-center justify-center bg-slate-100 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-border-dark text-slate-400">
                                                                <span class="material-symbols-outlined text-[20px]">image</span>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="p-4">
                                                        <p class="text-slate-900 dark:text-white text-sm font-bold">{{ $product->name }}</p>
                                                        <p class="text-slate-500 text-xs mt-0.5">{{ $product->slug }}</p>
                                                    </td>
                                                    <td class="p-4 text-sm font-medium text-slate-600 dark:text-slate-400">
                                                        {{ $product->category ? $product->category->name : 'N/A' }}
                                                    </td>
                                                    <td class="p-4">
                                                        @if($product->sale_price && $product->sale_price < $product->price)
                                                            <span
                                                                class="text-primary font-bold text-sm">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                                                            <span
                                                                class="text-slate-400 line-through text-xs block">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                                        @else
                                                            <span
                                                                class="text-slate-900 dark:text-white font-bold text-sm">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                                        @endif
                                                    </td>
                                                    <td class="p-4">
                                                        @if ($product->is_active)
                                                            <span
                                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                                                <span class="size-1.5 rounded-full bg-emerald-500"></span> Hoạt động
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                                                <span class="size-1.5 rounded-full bg-slate-500"></span> Ẩn
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="p-4 text-right">
                                                        <div class="flex items-center justify-end gap-2">
                                                            <button x-data x-on:click="$dispatch('open-edit-product', {{ json_encode([
                                'id' => $product->id,
                                'name' => $product->name,
                                'slug' => $product->slug,
                                'description' => $product->description,
                                'price' => $product->price,
                                'sale_price' => $product->sale_price,
                                'version' => $product->version,
                                'platform' => $product->platform,
                                'developer' => $product->developer,
                                'category_id' => $product->category_id,
                                'is_active' => $product->is_active,
                                'assets' => $product->assets->map(function ($a) {
                                    return ['id' => $a->id, 'url' => $a->url_or_path, 'is_primary' => $a->is_primary, 'type' => $a->type]; })
                            ]) }})" class="p-1.5 text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition-colors"
                                                                title="Sửa">
                                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                                            </button>
                                                            <button x-data
                                                                x-on:click="$dispatch('open-delete-product', {{ json_encode(['id' => $product->id, 'name' => $product->name]) }})"
                                                                class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded transition-colors"
                                                                title="Xóa">
                                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span
                                            class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600">inventory_2</span>
                                        <p class="text-slate-500 text-sm">Không tìm thấy sản phẩm nào.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-border-dark bg-slate-50 dark:bg-background-dark/30">
                    {{ $products->links('pagination::tailwind') }}
                </div>
            @endif
        </div>
    </div>

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
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        }

        function applyFilters() {
            const status = document.getElementById('filterStatus').value;
            const search = document.getElementById('searchInput') ? document.getElementById('searchInput').value : '';
            const params = new URLSearchParams(window.location.search);

            if (status) params.set('status', status);
            else params.delete('status');

            if (search) params.set('search', search);
            else params.delete('search');

            params.delete('page');

            window.location.href = '{{ route("admin.products.index") }}?' + params.toString();
        }
    </script>

    {{-- Modals --}}
    <x-admin.product-crud.modal-create :categories="$categories" />
    <x-admin.product-crud.modal-edit :categories="$categories" />
    <x-admin.product-crud.modal-delete />
</x-admin-layout>