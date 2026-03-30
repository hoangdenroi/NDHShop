<x-admin-layout title="NDHShop - Admin - Gift Templates">
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
                        <option value="" {{ request('category') == '' ? 'selected' : '' }}>Tất cả chủ đề</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <span
                        class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                </div>

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

                {{-- Tìm kiếm --}}
                <div class="relative flex-1 w-full sm:min-w-[200px]">
                    <span
                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">search</span>
                    <input type="text" id="searchInput" placeholder="Tìm kiếm mẫu..."
                        value="{{ request('search') }}"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-10 pr-4 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                        oninput="debounceSearch()">
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button x-data x-on:click="$dispatch('open-modal', 'create-gift-template')"
                    class="flex items-center gap-2 px-3 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-primary/25 whitespace-nowrap">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Thêm mẫu mới
                </button>
            </div>
        </div>

        {{-- Bảng danh sách templates --}}
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
                                Mẫu template</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Chủ đề</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">
                                Lượt dùng</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Giá</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Trạng thái</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">
                                Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-border-dark">
                        @forelse($templates as $template)
                            <tr class="hover:bg-slate-50 dark:hover:bg-background-dark/30 transition-colors group">
                                <td class="p-4 text-center text-sm">{{ $loop->iteration }}</td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 overflow-hidden shrink-0 border border-slate-200 dark:border-border-dark">
                                            @if($template->thumbnail)
                                                <img src="{{ $template->thumbnail }}" alt="{{ $template->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                    <span class="material-symbols-outlined text-[20px]">redeem</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-slate-900 dark:text-white text-sm font-bold">{{ $template->name }}</p>
                                            <p class="text-slate-500 text-xs mt-0.5">/{{ $template->slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="text-slate-600 dark:text-slate-300 text-sm font-medium bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded">
                                        {{ $template->category_label }}
                                    </span>
                                </td>
                                <td class="p-4 text-center text-sm text-slate-600 dark:text-slate-400">
                                    {{ number_format($template->usage_count) }}
                                </td>
                                <td class="p-4">
                                    @if($template->is_premium)
                                        <span class="text-primary font-bold text-sm">{{ number_format($template->price, 0, ',', '.') }}đ</span>
                                    @else
                                        <span class="text-emerald-500 text-sm font-medium">Miễn phí</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if ($template->is_active)
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
                                        <button x-data
                                            x-on:click="$dispatch('open-edit-gift-template', {{ json_encode([
                                                'id' => $template->id,
                                                'name' => $template->name,
                                                'slug' => $template->slug,
                                                'thumbnail' => $template->thumbnail,
                                                'category_id' => $template->category_id,
                                                'html_code' => $template->decoded_html,
                                                'css_code' => $template->decoded_css,
                                                'js_code' => $template->decoded_js,
                                                'schema' => $template->schema ? json_encode($template->schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '',
                                                'is_active' => $template->is_active,
                                                'is_premium' => $template->is_premium,
                                                'price' => $template->price,
                                            ], JSON_UNESCAPED_UNICODE) }})"
                                            class="p-1.5 text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition-colors"
                                            title="Sửa">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <button x-data
                                            x-on:click="$dispatch('open-delete-gift-template', {{ json_encode(['id' => $template->id, 'name' => $template->name]) }})"
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
                                            class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600">redeem</span>
                                        <p class="text-slate-500 text-sm">Không tìm thấy mẫu template nào.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($templates->hasPages())
                <div class="flex flex-col sm:flex-row items-center justify-between p-4 border-t border-slate-200 dark:border-border-dark bg-slate-50 dark:bg-background-dark/30 gap-4">
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        Hiển thị <span class="font-bold text-slate-900 dark:text-white">{{ $templates->firstItem() }}</span>
                        đến
                        <span class="font-bold text-slate-900 dark:text-white">{{ $templates->lastItem() }}</span> trong <span
                            class="font-bold text-slate-900 dark:text-white">{{ $templates->total() }}</span> mẫu
                    </div>
                    <div>
                        {{ $templates->links('pagination::tailwind') }}
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
            const category = document.getElementById('filterCategory').value;
            const status = document.getElementById('filterStatus').value;
            const search = document.getElementById('searchInput') ? document.getElementById('searchInput').value : '';
            const params = new URLSearchParams(window.location.search);

            if (category) params.set('category', category);
            else params.delete('category');

            if (status) params.set('status', status);
            else params.delete('status');

            if (search) params.set('search', search);
            else params.delete('search');

            params.delete('page');

            window.location.href = '{{ route("admin.gift-templates.index") }}?' + params.toString();
        }
    </script>

    {{-- Modal CRUD --}}
    <x-admin.gift-template-crud.modal-create :categories="$categories" />
    <x-admin.gift-template-crud.modal-edit :categories="$categories" />
    <x-admin.gift-template-crud.modal-delete />
</x-admin-layout>
