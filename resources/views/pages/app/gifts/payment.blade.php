@extends('layouts.app.app-layout')
@section('content')
    <div class="container mx-auto px-4 py-8 max-w-2xl" x-data="{
            processing: false,
            couponCode: '',
            couponApplied: false,
            couponLoading: false,
            discount: 0,
            appliedCode: '',
            subtotal: {{ $amount }},
            userBalance: {{ $user->balance }},

            get total() {
                return Math.max(0, this.subtotal - this.discount);
            },

            get hasEnoughBalance() {
                return this.userBalance >= this.total;
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
                            detail: { type: 'error', title: 'Lỗi', message: res.body.message || 'Mã giảm giá không hợp lệ.' }
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
            {{-- <a href="{{ route('app.gifts.choose-plan', $gift->unitcode) }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-primary transition-colors mb-4">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Quay lại chọn gói
            </a> --}}
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white">
                Xác nhận <span class="text-primary">thanh toán</span>
            </h1>
        </div>

        {{-- Order Summary --}}
        <div
            class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-2xl overflow-hidden shadow-sm mb-6">
            <div class="bg-primary/5 border-b border-primary/10 p-5">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">receipt_long</span>
                    Chi tiết đơn hàng
                </h2>
            </div>

            <div class="p-5 space-y-4">
                {{-- Gift info --}}
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Trang quà tặng</span>
                    <span
                        class="text-sm font-bold text-slate-900 dark:text-white">{{ \Illuminate\Support\Str::limit($gift->meta_title, 30) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Mẫu</span>
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $gift->template->name }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Gói dịch vụ</span>
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold
                                    {{ $plan === 'premium' ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-600' }}">
                        {{ $plan === 'premium' ? '⭐ Premium' : 'Basic' }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Thời hạn</span>
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                        {{ $plan === 'premium' ? 'Vĩnh viễn' : '7 ngày' }}
                    </span>
                </div>

                <hr class="border-slate-100 dark:border-border-dark mt-4 mb-4">

                {{-- nhập mã giảm giá --}}
                <div class="">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]">confirmation_number</span>
                        Mã giảm giá
                    </h3>

                    {{-- Khi chưa áp dụng --}}
                    <div x-show="!couponApplied" class="flex gap-2">
                        <input type="text" x-model="couponCode" placeholder="Nhập mã giảm giá (nếu có)..."
                            @keydown.enter.prevent="applyCoupon()"
                            class="flex-1 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all uppercase">
                        <button type="button" @click="applyCoupon()" :disabled="couponLoading || !couponCode.trim()"
                            class="px-5 py-3 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary/90 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 shrink-0">
                            <span class="material-symbols-outlined text-[18px]" :class="couponLoading ? 'animate-spin' : ''"
                                x-text="couponLoading ? 'progress_activity' : 'check'"></span>
                            Áp dụng
                        </button>
                    </div>

                    {{-- Khi đã áp dụng --}}
                    <div x-show="couponApplied" x-cloak
                        class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-green-500 text-[20px]">check_circle</span>
                            <span class="text-sm font-bold text-green-700 dark:text-green-400">Mã <span
                                    x-text="appliedCode"></span></span>
                            <span class="text-sm text-green-600">(-<span x-text="formatMoney(discount)"></span>)</span>
                        </div>
                        <button type="button" @click="removeCoupon()"
                            class="text-slate-400 hover:text-rose-500 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                </div>

                <hr class="border-slate-100 dark:border-border-dark mt-4 mb-4">

                {{-- Tổng tiền --}}
                <div class="space-y-3 pt-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500">Tạm tính</span>
                        <span
                            class="text-sm font-bold text-slate-900 dark:text-white">{{ number_format($amount, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="flex items-center justify-between" x-show="couponApplied" x-cloak>
                        <span class="text-sm text-emerald-600 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">local_offer</span>
                            Giảm giá
                        </span>
                        <span class="text-sm font-bold text-emerald-600" x-text="'-' + formatMoney(discount)"></span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-base font-bold text-slate-900 dark:text-white">Tổng thanh toán</span>
                        <span class="text-2xl font-black text-primary" x-text="formatMoney(total)"></span>
                    </div>
                </div>
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
                <span class="text-lg font-black" :class="hasEnoughBalance ? 'text-emerald-500' : 'text-rose-500'">
                    {{ number_format($user->balance, 0, ',', '.') }}đ
                </span>
            </div>

            <template x-if="!hasEnoughBalance">
                <div
                    class="mt-3 p-3 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-xl">
                    <p class="text-sm text-rose-600 dark:text-rose-400 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">warning</span>
                        Số dư không đủ. Vui lòng nạp thêm
                        <strong x-text="formatMoney(total - userBalance)"></strong> để tiếp tục.
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

        {{-- Button thanh toán --}}
        <form method="POST" action="{{ route('app.gifts.process-payment', $gift->unitcode) }}"
            x-on:submit="processing = true">
            @csrf
            <input type="hidden" name="plan" value="{{ $plan }}">
            <input type="hidden" name="coupon_code" :value="appliedCode">

            <template x-if="total === 0">
                {{-- Gói miễn phí → kích hoạt ngay --}}
                <button type="submit" :disabled="processing"
                    class="w-full py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-500/90 hover:to-emerald-600/90 text-white font-bold text-lg rounded-xl shadow-lg shadow-emerald-500/30 transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <template x-if="!processing">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined">rocket_launch</span>
                            Kích hoạt miễn phí
                        </span>
                    </template>
                    <template x-if="processing">
                        <span class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                    fill="none"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                                </path>
                            </svg>
                            Đang xử lý...
                        </span>
                    </template>
                </button>
            </template>

            <template x-if="total > 0">
                <button type="submit" :disabled="processing || !hasEnoughBalance"
                    class="w-full py-4 bg-gradient-to-r from-primary to-purple-600 hover:from-primary/90 hover:to-purple-600/90 text-white font-bold text-lg rounded-xl shadow-lg shadow-primary/30 transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <template x-if="!processing">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined">payments</span>
                            Thanh toán <span x-text="formatMoney(total)"></span>
                        </span>
                    </template>
                    <template x-if="processing">
                        <span class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                    fill="none"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                                </path>
                            </svg>
                            Đang xử lý...
                        </span>
                    </template>
                </button>
            </template>
        </form>

        {{-- Security note --}}
        <p class="text-center text-xs text-slate-400 mt-4 flex items-center justify-center gap-1">
            <span class="material-symbols-outlined text-[14px]">lock</span>
            Thanh toán an toàn • Dữ liệu được mã hóa
        </p>
    </div>
@endsection