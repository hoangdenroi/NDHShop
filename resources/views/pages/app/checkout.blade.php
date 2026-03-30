@extends('layouts.app.app-layout')
@section('title', 'Thanh toán - NDHShop')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-2xl" x-data="{
        processing: false,
        couponCode: '',
        couponApplied: false,
        couponLoading: false,
        discount: 0,
        appliedCode: '',
        subtotal: {{ $subtotal }},

        get total() {
            return Math.max(0, this.subtotal - this.discount);
        },

        formatMoney(val) {
            return new Intl.NumberFormat('vi-VN').format(val) + 'đ';
        },

        applyCoupon() {
            if (!this.couponCode.trim()) return;
            this.couponLoading = true;

            fetch('{{ route('app.checkout.apply-coupon') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ code: this.couponCode, subtotal: this.subtotal })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(res => {
                this.couponLoading = false;
                if (res.body.success) {
                    this.discount = res.body.discount;
                    this.appliedCode = res.body.coupon_code;
                    this.couponApplied = true;
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'success', title: 'Thành công', message: res.body.message }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'error', title: 'Lỗi', message: res.body.message }
                    }));
                }
            })
            .catch(() => {
                this.couponLoading = false;
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { type: 'error', title: 'Lỗi', message: 'Không thể áp dụng mã giảm giá.' }
                }));
            });
        },

        removeCoupon() {
            this.discount = 0;
            this.appliedCode = '';
            this.couponApplied = false;
            this.couponCode = '';
        }
    }">
        {{-- Breadcrumb --}}
        <div class="mb-8">
            <a href="{{ route('app.src-app-game') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-primary transition-colors mb-4">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Tiếp tục mua sắm
            </a>
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white">
                Xác nhận <span class="text-primary">thanh toán</span>
            </h1>
        </div>

        {{-- Chi tiết đơn hàng --}}
        <div class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-2xl overflow-hidden shadow-sm mb-6">
            <div class="bg-primary/5 border-b border-primary/10 p-5">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">receipt_long</span>
                    Chi tiết đơn hàng
                    <span class="ml-auto text-sm font-medium text-slate-500">{{ $cartItems->count() }} sản phẩm</span>
                </h2>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($cartItems as $item)
                    @php
                        $image = $item->product->assets->first();
                        $imageUrl = $image ? $image->url_or_path : asset('images/placeholder.png');
                    @endphp
                    <div class="flex items-center gap-4 p-5">
                        <img src="{{ $imageUrl }}" alt="{{ $item->product->name }}"
                            class="w-14 h-14 rounded-xl object-cover border border-slate-100 dark:border-slate-700 shrink-0">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $item->product->name }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $item->product->platform ?? 'Đa nền tảng' }}</p>
                        </div>
                        <span class="text-sm font-bold text-primary shrink-0">{{ number_format($item->price, 0, ',', '.') }}đ</span>
                    </div>
                @endforeach
            </div>

            <div class="p-5 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Tạm tính</span>
                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                </div>
                <div class="flex items-center justify-between" x-show="couponApplied" x-cloak>
                    <span class="text-sm text-green-600 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">local_offer</span>
                        Giảm giá (<span x-text="appliedCode"></span>)
                    </span>
                    <span class="text-sm font-bold text-green-600" x-text="'-' + formatMoney(discount)"></span>
                </div>
                <hr class="border-slate-100 dark:border-border-dark">
                <div class="flex items-center justify-between">
                    <span class="text-base font-bold text-slate-900 dark:text-white">Tổng thanh toán</span>
                    <span class="text-2xl font-black text-primary" x-text="formatMoney(total)"></span>
                </div>
            </div>
        </div>

        {{-- Mã giảm giá --}}
        <div class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-2xl p-5 mb-6">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">confirmation_number</span>
                Mã giảm giá
            </h3>

            {{-- Khi chưa áp dụng --}}
            <div x-show="!couponApplied" class="flex gap-2">
                <input type="text" x-model="couponCode" placeholder="Nhập mã giảm giá..."
                    @keydown.enter.prevent="applyCoupon()"
                    class="flex-1 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all uppercase">
                <button @click="applyCoupon()" :disabled="couponLoading || !couponCode.trim()"
                    class="px-5 py-3 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary/90 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 shrink-0">
                    <span class="material-symbols-outlined text-[18px]" :class="couponLoading ? 'animate-spin' : ''" x-text="couponLoading ? 'progress_activity' : 'check'"></span>
                    Áp dụng
                </button>
            </div>

            {{-- Khi đã áp dụng --}}
            <div x-show="couponApplied" x-cloak class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-500 text-[20px]">check_circle</span>
                    <span class="text-sm font-bold text-green-700 dark:text-green-400">Mã <span x-text="appliedCode"></span> đã được áp dụng</span>
                    <span class="text-sm text-green-600">(-<span x-text="formatMoney(discount)"></span>)</span>
                </div>
                <button @click="removeCoupon()" class="text-slate-400 hover:text-red-500 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
        </div>

        {{-- Số dư tài khoản --}}
        <div class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-2xl p-5 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-full bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-emerald-500 text-[20px]">account_balance_wallet</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">Số dư tài khoản</p>
                        <p class="text-xs text-slate-500">Thanh toán bằng số dư</p>
                    </div>
                </div>
                <span class="text-lg font-black" :class="{{ $user->balance }} >= total ? 'text-emerald-500' : 'text-rose-500'">
                    {{ number_format($user->balance, 0, ',', '.') }}đ
                </span>
            </div>

            <template x-if="{{ $user->balance }} < total">
                <div class="mt-3 p-3 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-xl">
                    <p class="text-sm text-rose-600 dark:text-rose-400 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">warning</span>
                        Số dư không đủ. Vui lòng nạp thêm <strong x-text="formatMoney(total - {{ $user->balance }})"></strong> để tiếp tục.
                    </p>
                </div>
            </template>
        </div>

        {{-- Error message --}}
        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-xl">
                <p class="text-sm text-rose-600 dark:text-rose-400 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">error</span>
                    {{ session('error') }}
                </p>
            </div>
        @endif

        {{-- Nút thanh toán --}}
        <form method="POST" action="{{ route('app.checkout.process') }}" x-on:submit="processing = true">
            @csrf
            <input type="hidden" name="coupon_code" :value="appliedCode">

            <button type="submit" :disabled="processing || {{ $user->balance }} < total"
                class="w-full py-4 bg-gradient-to-r from-primary to-purple-600 hover:from-primary/90 hover:to-purple-600/90 text-white font-bold text-lg rounded-xl shadow-lg shadow-primary/30 transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <template x-if="!processing">
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined">payments</span>
                        Thanh toán <span x-text="formatMoney(total)"></span>
                    </span>
                </template>
                <template x-if="processing">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Đang xử lý...
                    </span>
                </template>
            </button>
        </form>

        {{-- Security note --}}
        <p class="text-center text-xs text-slate-400 mt-4 flex items-center justify-center gap-1">
            <span class="material-symbols-outlined text-[14px]">lock</span>
            Thanh toán an toàn • Dữ liệu được mã hóa
        </p>
    </div>
@endsection
