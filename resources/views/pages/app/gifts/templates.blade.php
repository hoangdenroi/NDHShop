@extends('layouts.app.app-layout')
@section('content')
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        {{-- Tiêu đề & Giới thiệu --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white mb-4 tracking-tight">
                Kho quà tặng <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-purple-600">tương tác</span>
            </h1>
            <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto text-lg">
                Chọn một mẫu bên dưới, thêm câu chúc và hình ảnh kỷ niệm của bạn để tạo ra món quà kỹ thuật số độc đáo gửi tặng người thân yêu.
            </p>
        </div>

        {{-- Thanh Lọc --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8 bg-white dark:bg-surface-dark p-4 rounded-2xl border border-slate-200 dark:border-border-dark shadow-sm">
            <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0 hide-scrollbar">
                <a href="{{ route('app.gifts.templates') }}" 
                   class="whitespace-nowrap px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ request('category') ? 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' : 'bg-primary text-white shadow-md shadow-primary/20' }}">
                    Tất cả
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('app.gifts.templates', ['category' => $cat->id]) }}"
                       class="whitespace-nowrap px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ request('category') == $cat->id ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
            
            <a href="{{ route('app.gifts.my-gifts') }}" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-white text-white dark:text-slate-900 text-sm font-bold rounded-xl transition-all shadow-md">
                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                Quà đã tạo
            </a>
        </div>

        {{-- Grid Danh Sách Mẫu --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($templates as $template)
                <div class="group bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-2xl flex flex-col overflow-hidden hover:shadow-xl hover:shadow-primary/5 hover:border-primary/30 transition-all duration-300 transform hover:-translate-y-1">
                    {{-- Thumbnail --}}
                    <div class="relative w-full aspect-video bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        @if($template->thumbnail)
                            <img src="{{ $template->thumbnail }}" alt="{{ $template->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-600">
                                <span class="material-symbols-outlined text-[48px]">redeem</span>
                            </div>
                        @endif

                        {{-- Badges --}}
                        <div class="absolute top-3 left-3 flex items-center gap-2">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-md bg-white/90 dark:bg-black/80 text-slate-800 dark:text-slate-200 backdrop-blur-sm border border-black/5 dark:border-white/5 shadow-sm">
                                {{ $template->category_label }}
                            </span>
                        </div>
                        <div class="absolute top-3 right-3 flex items-center gap-2">
                            @if($template->is_premium)
                                <span class="flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-md bg-gradient-to-r from-amber-400 to-orange-500 text-white shadow-sm">
                                    <span class="material-symbols-outlined text-[14px]">star</span>
                                    Premium
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-bold rounded-md bg-emerald-500 text-white shadow-sm">
                                    Miễn phí
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Thông tin --}}
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors line-clamp-1">
                            {{ $template->name }}
                        </h3>
                        <div class="flex items-center gap-4 mt-auto pt-4">
                            <div class="flex-1">
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider mb-0.5">Giá</p>
                                @if($template->is_premium)
                                    <p class="text-lg font-black text-rose-500">{{ number_format($template->price, 0, ',', '.') }}đ</p>
                                @else
                                    <p class="text-lg font-black text-emerald-500">Free</p>
                                @endif
                            </div>
                            <div class="flex-1 text-right">
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider mb-0.5">Lượt dùng</p>
                                <p class="text-base font-bold text-slate-700 dark:text-slate-300">{{ number_format($template->usage_count) }}</p>
                            </div>
                        </div>
                        
                        {{-- Nút Tạo --}}
                        <a href="{{ route('app.gifts.create', $template->slug) }}" class="mt-5 w-full block text-center py-2.5 rounded-xl text-sm font-bold transition-all bg-primary/10 text-primary hover:bg-primary hover:text-white border border-primary/20 hover:border-primary">
                            Tạo thiệp ngay
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-1 sm:col-span-2 lg:col-span-3 xl:col-span-4 py-16 text-center">
                    <div class="size-20 bg-slate-100 dark:bg-surface-dark rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-200 dark:border-border-dark">
                        <span class="material-symbols-outlined text-[32px] text-slate-400">search_off</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Chưa có mẫu nào</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">Hiện tại chưa có mẫu template nào trong chủ đề này.</p>
                    @if(request('category'))
                        <a href="{{ route('app.gifts.templates') }}" class="inline-flex items-center gap-2 text-primary font-semibold mt-4 hover:underline">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            Xem tất cả mẫu
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($templates->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $templates->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
@endsection
