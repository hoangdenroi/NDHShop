@extends('layouts.app.app-layout')

@section('content')
<!-- Hero Banner -->
<section class="relative overflow-hidden py-20 px-6 rounded-3xl">
    <div class="max-w-screen-2xl mx-auto flex flex-col lg:flex-row items-center gap-12">
        <div class="flex-1 space-y-8 z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-600/10 text-primary text-xs font-bold tracking-widest uppercase">
                <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
                Hạ Tầng Thế Hệ Mới
            </div>
            <h1 class="text-6xl md:text-7xl font-extrabold font-manrope text-slate-900 dark:text-white leading-[1.1] tracking-tight">
                Hiệu Suất Cao <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-blue-600">Cloud VPS</span>
            </h1>
            <p class="text-xl text-slate-600 dark:text-slate-400 max-w-xl leading-relaxed">
                Triển khai máy chủ ảo siêu tốc chỉ trong vài giây. Tối ưu hóa cho khối lượng công việc lớn, toàn vẹn dữ liệu và khả năng mở rộng toàn cầu với lưu trữ NVMe.
            </p>
            <div class="flex items-center gap-4">
                <a href="#pricing" class="px-8 py-4 bg-gradient-to-br from-primary to-blue-600 text-white font-bold rounded-xl shadow-xl shadow-primary/30 flex items-center gap-2 hover:scale-[0.98] transition-transform">
                    Khám Phá Bảng Giá
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
                @auth
                    <a href="{{ route('app.vps.orders') }}" class="px-6 py-4 border border-primary/30 text-primary font-bold rounded-xl hover:bg-primary/5 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">history</span>
                        Đơn hàng VPS
                    </a>
                @endauth
            </div>
        </div>
        <div class="flex-1 relative">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/10 rounded-full blur-[100px]"></div>
            <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-blue-200/20 dark:bg-blue-900/20 rounded-full blur-[80px]"></div>
            <div class="relative z-10 p-4 rounded-[2rem] shadow-2xl shadow-primary/10 border border-white/40 dark:border-white/10" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px);">
                <img class="rounded-[1.5rem] w-full h-[500px] object-cover" data-alt="abstract digital representation of global network and cloud server data streams in vibrant blue and violet" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAm0dS3taZEr_sI2Xmcp7IPlIl931tc0kds4Zs9kaRkmd01iusFvELBAAt1Fv-YNsVCSos73w2Li6WBCmH34KdadbPImn0FyjgjHyjTekWMVjPUXdUkwbrwNirTj6PzMVZC9PglKD4EQ7sT10fnqgnjuY-M4m7_epc1K78V2BWKKwbArL8AOo9-F1e8T9BfC7Lp1sOdFCnwaqjHjjAz5CY3unsQFlYiVH89wDO1ZmcfAPcRVsJGTNLEnJh_hPY0manj61EXidePL9k"/>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section: Dynamic từ DB -->
<section id="pricing" class="bg-[#f2f3ff] dark:bg-slate-800/50 py-24 px-6 rounded-3xl relative">
    <div class="max-w-screen-2xl mx-auto">
        <div class="text-center mb-16 space-y-4">
            <h2 class="text-4xl font-extrabold font-manrope text-slate-900 dark:text-white tracking-tight">Khả Năng Mở Rộng Linh Hoạt</h2>
            <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">Lựa chọn động cơ tiếp sức cho tham vọng của bạn. Từ bản mẫu startup đến cụm máy chủ doanh nghiệp, chúng tôi đã sẵn sàng kiến trúc cho bạn.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($categories as $category)
                <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] transition-all duration-300 hover:-translate-y-2 flex flex-col group border border-transparent
                    {{ $category->is_best_seller ? 'shadow-xl shadow-primary/10 ring-2 ring-primary/20 relative' : 'hover:shadow-2xl hover:shadow-primary/5 hover:border-primary/10' }}">

                    @if($category->is_best_seller)
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-primary text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full">Phổ Biến Nhất</div>
                    @endif

                    <div class="w-14 h-14 {{ $category->is_best_seller ? 'bg-primary text-white' : 'bg-primary/10 text-primary' }} rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">cloud</span>
                    </div>

                    <h3 class="text-2xl font-bold font-manrope text-slate-900 dark:text-white mb-2">{{ $category->name }}</h3>

                    <div class="flex items-baseline gap-1 mb-8">
                        <span class="text-4xl font-extrabold text-primary">{{ number_format($category->price, 0, ',', '.') }}đ</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">/ tháng</span>
                    </div>

                    <div class="space-y-4 mb-10 flex-grow">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-xl">memory</span>
                            <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $category->cpu }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-xl">database</span>
                            <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $category->ram }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-xl">storage</span>
                            <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $category->storage }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-xl">speed</span>
                            <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $category->bandwidth }}</span>
                        </div>
                        @if($category->warranty)
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary text-xl">verified</span>
                                <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $category->warranty }}</span>
                            </div>
                        @endif
                    </div>

                    <a href="{{ route('app.vps.show', $category->slug) }}"
                        class="w-full py-4 text-center font-bold rounded-xl transition-all block
                        {{ $category->is_best_seller ? 'bg-primary text-white shadow-lg shadow-primary/20 hover:scale-95' : 'bg-[#f2f3ff] dark:bg-slate-800/50 text-primary hover:bg-primary hover:text-white' }}">
                        MUA NGAY
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <span class="material-symbols-outlined text-[64px] text-slate-300 dark:text-slate-600">dns</span>
                    <p class="text-slate-500 mt-4">Chưa có gói VPS nào. Vui lòng quay lại sau!</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection