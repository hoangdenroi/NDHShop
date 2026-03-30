<x-admin-layout title="NDHShop - Admin - Danh mục Quà tặng">
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
                        <option value="" {{ request('status') == '' ? 'selected' : '' }}>Tất cả trạng thái</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Đang ẩn</option>
                    </select>
                    <span
                        class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                </div>

                {{-- Lọc đã xóa --}}
                {{-- <div class="relative flex-1 sm:flex-none w-full sm:w-44">
                    <span
                        class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">delete_sweep</span>
                    <select id="filterDeleted"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-9 pr-8 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer appearance-none transition-colors"
                        onchange="applyFilters()">
                        <option value="all" {{ request('is_deleted') == 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="" {{ request('is_deleted') == '' ? 'selected' : '' }}>Chưa xóa</option>
                        <option value="1" {{ request('is_deleted') == '1' ? 'selected' : '' }}>Đã xóa</option>
                    </select>
                    <span
                        class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                </div> --}}

                {{-- Tìm kiếm --}}
                <div class="relative flex-1 w-full sm:min-w-[200px]">
                    <span
                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">search</span>
                    <input type="text" id="searchInput" placeholder="Tìm kiếm danh mục..."
                        value="{{ request('search') }}"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-10 pr-4 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                        oninput="debounceSearch()">
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button x-data x-on:click="$dispatch('open-modal', 'create-gift-category')"
                    class="flex items-center gap-2 px-3 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-primary/25 whitespace-nowrap">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Thêm mới
                </button>
            </div>
        </div>

        {{-- Bảng danh sách --}}
        <div
            class="rounded-lg border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark overflow-hidden flex flex-col">
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-background-dark/50 border-b border-slate-200 dark:border-border-dark">
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                STT</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Tên danh mục</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">
                                Icon</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">
                                Thứ tự</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">
                                Số templates</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Trạng thái</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Ngày tạo</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">
                                Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-border-dark">
                        @forelse($categories as $category)
                            <tr class="hover:bg-slate-50 dark:hover:bg-background-dark/30 transition-colors group {{ $category->is_deleted ? 'opacity-60' : '' }}">
                                <td class="p-4 text-center text-sm">{{ $loop->iteration }}</td>
                                <td class="p-4">
                                    <p class="text-slate-900 dark:text-white text-sm font-bold flex items-center gap-2">
                                        {{ $category->name }}
                                        @if($category->is_deleted)
                                        <span class="px-1.5 py-0.5 rounded bg-rose-100 text-rose-600 text-[10px] uppercase font-bold">Đã xóa</span>
                                        @endif
                                    </p>
                                    <p class="text-slate-500 text-xs mt-1 truncate max-w-[200px]" title="{{ $category->description }}">
                                        {{ $category->description ?: 'Không có mô tả' }}
                                    </p>
                                    <div class="mt-1 flex items-center gap-2 text-xs text-slate-400">
                                        <span class="material-symbols-outlined text-[14px]">link</span>
                                        /{{ $category->slug }}
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    @if($category->icon)
                                        <span class="material-symbols-outlined text-[20px] text-slate-600 dark:text-slate-300">{{ $category->icon }}</span>
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center text-sm text-slate-600 dark:text-slate-400">
                                    {{ $category->sort_order }}
                                </td>
                                <td class="p-4 text-center">
                                    <span class="text-slate-600 dark:text-slate-300 text-sm font-medium bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded">
                                        {{ $category->gift_templates_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    @if ($category->is_active)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                            <span class="size-1.5 rounded-full bg-emerald-500"></span> Hoạt động
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                            <span class="size-1.5 rounded-full bg-amber-500"></span> Ẩn
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $category->created_at->format('d/m/Y') }}
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button x-data
                                            x-on:click="$dispatch('open-edit-gift-category', {{ json_encode(['id' => $category->id, 'name' => $category->name, 'slug' => $category->slug, 'description' => $category->description, 'icon' => $category->icon, 'sort_order' => $category->sort_order, 'is_active' => $category->is_active]) }})"
                                            class="p-1.5 text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition-colors"
                                            title="Sửa">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        @if(!$category->is_deleted)
                                        <button x-data
                                            x-on:click="$dispatch('open-delete-gift-category', {{ json_encode(['id' => $category->id, 'name' => $category->name, 'gift_templates_count' => $category->gift_templates_count ?? 0]) }})"
                                            class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded transition-colors"
                                            title="Xóa">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span
                                            class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600">category</span>
                                        <p class="text-slate-500 text-sm">Không tìm thấy danh mục quà tặng nào.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($categories->hasPages())
                <div class="flex flex-col sm:flex-row items-center justify-between p-4 border-t border-slate-200 dark:border-border-dark bg-slate-50 dark:bg-background-dark/30 gap-4">
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        Hiển thị <span class="font-bold text-slate-900 dark:text-white">{{ $categories->firstItem() }}</span>
                        đến
                        <span class="font-bold text-slate-900 dark:text-white">{{ $categories->lastItem() }}</span> trong <span
                            class="font-bold text-slate-900 dark:text-white">{{ $categories->total() }}</span> danh mục
                    </div>
                    <div>
                        {{ $categories->links('pagination::tailwind') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Script xử lý filter --}}
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
            const is_deleted = document.getElementById('filterDeleted').value;
            const search = document.getElementById('searchInput') ? document.getElementById('searchInput').value : '';
            const params = new URLSearchParams(window.location.search);

            if (status) params.set('status', status);
            else params.delete('status');

            if (is_deleted) params.set('is_deleted', is_deleted);
            else params.delete('is_deleted');

            if (search) params.set('search', search);
            else params.delete('search');

            params.delete('page');

            window.location.href = '{{ route("admin.gift-categories.index") }}?' + params.toString();
        }
    </script>

    {{-- Modal CRUD --}}
    <x-admin.gift-category-crud.modal-create />
    <x-admin.gift-category-crud.modal-edit />
    <x-admin.gift-category-crud.modal-delete />
</x-admin-layout>
