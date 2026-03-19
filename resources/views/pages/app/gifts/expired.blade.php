@extends('layouts.app.app-layout')
@section('content')
    <div class="min-h-[70vh] flex flex-col items-center justify-center p-4">
        <div class="text-center max-w-lg mx-auto bg-white dark:bg-surface-dark p-8 md:p-12 rounded-3xl border border-slate-200 dark:border-border-dark shadow-xl">
            <div class="size-20 bg-rose-50 dark:bg-rose-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-[40px] text-rose-500">hourglass_disabled</span>
            </div>
            
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-3">
                Ôi không! Món quà đã hết hạn 🥀
            </h1>
            
            <p class="text-slate-500 dark:text-slate-400 text-lg mb-6">
                Trang quà tặng này đã vượt quá thời gian lưu trữ. Rất tiếc bạn không thể xem được nội dung.
            </p>

            {{-- Upsell: Nâng cấp Premium để reactivate --}}
            @if(isset($giftPage) && $giftPage->plan === 'basic' && auth()->check() && auth()->id() === $giftPage->user_id)
                <div class="bg-primary/5 border border-primary/20 rounded-2xl p-5 mb-6 text-left">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-[24px] mt-0.5 shrink-0">upgrade</span>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white text-sm mb-1">Bạn là người tạo gift này!</p>
                            <p class="text-sm text-slate-500 mb-3">
                                Nâng cấp lên Premium để kích hoạt lại link vĩnh viễn, bỏ watermark và có analytics chi tiết.
                            </p>
                            <form method="POST" action="{{ route('app.gifts.upgrade', $giftPage->share_code) }}">
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

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('app.home') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-colors">
                    Về trang chủ
                </a>
                <a href="{{ route('app.gifts.templates') }}" class="px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl shadow-lg shadow-primary/30 transition-transform transform hover:-translate-y-0.5">
                    Tạo món quà của riêng bạn
                </a>
            </div>
        </div>
    </div>
@endsection
