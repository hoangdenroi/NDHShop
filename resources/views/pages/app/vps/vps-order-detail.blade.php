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
                                <span
                                    class="material-symbols-outlined text-{{ $cfg['color'] }}-500 text-[24px]">{{ $cfg['icon'] }}</span>
                            </div>
                            <div>
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $cfg['bg'] }} text-white">
                                    {{ $cfg['label'] }}
                                </span>
                                <p class="text-xs text-slate-500 mt-1">{{ $order->order_code }}</p>
                            </div>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Gói VPS</span>
                                <span
                                    class="font-bold text-slate-900 dark:text-white">{{ $order->vpsCategory->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Hệ điều hành</span>
                                <span
                                    class="font-medium text-slate-900 dark:text-white">{{ $order->operating_system }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Location</span>
                                <span class="font-medium text-slate-900 dark:text-white">{{ $order->location }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Thời hạn</span>
                                <span class="font-medium text-slate-900 dark:text-white">{{ $order->duration_months }}
                                    tháng</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Giá</span>
                                <span class="font-bold text-primary">{{ number_format($order->price, 0, ',', '.') }}đ</span>
                            </div>
                            @if($order->discount_amount > 0)
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Giảm giá</span>
                                    <span
                                        class="font-medium text-emerald-500">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                                </div>
                            @endif
                            @if($order->expires_at)
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Hết hạn</span>
                                    <span
                                        class="font-medium {{ $order->isExpired() ? 'text-rose-500' : 'text-slate-900 dark:text-white' }}">
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
                                <span
                                    class="text-slate-700 dark:text-slate-300">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Cột phải: Panel quản lý VPS --}}
                <div class="lg:col-span-2" x-data="{ activeTab: 'info' }">

                    {{-- Tabs Nav --}}
                    <div
                        class="flex overflow-x-auto border-b border-slate-200 dark:border-border-dark mb-6 gap-6 custom-scrollbar focus-visible:outline-none">
                        <button @click="activeTab = 'info'"
                            :class="activeTab === 'info' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">info</span> Thông Tin
                        </button>
                        @if($order->isActive())
                            <button @click="activeTab = 'connection'"
                                :class="activeTab === 'connection' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'"
                                class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">vpn_key</span> Kết Nối
                            </button>
                            @if($order->hetzner_server_id)
                                <button @click="activeTab = 'control'"
                                    :class="activeTab === 'control' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'"
                                    class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]">tune</span> Điều Khiển
                                </button>
                                <button @click="activeTab = 'security'"
                                    :class="activeTab === 'security' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'"
                                    class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]">security</span> Bảo Mật
                                </button>
                            @endif
                        @endif
                        <button @click="activeTab = 'settings'"
                            :class="activeTab === 'settings' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">settings</span> Cài Đặt
                        </button>
                    </div>

                    {{-- Tabs Content --}}
                    <div class="space-y-6">

                        @if($order->isActive())
                            {{-- Tab Kết Nối --}}
                            <div x-show="activeTab === 'connection'" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                                {{-- Thông tin kết nối --}}
                                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-border-dark"
                                    x-data="{ showPass: false }">
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary">vpn_key</span>
                                        Thông Tin Kết Nối
                                    </h3>

                                    <div class="space-y-4">
                                        {{-- IPv4 --}}
                                        <div
                                            class="flex items-center justify-between p-3 bg-slate-50 dark:bg-background-dark rounded-lg">
                                            <div>
                                                <p class="text-xs text-slate-500 mb-0.5">Địa chỉ IPv4</p>
                                                <p class="text-sm font-mono font-bold text-slate-900 dark:text-white">
                                                    {{ $order->ip_address ?? 'Đang cấp phát...' }}</p>
                                            </div>
                                            @if($order->ip_address)
                                                <button
                                                    onclick="navigator.clipboard.writeText('{{ $order->ip_address }}').then(() => alert('Đã sao chép IP!'))"
                                                    class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                    title="Sao chép">
                                                    <span class="material-symbols-outlined text-[18px]">content_copy</span>
                                                </button>
                                            @endif
                                        </div>

                                        {{-- IPv6 --}}
                                        @if($order->ipv6_address)
                                            <div
                                                class="flex items-center justify-between p-3 bg-slate-50 dark:bg-background-dark rounded-lg">
                                                <div>
                                                    <p class="text-xs text-slate-500 mb-0.5">Địa chỉ IPv6</p>
                                                    <p
                                                        class="text-sm font-mono font-bold text-slate-900 dark:text-white truncate max-w-[300px]">
                                                        {{ $order->ipv6_address }}</p>
                                                </div>
                                                <button
                                                    onclick="navigator.clipboard.writeText('{{ $order->ipv6_address }}').then(() => alert('Đã sao chép IPv6!'))"
                                                    class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors shrink-0"
                                                    title="Sao chép">
                                                    <span class="material-symbols-outlined text-[18px]">content_copy</span>
                                                </button>
                                            </div>
                                        @endif

                                        {{-- Username --}}
                                        <div
                                            class="flex items-center justify-between p-3 bg-slate-50 dark:bg-background-dark rounded-lg">
                                            <div>
                                                <p class="text-xs text-slate-500 mb-0.5">Username</p>
                                                <p class="text-sm font-mono font-bold text-slate-900 dark:text-white">
                                                    {{ $order->username ?? 'root' }}</p>
                                            </div>
                                            <button
                                                onclick="navigator.clipboard.writeText('{{ $order->username ?? 'root' }}').then(() => alert('Đã sao chép username!'))"
                                                class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                title="Sao chép">
                                                <span class="material-symbols-outlined text-[18px]">content_copy</span>
                                            </button>
                                        </div>

                                        {{-- SSH Port --}}
                                        <div
                                            class="flex items-center justify-between p-3 bg-slate-50 dark:bg-background-dark rounded-lg">
                                            <div>
                                                <p class="text-xs text-slate-500 mb-0.5">Port SSH</p>
                                                <p class="text-sm font-mono font-bold text-slate-900 dark:text-white">22 <span
                                                        class="text-[10px] font-sans font-normal text-slate-400 ml-1">(Mặc
                                                        định)</span></p>
                                            </div>
                                            <button
                                                onclick="navigator.clipboard.writeText('22').then(() => alert('Đã sao chép Port!'))"
                                                class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                title="Sao chép">
                                                <span class="material-symbols-outlined text-[18px]">content_copy</span>
                                            </button>
                                        </div>

                                        {{-- Mật khẩu Root --}}
                                        @if($order->root_password)
                                            <div
                                                class="flex items-center justify-between p-3 bg-slate-50 dark:bg-background-dark rounded-lg">
                                                <div>
                                                    <p class="text-xs text-slate-500 mb-0.5">Mật khẩu Root</p>
                                                    <p class="text-sm font-mono font-bold text-slate-900 dark:text-white">
                                                        <span x-show="!showPass">••••••••••</span>
                                                        <span x-show="showPass" x-cloak>{{ $order->root_password }}</span>
                                                    </p>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <button @click="showPass = !showPass"
                                                        class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                        title="Hiện/Ẩn">
                                                        <span class="material-symbols-outlined text-[18px]"
                                                            x-text="showPass ? 'visibility_off' : 'visibility'"></span>
                                                    </button>
                                                    <button
                                                        onclick="navigator.clipboard.writeText('{{ $order->root_password }}').then(() => alert('Đã sao chép mật khẩu!'))"
                                                        class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                        title="Sao chép">
                                                        <span class="material-symbols-outlined text-[18px]">content_copy</span>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Hướng dẫn kết nối ssh --}}
                                        @php
                                            $usesSshKey = false;
                                            $sshKeyName = '';
                                            if ($order->note && str_contains($order->note, '[SSH Key Name]')) {
                                                $usesSshKey = true;
                                                preg_match('/\[SSH Key Name\]:\s*(.*)/', $order->note, $matches);
                                                $sshKeyName = $matches[1] ?? 'Khóa cá nhân';
                                            }
                                        @endphp
                                        <div class="bg-primary/5 border border-primary/20 rounded-lg p-4 mt-2">
                                            <p class="text-sm font-bold text-primary mb-2">Hướng dẫn kết nối SSH</p>
                                            <div
                                                class="flex items-center justify-between bg-white dark:bg-background-dark px-3 py-2 rounded border border-slate-200 dark:border-border-dark">
                                                <code
                                                    class="text-sm text-slate-700 dark:text-slate-300 font-mono">ssh {{ ($order->username ?? 'root') . '@' . ($order->ip_address ?? '...') }}</code>
                                                <button
                                                    onclick="navigator.clipboard.writeText('ssh {{ ($order->username ?? 'root') . '@' . ($order->ip_address ?? '...') }}').then(() => alert('Đã sao chép lệnh SSH!'))"
                                                    class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors shrink-0 outline-none"
                                                    title="Sao chép">
                                                    <span class="material-symbols-outlined text-[16px]">content_copy</span>
                                                </button>
                                            </div>
                                            @if($usesSshKey)
                                                <div class="mt-3 flex items-start gap-2 text-sm text-slate-600 dark:text-slate-400">
                                                    <span class="material-symbols-outlined text-amber-500 text-[18px]">key</span>
                                                    <div>
                                                        <p>Bạn đã chọn kết nối bằng <strong>SSH Key</strong> ({{ $sshKeyName }}).
                                                        </p>
                                                        <p class="mt-0.5">Vui lòng sử dụng Private Key tương ứng trên máy tính của
                                                            bạn để đăng nhập, hệ thống sẽ tự động xác thực mà không cần mật khẩu.
                                                        </p>
                                                    </div>
                                                </div>
                                            @else
                                                <p class="text-xs text-slate-500 mt-2">Sử dụng Mật khẩu Root ở trên để đăng nhập khi
                                                    được yêu cầu.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div> <!-- Hết Tab Kết Nối -->

                            {{-- Tab: Điều Khiển (Chỉ hiển thị nếu là VPS auto Hetzner) --}}
                            @if($order->hetzner_server_id)
                                <div x-show="activeTab === 'control'" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                                    <div
                                        class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-border-dark">
                                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                            <span class="material-symbols-outlined text-primary">tune</span>
                                            Bảng Điều Khiển
                                        </h3>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                            {{-- Restart --}}
                                            <form method="POST" action="{{ route('app.vps.reboot', $order) }}"
                                                onsubmit="return confirm('Xác nhận restart VPS?')">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full p-4 rounded-xl border border-slate-200 dark:border-border-dark hover:border-blue-500 hover:bg-blue-500/5 transition-all text-center group">
                                                    <span
                                                        class="material-symbols-outlined text-blue-500 text-[32px] mb-2 group-hover:animate-spin">refresh</span>
                                                    <p class="text-sm font-bold text-slate-900 dark:text-white">Restart VPS</p>
                                                    <p class="text-xs text-slate-500 mt-1">Khởi động lại server</p>
                                                </button>
                                            </form>



                                            {{-- Rebuild OS --}}
                                            <div x-data="{ showRebuild: false }">
                                                <button @click="showRebuild = true"
                                                    class="w-full p-4 rounded-xl border border-slate-200 dark:border-border-dark hover:border-purple-500 hover:bg-purple-500/5 transition-all text-center group">
                                                    <span
                                                        class="material-symbols-outlined text-purple-500 text-[32px] mb-2">build</span>
                                                    <p class="text-sm font-bold text-slate-900 dark:text-white">Cài lại HĐH</p>
                                                    <p class="text-xs text-slate-500 mt-1">Rebuild server</p>
                                                </button>

                                                {{-- Modal chọn OS mới --}}
                                                @php
                                                    $sortedOS = $operatingSystems->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
                                                    $rebuildGroupedOS = [];
                                                    foreach ($sortedOS as $os) {
                                                        $nameLower = strtolower($os->name);
                                                        if (str_contains($nameLower, 'ubuntu'))
                                                            $flavor = 'Ubuntu';
                                                        elseif (str_contains($nameLower, 'debian'))
                                                            $flavor = 'Debian';
                                                        elseif (str_contains($nameLower, 'centos'))
                                                            $flavor = 'CentOS';
                                                        elseif (str_contains($nameLower, 'rocky'))
                                                            $flavor = 'Rocky Linux';
                                                        elseif (str_contains($nameLower, 'alma'))
                                                            $flavor = 'AlmaLinux';
                                                        elseif (str_contains($nameLower, 'fedora'))
                                                            $flavor = 'Fedora';
                                                        elseif (str_contains($nameLower, 'suse'))
                                                            $flavor = 'openSUSE';
                                                        elseif (str_contains($nameLower, 'windows'))
                                                            $flavor = 'Windows';
                                                        else
                                                            $flavor = explode(' ', $os->name)[0];

                                                        $rebuildGroupedOS[$flavor][] = $os;
                                                    }
                                                    ksort($rebuildGroupedOS, SORT_NATURAL | SORT_FLAG_CASE);
                                                    $rebuildFirstFlavor = !empty($rebuildGroupedOS) ? array_key_first($rebuildGroupedOS) : '';
                                                    $rebuildFirstOs = !empty($rebuildGroupedOS) ? $rebuildGroupedOS[$rebuildFirstFlavor][0]->hetzner_name : '';
                                                @endphp
                                                <template x-teleport="body">
                                                    <div x-show="showRebuild" x-transition:enter="transition ease-out duration-200"
                                                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                        x-transition:leave="transition ease-in duration-150"
                                                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                                        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                                                        @click.self="showRebuild = false" style="display: none;">
                                                        <div class="bg-white dark:bg-surface-dark rounded-xl shadow-2xl w-full max-w-2xl border border-slate-200 dark:border-border-dark"
                                                            x-data="{ rebuildFlavor: '{{ $rebuildFirstFlavor }}', rebuildOs: '{{ $rebuildFirstOs }}' }">
                                                            <div
                                                                class="px-6 py-4 border-b border-slate-200 dark:border-border-dark">
                                                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Chọn Hệ
                                                                    Điều Hành Mới</h3>
                                                                <p class="text-sm text-rose-500 mt-1">⚠️ Tất cả dữ liệu trên VPS sẽ
                                                                    bị xóa!</p>
                                                            </div>
                                                            <form method="POST" action="{{ route('app.vps.rebuild', $order) }}"
                                                                class="p-6 space-y-4"
                                                                onsubmit="return confirm('XÁC NHẬN REBUILD?\n\n⚠️ Tất cả dữ liệu sẽ bị XÓA VĨNH VIỄN!\nMật khẩu root mới sẽ được cập nhật.')">
                                                                @csrf
                                                                <input type="hidden" name="image" x-model="rebuildOs">

                                                                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                                                                    @foreach($rebuildGroupedOS as $flavor => $osList)
                                                                        <label
                                                                            class="relative cursor-pointer group flex flex-col bg-white dark:bg-background-dark/50 border rounded-xl transition-all"
                                                                            :class="rebuildFlavor === '{{ $flavor }}' ? 'border-primary ring-1 ring-primary/30 z-10' : 'border-slate-200 dark:border-border-dark hover:border-slate-300 dark:hover:border-slate-600'">
                                                                            <input type="radio" value="{{ $flavor }}"
                                                                                x-model="rebuildFlavor"
                                                                                @change="rebuildOs = '{{ $osList[0]->hetzner_name }}'"
                                                                                class="sr-only">

                                                                            <div class="px-4 py-3 flex items-center justify-between border-b border-slate-100 dark:border-border-dark bg-slate-50/50 dark:bg-slate-800/30 rounded-t-xl"
                                                                                :class="rebuildFlavor === '{{ $flavor }}' ? 'bg-primary/5 dark:bg-primary/10' : ''">
                                                                                <div class="flex items-center gap-2">
                                                                                    <div class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs shadow-sm bg-white dark:bg-slate-900 border"
                                                                                        :class="rebuildFlavor === '{{ $flavor }}' ? 'border-primary text-primary' : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400'">
                                                                                        {{ substr($flavor, 0, 1) }}
                                                                                    </div>
                                                                                    <span
                                                                                        class="font-bold text-sm text-slate-900 dark:text-white">{{ $flavor }}</span>
                                                                                </div>
                                                                                <span
                                                                                    class="material-symbols-outlined text-sm text-slate-400 group-hover:text-amber-500 transition-colors"
                                                                                    :class="rebuildFlavor === '{{ $flavor }}' ? 'text-amber-500' : ''">bolt</span>
                                                                            </div>

                                                                            <div
                                                                                class="p-2 relative bg-white dark:bg-surface-dark rounded-b-xl">
                                                                                @if(count($osList) > 1)
                                                                                    <span
                                                                                        class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-[16px] text-slate-500">expand_more</span>
                                                                                    <select x-show="rebuildFlavor === '{{ $flavor }}'"
                                                                                        x-model="rebuildOs"
                                                                                        class="w-full bg-slate-100 dark:bg-slate-800 border-none rounded text-xs font-medium text-slate-700 dark:text-slate-300 py-2 pl-3 pr-8 focus:ring-0 appearance-none cursor-pointer">
                                                                                        @foreach($osList as $os)
                                                                                            <option value="{{ $os->hetzner_name }}">
                                                                                                {{ trim(str_ireplace([$flavor, 'Linux'], '', $os->name)) ?: $os->name }}
                                                                                                ({{ $os->architecture }})
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                    <div x-show="rebuildFlavor !== '{{ $flavor }}'"
                                                                                        class="text-xs text-center text-slate-500 font-medium py-2 rounded border border-transparent px-2 truncate">
                                                                                        {{ trim(str_ireplace([$flavor, 'Linux'], '', $osList[0]->name)) ?: $osList[0]->name }}
                                                                                    </div>
                                                                                @else
                                                                                    <div
                                                                                        class="text-xs text-center font-medium py-2 rounded focus:ring-0 bg-transparent text-slate-700 dark:text-slate-300">
                                                                                        {{ trim(str_ireplace([$flavor, 'Linux'], '', $osList[0]->name)) ?: $osList[0]->name }}
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </label>
                                                                    @endforeach
                                                                </div>

                                                                @if($operatingSystems->isEmpty())
                                                                    <p class="text-sm text-rose-500">Chưa có HĐH nào. Liên hệ admin.</p>
                                                                @endif

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
                                </div> <!-- Hết Tab Điều Khiển -->

                                <div x-show="activeTab === 'security'" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                                    <div
                                        class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-border-dark">
                                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                            <span class="material-symbols-outlined text-primary">security</span>
                                            Bảo Mật
                                        </h3>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                            {{-- Đổi mật khẩu --}}
                                            <form method="POST" action="{{ route('app.vps.reset-password', $order) }}"
                                                onsubmit="return confirm('Xác nhận đổi mật khẩu root?\nMật khẩu mới sẽ được hiển thị sau khi hoàn tất.')">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full p-4 rounded-xl border border-slate-200 dark:border-border-dark hover:border-amber-500 hover:bg-amber-500/5 transition-all text-center group">
                                                    <span
                                                        class="material-symbols-outlined text-amber-500 text-[32px] mb-2">lock_reset</span>
                                                    <p class="text-sm font-bold text-slate-900 dark:text-white">Đổi mật khẩu</p>
                                                    <p class="text-xs text-slate-500 mt-1">Reset mật khẩu root</p>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div> <!-- Hết Tab Bảo Mật -->
                            @else
                                {{-- Gói manual — không có panel điều khiển, hiển thị trong tab connection để báo cho user biết --}}
                                <div x-show="activeTab === 'connection'" x-transition.opacity.duration.200ms x-cloak>
                                    <div
                                        class="bg-amber-50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/20 rounded-2xl p-6 shadow-sm">
                                        <div class="flex items-start gap-3">
                                            <span
                                                class="material-symbols-outlined text-amber-500 text-[24px] shrink-0 mt-0.5">info</span>
                                            <div>
                                                <p class="text-sm font-bold text-amber-700 dark:text-amber-400">VPS Thủ Công</p>
                                                <p class="text-sm text-amber-600 dark:text-amber-400/80 mt-1">
                                                    VPS này được quản lý thủ công. Các chức năng Restart, Đổi mật khẩu, Rebuild
                                                    không khả dụng.
                                                    Vui lòng liên hệ <strong>Admin</strong> nếu cần hỗ trợ.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                            @endif
                        @elseif($order->status === 'pending')
                                {{-- Thông báo cho đơn pending --}}
                                <div x-show="activeTab === 'info'" x-transition.opacity.duration.200ms x-cloak>
                                    <div
                                        class="bg-blue-50 dark:bg-blue-500/5 border border-blue-200 dark:border-blue-500/20 rounded-2xl p-6 shadow-sm">
                                        <div class="flex items-start gap-3">
                                            <span
                                                class="material-symbols-outlined text-blue-500 text-[24px] shrink-0 mt-0.5 animate-pulse">hourglass_top</span>
                                            <div>
                                                <p class="text-sm font-bold text-blue-700 dark:text-blue-400">Đang chờ xử lý</p>
                                                <p class="text-sm text-blue-600 dark:text-blue-400/80 mt-1">
                                                    Đơn hàng của bạn đang được xử lý. Thông tin kết nối sẽ được cập nhật khi VPS
                                                    sẵn sàng.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Tab Lịch Sử --}}
                            <div x-show="activeTab === 'info'" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                                <div
                                    class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-border-dark">
                                    <h3
                                        class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary">history</span>
                                        Lịch Sử Hoạt Động
                                    </h3>

                                    @php
                                        $formattedLogs = $order->logs->map(function ($log) {
                                            $actionIcons = [
                                                'created' => 'add_circle',
                                                'provisioned' => 'check_circle',
                                                'restarted' => 'refresh',
                                                'rebuilt' => 'build',
                                                'password_reset' => 'lock_reset',
                                                'renewed' => 'autorenew',
                                                'cancelled' => 'cancel',
                                                'expired' => 'schedule',
                                                'failed' => 'error',
                                            ];
                                            return [
                                                'action' => $log->action,
                                                'icon' => $actionIcons[$log->action] ?? 'info',
                                                'detail' => $log->detail ?? ucfirst($log->action),
                                                'amount' => $log->amount > 0 ? number_format($log->amount, 0, ',', '.') . 'đ' : null,
                                                'created_at' => $log->created_at->format('H:i d/m/Y'),
                                            ];
                                        })->values()->toJson();
                                    @endphp

                                    <div x-data="{
                            logs: {{ $formattedLogs }},
                            currentPage: 1,
                            perPage: 5,
                            get totalPages() { return Math.ceil(this.logs.length / this.perPage); },
                            get paginatedLogs() {
                                let start = (this.currentPage - 1) * this.perPage;
                                let end = start + this.perPage;
                                return this.logs.slice(start, end);
                            }
                        }" class="space-y-4">

                                        <div class="space-y-3">
                                            <template x-if="logs.length === 0">
                                                <p class="text-sm text-slate-500 text-center py-4">Chưa có hoạt động nào.
                                                </p>
                                            </template>

                                            <template x-for="(log, index) in paginatedLogs" :key="index">
                                                <div class="flex gap-3 p-3 rounded-lg bg-slate-50 dark:bg-background-dark">
                                                    <div class="shrink-0 mt-0.5">
                                                        <span class="material-symbols-outlined text-primary text-[18px]"
                                                            x-text="log.icon"></span>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm text-slate-700 dark:text-slate-300"
                                                            x-text="log.detail"></p>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <span class="text-xs text-slate-400"
                                                                x-text="log.created_at"></span>
                                                            <template x-if="log.amount">
                                                                <span class="text-xs font-bold text-primary"
                                                                    x-text="log.amount"></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Pagination Controls -->
                                        <template x-if="totalPages > 1">
                                            <div
                                                class="flex items-center justify-between border-t border-slate-100 dark:border-border-dark pt-3 mt-4">
                                                <button type="button" @click="currentPage--" :disabled="currentPage === 1"
                                                    class="px-3 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-300 rounded-lg border border-slate-200 dark:border-border-dark disabled:opacity-50 disabled:cursor-not-allowed hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                                                    Trước
                                                </button>
                                                <span class="text-xs text-slate-500">
                                                    Trang <span x-text="currentPage"
                                                        class="font-bold text-slate-900 dark:text-white"></span> / <span
                                                        x-text="totalPages"></span>
                                                </span>
                                                <button type="button" @click="currentPage++"
                                                    :disabled="currentPage === totalPages"
                                                    class="px-3 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-300 rounded-lg border border-slate-200 dark:border-border-dark disabled:opacity-50 disabled:cursor-not-allowed hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center gap-1">
                                                    Sau <span
                                                        class="material-symbols-outlined text-[16px]">chevron_right</span>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div> <!-- Hết Tab Lịch Sử -->

                            {{-- Tab Cài Đặt --}}
                            <div x-show="activeTab === 'settings'" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                                {{-- Nút hệ thống (Hủy + Gia hạn) --}}
                                <div
                                    class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-border-dark space-y-6">
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary">settings</span>
                                        Cài Đặt Đơn Hàng
                                    </h3>

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
                                        <div
                                            class="p-4 bg-slate-50 dark:bg-background-dark rounded-xl border border-slate-200 dark:border-border-dark">
                                            <form method="POST" action="{{ route('app.vps.renew', $order) }}"
                                                x-data="{ months: {{ $firstMonth }} }"
                                                @submit="if(!confirm('Xác nhận gia hạn VPS {{ $order->order_code }} thêm ' + months + ' tháng? với giá ' + (months * {{ $order->price }}) + 'đ')) $event.preventDefault()">
                                                @csrf
                                                <div class="mb-3">
                                                    <p class="text-sm font-bold text-slate-900 dark:text-white">Gia Hạn Thêm</p>
                                                    <p class="text-xs text-slate-500 mt-1">Đảm bảo số dư tài khoản của bạn đủ để
                                                        thanh toán chu kì gia hạn.</p>
                                                </div>
                                                <div class="flex gap-3">
                                                    <select name="duration_months" x-model="months"
                                                        class="flex-1 w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-lg px-3 py-2 text-sm dark:text-white appearance-none focus:ring-1 focus:ring-primary focus:border-primary">
                                                        @foreach($availableMonths as $m)
                                                            <option value="{{ $m }}">{{ $m }} tháng</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit"
                                                        class="px-4 py-2 shrink-0 bg-emerald-500 text-white font-bold rounded-lg text-sm hover:bg-emerald-600 transition-colors flex items-center justify-center gap-1 shadow-sm">
                                                        <span class="material-symbols-outlined text-[18px]">autorenew</span>
                                                        Thanh toán
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif

                                    @if($order->canCancel())
                                        <div
                                            class="p-4 bg-rose-50 dark:bg-rose-500/5 rounded-xl border border-rose-200 dark:border-rose-500/20">
                                            <form method="POST" action="{{ route('app.vps.cancel', $order) }}"
                                                onsubmit="return confirm('XÁC NHẬN HỦY VPS {{ $order->order_code }}?\n\n⚠️ Mọi dữ liệu trên máy chủ sẽ bị xóa không thể khôi phục.\nSố tiền hoàn lại: {{ number_format($order->refundAmount(), 0, ',', '.') }}đ sẽ được cộng vào số dư tài khoản của bạn.')">
                                                @csrf
                                                <div class="mb-3">
                                                    <p class="text-sm font-bold text-rose-700 dark:text-rose-400">Hủy Dịch Vụ
                                                    </p>
                                                    <p class="text-xs text-rose-600 dark:text-rose-400/80 mt-1">
                                                        Sau khi hủy, máy chủ và dữ liệu web của bạn sẽ bị <strong>Xóa Vĩnh
                                                            Viễn</strong>.
                                                        Tiền hoàn trả: <strong
                                                            class="underline">{{ number_format($order->refundAmount(), 0, ',', '.') }}đ</strong>
                                                        cho thời gian chia nhỏ chưa sử dụng.
                                                    </p>
                                                </div>
                                                <button type="submit"
                                                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-rose-300 text-rose-600 font-bold rounded-lg text-sm hover:bg-rose-500 hover:text-white transition-colors flex items-center justify-center gap-2 shadow-sm">
                                                    <span class="material-symbols-outlined text-[18px]">delete_forever</span>
                                                    Xác Nhận Hủy VPS
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        @if(!$order->canRenew())
                                            <p class="text-sm text-slate-500 mb-0">Chưa có tuỳ chọn cài đặt khả dụng lúc này.</p>
                                        @endif
                                    @endif
                                </div>
                            </div> <!-- Hết Tab Cài Đặt -->

                        </div> <!-- Hết wrapper Tabs Content -->
                    </div>
                </div>
            </div>
    </section>
@endsection