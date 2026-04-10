@extends('layouts.app.app-layout')

@section('content')
    <!-- Hero Banner -->
    <section class="relative overflow-hidden py-20 px-6 rounded-3xl">
        <div class="max-w-screen-2xl mx-auto flex flex-col lg:flex-row items-center gap-12">
            <div class="flex-1 space-y-8 z-10">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-600/10 text-primary text-xs font-bold tracking-widest uppercase">
                    <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
                    Hạ Tầng Thế Hệ Mới
                </div>
                <h1
                    class="text-6xl md:text-7xl font-extrabold font-manrope text-slate-900 dark:text-white leading-[1.1] tracking-tight">
                    Hiệu Suất Cao <br />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-blue-600">Cloud VPS</span>
                </h1>
                <p class="text-xl text-slate-600 dark:text-slate-400 max-w-xl leading-relaxed">
                    Triển khai máy chủ ảo siêu tốc chỉ trong vài giây. Tối ưu hóa cho khối lượng công việc lớn, toàn vẹn dữ
                    liệu và khả năng mở rộng toàn cầu với lưu trữ NVMe.
                </p>
                <div class="flex items-center gap-4">
                    <a href="#pricing"
                        class="px-8 py-4 bg-gradient-to-br from-primary to-blue-600 text-white font-bold rounded-xl shadow-xl shadow-primary/30 flex items-center gap-2 hover:scale-[0.98] transition-transform">
                        Khám Phá Bảng Giá
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                    @auth
                        <a href="{{ route('app.vps.orders') }}"
                            class="px-6 py-4 border border-primary/30 text-primary font-bold rounded-xl hover:bg-primary/5 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">history</span>
                            Đơn hàng VPS
                        </a>
                    @endauth
                </div>
            </div>
            <div class="flex-1 relative">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/10 rounded-full blur-[100px]"></div>
                <div
                    class="absolute -bottom-24 -left-24 w-72 h-72 bg-blue-200/20 dark:bg-blue-900/20 rounded-full blur-[80px]">
                </div>
                <div class="relative z-10 p-4 rounded-[2rem] shadow-2xl shadow-primary/10 border border-white/40 dark:border-white/10"
                    style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px);">
                    <img class="rounded-[1.5rem] w-full h-[500px] object-cover"
                        data-alt="abstract digital representation of global network and cloud server data streams in vibrant blue and violet"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAm0dS3taZEr_sI2Xmcp7IPlIl931tc0kds4Zs9kaRkmd01iusFvELBAAt1Fv-YNsVCSos73w2Li6WBCmH34KdadbPImn0FyjgjHyjTekWMVjPUXdUkwbrwNirTj6PzMVZC9PglKD4EQ7sT10fnqgnjuY-M4m7_epc1K78V2BWKKwbArL8AOo9-F1e8T9BfC7Lp1sOdFCnwaqjHjjAz5CY3unsQFlYiVH89wDO1ZmcfAPcRVsJGTNLEnJh_hPY0manj61EXidePL9k" />
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section: Dynamic từ DB -->
    @php
        $groupedCategories = $categories->groupBy('server_group');
        $tabs = [
            'cost-optimized' => ['name' => 'Tối Ưu Chi Phí', 'desc' => 'Tối ưu chi phí hiệu quả. Phù hợp cho ứng dụng nhỏ, tác vụ nhẹ và môi trường thử nghiệm (Staging).'],
            'regular' => ['name' => 'Hiệu Suất Tiêu Chuẩn', 'desc' => 'Hiệu năng CPU cao dựa trên kiến trúc chuẩn. Lý tưởng cho khối lượng công việc đa dạng xử lý mức trung bình.'],
            'general-purpose' => ['name' => 'Đa Dụng (Dedicated)', 'desc' => 'Cung cấp vCPU chuyên dụng mạnh mẽ. Mang lại tính nhất quán cho các hệ thống Core kinh doanh khắt khe.'],
        ];
        $firstTab = array_key_first($tabs);
    @endphp
    <section id="pricing" class="bg-[#f2f3ff] dark:bg-slate-800/50 py-24 px-6 rounded-3xl relative">
        <div class="max-w-screen-2xl mx-auto" x-data="{ activeTab: '{{ $firstTab }}' }">
            <div class="text-center mb-16 space-y-4">
                <h2 class="text-4xl font-extrabold font-manrope text-slate-900 dark:text-white tracking-tight">Khả Năng Mở
                    Rộng Linh Hoạt</h2>
                <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">Lựa chọn động cơ tiếp sức cho tham vọng của
                    bạn. Từ bản mẫu startup đến cụm máy chủ doanh nghiệp, chúng tôi đã sẵn sàng kiến trúc cho bạn.</p>
            </div>



            @if($categories->count() > 0)
                <!-- Custom Tabs Menu -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-12">
                    @foreach($tabs as $key => $tab)
                        <button @click="activeTab = '{{ $key }}'"
                            :class="activeTab === '{{ $key }}' ? 'border-primary ring-2 ring-primary/20 bg-primary/5' : 'border-slate-200 dark:border-border-dark bg-white dark:bg-slate-900 hover:border-primary/50 hover:bg-slate-50 dark:hover:bg-slate-800'"
                            class="p-5 rounded-[1.5rem] border text-left w-full transition-all relative overflow-hidden group">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-5 h-5 rounded-full border-[2.5px] transition-colors flex items-center justify-center shrink-0"
                                    :class="activeTab === '{{ $key }}' ? 'border-primary' : 'border-slate-300 dark:border-slate-600'">
                                    <div class="w-2.5 h-2.5 bg-primary rounded-full transition-transform"
                                        :class="activeTab === '{{ $key }}' ? 'scale-100' : 'scale-0'"></div>
                                </div>
                                <span class="text-lg font-bold text-slate-900 dark:text-white"
                                    :class="activeTab === '{{ $key }}' ? 'text-primary dark:text-primary' : ''">{{ $tab['name'] }}</span>
                            </div>
                            <p class="text-sm text-slate-500 pl-8 leading-relaxed">{{ $tab['desc'] }}</p>
                        </button>
                    @endforeach
                </div>

                <!-- Tab Content (Category Grids) -->
                @foreach($tabs as $key => $tab)
                    <div x-show="activeTab === '{{ $key }}'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                        style="display: none;" @if($key === 'cost-optimized') x-data="{ arch: 'arm' }" @endif>

                        @if($key === 'cost-optimized')
                            <div
                                class="flex items-center mb-8 p-1.5 bg-white dark:bg-slate-900 rounded-2xl w-fit shadow-sm border border-slate-200 dark:border-border-dark mx-auto md:mx-0">
                                <button @click="arch = 'arm'"
                                    :class="arch === 'arm' ? 'bg-slate-100 dark:bg-slate-800 text-primary font-bold shadow-sm ring-1 ring-black/5 dark:ring-white/10' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                    class="px-6 py-2.5 rounded-xl text-sm transition-all focus:outline-none flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]">bolt</span> Arm64 (Ampere)
                                </button>
                                <button @click="arch = 'x86'"
                                    :class="arch === 'x86' ? 'bg-slate-100 dark:bg-slate-800 text-primary font-bold shadow-sm ring-1 ring-black/5 dark:ring-white/10' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                    class="px-6 py-2.5 rounded-xl text-sm transition-all focus:outline-none flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]">memory</span> x86 (Intel/AMD)
                                </button>
                            </div>
                        @else
                            {{-- Spacing matching the radio toggle height --}}
                            <div class="h-8 mb-8 hidden md:block"></div>
                        @endif

                        @if($groupedCategories->has($key))
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                @foreach($groupedCategories[$key] as $category)
                                    @php $isArm = str_starts_with($category->hetzner_server_type, 'cax'); @endphp
                                    <div @if($key === 'cost-optimized') x-show="arch === '{{ $isArm ? 'arm' : 'x86' }}'" x-transition @endif
                                        class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] transition-all duration-300 hover:-translate-y-2 flex flex-col group border border-transparent
                                                                                                {{ $category->is_best_seller ? 'shadow-xl shadow-primary/10 ring-2 ring-primary/20 relative' : 'hover:shadow-2xl hover:shadow-primary/5 hover:border-primary/10' }}">

                                        @if($category->is_best_seller)
                                            <div
                                                class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-primary text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full">
                                                Phổ Biến Nhất</div>
                                        @endif

                                        <div
                                            class="w-14 h-14 {{ $category->is_best_seller ? 'bg-primary text-white' : 'bg-primary/10 text-primary' }} rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                            <span class="material-symbols-outlined text-3xl"
                                                style="font-variation-settings: 'FILL' 1;">cloud</span>
                                        </div>

                                        <h3 class="text-2xl font-bold font-manrope text-slate-900 dark:text-white mb-2">
                                            {{ $category->name }}
                                        </h3>

                                        <div class="flex items-baseline gap-1 mb-8">
                                            <span
                                                class="text-4xl font-extrabold text-primary">{{ number_format($category->price, 0, ',', '.') }}đ</span>
                                            <span class="text-sm font-medium text-slate-600 dark:text-slate-400">/ tháng</span>
                                        </div>

                                        <div class="space-y-4 mb-10 flex-grow">
                                            <div class="flex items-center gap-3">
                                                <span class="material-symbols-outlined text-primary text-xl">memory</span>
                                                <span
                                                    class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $category->cpu }}</span>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="material-symbols-outlined text-primary text-xl">database</span>
                                                <span
                                                    class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $category->ram }}</span>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="material-symbols-outlined text-primary text-xl">storage</span>
                                                <span
                                                    class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $category->storage }}</span>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="material-symbols-outlined text-primary text-xl">speed</span>
                                                <span
                                                    class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $category->bandwidth }}</span>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="material-symbols-outlined text-primary text-xl">public</span>
                                                <span
                                                    class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $category->metadata['ip'] ?? '1 IPv4' }}</span>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="material-symbols-outlined text-primary text-xl">security</span>
                                                <span
                                                    class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $category->metadata['firewall'] ?? 'Tường lửa cơ bản' }}</span>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="material-symbols-outlined text-primary text-xl">backup</span>
                                                <span
                                                    class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $category->metadata['backup'] ?? 'Thủ công' }}</span>
                                            </div>
                                            @if($category->warranty)
                                                <div class="flex items-center gap-3">
                                                    <span class="material-symbols-outlined text-primary text-xl">verified</span>
                                                    <span
                                                        class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $category->warranty }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <a href="{{ route('app.vps.show', $category->slug) }}"
                                            class="w-full py-4 text-center font-bold rounded-xl transition-all block
                                                                                                    {{ $category->is_best_seller ? 'bg-primary text-white shadow-lg shadow-primary/20 hover:scale-95' : 'bg-[#f2f3ff] dark:bg-slate-800/50 text-primary hover:bg-primary hover:text-white' }}">
                                            MUA NGAY
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div
                                class="text-center py-16 bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-200 dark:border-border-dark">
                                <span
                                    class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600 mb-4">inventory_2</span>
                                <p class="text-slate-500 font-medium">Chưa có gói VPS nào trong nhóm này.</p>
                                <p class="text-sm text-slate-400 mt-1">Vui lòng đổi nhóm cấu hình khác hoặc quay lại sau!</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="text-center py-16">
                    <span class="material-symbols-outlined text-[64px] text-slate-300 dark:text-slate-600">dns</span>
                    <p class="text-slate-500 mt-4">Chưa có gói VPS nào. Vui lòng quay lại sau!</p>
                </div>
            @endif
        </div>
    </section>
@endsection