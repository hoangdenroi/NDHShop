<x-admin-layout title="NDHShop - Admin - Bài viết">
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
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đã xuất bản</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Đang ẩn</option>
                    </select>
                    <span
                        class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                </div>
                
                {{-- Lọc bài viết đã xóa --}}
                <div class="relative flex-1 sm:flex-none w-full sm:w-44">
                    <span
                        class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">delete_sweep</span>
                    <select id="filterDeleted"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-9 pr-8 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer appearance-none transition-colors"
                        onchange="applyFilters()">
                        <option value="" {{ request('is_deleted') == '' ? 'selected' : '' }}>Chưa xóa</option>
                        <option value="1" {{ request('is_deleted') == '1' ? 'selected' : '' }}>Đã xóa</option>
                        <option value="all" {{ request('is_deleted') == 'all' ? 'selected' : '' }}>Tất cả</option>
                    </select>
                    <span
                        class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                </div>

                {{-- search --}}
                <div class="relative flex-1 w-full sm:min-w-[200px]">
                    <span
                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">search</span>
                    <input type="text" id="searchInput" placeholder="Tìm kiếm bài viết..."
                        value="{{ request('search') }}"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-10 pr-4 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                        oninput="debounceSearch()">
                </div>
            </div>

            {{-- Thêm nút xuất dữ liệu --}}
            <div class="flex items-center gap-2 shrink-0">
                <button x-data x-on:click="$dispatch('open-modal', 'create-blog-post')"
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

        {{-- Bảng danh sách bài viết --}}
        <div
            class="rounded-lg border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark overflow-hidden flex flex-col">
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-background-dark/50 border-b border-slate-200 dark:border-border-dark">
                            <th class="p-4 w-12">
                                <input
                                    class="checkbox-custom rounded bg-slate-100 dark:bg-background-dark border-slate-300 dark:border-border-dark text-primary focus:ring-0 w-4 h-4 cursor-pointer"
                                    type="checkbox" />
                            </th>
                            <th
                                class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                STT</th>
                            <th
                                class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Bài viết</th>
                            <th
                                class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Danh mục</th>
                            <th
                                class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Lượt xem</th>
                            <th
                                class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Trạng thái</th>
                            <th
                                class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Ngày xuất bản</th>
                            <th
                                class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">
                                Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-border-dark">
                        @forelse($posts as $post)
                            <tr class="hover:bg-slate-50 dark:hover:bg-background-dark/30 transition-colors group {{ $post->is_deleted ? 'opacity-60' : '' }}">
                                <td class="p-4">
                                    <input
                                        class="checkbox-custom rounded bg-slate-100 dark:bg-background-dark border-slate-300 dark:border-border-dark text-primary focus:ring-0 w-4 h-4 cursor-pointer"
                                        type="checkbox" value="{{ $post->id }}" />
                                </td>
                                <td class="p-4 text-center">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg bg-slate-100 overflow-hidden shrink-0">
                                            @if($post->thumbnail)
                                                <img src="{{ $post->thumbnail }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                    <span class="material-symbols-outlined">image</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-slate-900 dark:text-white text-sm font-bold flex items-center gap-2">
                                                {{ $post->title }}
                                                @if($post->is_deleted)
                                                <span class="px-1.5 py-0.5 rounded bg-rose-100 text-rose-600 text-[10px] uppercase font-bold">Đã xóa</span>
                                                @endif
                                            </p>
                                            <p class="text-slate-500 text-xs mt-1 truncate max-w-[200px]" title="{{ $post->summary }}">
                                                {{ $post->summary ?: 'Không có tóm tắt' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="text-slate-600 dark:text-slate-300 text-sm font-medium bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded">
                                        {{ $post->category ? $post->category->name : 'Không phân loại' }}
                                    </span>
                                </td>
                                <td class="p-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ number_format($post->view_count) }}
                                </td>
                                <td class="p-4">
                                    @if ($post->is_published)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                            <span class="size-1.5 rounded-full bg-emerald-500"></span> Đã xuất bản
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                            <span class="size-1.5 rounded-full bg-amber-500"></span> Nháp/Ẩn
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button x-data
                                            x-on:click="$dispatch('open-edit-blog-post', {{ json_encode(['id' => $post->id, 'title' => $post->title, 'slug' => $post->slug, 'category_id' => $post->category_id, 'summary' => $post->summary, 'content' => $post->content, 'thumbnail' => $post->thumbnail, 'is_published' => $post->is_published, 'meta_title' => $post->meta_title, 'meta_description' => $post->meta_description, 'meta_keywords' => $post->meta_keywords]) }})"
                                            class="p-1.5 text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition-colors"
                                            title="Sửa">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        @if(!$post->is_deleted)
                                        <button x-data
                                            x-on:click="$dispatch('open-delete-blog-post', {{ json_encode(['id' => $post->id, 'title' => $post->title]) }})"
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
                                            class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600">article</span>
                                        <p class="text-slate-500 text-sm">Không tìm thấy bài viết nào.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($posts->hasPages())
                <div class="flex flex-col sm:flex-row items-center justify-between p-4 border-t border-slate-200 dark:border-border-dark bg-slate-50 dark:bg-background-dark/30 gap-4">
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        Hiển thị <span class="font-bold text-slate-900 dark:text-white">{{ $posts->firstItem() }}</span>
                        đến
                        <span class="font-bold text-slate-900 dark:text-white">{{ $posts->lastItem() }}</span> trong <span
                            class="font-bold text-slate-900 dark:text-white">{{ $posts->total() }}</span> bài viết
                    </div>
                    <div>
                        {{ $posts->links('pagination::tailwind') }}
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

            // Cập nhật status
            if (status) {
                params.set('status', status);
            } else {
                params.delete('status');
            }

            // Cập nhật deleted
            if (is_deleted) {
                if(is_deleted === 'all') {
                    params.set('is_deleted', 'all');
                } else {
                    params.set('is_deleted', is_deleted);
                }
            } else {
                params.delete('is_deleted');
            }

            // Cập nhật search
            if (search) {
                params.set('search', search);
            } else {
                params.delete('search');
            }

            // Reset về trang 1 khi filter
            params.delete('page');

            window.location.href = '{{ route("admin.blogs-posts.index") }}?' + params.toString();
        }
    </script>

    {{-- Modal CRUD --}}
    <x-admin.blog-post-crud.modal-create />
    <x-admin.blog-post-crud.modal-edit />
    <x-admin.blog-post-crud.modal-delete />
</x-admin-layout>
