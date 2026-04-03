@extends('layouts.app.app-layout')

@section('content')
<section class="py-12 px-6">
    <div class="max-w-screen-xl mx-auto">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8">
            <a href="{{ route('app.vps') }}" class="hover:text-primary transition-colors">Cloud VPS</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-slate-900 dark:text-white font-medium">{{ $category->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Cột trái: Thông tin gói VPS --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-border-dark">
                    @if($category->is_best_seller)
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-500/10 text-amber-500 text-xs font-bold rounded-full mb-4">
                            ⭐ Phổ Biến Nhất
                        </span>
                    @endif

                    <h1 class="text-3xl font-extrabold font-manrope text-slate-900 dark:text-white mb-2">{{ $category->name }}</h1>

                    <div class="flex items-baseline gap-1 mb-6">
                        <span class="text-4xl font-extrabold text-primary">{{ number_format($category->price, 0, ',', '.') }}đ</span>
                        <span class="text-sm font-medium text-slate-500">/ tháng</span>
                    </div>

                    {{-- Thông số kỹ thuật --}}
                    <div class="space-y-4 pb-6 border-b border-slate-200 dark:border-border-dark">
                        @php
                            $specs = [
                                ['icon' => 'memory', 'label' => 'CPU', 'value' => $category->cpu],
                                ['icon' => 'database', 'label' => 'RAM', 'value' => $category->ram],
                                ['icon' => 'storage', 'label' => 'Storage', 'value' => $category->storage],
                                ['icon' => 'speed', 'label' => 'Bandwidth', 'value' => $category->bandwidth],
                            ];
                        @endphp
                        @foreach($specs as $spec)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-primary text-xl">{{ $spec['icon'] }}</span>
                                    <span class="text-sm text-slate-500">{{ $spec['label'] }}</span>
                                </div>
                                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $spec['value'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Server Type Hetzner --}}
                    <div class="pt-4 flex items-center justify-between">
                        <span class="text-xs text-slate-400">Hetzner Server Type</span>
                        <code class="text-xs bg-slate-100 dark:bg-background-dark px-2 py-1 rounded font-mono text-primary">{{ $category->hetzner_server_type }}</code>
                    </div>

                    @if($category->warranty)
                        <div class="mt-3 flex items-center gap-2 text-sm">
                            <span class="material-symbols-outlined text-emerald-500 text-[18px]">verified</span>
                            <span class="text-slate-600 dark:text-slate-400">{{ $category->warranty }}</span>
                        </div>
                    @endif

                    @if($category->description)
                        <p class="mt-4 text-sm text-slate-500 leading-relaxed">{{ $category->description }}</p>
                    @endif
                </div>
            </div>

            {{-- Cột phải: Form đặt mua --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-border-dark">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">shopping_cart</span>
                        Đặt Mua VPS
                    </h2>

                    <form method="POST" action="{{ route('app.vps.purchase', $category->slug) }}" x-data="vpsPurchaseForm()">
                        @csrf

                        <div class="space-y-6">
                            {{-- Chọn Hệ Điều Hành --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                                    Hệ điều hành <span class="text-rose-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach($operatingSystems as $os)
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="operating_system" value="{{ $os->hetzner_name }}"
                                                x-model="selectedOs" class="sr-only peer" {{ $loop->first ? 'checked' : '' }}>
                                            <div class="p-3 rounded-xl border-2 border-slate-200 dark:border-border-dark peer-checked:border-primary peer-checked:bg-primary/5 transition-all hover:border-primary/50">
                                                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $os->name }}</p>
                                                <p class="text-xs text-slate-500 mt-0.5 capitalize">{{ $os->os_flavor ?? '' }} • {{ $os->architecture }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @if($operatingSystems->isEmpty())
                                    <p class="text-sm text-rose-500">Chưa có HĐH nào. Liên hệ admin.</p>
                                @endif
                            </div>

                            {{-- Chọn Location --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                                    Vị trí Datacenter <span class="text-rose-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach($locations as $location)
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="location" value="{{ $location->hetzner_name }}"
                                                x-model="selectedLocation" class="sr-only peer" {{ $loop->first ? 'checked' : '' }}>
                                            <div class="p-3 rounded-xl border-2 border-slate-200 dark:border-border-dark peer-checked:border-primary peer-checked:bg-primary/5 transition-all hover:border-primary/50">
                                                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $location->city ?? $location->name }}</p>
                                                <p class="text-xs text-slate-500 mt-0.5">{{ $location->country }} • {{ $location->hetzner_name }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Thời hạn --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                                    Thời hạn sử dụng <span class="text-rose-500">*</span>
                                </label>
                                <div class="grid grid-cols-4 gap-3">
                                    @foreach([1, 3, 6, 12] as $month)
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="duration_months" value="{{ $month }}"
                                                x-model="months" class="sr-only peer" {{ $month === 1 ? 'checked' : '' }}>
                                            <div class="p-3 rounded-xl border-2 border-slate-200 dark:border-border-dark peer-checked:border-primary peer-checked:bg-primary/5 transition-all hover:border-primary/50 text-center">
                                                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $month }}</p>
                                                <p class="text-xs text-slate-500">tháng</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Mã giảm giá --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    Mã giảm giá (không bắt buộc)
                                </label>
                                <input type="text" name="coupon_code" placeholder="Nhập mã coupon..."
                                    class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white uppercase">
                            </div>

                            {{-- Ghi chú --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    Ghi chú (không bắt buộc)
                                </label>
                                <textarea name="note" rows="2" placeholder="Ghi chú cho đơn hàng..."
                                    class="w-full border border-slate-300 dark:border-border-dark bg-white dark:bg-background-dark rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 dark:text-white resize-none"></textarea>
                            </div>

                            {{-- Tổng tiền + Nút mua --}}
                            <div class="bg-slate-50 dark:bg-background-dark rounded-xl p-5 space-y-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-slate-500">Giá gói / tháng</span>
                                    <span class="text-slate-900 dark:text-white font-medium">{{ number_format($category->price, 0, ',', '.') }}đ</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-slate-500">Thời hạn</span>
                                    <span class="text-slate-900 dark:text-white font-medium" x-text="months + ' tháng'"></span>
                                </div>
                                <div class="border-t border-slate-200 dark:border-border-dark pt-3 flex items-center justify-between">
                                    <span class="text-base font-bold text-slate-900 dark:text-white">Tổng thanh toán</span>
                                    <span class="text-2xl font-extrabold text-primary" x-text="formatVND({{ $category->price }} * months)"></span>
                                </div>
                            </div>

                            @auth
                                <button type="submit"
                                    class="w-full py-4 bg-gradient-to-br from-primary to-blue-600 text-white font-bold rounded-xl shadow-xl shadow-primary/30 hover:scale-[0.98] transition-transform text-lg flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined">rocket_launch</span>
                                    Đặt Mua VPS Ngay
                                </button>
                                <p class="text-center text-xs text-slate-500">
                                    Số dư hiện tại: <span class="font-bold text-primary">{{ number_format(auth()->user()->balance, 0, ',', '.') }}đ</span>
                                </p>
                            @else
                                <a href="{{ route('login') }}"
                                    class="w-full py-4 bg-gradient-to-br from-primary to-blue-600 text-white font-bold rounded-xl shadow-xl shadow-primary/30 hover:scale-[0.98] transition-transform text-lg flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined">login</span>
                                    Đăng nhập để mua
                                </a>
                            @endauth
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function vpsPurchaseForm() {
        return {
            selectedOs: '{{ $operatingSystems->first()?->hetzner_name ?? '' }}',
            selectedLocation: '{{ $locations->first()?->hetzner_name ?? '' }}',
            months: 1,
            formatVND(amount) {
                return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
            }
        };
    }
</script>
@endsection
