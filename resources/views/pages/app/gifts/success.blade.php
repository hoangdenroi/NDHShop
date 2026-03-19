@extends('layouts.app.app-layout')
@section('content')
    <div class="container mx-auto px-4 py-8 max-w-2xl" x-data="giftSuccess()">
        {{-- Confetti animation --}}
        <div class="text-center mb-8">
            <div class="size-20 bg-emerald-50 dark:bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                <span class="material-symbols-outlined text-[40px] text-emerald-500">celebration</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-2">
                🎉 Chúc mừng! Gift đã sẵn sàng
            </h1>
            <p class="text-slate-500">Chia sẻ link bên dưới để gửi món quà tới người thương nhé!</p>
        </div>

        {{-- Gift Info Card --}}
        <div class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-2xl overflow-hidden shadow-sm mb-6">
            <div class="p-5 flex items-center gap-4 border-b border-slate-100 dark:border-border-dark">
                <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-[24px]">redeem</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-900 dark:text-white truncate">{{ $gift->meta_title }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold
                            {{ $gift->isPremium() ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-600' }}">
                            {{ $gift->isPremium() ? '⭐ Premium' : 'Basic' }}
                        </span>
                        @if($gift->expires_at)
                            <span class="text-xs text-slate-400">• Hết hạn: {{ $gift->expires_at->format('d/m/Y') }}</span>
                        @else
                            <span class="text-xs text-emerald-500 font-medium">• Vĩnh viễn</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tab: Link / QR --}}
            <div class="p-5">
                <div class="flex border-b border-slate-100 dark:border-border-dark mb-5">
                    <button @click="activeTab = 'link'" 
                        :class="activeTab === 'link' ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600'"
                        class="flex items-center gap-2 pb-3 px-4 text-sm font-bold border-b-2 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">link</span>
                        Link chia sẻ
                    </button>
                    <button @click="activeTab = 'qr'"
                        :class="activeTab === 'qr' ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600'"
                        class="flex items-center gap-2 pb-3 px-4 text-sm font-bold border-b-2 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">qr_code_2</span>
                        Mã QR
                    </button>
                </div>

                {{-- Tab: Link --}}
                <div x-show="activeTab === 'link'" x-transition>
                    <div class="flex items-center gap-2 bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-border-dark rounded-xl p-3">
                        <input type="text" value="{{ $gift->share_url }}" readonly id="share-link"
                            class="flex-1 bg-transparent text-sm font-mono text-slate-700 dark:text-slate-300 focus:outline-none select-all truncate">
                        <button @click="copyLink()" 
                            class="shrink-0 flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary/90 text-white text-sm font-bold rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-[16px]" x-text="copied ? 'check' : 'content_copy'"></span>
                            <span x-text="copied ? 'Đã copy!' : 'Copy'"></span>
                        </button>
                    </div>

                    {{-- Nút mở link --}}
                    <div class="mt-4 text-center">
                        <a href="{{ $gift->share_url }}" target="_blank" 
                            class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary/80 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                            Mở thử trong tab mới
                        </a>
                    </div>
                </div>

                {{-- Tab: QR --}}
                <div x-show="activeTab === 'qr'" x-transition class="text-center">
                    <div class="inline-block bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                        <img :src="qrUrl" alt="QR Code" class="w-48 h-48 mx-auto">
                    </div>
                    <p class="text-xs text-slate-400 mt-3">Quét mã QR để mở trang quà tặng</p>
                    
                    {{-- Download QR --}}
                    <a :href="qrUrl" download="gift-qr-{{ $gift->share_code }}.png"
                        class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[16px]">download</span>
                        Tải QR về máy
                    </a>
                </div>
            </div>
        </div>

        {{-- Countdown cho Basic --}}
        @if(!$gift->isPremium() && $gift->expires_at)
            <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-2xl p-5 mb-6">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-amber-500 text-[24px] mt-0.5 shrink-0">timer</span>
                    <div>
                        <p class="font-bold text-amber-700 dark:text-amber-400 mb-1">
                            Link sẽ hết hạn sau {{ $gift->expires_at->diffInDays(now()) }} ngày
                        </p>
                        <p class="text-sm text-amber-600 dark:text-amber-400/80 mb-3">
                            Nâng cấp lên Premium để giữ link vĩnh viễn, bỏ watermark và có analytics chi tiết.
                        </p>
                        <form method="POST" action="{{ route('app.gifts.upgrade', $gift->share_code) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-purple-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-primary/20 transition-all transform hover:-translate-y-0.5">
                                <span class="material-symbols-outlined text-[16px]">upgrade</span>
                                Nâng cấp Premium — {{ number_format(\App\Models\GiftPage::PLAN_PRICES['premium'], 0, ',', '.') }}đ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Nút quay về --}}
        <div class="text-center">
            <a href="{{ route('app.gifts.my-gifts') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-colors">
                <span class="material-symbols-outlined text-[18px]">dashboard</span>
                Về trang Quà tặng của tôi
            </a>
        </div>
    </div>

    <script>
        function giftSuccess() {
            return {
                activeTab: 'link',
                copied: false,
                qrUrl: 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent('{{ $gift->share_url }}'),
                copyLink() {
                    navigator.clipboard.writeText('{{ $gift->share_url }}');
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                }
            }
        }
    </script>
@endsection
