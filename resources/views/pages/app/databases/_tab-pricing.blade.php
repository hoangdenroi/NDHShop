{{-- ============================================ --}}
{{-- TAB: BẢNG GIÁ CLOUD PLAN --}}
{{-- Cho phép user chọn chu kỳ, xem giá real-time, mở modal xác nhận thanh toán --}}
{{-- Dữ liệu: $currentPlan, $planLabel --}}
{{-- ============================================ --}}
@php
    $daysLeft = auth()->user()->cloud_plan_expires_at 
        ? now()->diffInDays(auth()->user()->cloud_plan_expires_at, false) 
        : 999;
@endphp

<div x-data="cloudPlanPricing()" x-init="init()">

    {{-- Tiêu đề --}}
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Chọn gói Cloud Plan</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Một gói duy nhất — mở khóa cả Database & Storage</p>
    </div>

    {{-- Chọn chu kỳ thanh toán --}}
    <div class="flex items-center justify-center gap-2 mb-8 flex-wrap">
        @foreach(config('cloud_plan.billing_cycles') as $key => $cycle)
            <button @click="selectCycle('{{ $key }}')"
                :disabled="loading || showCheckout"
                :class="selectedCycle === '{{ $key }}'
                    ? 'bg-primary text-white shadow-sm'
                    : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:border-primary/50'"
                class="px-4 py-2 rounded-xl text-sm font-medium transition-all">
                {{ $cycle['label'] }}
                @if($cycle['discount'] > 0)
                    <span class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full"
                        :class="selectedCycle === '{{ $key }}'
                            ? 'bg-white/20 text-white'
                            : 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400'">
                        -{{ $cycle['discount'] }}%
                    </span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- Bảng giá 3 gói --}}
    <div class="grid gap-5 @if($currentPlan === 'free') sm:grid-cols-3 @elseif($currentPlan === 'pro') sm:grid-cols-2 max-w-4xl mx-auto @else sm:grid-cols-1 max-w-md mx-auto @endif">

        {{-- ===== FREE ===== --}}
        @if($currentPlan === 'free')
        @php $free = config('cloud_plan.plans.free'); @endphp
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm p-6 flex flex-col">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Free</h3>
            <p class="text-3xl font-extrabold text-slate-900 dark:text-white mt-3">0đ<span class="text-sm font-normal text-slate-500">/tháng</span></p>
            <p class="text-xs text-slate-500 mt-1">Dùng thử miễn phí</p>
            <div class="mt-5 mb-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Database</p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $free['max_databases'] }} database</li>
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $free['max_connections'] }} connections</li>
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $free['max_db_storage_mb'] }} MB / DB</li>
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ ucfirst($free['engines'][0]) }} only</li>
                </ul>
            </div>
            <div class="mb-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Storage</p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $free['max_buckets'] }} buckets · {{ $free['max_storage_mb'] }} MB</li>
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $free['max_file_size_mb'] }} MB/file</li>
                </ul>
            </div>
            <div class="flex-1">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Khác</p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-base">cancel</span> Không backup</li>
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-base">cancel</span> Không CDN</li>
                </ul>
            </div>
            @if($currentPlan === 'free')
                <button class="mt-6 w-full py-2.5 rounded-xl border-2 border-slate-200 dark:border-slate-600 text-sm font-semibold text-slate-400 cursor-default">Gói hiện tại</button>
            @else
                <button @click="openCheckout('downgrade', 'free', 'Free', 0)"
                    :disabled="loading || showCheckout"
                    class="mt-6 w-full py-2.5 rounded-xl border-2 border-red-200 dark:border-red-800 text-sm font-semibold text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors disabled:opacity-50 disabled:pointer-events-none">
                    Hạ về Free
                </button>
            @endif
        </div>
        @endif

        {{-- ===== PRO ===== --}}
        @if(in_array($currentPlan, ['free', 'pro']))
        @php $pro = config('cloud_plan.plans.pro'); @endphp
        <div class="bg-white dark:bg-slate-800 rounded-xl border-2 shadow-lg p-6 flex flex-col relative
            {{ $currentPlan === 'pro' ? 'border-emerald-500 shadow-emerald-500/10' : 'border-primary shadow-primary/10' }}">
            @if($currentPlan === 'pro')
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full">Gói hiện tại</span>
            @else
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full">Phổ biến</span>
            @endif
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pro</h3>
            <div class="mt-3">
                <p class="text-3xl font-extrabold text-primary">
                    <span x-text="formatPrice(prices.pro?.final_amount ?? {{ $pro['price'] }})"></span>
                    <span class="text-sm font-normal text-slate-500">/<span x-text="cycleLabel()"></span></span>
                </p>
                <div x-show="prices.pro?.discount_percent > 0" x-cloak class="flex items-center gap-2 mt-1">
                    <span class="text-xs text-slate-400 line-through" x-text="formatPrice(prices.pro?.original_amount)"></span>
                    <span class="text-xs font-semibold text-emerald-500" x-text="'Tiết kiệm ' + formatPrice(prices.pro?.discount_amount)"></span>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-1">Phù hợp developer & freelancer</p>
            <div class="mt-5 mb-2">
                <p class="text-xs font-bold text-primary/60 uppercase tracking-wider mb-2">Database</p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $pro['max_databases'] }} databases</li>
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $pro['max_connections'] }} connections</li>
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $pro['max_db_storage_mb'] }} MB / DB</li>
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> MySQL + PostgreSQL</li>
                </ul>
            </div>
            <div class="mb-2">
                <p class="text-xs font-bold text-primary/60 uppercase tracking-wider mb-2">Storage</p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $pro['max_buckets'] }} buckets · {{ number_format($pro['max_storage_mb'] / 1024, 0) }} GB</li>
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $pro['max_file_size_mb'] }} MB/file · CDN</li>
                </ul>
            </div>
            <div class="flex-1">
                <p class="text-xs font-bold text-primary/60 uppercase tracking-wider mb-2">Khác</p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> Backup hàng tuần</li>
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $pro['max_api_keys'] }} API Keys</li>
                </ul>
            </div>
            @if($currentPlan === 'pro')
                @if($daysLeft <= 7)
                    <button @click="openCheckout('renew', 'pro', 'Pro', prices.pro?.final_amount ?? {{ $pro['price'] }})"
                        :disabled="loading || showCheckout"
                        class="mt-6 w-full py-2.5 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 transition-colors shadow-sm disabled:opacity-50 disabled:pointer-events-none">Gia hạn Pro</button>
                @else
                    <button class="mt-6 w-full py-2.5 rounded-xl border-2 border-slate-200 dark:border-slate-600 text-sm font-semibold text-slate-400 cursor-default">Gói hiện tại</button>
                @endif
            @elseif($currentPlan === 'max')
                <button @click="openCheckout('downgrade', 'pro', 'Pro', 0)"
                    :disabled="loading || showCheckout"
                    class="mt-6 w-full py-2.5 rounded-xl border-2 border-amber-300 dark:border-amber-700 text-sm font-semibold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors disabled:opacity-50 disabled:pointer-events-none">Hạ về Pro</button>
            @else
                <button @click="openCheckout('upgrade', 'pro', 'Pro', prices.pro?.final_amount ?? {{ $pro['price'] }})"
                    :disabled="loading || showCheckout"
                    class="mt-6 w-full py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-colors shadow-sm disabled:opacity-50 disabled:pointer-events-none">Nâng cấp Pro</button>
            @endif
        </div>
        @endif

        {{-- ===== MAX ===== --}}
        @php $max = config('cloud_plan.plans.max'); @endphp
        <div class="bg-white dark:bg-slate-800 rounded-xl border shadow-sm p-6 flex flex-col relative
            {{ $currentPlan === 'max' ? 'border-2 border-emerald-500 shadow-emerald-500/10' : 'border-slate-100 dark:border-slate-700' }}">
            @if($currentPlan === 'max')
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full">Gói hiện tại</span>
            @else
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">Mạnh nhất</span>
            @endif
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Max</h3>
            <div class="mt-3">
                <p class="text-3xl font-extrabold text-slate-900 dark:text-white">
                    <span x-text="formatPrice(prices.max?.final_amount ?? {{ $max['price'] }})"></span>
                    <span class="text-sm font-normal text-slate-500">/<span x-text="cycleLabel()"></span></span>
                </p>
                <div x-show="prices.max?.discount_percent > 0" x-cloak class="flex items-center gap-2 mt-1">
                    <span class="text-xs text-slate-400 line-through" x-text="formatPrice(prices.max?.original_amount)"></span>
                    <span class="text-xs font-semibold text-emerald-500" x-text="'Tiết kiệm ' + formatPrice(prices.max?.discount_amount)"></span>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-1">Dành cho team & startup</p>
            <div class="mt-5 mb-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Database</p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $max['max_databases'] }} databases</li>
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $max['max_connections'] }} connections</li>
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ number_format($max['max_db_storage_mb'] / 1024, 0) }} GB / DB</li>
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> MySQL + PostgreSQL</li>
                </ul>
            </div>
            <div class="mb-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Storage</p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $max['max_buckets'] }} buckets · {{ number_format($max['max_storage_mb'] / 1024, 0) }} GB</li>
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $max['max_file_size_mb'] }} MB/file · CDN</li>
                </ul>
            </div>
            <div class="flex-1">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Khác</p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> Backup hàng ngày</li>
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> {{ $max['max_api_keys'] }} API Keys · Hỗ trợ ưu tiên</li>
                </ul>
            </div>
            @if($currentPlan === 'max')
                @if($daysLeft <= 7)
                    <button @click="openCheckout('renew', 'max', 'Max', prices.max?.final_amount ?? {{ $max['price'] }})"
                        :disabled="loading || showCheckout"
                        class="mt-6 w-full py-2.5 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 transition-colors shadow-sm disabled:opacity-50 disabled:pointer-events-none">Gia hạn Max</button>
                @else
                    <button class="mt-6 w-full py-2.5 rounded-xl border-2 border-slate-200 dark:border-slate-600 text-sm font-semibold text-slate-400 cursor-default">Gói hiện tại</button>
                @endif
            @else
                <button @click="openCheckout('upgrade', 'max', 'Max', prices.max?.final_amount ?? {{ $max['price'] }})"
                    :disabled="loading || showCheckout"
                    class="mt-6 w-full py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-white text-sm font-semibold hover:from-amber-600 hover:to-orange-600 transition-all shadow-sm disabled:opacity-50 disabled:pointer-events-none">Nâng cấp Max</button>
            @endif
        </div>
    </div>

    {{-- Thông tin hoàn tiền --}}
    @if($currentPlan !== 'free')
    <div x-show="refundAmount > 0" x-cloak
        class="mt-5 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 flex items-start gap-3">
        <span class="material-symbols-outlined text-blue-500 text-lg shrink-0 mt-0.5">info</span>
        <div class="text-sm text-blue-800 dark:text-blue-300">
            <p class="font-semibold">Khi thay đổi gói</p>
            <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">
                Bạn sẽ được hoàn <strong x-text="formatPrice(refundAmount)"></strong> (70% giá trị còn lại gói {{ $planLabel }}) vào số dư tài khoản.
            </p>
        </div>
    </div>
    @endif

    {{-- Ghi chú --}}
    <div class="mt-6 space-y-3">
        <div class="bg-amber-50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/20 rounded-xl p-4 flex items-start gap-3">
            <span class="material-symbols-outlined text-amber-500 text-lg shrink-0 mt-0.5">info</span>
            <div class="text-sm text-amber-800 dark:text-amber-300">
                <p class="font-semibold mb-1">Lưu ý khi hết hạn gói</p>
                <p class="text-xs text-amber-700 dark:text-amber-400">Khi gói hết hạn, các resource vượt quota Free sẽ bị <strong>tạm dừng</strong>. Bạn có <strong>7 ngày</strong> để gia hạn. Sau 7 ngày, dữ liệu sẽ bị xóa vĩnh viễn.</p>
            </div>
        </div>
        <div class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 flex items-start gap-3">
            <span class="material-symbols-outlined text-primary text-lg shrink-0 mt-0.5">support_agent</span>
            <div class="text-sm text-slate-700 dark:text-slate-300">
                <p class="font-semibold mb-1">Chỉ cần 1 dịch vụ?</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Nếu bạn chỉ muốn nâng cấp riêng <strong>Database</strong> hoặc <strong>Storage</strong>, hãy liên hệ Admin để được hỗ trợ gói tùy chỉnh.</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- MODAL XÁC NHẬN THANH TOÁN --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="showCheckout" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showCheckout = false"></div>
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md border border-slate-100 dark:border-slate-700 overflow-hidden"
            @click.away="showCheckout = false">

            {{-- Header gradient --}}
            <div class="px-6 pt-6 pb-4"
                :class="checkout.action === 'downgrade'
                    ? 'bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20'
                    : 'bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20'">
                <button @click="showCheckout = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-xl flex items-center justify-center shadow-sm"
                        :class="checkout.action === 'downgrade'
                            ? 'bg-gradient-to-br from-red-500 to-orange-500'
                            : checkout.action === 'renew'
                                ? 'bg-gradient-to-br from-emerald-500 to-teal-500'
                                : 'bg-gradient-to-br from-blue-500 to-indigo-600'">
                        <span class="material-symbols-outlined text-white text-2xl"
                            x-text="checkout.action === 'downgrade' ? 'arrow_downward' : checkout.action === 'renew' ? 'autorenew' : 'upgrade'"></span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white"
                            x-text="checkout.action === 'upgrade' ? 'Nâng cấp gói ' + checkout.planLabel
                                : checkout.action === 'renew' ? 'Gia hạn gói ' + checkout.planLabel
                                : 'Hạ về gói ' + checkout.planLabel"></h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400" x-text="'Chu kỳ: ' + cycleLabel()"></p>
                    </div>
                </div>
            </div>

            {{-- Nội dung modal --}}
            <div class="px-6 py-5 space-y-5">

                {{-- Chi tiết thanh toán --}}
                <div class="space-y-3">
                    {{-- Giá gốc (chỉ hiện khi upgrade/renew) --}}
                    <template x-if="checkout.action !== 'downgrade'">
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Giá gói <span x-text="checkout.planLabel"></span></span>
                                <span class="font-medium text-slate-700 dark:text-slate-300" x-text="formatPrice(checkout.originalAmount)"></span>
                            </div>
                            {{-- Chiết khấu chu kỳ --}}
                            <template x-if="checkout.cycleDiscount > 0">
                                <div class="flex items-center justify-between text-sm mt-2">
                                    <span class="text-emerald-600 dark:text-emerald-400">Chiết khấu chu kỳ</span>
                                    <span class="font-medium text-emerald-600 dark:text-emerald-400" x-text="'-' + formatPrice(checkout.cycleDiscount)"></span>
                                </div>
                            </template>
                            {{-- Coupon discount --}}
                            <template x-if="coupon.applied">
                                <div class="flex items-center justify-between text-sm mt-2">
                                    <span class="text-emerald-600 dark:text-emerald-400">
                                        Mã giảm giá (<span x-text="coupon.code"></span>)
                                    </span>
                                    <span class="font-medium text-emerald-600 dark:text-emerald-400" x-text="'-' + formatPrice(coupon.discount)"></span>
                                </div>
                            </template>
                            <div class="border-t border-slate-100 dark:border-slate-700 mt-3 pt-3 flex items-center justify-between">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">Thành tiền</span>
                                <span class="text-xl font-extrabold text-primary" x-text="formatPrice(checkout.finalAmount)"></span>
                            </div>
                        </div>
                    </template>

                    {{-- Thông tin hoàn tiền (downgrade) --}}
                    <template x-if="checkout.action === 'downgrade'">
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="material-symbols-outlined text-emerald-500 text-base">account_balance_wallet</span>
                                <span class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">Hoàn tiền vào số dư</span>
                            </div>
                            <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400" x-text="formatPrice(refundAmount)"></p>
                            <p class="text-xs text-emerald-700 dark:text-emerald-500 mt-1">70% giá trị còn lại gói {{ $planLabel }}</p>
                        </div>
                    </template>
                </div>

                {{-- Nhập mã giảm giá (chỉ hiện khi upgrade/renew) --}}
                <template x-if="checkout.action !== 'downgrade'">
                    <div>
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 block">Mã giảm giá</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="coupon.code" placeholder="Nhập mã giảm giá..."
                                :disabled="coupon.applied || coupon.loading"
                                @keydown.enter.prevent="applyCoupon()"
                                class="flex-1 border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors disabled:opacity-50 uppercase font-mono tracking-wider">
                            <template x-if="!coupon.applied">
                                <button @click="applyCoupon()" :disabled="!coupon.code.trim() || coupon.loading"
                                    class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-700 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors disabled:opacity-50 shrink-0">
                                    <span x-show="!coupon.loading">Áp dụng</span>
                                    <svg x-show="coupon.loading" class="animate-spin h-4 w-4 mx-2" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </button>
                            </template>
                            <template x-if="coupon.applied">
                                <button @click="removeCoupon()"
                                    class="px-3 py-2.5 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors shrink-0">
                                    <span class="material-symbols-outlined text-base">close</span>
                                </button>
                            </template>
                        </div>
                        {{-- Thông báo coupon --}}
                        <template x-if="coupon.message">
                            <p class="text-xs mt-2" :class="coupon.applied ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500'">
                                <span class="material-symbols-outlined text-xs align-middle mr-0.5" x-text="coupon.applied ? 'check_circle' : 'error'"></span>
                                <span x-text="coupon.message"></span>
                            </p>
                        </template>
                    </div>
                </template>

                {{-- Cảnh báo downgrade --}}
                <template x-if="checkout.action === 'downgrade'">
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-3 flex items-start gap-2">
                        <span class="material-symbols-outlined text-amber-500 text-sm mt-0.5">warning</span>
                        <p class="text-xs text-amber-700 dark:text-amber-400">Các resource vượt quota gói mới sẽ bị <strong>tạm dừng</strong> ngay lập tức.</p>
                    </div>
                </template>

                {{-- Số dư hiện tại --}}
                <template x-if="checkout.action !== 'downgrade'">
                    <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-700/50 rounded-xl px-4 py-3">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Số dư tài khoản</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ number_format(auth()->user()->balance) }}đ</span>
                    </div>
                </template>
            </div>

            {{-- Nút xác nhận --}}
            <div class="px-6 pb-6">
                <button @click="confirmAction()" :disabled="loading"
                    class="w-full py-3 rounded-xl text-white text-sm font-semibold transition-all shadow-sm disabled:opacity-50 flex items-center justify-center gap-2"
                    :class="checkout.action === 'downgrade'
                        ? 'bg-red-500 hover:bg-red-600'
                        : checkout.action === 'renew'
                            ? 'bg-emerald-500 hover:bg-emerald-600'
                            : 'bg-primary hover:bg-primary/90'">
                    <template x-if="!loading">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg"
                                x-text="checkout.action === 'downgrade' ? 'arrow_downward' : 'check_circle'"></span>
                            <span x-text="checkout.action === 'upgrade' ? 'Xác nhận nâng cấp'
                                : checkout.action === 'renew' ? 'Xác nhận gia hạn'
                                : 'Xác nhận hạ gói'"></span>
                        </span>
                    </template>
                    <template x-if="loading">
                        <span class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Đang xử lý...
                        </span>
                    </template>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Alpine.js logic --}}
