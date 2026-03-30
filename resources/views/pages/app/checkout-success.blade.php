@extends('layouts.app.app-layout')
@section('title', 'Thanh toán thành công - NDHShop')

@section('content')
    <div class="container mx-auto px-4 py-16 max-w-lg text-center">
        {{-- Icon thành công --}}
        <div class="mb-6 flex justify-center">
            <div
                class="size-20 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30 animate-bounce">
                <span class="material-symbols-outlined text-white text-[40px]"
                    style="font-variation-settings: 'FILL' 1;">check_circle</span>
            </div>
        </div>

        <h1 class="text-3xl font-black text-slate-900 dark:text-white mb-3">
            Thanh toán <span class="text-emerald-500">thành công!</span>
        </h1>
        <p class="text-slate-500 dark:text-slate-400 mb-8 max-w-sm mx-auto">
            Cảm ơn bạn đã mua hàng tại NDHShop. Đơn hàng của bạn đã được xử lý thành công.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('app.profile', ['tab' => 'purchases']) }}"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-primary text-white font-bold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-[20px]">storefront</span>
                Xem đơn hàng
            </a>
            <a href="{{ route('app.home') }}"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                <span class="material-symbols-outlined text-[20px]">home</span>
                Về trang chủ
            </a>
        </div>
    </div>
@endsection