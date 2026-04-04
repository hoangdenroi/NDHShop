@extends('layouts.app.app-layout')

@section('content')
<section class="py-12 px-6">
    <div class="max-w-screen-xl mx-auto">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8">
            <a href="{{ route('app.vps.orders') }}" class="hover:text-primary transition-colors">Đơn hàng VPS</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-slate-900 dark:text-white font-medium">{{ $order->order_code }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Cột trái: Thông tin đơn hàng --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Trạng thái + Thông tin --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-border-dark">
                    @php
                        $statusConfig = [
                            'active' => ['icon' => 'check_circle', 'color' => 'emerald', 'label' => 'Đang hoạt động', 'bg' => 'bg-emerald-500'],
                            'pending' => ['icon' => 'pending', 'color' => 'amber', 'label' => 'Chờ xử lý', 'bg' => 'bg-amber-500'],
                            'provisioning' => ['icon' => 'sync', 'color' => 'blue', 'label' => 'Đang tạo', 'bg' => 'bg-blue-500'],
                            'expired' => ['icon' => 'schedule', 'color' => 'slate', 'label' => 'Hết hạn', 'bg' => 'bg-slate-500'],
                            'cancelled' => ['icon' => 'cancel', 'color' => 'rose', 'label' => 'Đã hủy', 'bg' => 'bg-rose-500'],
                            'failed' => ['icon' => 'error', 'color' => 'rose', 'label' => 'Thất bại', 'bg' => 'bg-rose-500'],
                            'suspended' => ['icon' => 'block', 'color' => 'orange', 'label' => 'Tạm khóa', 'bg' => 'bg-orange-500'],
                        ];
                        $cfg = $statusConfig[$order->status] ?? ['icon' => 'help', 'color' => 'slate', 'label' => $order->status, 'bg' => 'bg-slate-500'];
                    @endphp

                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 rounded-lg bg-{{ $cfg['color'] }}-500/10">
                            <span class="material-symbols-outlined text-{{ $cfg['color'] }}-500 text-[24px]">{{ $cfg['icon'] }}</span>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $cfg['bg'] }} text-white">
                                {{ $cfg['label'] }}
                            </span>
                            <p class="text-xs text-slate-500 mt-1">{{ $order->order_code }}</p>
                        </div>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Gói VPS</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $order->vpsCategory->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Hệ điều hành</span>
                            <span class="font-medium text-slate-900 dark:text-white">{{ $order->operating_system }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Location</span>
                            <span class="font-medium text-slate-900 dark:text-white">{{ $order->location }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Thời hạn</span>
                            <span class="font-medium text-slate-900 dark:text-white">{{ $order->duration_months }} tháng</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Giá</span>
                            <span class="font-bold text-primary">{{ number_format($order->price, 0, ',', '.') }}đ</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Giảm giá</span>
                                <span class="font-medium text-emerald-500">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                            </div>
                        @endif
                        @if($order->expires_at)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Hết hạn</span>
                                <span class="font-medium {{ $order->isExpired() ? 'text-rose-500' : 'text-slate-900 dark:text-white' }}">
                                    {{ $order->expires_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            @if(!$order->isExpired() && $order->status === 'active')
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Còn lại</span>
                                    <span class="font-bold text-emerald-500">{{ $order->daysRemaining() }} ngày</span>
                                </div>
                            @endif
                        @endif
                        <div class="flex justify-between">
                            <span class="text-slate-500">Ngày đặt</span>
                            <span class="text-slate-700 dark:text-slate-300">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Nút hệ thống (Hủy + Gia hạn) --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-border-dark space-y-3">
                    @if($order->canRenew())
                        @php
                            $availableMonths = [1, 3, 6, 12];
                            if ($order->vpsCategory && !empty($order->vpsCategory->metadata['available_months'])) {
                                $availableMonths = $order->vpsCategory->metadata['available_months'];
                            }
                            $availableMonths = array_map('intval', $availableMonths);
                            sort($availableMonths);
                            $firstMonth = !empty($availableMonths) ? $availableMonths[0] : 1;
                        @endphp
                        <form method="POST" action="{{ route('app.vps.renew', $order) }}" x-data="{ months: {{ $firstMonth }} }"
                            @submit="if(!confirm('Xác nhận gia hạn VPS {{ $order->order_code }} thêm ' + months + ' tháng? với giá ' + (months * {{ $order->price }}) + 'đ')) $event.preventDefault()">
                            @csrf
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Gia hạn thêm</label>
                            <div class="flex gap-2">
                                <select name="duration_months" x-model="months"
                                    class="flex-1 border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm dark:text-white">
                                    @foreach($availableMonths as $m)
                                        <option value="{{ $m }}">{{ $m }} tháng</option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="px-4 py-2 bg-emerald-500 text-white font-bold rounded-lg text-sm hover:bg-emerald-600 transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[18px]">autorenew</span>
                                    Gia hạn
                                </button>
                            </div>
                        </form>
                    @endif

                    @if($order->canCancel())
                        <form method="POST" action="{{ route('app.vps.cancel', $order) }}"
                            onsubmit="return confirm('Xác nhận hủy VPS {{ $order->order_code }}?\nSố tiền hoàn lại: {{ number_format($order->refundAmount(), 0, ',', '.') }}đ\nLưu ý: Số tiền hoàn lại sẽ được cộng vào số dư tài khoản của bạn.\nBackup các dữ liệu quan trọng trước khi xác nhận hủy VPS')">
                            @csrf
                            <button type="submit"
                                class="w-full px-4 py-2 border border-rose-300 text-rose-500 font-medium rounded-lg text-sm hover:bg-rose-500/10 transition-colors flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">cancel</span>
                                Hủy VPS (hoàn {{ number_format($order->refundAmount(), 0, ',', '.') }}đ)
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Cột phải: Panel quản lý VPS --}}
            <div class="lg:col-span-2 space-y-6">

                @if($order->isActive())
                    {{-- Thông tin kết nối --}}
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-border-dark" x-data="{ showPass: false }">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">vpn_key</span>
                            Thông Tin Kết Nối
                        </h3>

                        <div class="space-y-4">
                            {{-- IPv4 --}}
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-background-dark rounded-lg">
                                <div>
                                    <p class="text-xs text-slate-500 mb-0.5">Địa chỉ IPv4</p>
                                    <p class="text-sm font-mono font-bold text-slate-900 dark:text-white">{{ $order->ip_address ?? 'Đang cấp phát...' }}</p>
                                </div>
                                @if($order->ip_address)
                                    <button onclick="navigator.clipboard.writeText('{{ $order->ip_address }}').then(() => alert('Đã sao chép IP!'))"
                                        class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Sao chép">
                                        <span class="material-symbols-outlined text-[18px]">content_copy</span>
                                    </button>
                                @endif
                            </div>

                            {{-- IPv6 --}}
                            @if($order->ipv6_address)
                                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-background-dark rounded-lg">
                                    <div>
                                        <p class="text-xs text-slate-500 mb-0.5">Địa chỉ IPv6</p>
                                        <p class="text-sm font-mono font-bold text-slate-900 dark:text-white truncate max-w-[300px]">{{ $order->ipv6_address }}</p>
                                    </div>
                                    <button onclick="navigator.clipboard.writeText('{{ $order->ipv6_address }}').then(() => alert('Đã sao chép IPv6!'))"
                                        class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors shrink-0" title="Sao chép">
                                        <span class="material-symbols-outlined text-[18px]">content_copy</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Username --}}
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-background-dark rounded-lg">
                                <div>
                                    <p class="text-xs text-slate-500 mb-0.5">Username</p>
                                    <p class="text-sm font-mono font-bold text-slate-900 dark:text-white">{{ $order->username ?? 'root' }}</p>
                                </div>
                                <button onclick="navigator.clipboard.writeText('{{ $order->username ?? 'root' }}').then(() => alert('Đã sao chép username!'))"
                                    class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Sao chép">
                                    <span class="material-symbols-outlined text-[18px]">content_copy</span>
                                </button>
                            </div>

                            {{-- Mật khẩu Root --}}
                            @if($order->root_password)
                                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-background-dark rounded-lg">
                                    <div>
                                        <p class="text-xs text-slate-500 mb-0.5">Mật khẩu Root</p>
                                        <p class="text-sm font-mono font-bold text-slate-900 dark:text-white">
                                            <span x-show="!showPass">••••••••••</span>
                                            <span x-show="showPass" x-cloak>{{ $order->root_password }}</span>
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button @click="showPass = !showPass"
                                            class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Hiện/Ẩn">
                                            <span class="material-symbols-outlined text-[18px]" x-text="showPass ? 'visibility_off' : 'visibility'"></span>
                                        </button>
                                        <button onclick="navigator.clipboard.writeText('{{ $order->root_password }}').then(() => alert('Đã sao chép mật khẩu!'))"
                                            class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Sao chép">
                                            <span class="material-symbols-outlined text-[18px]">content_copy</span>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            {{-- Hướng dẫn kết nối --}}
                            <div class="bg-primary/5 border border-primary/20 rounded-lg p-4 mt-2">
                                <p class="text-sm font-bold text-primary mb-2">Hướng dẫn kết nối SSH</p>
                                <code class="text-sm text-slate-700 dark:text-slate-300 bg-white dark:bg-background-dark px-3 py-2 rounded block font-mono">
                                    ssh {{ $order->username ?? 'root' }}@{{ $order->ip_address ?? '...' }}
                                </code>
                            </div>
                        </div>
                    </div>

                    {{-- Bảng điều khiển VPS — chỉ cho gói auto Hetzner --}}
                    @if($order->hetzner_server_id)
                        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-border-dark">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">tune</span>
                                Bảng Điều Khiển
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                {{-- Restart --}}
                                <form method="POST" action="{{ route('app.vps.reboot', $order) }}"
                                    onsubmit="return confirm('Xác nhận restart VPS?')">
                                    @csrf
                                    <button type="submit"
                                        class="w-full p-4 rounded-xl border border-slate-200 dark:border-border-dark hover:border-blue-500 hover:bg-blue-500/5 transition-all text-center group">
                                        <span class="material-symbols-outlined text-blue-500 text-[32px] mb-2 group-hover:animate-spin">refresh</span>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">Restart VPS</p>
                                        <p class="text-xs text-slate-500 mt-1">Khởi động lại server</p>
                                    </button>
                                </form>

                                {{-- Đổi mật khẩu --}}
                                <form method="POST" action="{{ route('app.vps.reset-password', $order) }}"
                                    onsubmit="return confirm('Xác nhận đổi mật khẩu root?\nMật khẩu mới sẽ được hiển thị sau khi hoàn tất.')">
                                    @csrf
                                    <button type="submit"
                                        class="w-full p-4 rounded-xl border border-slate-200 dark:border-border-dark hover:border-amber-500 hover:bg-amber-500/5 transition-all text-center group">
                                        <span class="material-symbols-outlined text-amber-500 text-[32px] mb-2">lock_reset</span>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">Đổi mật khẩu</p>
                                        <p class="text-xs text-slate-500 mt-1">Reset mật khẩu root</p>
                                    </button>
                                </form>

                                {{-- Rebuild OS --}}
                                <div x-data="{ showRebuild: false }">
                                    <button @click="showRebuild = true"
                                        class="w-full p-4 rounded-xl border border-slate-200 dark:border-border-dark hover:border-purple-500 hover:bg-purple-500/5 transition-all text-center group">
                                        <span class="material-symbols-outlined text-purple-500 text-[32px] mb-2">build</span>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">Cài lại HĐH</p>
                                        <p class="text-xs text-slate-500 mt-1">Rebuild server</p>
                                    </button>

                                    {{-- Modal chọn OS mới --}}
                                    <template x-teleport="body">
                                        <div x-show="showRebuild"
                                            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                                            @click.self="showRebuild = false" style="display: none;">
                                            <div class="bg-white dark:bg-surface-dark rounded-xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-border-dark">
                                                <div class="px-6 py-4 border-b border-slate-200 dark:border-border-dark">
                                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Chọn Hệ Điều Hành Mới</h3>
                                                    <p class="text-sm text-rose-500 mt-1">⚠️ Tất cả dữ liệu trên VPS sẽ bị xóa!</p>
                                                </div>
                                                <form method="POST" action="{{ route('app.vps.rebuild', $order) }}" class="p-6 space-y-4"
                                                    onsubmit="return confirm('XÁC NHẬN REBUILD?\n\n⚠️ Tất cả dữ liệu sẽ bị XÓA VĨNH VIỄN!\nMật khẩu root mới sẽ được cập nhật.')">
                                                    @csrf
                                                    <div class="space-y-2">
                                                        @foreach($operatingSystems as $os)
                                                            <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-border-dark hover:border-primary/50 cursor-pointer transition-colors">
                                                                <input type="radio" name="image" value="{{ $os->hetzner_name }}"
                                                                    class="text-primary focus:ring-primary" {{ $loop->first ? 'checked' : '' }}>
                                                                <div>
                                                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $os->name }}</p>
                                                                    <p class="text-xs text-slate-500 capitalize">{{ $os->os_flavor ?? '' }} • {{ $os->architecture }}</p>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                    <div class="flex justify-end gap-3 pt-2">
                                                        <button type="button" @click="showRebuild = false"
                                                            class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-background-dark rounded-lg hover:bg-slate-200 transition-colors">
                                                            Hủy
                                                        </button>
                                                        <button type="submit"
                                                            class="px-4 py-2 text-sm font-medium text-white bg-rose-500 hover:bg-rose-600 rounded-lg transition-colors">
                                                            Rebuild ngay
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Gói manual — không có panel điều khiển --}}
                        <div class="bg-amber-50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/20 rounded-2xl p-6">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-amber-500 text-[24px] shrink-0 mt-0.5">info</span>
                                <div>
                                    <p class="text-sm font-bold text-amber-700 dark:text-amber-400">VPS Thủ Công</p>
                                    <p class="text-sm text-amber-600 dark:text-amber-400/80 mt-1">
                                        VPS này được quản lý thủ công. Các chức năng Restart, Đổi mật khẩu, Rebuild không khả dụng.
                                        Vui lòng liên hệ <strong>Admin</strong> nếu cần hỗ trợ.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                @elseif($order->status === 'pending')
                    {{-- Thông báo cho đơn pending --}}
                    <div class="bg-blue-50 dark:bg-blue-500/5 border border-blue-200 dark:border-blue-500/20 rounded-2xl p-6">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-blue-500 text-[24px] shrink-0 mt-0.5 animate-pulse">hourglass_top</span>
                            <div>
                                <p class="text-sm font-bold text-blue-700 dark:text-blue-400">Đang chờ xử lý</p>
                                <p class="text-sm text-blue-600 dark:text-blue-400/80 mt-1">
                                    Đơn hàng của bạn đang được xử lý. Thông tin kết nối sẽ được cập nhật khi VPS sẵn sàng.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Log hoạt động --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-border-dark">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">history</span>
                        Lịch Sử Hoạt Động
                    </h3>

                    <div class="space-y-3">
                        @forelse($order->logs as $log)
                            <div class="flex gap-3 p-3 rounded-lg bg-slate-50 dark:bg-background-dark">
                                <div class="shrink-0 mt-0.5">
                                    @php
                                        $actionIcons = [
                                            'created' => 'add_circle', 'provisioned' => 'check_circle', 'restarted' => 'refresh',
                                            'rebuilt' => 'build', 'password_reset' => 'lock_reset', 'renewed' => 'autorenew',
                                            'cancelled' => 'cancel', 'expired' => 'schedule', 'failed' => 'error',
                                        ];
                                    @endphp
                                    <span class="material-symbols-outlined text-primary text-[18px]">{{ $actionIcons[$log->action] ?? 'info' }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-700 dark:text-slate-300">{{ $log->detail ?? ucfirst($log->action) }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-slate-400">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                        @if($log->amount > 0)
                                            <span class="text-xs font-bold text-primary">{{ number_format($log->amount, 0, ',', '.') }}đ</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500 text-center py-4">Chưa có hoạt động nào.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
