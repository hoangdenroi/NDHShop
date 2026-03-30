<x-admin-layout title="NDHShop - Admin - Đơn hàng">
    <div class="flex flex-col gap-6">

        {{-- Thanh lọc & công cụ --}}
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 bg-white/80 dark:bg-surface-dark border border-slate-200 dark:border-border-dark p-4 rounded-xl backdrop-blur-sm">
            <div class="flex flex-col sm:flex-row flex-wrap items-center gap-2 w-full xl:w-auto flex-1">
                {{-- Lọc theo Status --}}
                <div class="relative flex-1 sm:flex-none w-full sm:w-48">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">filter_list</span>
                    <select id="filterStatus"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-9 pr-8 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer appearance-none transition-colors"
                        onchange="applyFilters()">
                        <option value="" {{ request('status') == '' ? 'selected' : '' }}>Tất cả trạng thái</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn tất</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                </div>
                {{-- search --}}
                <div class="relative flex-1 w-full sm:min-w-[200px]">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">search</span>
                    <input type="text" id="searchInput" placeholder="Tìm theo mã đơn, tên hoặc email khách hàng..."
                        value="{{ request('search') }}"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-10 pr-4 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                        oninput="debounceSearch()">
                </div>
            </div>
        </div>

        {{-- Bảng danh sách --}}
        <div class="rounded-lg border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark overflow-hidden flex flex-col">
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-background-dark/50 border-b border-slate-200 dark:border-border-dark">
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Mã đơn</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Khách hàng</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Sản phẩm</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tổng tiền</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Ngày tạo</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-border-dark">
                        @forelse($orders as $order)
                            <tr class="hover:bg-slate-50 dark:hover:bg-background-dark/30 transition-colors group">
                                <td class="p-4">
                                    <span class="font-bold text-primary px-2 py-0.5 bg-primary/10 rounded text-sm">{{ $order->order_code }}</span>
                                    @if($order->coupon_code)
                                        <span class="block text-xs text-green-600 mt-1">
                                            <span class="material-symbols-outlined text-[12px] align-middle">local_offer</span>
                                            {{ $order->coupon_code }} (-{{ number_format($order->discount_amount, 0, ',', '.') }}đ)
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($order->user)
                                        <div class="flex items-center gap-2.5">
                                            @if($order->user->avatar_url)
                                                <img src="{{ $order->user->avatar_url }}" class="w-8 h-8 rounded-full object-cover border border-slate-200 dark:border-slate-700" alt="">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold">
                                                    {{ strtoupper(substr($order->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $order->user->name }}</p>
                                                <p class="text-xs text-slate-400 truncate">{{ $order->user->email }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic text-sm">Đã xóa</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $order->items->count() }} sản phẩm</span>
                                </td>
                                <td class="p-4">
                                    <span class="text-sm font-bold text-primary">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                                </td>
                                <td class="p-4">
                                    @php
                                        $statusMap = [
                                            'completed' => ['label' => 'Hoàn tất', 'class' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20'],
                                            'pending' => ['label' => 'Chờ xử lý', 'class' => 'bg-amber-500/10 text-amber-600 border-amber-500/20'],
                                            'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-rose-500/10 text-rose-500 border-rose-500/20'],
                                        ];
                                        $st = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => 'bg-slate-500/10 text-slate-500 border-slate-500/20'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border {{ $st['class'] }} whitespace-nowrap">
                                        <span class="size-1.5 rounded-full bg-current"></span>
                                        {{ $st['label'] }}
                                    </span>
                                </td>
                                <td class="p-4 text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="p-4 text-right">
                                    <button x-data @click="$dispatch('open-order-detail', { id: {{ $order->id }} })"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-primary bg-primary/5 hover:bg-primary/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        Chi tiết
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600">receipt_long</span>
                                        <p class="text-slate-500 text-sm">Không có đơn hàng nào.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($orders->hasPages())
                <div class="flex flex-col sm:flex-row items-center justify-between p-4 border-t border-slate-200 dark:border-border-dark bg-slate-50 dark:bg-background-dark/30 gap-4">
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        Hiển thị <span class="font-bold text-slate-900 dark:text-white">{{ $orders->firstItem() }}</span> đến
                        <span class="font-bold text-slate-900 dark:text-white">{{ $orders->lastItem() }}</span> trong <span class="font-bold text-slate-900 dark:text-white">{{ $orders->total() }}</span> đơn hàng
                    </div>
                    <div>
                        {{ $orders->links('pagination::tailwind') }}
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
            searchTimeout = setTimeout(() => applyFilters(), 500);
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
            window.location.href = '{{ route("admin.orders.index") }}?' + params.toString();
        }
    </script>

    {{-- Modal chi tiết đơn hàng --}}
    <x-admin.order-crud.modal-detail />
</x-admin-layout>
