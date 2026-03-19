@extends('layouts.app.app-layout')
@section('content')
    <div x-data="{ showConfirm: false, selectedPlan: {} }" class="container mx-auto px-4 py-8 max-w-5xl">
        {{-- Breadcrumb --}}
        <div class="mb-8">
            <a href="{{ route('app.gifts.templates') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-primary transition-colors mb-4">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Quay lại
            </a>
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white">
                Chọn gói <span class="text-primary">dịch vụ</span>
            </h1>
            <p class="text-slate-500 mt-1">Chọn gói phù hợp để kích hoạt trang quà tặng của bạn.</p>
        </div>

        {{-- Thông tin gift đang tạo --}}
        <div class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-2xl p-5 mb-8 flex items-center gap-4">
            <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-primary text-[24px]">redeem</span>
            </div>
            <div>
                <p class="font-bold text-slate-900 dark:text-white">{{ $gift->meta_title ?? 'Trang quà tặng' }}</p>
                <p class="text-sm text-slate-500">Mẫu: {{ $gift->template->name ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Pricing Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($plans as $planKey => $plan)
                @php $isPremium = ($planKey === 'premium'); @endphp
                <div class="relative bg-white dark:bg-surface-dark border-2 rounded-2xl overflow-hidden transition-all hover:shadow-xl group
                    {{ $isPremium ? 'border-primary shadow-lg shadow-primary/10' : 'border-slate-200 dark:border-border-dark' }}">
                    
                    {{-- Badge Premium --}}
                    @if($isPremium)
                        <div class="absolute top-0 right-0 bg-gradient-to-r from-primary to-purple-600 text-white text-xs font-bold px-4 py-1.5 rounded-bl-xl">
                            ⭐ PHỔ BIẾN NHẤT
                        </div>
                    @endif

                    <div class="p-6 md:p-8">
                        {{-- Tên gói + Giá --}}
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ $plan['name'] }}</h3>
                        <div class="flex items-baseline gap-1 mb-1">
                            @if($plan['price'] == 0)
                                <span class="text-4xl font-black text-emerald-500">Miễn phí</span>
                            @else
                                <span class="text-4xl font-black text-primary">{{ number_format($plan['price'], 0, ',', '.') }}</span>
                                <span class="text-lg font-bold text-slate-400">đ</span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 mb-6">Thời hạn: <strong>{{ $plan['duration'] }}</strong></p>

                        {{-- Features --}}
                        <ul class="space-y-3 mb-8">
                            @foreach($plan['features'] as $feature)
                                <li class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                                    <span class="material-symbols-outlined text-[18px] text-emerald-500">check_circle</span>
                                    {{ $feature }}
                                </li>
                            @endforeach
                            @foreach($plan['disabled'] as $feature)
                                <li class="flex items-center gap-2 text-sm text-slate-400">
                                    <span class="material-symbols-outlined text-[18px] text-slate-300">cancel</span>
                                    <span class="line-through">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        {{-- Button chọn gói → mở modal xác nhận --}}
                        <button type="button"
                            @click="selectedPlan = {
                                key: '{{ $planKey }}',
                                name: '{{ $plan['name'] }}',
                                price: {{ $plan['price'] }},
                                priceFormatted: '{{ $plan['price'] == 0 ? 'Miễn phí' : number_format($plan['price'], 0, ',', '.') . 'đ' }}',
                                duration: '{{ $plan['duration'] }}',
                                isPremium: {{ $isPremium ? 'true' : 'false' }}
                            }; showConfirm = true"
                            class="w-full py-3.5 rounded-xl font-bold text-sm transition-all transform hover:-translate-y-0.5
                                {{ $isPremium 
                                    ? 'bg-gradient-to-r from-primary to-purple-600 hover:from-primary/90 hover:to-purple-600/90 text-white shadow-lg shadow-primary/30' 
                                    : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                            {{ $isPremium ? '🚀 Chọn Premium' : 'Dùng gói Miễn phí' }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Ghi chú --}}
        <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl">
            <p class="text-sm text-amber-700 dark:text-amber-400 flex items-start gap-2">
                <span class="material-symbols-outlined text-[18px] mt-0.5 shrink-0">info</span>
                <span>
                    <strong>Lưu ý:</strong> Gói Basic sẽ hết hạn sau 7 ngày. Bạn có thể nâng cấp lên Premium bất cứ lúc nào để giữ link vĩnh viễn.
                    Thanh toán bằng số dư tài khoản.
                </span>
            </p>
        </div>

        {{-- Modal xác nhận thanh toán --}}
        <template x-if="showConfirm">
            <div class="fixed inset-0 z-[9998] flex items-center justify-center p-4" @keydown.escape.window="showConfirm = false">
                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showConfirm = false"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                </div>

                {{-- Dialog --}}
                <div class="relative bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-2xl shadow-2xl w-full max-w-md"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                    {{-- Header --}}
                    <div class="p-6 pb-0">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="size-11 rounded-xl flex items-center justify-center"
                                :class="selectedPlan.isPremium ? 'bg-gradient-to-br from-primary/20 to-purple-500/20' : 'bg-emerald-50 dark:bg-emerald-500/10'">
                                <span class="material-symbols-outlined text-[22px]"
                                    :class="selectedPlan.isPremium ? 'text-primary' : 'text-emerald-500'">verified</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Xác nhận thanh toán</h3>
                                <p class="text-sm text-slate-500">Vui lòng kiểm tra thông tin bên dưới</p>
                            </div>
                        </div>
                    </div>

                    {{-- Thông tin chi tiết --}}
                    <div class="px-6 py-4">
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500">Gói dịch vụ</span>
                                <span class="text-sm font-bold text-slate-900 dark:text-white" x-text="selectedPlan.name"></span>
                            </div>
                            <div class="border-t border-slate-200 dark:border-slate-700"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500">Thời hạn</span>
                                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300" x-text="selectedPlan.duration"></span>
                            </div>
                            <div class="border-t border-slate-200 dark:border-slate-700"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500">Trang quà tặng</span>
                                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ Str::limit($gift->meta_title ?? 'Quà tặng', 25) }}</span>
                            </div>
                            <div class="border-t border-slate-200 dark:border-slate-700"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">Tổng thanh toán</span>
                                <span class="text-lg font-black" :class="selectedPlan.price === 0 ? 'text-emerald-500' : 'text-primary'" x-text="selectedPlan.priceFormatted"></span>
                            </div>
                        </div>

                        {{-- Thông báo trừ số dư (chỉ hiện khi có giá) --}}
                        <template x-if="selectedPlan.price > 0">
                            <p class="mt-3 text-xs text-slate-500 dark:text-slate-400 flex items-start gap-1.5">
                                <span class="material-symbols-outlined text-[14px] mt-0.5 shrink-0">account_balance_wallet</span>
                                Số tiền sẽ được trừ trực tiếp từ số dư tài khoản của bạn.
                            </p>
                        </template>
                    </div>

                    {{-- Actions --}}
                    <div class="p-6 pt-2 flex gap-3">
                        <button type="button" @click="showConfirm = false"
                            class="flex-1 py-3 rounded-xl font-bold text-sm bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors">
                            Hủy
                        </button>
                        <form method="POST" action="{{ route('app.gifts.process-payment', $gift->unitcode) }}" class="flex-1">
                            @csrf
                            <input type="hidden" name="plan" :value="selectedPlan.key">
                            <button type="submit"
                                class="w-full py-3 rounded-xl font-bold text-sm text-white transition-all"
                                :class="selectedPlan.isPremium
                                    ? 'bg-gradient-to-r from-primary to-purple-600 hover:from-primary/90 hover:to-purple-600/90 shadow-lg shadow-primary/30'
                                    : 'bg-emerald-500 hover:bg-emerald-600 shadow-lg shadow-emerald-500/30'">
                                <span x-text="selectedPlan.price === 0 ? '✓ Xác nhận kích hoạt' : '💳 Xác nhận thanh toán'"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection
