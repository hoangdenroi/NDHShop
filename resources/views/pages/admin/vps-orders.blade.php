<x-admin-layout title="NDHShop - Admin - Đơn hàng VPS">
    <div class="flex flex-col gap-6">

        {{-- Thống kê tổng quan --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $statItems = [
                    ['label' => 'Tổng đơn', 'value' => $stats['total'], 'icon' => 'receipt_long', 'color' => 'primary'],
                    ['label' => 'Đang hoạt động', 'value' => $stats['active'], 'icon' => 'check_circle', 'color' => 'emerald-500'],
                    ['label' => 'Chờ xử lý', 'value' => $stats['pending'], 'icon' => 'pending', 'color' => 'amber-500'],
                    ['label' => 'Hết hạn', 'value' => $stats['expired'], 'icon' => 'schedule', 'color' => 'rose-500'],
                ];
            @endphp
            @foreach($statItems as $stat)
                <div class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-xl p-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-{{ $stat['color'] }}/10">
                            <span class="material-symbols-outlined text-{{ $stat['color'] }} text-[20px]">{{ $stat['icon'] }}</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stat['value'] }}</p>
                            <p class="text-xs text-slate-500">{{ $stat['label'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Thanh lọc --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white/80 dark:bg-surface-dark border border-slate-200 dark:border-border-dark p-4 rounded-xl backdrop-blur-sm">
            <div class="flex flex-col sm:flex-row flex-wrap items-center gap-2 w-full flex-1">
                <div class="relative flex-1 sm:flex-none w-full sm:w-44">
                    <select id="filterStatus"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg px-3 py-2 w-full focus:outline-none focus:border-primary cursor-pointer"
                        onchange="applyFilters()">
                        <option value="" {{ request('status') == '' ? 'selected' : '' }}>Tất cả trạng thái</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="provisioning" {{ request('status') == 'provisioning' ? 'selected' : '' }}>Đang tạo</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                    </select>
                </div>
                <div class="relative flex-1 w-full sm:min-w-[200px]">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input type="text" id="searchInput" placeholder="Tìm theo mã đơn, IP, email..." value="{{ request('search') }}"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-sm rounded-lg pl-10 pr-4 py-2 w-full focus:outline-none focus:border-primary dark:text-white"
                        oninput="debounceSearch()">
                </div>
            </div>
        </div>

        {{-- Bảng đơn hàng --}}
        <div class="rounded-lg border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-background-dark/50 border-b border-slate-200 dark:border-border-dark">
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Đơn hàng</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Khách hàng</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Gói VPS</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">IP / Server</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Giá</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Hết hạn</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-border-dark">
                        @forelse($orders as $order)
                            <tr class="hover:bg-slate-50 dark:hover:bg-background-dark/30 transition-colors">
                                <td class="p-4">
                                    <span class="font-bold text-primary text-sm">{{ $order->order_code }}</span>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </td>
                                <td class="p-4">
                                    <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $order->user->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-500">{{ $order->user->email ?? '' }}</div>
                                </td>
                                <td class="p-4">
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ $order->vpsCategory->name ?? 'N/A' }}</span>
                                </td>
                                <td class="p-4">
                                    @if($order->ip_address)
                                        <code class="text-xs bg-slate-100 dark:bg-background-dark px-2 py-1 rounded font-mono text-primary">{{ $order->ip_address }}</code>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Chưa có</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ number_format($order->price, 0, ',', '.') }}đ</span>
                                    <p class="text-xs text-slate-500">{{ $order->duration_months }} tháng</p>
                                </td>
                                <td class="p-4 text-sm">
                                    @if($order->expires_at)
                                        @if($order->expires_at->isPast())
                                            <span class="text-rose-500 font-medium">Đã hết hạn</span>
                                        @else
                                            <span class="text-slate-600 dark:text-slate-400">{{ $order->expires_at->format('d/m/Y') }}</span>
                                            <p class="text-xs text-emerald-500">Còn {{ $order->daysRemaining() }} ngày</p>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic">—</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @php
                                        $statusMap = [
                                            'active' => ['Hoạt động', 'emerald'],
                                            'pending' => ['Chờ xử lý', 'amber'],
                                            'provisioning' => ['Đang tạo', 'blue'],
                                            'expired' => ['Hết hạn', 'slate'],
                                            'cancelled' => ['Đã hủy', 'rose'],
                                            'failed' => ['Thất bại', 'rose'],
                                            'suspended' => ['Tạm khóa', 'orange'],
                                        ];
                                        [$statusLabel, $statusColor] = $statusMap[$order->status] ?? [$order->status, 'slate'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $statusColor }}-500/10 text-{{ $statusColor }}-500 border border-{{ $statusColor }}-500/20 whitespace-nowrap">
                                        <span class="size-1.5 rounded-full bg-{{ $statusColor }}-500"></span>
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        {{-- Nút giao VPS cho đơn pending manual --}}
                                        @if($order->status === 'pending' && $order->vpsCategory?->isManual())
                                            <button x-data @click="$dispatch('open-fulfill-modal', {{ json_encode(['id' => $order->id, 'code' => $order->order_code]) }})"
                                                class="p-1.5 text-slate-400 hover:text-emerald-500 hover:bg-emerald-500/10 rounded transition-colors" title="Giao VPS">
                                                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                            </button>
                                        @endif
                                        @if(in_array($order->status, ['pending', 'active']))
                                            <form method="POST" action="{{ route('admin.vps-orders.cancel', $order) }}"
                                                onsubmit="return confirm('Xác nhận hủy đơn {{ $order->order_code }}? Tiền sẽ được hoàn cho khách.')">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 rounded transition-colors" title="Hủy đơn">
                                                    <span class="material-symbols-outlined text-[18px]">cancel</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600">receipt_long</span>
                                        <p class="text-slate-500 text-sm">Không có đơn hàng VPS nào.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="flex flex-col sm:flex-row items-center justify-between p-4 border-t border-slate-200 dark:border-border-dark bg-slate-50 dark:bg-background-dark/30 gap-4">
                    <div class="text-sm text-slate-500">
                        Hiển thị <span class="font-bold text-slate-900 dark:text-white">{{ $orders->firstItem() }}</span> đến
                        <span class="font-bold text-slate-900 dark:text-white">{{ $orders->lastItem() }}</span> trong
                        <span class="font-bold text-slate-900 dark:text-white">{{ $orders->total() }}</span> đơn
                    </div>
                    <div>{{ $orders->links('pagination::tailwind') }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal giao VPS thủ công --}}
    <div x-data="{ showFulfill: false, fulfillOrderId: null, fulfillOrderCode: '' }"
        @open-fulfill-modal.window="showFulfill = true; fulfillOrderId = $event.detail.id; fulfillOrderCode = $event.detail.code">
        <template x-teleport="body">
            <div x-show="showFulfill" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                @click.self="showFulfill = false" style="display: none;">

                <div class="bg-white dark:bg-surface-dark rounded-xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-border-dark">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-border-dark">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Giao VPS Thủ Công</h3>
                        <p class="text-sm text-slate-500 mt-1">Đơn: <span class="font-bold text-primary" x-text="fulfillOrderCode"></span></p>
                    </div>
                    <form :action="`{{ url('admin/vps/orders') }}/${fulfillOrderId}/fulfill`" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Địa chỉ IP <span class="text-rose-500">*</span></label>
                            <input type="text" name="ip_address" required placeholder="VD: 192.168.1.100"
                                class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Username <span class="text-rose-500">*</span></label>
                            <input type="text" name="username" required value="root"
                                class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mật khẩu <span class="text-rose-500">*</span></label>
                            <input type="text" name="password" required
                                class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white"
                                placeholder="Mật khẩu root/admin">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">IPv6 (không bắt buộc)</label>
                            <input type="text" name="ipv6_address"
                                class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Ghi chú Admin</label>
                            <textarea name="admin_note" rows="2"
                                class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white resize-none"
                                placeholder="Ghi chú nội bộ..."></textarea>
                        </div>
                        <div class="flex justify-end gap-3 pt-2 border-t border-slate-200 dark:border-border-dark">
                            <button type="button" @click="showFulfill = false"
                                class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-background-dark rounded-lg hover:bg-slate-200 transition-colors">
                                Hủy
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-emerald-500 hover:bg-emerald-600 rounded-lg transition-colors">
                                ✅ Giao VPS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

    <script>
        let searchTimeout = null;
        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => applyFilters(), 500);
        }
        function applyFilters() {
            const params = new URLSearchParams();
            const status = document.getElementById('filterStatus').value;
            const search = document.getElementById('searchInput').value;
            if (status) params.set('status', status);
            if (search) params.set('search', search);
            window.location.href = '{{ route("admin.vps-orders.index") }}?' + params.toString();
        }
    </script>
</x-admin-layout>