<script>
function cloudPlanPricing() {
    return {
        selectedCycle: '{{ auth()->user()->cloud_plan_billing_cycle && auth()->user()->cloud_plan !== "free" ? auth()->user()->cloud_plan_billing_cycle : "monthly" }}',
        prices: { pro: null, max: null },
        refundAmount: 0,
        loading: false,

        // Modal xác nhận
        showCheckout: false,
        checkout: {
            action: '',        // upgrade | renew | downgrade
            plan: '',          // pro | max | free
            planLabel: '',     // Pro | Max | Free
            originalAmount: 0, // Giá gốc (chưa trừ coupon)
            cycleDiscount: 0,  // Chiết khấu chu kỳ
            finalAmount: 0,    // Thành tiền (đã trừ coupon)
        },

        // Mã giảm giá
        coupon: {
            code: '',
            applied: false,
            discount: 0,
            loading: false,
            message: '',
        },

        async init() {
            await this.fetchPrices();
            @if($currentPlan !== 'free')
                await this.fetchRefund();
            @endif
        },

        selectCycle(cycle) {
            this.selectedCycle = cycle;
            this.fetchPrices();
        },

        cycleLabel() {
            const labels = { 'monthly': 'tháng', 'quarterly': '3 tháng', 'semiannual': '6 tháng', 'annual': 'năm' };
            return labels[this.selectedCycle] || 'tháng';
        },

        formatPrice(amount) {
            if (!amount && amount !== 0) return '0đ';
            return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
        },

        async fetchPrices() {
            try {
                const [proRes, maxRes] = await Promise.all([
                    fetch(`{{ route('app.cloud-plan.calculate-price') }}?plan=pro&cycle=${this.selectedCycle}`),
                    fetch(`{{ route('app.cloud-plan.calculate-price') }}?plan=max&cycle=${this.selectedCycle}`)
                ]);
                const [proData, maxData] = await Promise.all([proRes.json(), maxRes.json()]);
                if (proData.success) this.prices.pro = proData.data;
                if (maxData.success) this.prices.max = maxData.data;
            } catch (e) {
                console.error('Lỗi tải giá:', e);
            }
        },

        async fetchRefund() {
            try {
                const res = await fetch('{{ route('app.cloud-plan.refund-preview') }}');
                const data = await res.json();
                if (data.success) this.refundAmount = data.refund_amount;
            } catch (e) { console.error('Lỗi tải refund:', e); }
        },

        // Mở modal xác nhận
        openCheckout(action, plan, planLabel, amount) {
            const priceData = this.prices[plan];
            const originalAmount = priceData?.original_amount ?? amount;
            const cycleDiscount = priceData?.discount_amount ?? 0;
            const finalAmount = priceData?.final_amount ?? amount;

            this.checkout = {
                action,
                plan,
                planLabel,
                originalAmount,
                cycleDiscount,
                finalAmount,
            };

            // Reset coupon
            this.coupon = { code: '', applied: false, discount: 0, loading: false, message: '' };
            this.showCheckout = true;
        },

        // Áp dụng mã giảm giá
        async applyCoupon() {
            const code = this.coupon.code.trim();
            if (!code) return;

            this.coupon.loading = true;
            this.coupon.message = '';

            try {
                const res = await fetch('{{ route('app.cloud-plan.apply-coupon') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        code: code,
                        plan: this.checkout.plan,
                        cycle: this.selectedCycle
                    })
                });
                const data = await res.json();

                if (data.success) {
                    this.coupon.applied = true;
                    this.coupon.discount = data.data.discount_amount;
                    this.coupon.message = data.message;
                    this.checkout.finalAmount = data.data.final_price;
                } else {
                    this.coupon.message = data.message;
                }
            } catch (e) {
                this.coupon.message = 'Có lỗi xảy ra, vui lòng thử lại.';
            }
            this.coupon.loading = false;
        },

        // Bỏ mã giảm giá
        removeCoupon() {
            const priceData = this.prices[this.checkout.plan];
            this.checkout.finalAmount = priceData?.final_amount ?? this.checkout.originalAmount;
            this.coupon = { code: '', applied: false, discount: 0, loading: false, message: '' };
        },

        // Xác nhận thao tác (upgrade / renew / downgrade)
        async confirmAction() {
            this.loading = true;
            const { action, plan } = this.checkout;

            try {
                let url, body;

                if (action === 'upgrade') {
                    url = '{{ route('app.cloud-plan.upgrade') }}';
                    body = { plan, cycle: this.selectedCycle };
                } else if (action === 'renew') {
                    url = '{{ route('app.cloud-plan.renew') }}';
                    body = { cycle: this.selectedCycle };
                } else {
                    url = '{{ route('app.cloud-plan.downgrade') }}';
                    body = { new_plan: plan };
                }

                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(body)
                });
                const data = await res.json();

                if (data.success) {
                    this.showCheckout = false;
                    this.showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                    return; // Không reset loading — page sẽ reload
                } else {
                    this.showToast(data.message || 'Thao tác thất bại.', 'error');
                }
            } catch (e) {
                console.error('Cloud Plan action error:', e);
                this.showToast('Có lỗi xảy ra, vui lòng thử lại.', 'error');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
