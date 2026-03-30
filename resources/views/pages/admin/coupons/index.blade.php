<x-admin-layout title="NDHShop - Admin - Mã giảm giá">
    <div class="flex flex-col gap-6">

        {{-- Thanh lọc & công cụ --}}
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 bg-white/80 dark:bg-surface-dark border border-slate-200 dark:border-border-dark p-4 rounded-xl backdrop-blur-sm">
            <div class="flex flex-col sm:flex-row flex-wrap items-center gap-2 w-full xl:w-auto flex-1">
                {{-- Lọc theo Status --}}
                <div class="relative flex-1 sm:flex-none w-full sm:w-44">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">filter_list</span>
                    <select id="filterStatus"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-9 pr-8 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer appearance-none transition-colors"
                        onchange="applyFilters()">
                        <option value="" {{ request('status') == '' ? 'selected' : '' }}>Tất cả</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Dừng hoạt động</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                </div>
                {{-- search --}}
                <div class="relative flex-1 w-full sm:min-w-[200px]">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">search</span>
                    <input type="text" id="searchInput" placeholder="Tìm theo mã hoặc giá trị..." value="{{ request('search') }}"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-10 pr-4 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                        oninput="debounceSearch()">
                </div>
            </div>

            {{-- Thêm mới --}}
            <div class="flex items-center gap-2 shrink-0">
                <button x-data x-on:click="$dispatch('open-modal', 'create-coupon')"
                    class="flex items-center gap-2 px-3 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-primary/25 whitespace-nowrap">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Thêm mới
                </button>
            </div>
        </div>

        {{-- Bảng danh sách --}}
        <div class="rounded-lg border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark overflow-hidden flex flex-col">
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-background-dark/50 border-b border-slate-200 dark:border-border-dark">
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Mã / Cấu hình</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Đơn tối thiểu</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Lượt dùng</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Hạn dùng</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-border-dark">
                        @forelse($coupons as $coupon)
                            <tr class="hover:bg-slate-50 dark:hover:bg-background-dark/30 transition-colors group">
                                <td class="p-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="font-bold text-primary px-2 py-0.5 bg-primary/10 rounded w-fit">{{ $coupon->code }}</span>
                                        <span class="text-sm font-medium text-slate-900 dark:text-white mt-1">
                                            @if($coupon->type === 'percent')
                                                Giảm {{ $coupon->value }}%
                                            @else
                                                Giảm {{ number_format($coupon->value, 0, ',', '.') }}đ
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                        {{ number_format($coupon->min_order, 0, ',', '.') }}đ
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $coupon->used_count }}</span>
                                        <span class="text-xs text-slate-500">
                                            / {{ $coupon->max_uses ? $coupon->max_uses : 'Không giới hạn' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="p-4 text-sm text-slate-600 dark:text-slate-400">
                                    @if($coupon->expires_at)
                                        @if(\Carbon\Carbon::parse($coupon->expires_at)->isPast())
                                            <span class="text-rose-500 font-medium whitespace-nowrap">Hết hạn ({{ \Carbon\Carbon::parse($coupon->expires_at)->format('d/m/Y H:i') }})</span>
                                        @else
                                            <span class="whitespace-nowrap">{{ \Carbon\Carbon::parse($coupon->expires_at)->format('d/m/Y H:i') }}</span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic">Không hạn</span>
                                    @endif
                                </td>
                                <td class="p-4 text-sm">
                                    @if ($coupon->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 whitespace-nowrap">
                                            <span class="size-1.5 rounded-full bg-emerald-500"></span> Hoạt động
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20 whitespace-nowrap">
                                            <span class="size-1.5 rounded-full bg-slate-500"></span> Đã vô hiệu
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button x-data x-on:click="$dispatch('open-edit-coupon', {{ json_encode($coupon) }})"
                                            class="p-1.5 text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition-colors" title="Sửa">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <button x-data x-on:click="$dispatch('open-delete-coupon', {{ json_encode(['id' => $coupon->id, 'code' => $coupon->code]) }})"
                                            class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded transition-colors" title="Xóa">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600">local_offer</span>
                                        <p class="text-slate-500 text-sm">Không có dữ liệu mã giảm giá.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($coupons->hasPages())
                <div class="flex flex-col sm:flex-row items-center justify-between p-4 border-t border-slate-200 dark:border-border-dark bg-slate-50 dark:bg-background-dark/30 gap-4">
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        Hiển thị <span class="font-bold text-slate-900 dark:text-white">{{ $coupons->firstItem() }}</span> đến
                        <span class="font-bold text-slate-900 dark:text-white">{{ $coupons->lastItem() }}</span> trong <span class="font-bold text-slate-900 dark:text-white">{{ $coupons->total() }}</span> mã
                    </div>
                    <div>
                        {{ $coupons->links('pagination::tailwind') }}
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
            const search = document.getElementById('searchInput') ? document.getElementById('searchInput').value : '';
            const params = new URLSearchParams(window.location.search);

            if (status) params.set('status', status);
            else params.delete('status');

            if (search) params.set('search', search);
            else params.delete('search');

            params.delete('page');

            window.location.href = '{{ route("admin.coupons.index") }}?' + params.toString();
        }
    </script>

    {{-- Modal CRUD --}}
    <x-admin.coupon-crud.modal-create />
    <x-admin.coupon-crud.modal-edit />
    <x-admin.coupon-crud.modal-delete />
</x-admin-layout>
