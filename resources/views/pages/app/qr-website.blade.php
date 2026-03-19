@extends('layouts.app.app-layout')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">QR Website</h1>
        </div>

        {{-- Banner Test Gift Templates --}}
        <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-2xl p-8 text-white shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-3xl font-black mb-2">Trải nghiệm tính năng mới! 🎁</h2>
                <p class="text-indigo-100 mb-6 max-w-xl text-lg">
                    Tạo ngay những trang thiệp độc quyền, cá nhân hóa với lời chúc và hình ảnh kỷ niệm của bạn!
                </p>
                <a href="{{ route('app.gifts.templates') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-purple-600 font-bold rounded-xl hover:bg-indigo-50 transition-all shadow-md hover:shadow-xl transform hover:-translate-y-1">
                    <span class="material-symbols-outlined">magic_button</span>
                    Tạo ngay thiệp quà tặng
                </a>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 transform translate-x-1/4 translate-y-1/4">
                <span class="material-symbols-outlined text-[200px]">redeem</span>
            </div>
        </div>

    </div>
@endsection