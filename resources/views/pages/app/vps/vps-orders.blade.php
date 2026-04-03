@extends('layouts.app.app-layout')

@section('content')
<section class="py-12 px-6">
    <div class="max-w-screen-xl mx-auto">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold font-manrope text-slate-900 dark:text-white">Đơn Hàng VPS</h1>
                <p class="text-slate-500 mt-1">Quản lý tất cả VPS của bạn</p>
            </div>
            <a href="{{ route('app.vps') }}"
                class="flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary font-bold rounded-lg hover:bg-primary/20 transition-colors text-sm">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Mua thêm VPS
            </a>
        </div>

        {{-- Danh sách đơn hàng --}}
        <div class="space-y-4">
            @forelse($orders as $order)
                <a href="{{ route('app.vps.order-detail', $order) }}"
                    class="block bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-border-dark p-5 hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5 transition-all group">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            {{-- Icon trạng thái --}}
                            @php
                                $statusConfig = [
                                    'active' => ['icon' => 'check_circle', 'color' => 'emerald', 'label' => 'Đang hoạt động'],
                                    'pending' => ['icon' => 'pending', 'color' => 'amber', 'label' => 'Chờ xử lý'],
                                    'provisioning' => ['icon' => 'sync', 'color' => 'blue', 'label' => 'Đang tạo'],
                                    'expired' => ['icon' => 'schedule', 'color' => 'slate', 'label' => 'Hết hạn'],
                                    'cancelled' => ['icon' => 'cancel', 'color' => 'rose', 'label' => 'Đã hủy'],
                                    'failed' => ['icon' => 'error', 'color' => 'rose', 'label' => 'Thất bại'],
                                    'suspended' => ['icon' => 'block', 'color' => 'orange', 'label' => 'Tạm khóa'],
                                ];
                                $cfg = $statusConfig[$order->status] ?? ['icon' => 'help', 'color' => 'slate', 'label' => $order->status];
                            @endphp
                            <div class="p-3 rounded-xl bg-{{ $cfg['color'] }}-500/10 shrink-0">
                                <span class="material-symbols-outlined text-{{ $cfg['color'] }}-500 text-[24px]">{{ $cfg['icon'] }}</span>
                            </div>

                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                                        {{ $order->vpsCategory->name ?? 'VPS' }}
                                    </h3>
                                    <span class="text-xs font-mono text-primary bg-primary/10 px-2 py-0.5 rounded">{{ $order->order_code }}</span>
                                </div>
                                @if($order->ip_address)
                                    <p class="text-sm text-slate-500 mt-1 font-mono">IP: {{ $order->ip_address }}</p>
                                @endif
                                <p class="text-xs text-slate-400 mt-1">{{ $order->operating_system }} • {{ $order->location }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-6 sm:text-right">
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ number_format($order->price, 0, ',', '.') }}đ</p>
                                <p class="text-xs text-slate-500">{{ $order->duration_months }} tháng</p>
                            </div>
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $cfg['color'] }}-500/10 text-{{ $cfg['color'] }}-500 border border-{{ $cfg['color'] }}-500/20 whitespace-nowrap">
                                    <span class="size-1.5 rounded-full bg-{{ $cfg['color'] }}-500"></span>
                                    {{ $cfg['label'] }}
                                </span>
                                @if($order->expires_at && $order->status === 'active')
                                    <p class="text-xs text-slate-500 mt-1">Còn {{ $order->daysRemaining() }} ngày</p>
                                @endif
                            </div>
                            <span class="material-symbols-outlined text-slate-400 group-hover:text-primary transition-colors">chevron_right</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-border-dark p-16 text-center">
                    <span class="material-symbols-outlined text-[64px] text-slate-300 dark:text-slate-600">dns</span>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mt-4">Chưa có đơn hàng VPS nào</h3>
                    <p class="text-slate-500 mt-2">Khám phá các gói VPS hiệu suất cao của chúng tôi</p>
                    <a href="{{ route('app.vps') }}"
                        class="inline-flex items-center gap-2 mt-6 px-6 py-3 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:scale-95 transition-transform">
                        <span class="material-symbols-outlined">rocket_launch</span>
                        Khám phá gói VPS
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="mt-8">
                {{ $orders->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</section>
@endsection
