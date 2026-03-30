<x-admin-layout title="NDHShop - Admin - Lịch sử thao tác">
    <div class="flex flex-col gap-6">

        {{-- Thanh lọc & công cụ --}}
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 bg-white/80 dark:bg-surface-dark border border-slate-200 dark:border-border-dark p-4 rounded-xl backdrop-blur-sm">
            <div class="flex flex-col sm:flex-row flex-wrap items-center gap-2 w-full xl:w-auto flex-1">
                {{-- Lọc theo Action --}}
                <div class="relative flex-1 sm:flex-none w-full sm:w-44">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px]">filter_list</span>
                    <select id="filterAction"
                        class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-9 pr-8 py-2 w-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer appearance-none transition-colors"
                        onchange="applyFilters()">
                        <option value="" {{ request('action') == '' ? 'selected' : '' }}>Tất cả hành động</option>
                        <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Đăng nhập</option>
                        <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Đăng xuất</option>
                        <option value="topup_bank_transfer" {{ request('action') == 'topup_bank_transfer' ? 'selected' : '' }}>Nạp tiền tự động (SePay)</option>
                        <option value="admin_update_balance" {{ request('action') == 'admin_update_balance' ? 'selected' : '' }}>Admin đổi số dư</option>
                        <option value="purchased_product_order" {{ request('action') == 'purchased_product_order' ? 'selected' : '' }}>Mua Sản Phẩm</option>
                        <option value="order_refund_balance" {{ request('action') == 'order_refund_balance' ? 'selected' : '' }}>Hoàn Tiền (Hủy đơn)</option>
                        <option value="purchased_gift_template" {{ request('action') == 'purchased_gift_template' ? 'selected' : '' }}>Mua Gift Template</option>
                        <option value="upgraded_gift_premium" {{ request('action') == 'upgraded_gift_premium' ? 'selected' : '' }}>Nâng cấp Premium Gift</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                </div>
                
                {{-- Date Picker --}}
                <div class="relative flex-1 sm:flex-none w-full sm:w-44">
                    <input type="date" id="filterDate" value="{{ request('date') }}" onchange="applyFilters()"
                           class="bg-slate-100 dark:bg-background-dark border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 text-sm rounded-lg px-4 py-2 w-full focus:outline-none focus:border-primary" />
                </div>
            </div>
        </div>

        {{-- Bảng danh sách Logs --}}
        <div class="rounded-lg border border-slate-200 dark:border-border-dark bg-white dark:bg-surface-dark overflow-hidden flex flex-col">
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-background-dark/50 border-b border-slate-200 dark:border-border-dark">
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Thời gian</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Người thao tác</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Hành động</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Đối tượng</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider max-w-[300px]">Chi tiết thay đổi</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">IP / Thiết bị</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-border-dark">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50 dark:hover:bg-background-dark/30 transition-colors group">
                                <td class="p-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="p-4">
                                    @if($log->user)
                                        <div class="flex items-center gap-2">
                                            @if($log->user->avatar_url)
                                                <div class="size-8 rounded-full bg-cover bg-center border border-slate-200" style="background-image: url('{{ $log->user->avatar_url }}')"></div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $log->user->name }}</p>
                                                <p class="text-xs text-slate-500">{{ $log->user->email }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-slate-500 italic">Hệ thống / Gán danh</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @php
                                        $actionColor = match($log->action) {
                                            'login' => 'text-purple-600 bg-purple-100 dark:bg-purple-900/30 dark:text-purple-400',
                                            'logout' => 'text-slate-600 bg-slate-100 dark:bg-slate-900/30 dark:text-slate-400',
                                            'topup_bank_transfer' => 'text-emerald-600 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            'order_refund_balance' => 'text-amber-600 bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400',
                                            'admin_update_balance' => 'text-rose-600 bg-rose-100 dark:bg-rose-900/30 dark:text-rose-400',
                                            'purchased_product_order', 'purchased_gift_template', 'upgraded_gift_premium' => 'text-blue-600 bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400',
                                            default => 'text-slate-600 bg-slate-100 dark:bg-slate-900/30 dark:text-slate-400',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-md {{ $actionColor }}">
                                        {{ strtoupper($log->action) }}
                                    </span>
                                </td>
                                <td class="p-4 text-sm text-slate-600 dark:text-slate-400">
                                    @if($log->model_type)
                                        <span class="block text-xs font-mono text-slate-500">{{ class_basename($log->model_type) }}</span>
                                        <span class="block text-xs font-bold">ID: {{ $log->model_id }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-4 text-xs font-mono text-slate-600 dark:text-slate-400 max-w-[300px] overflow-x-auto">
                                    <div class="flex flex-col gap-1 max-h-32 overflow-y-auto">
                                        @if($log->old_values && count($log->old_values) > 0)
                                            <div class="bg-rose-50 dark:bg-rose-900/10 p-2 rounded text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30">
                                                <strong class="block mb-1 text-[10px] uppercase text-rose-400">Dữ liệu cũ:</strong>
                                                <pre class="whitespace-pre-wrap">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        @endif
                                        @if($log->new_values && count($log->new_values) > 0)
                                            <div class="bg-emerald-50 dark:bg-emerald-900/10 p-2 rounded text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30 mt-1">
                                                <strong class="block mb-1 text-[10px] uppercase text-emerald-400">Dữ liệu mới:</strong>
                                                <pre class="whitespace-pre-wrap">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4 text-xs text-slate-500 dark:text-slate-400">
                                    <div class="flex items-center gap-1 mb-1">
                                        <span class="material-symbols-outlined text-[14px]">router</span>
                                        {{ $log->ip_address ?? 'N/A' }}
                                    </div>
                                    <div class="flex items-start gap-1 max-w-[150px] truncate" title="{{ $log->user_agent }}">
                                        <span class="material-symbols-outlined text-[14px]">devices</span>
                                        <span class="truncate">{{ $log->user_agent ?? 'N/A' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-500">
                                    Chưa có dữ liệu log thao tác.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-border-dark bg-slate-50 dark:bg-background-dark/30">
                    {{ $logs->links('pagination::tailwind') }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function applyFilters() {
            const action = document.getElementById('filterAction').value;
            const date = document.getElementById('filterDate').value;
            const params = new URLSearchParams(window.location.search);

            if (action) params.set('action', action);
            else params.delete('action');

            if (date) params.set('date', date);
            else params.delete('date');

            params.delete('page');

            window.location.href = '{{ route("admin.audit-logs.index") }}?' + params.toString();
        }
    </script>
</x-admin-layout>
