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
                <button class="px-8 py-4 bg-gradient-to-br from-primary to-blue-600 text-white font-bold rounded-xl shadow-xl shadow-primary/30 flex items-center gap-2 hover:scale-[0.98] transition-transform">
                    Khám Phá Bảng Giá
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
                <div class="flex -space-x-3">
                    <img class="w-10 h-10 rounded-full border-2 border-white dark:border-slate-800 object-cover" data-alt="portrait of a software engineer in soft studio lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCQHeW3uTFxqunieMABMX8PIZkBA5U0pVcmTelaiGKHgn-acbLdLaEOGlz2v1kkyDfXN03XRChdYH5Fhr1_in-MPaXnmHr2EVjOLwnk-as-DAxtFK1B0qJnIt2AC1KcQ-4qWSeWW-_aJbdjPWiJYkedVY5M6vaaGFot1xc_wJRQ4TJt7ihM7zp0uLAObeRy5wF43zlJIDF8-0fvdQAAKOZkuuwTITvGzCG8rfNbcWswR9CMoDii-LnUGuZdU5FddUUQM-2JAlNBsew"/>
                    <img class="w-10 h-10 rounded-full border-2 border-white dark:border-slate-800 object-cover" data-alt="portrait of a female tech founder with a friendly smile" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC4TouOezfuv3ozK_W0GB8jKicLz5mNmzMnTzfb70nJlDymLBhaE4tjlvF0QCbQbAYZzwg1VAPMdnNTl_HY63r3eoguaOfTxG3Nvi1fFpwHZIIK_LaqLzN7QyBGnymblxeyqmGvmoGbViTJRRd7bUxhq8HcQ7Qt-VaPg1gepODLepsQt7TocWxORe5Mzk3aUoySdI7e0XciMZZN1Ul55te40VSURcrUO90KvhsxNY-AQgB5sF_2ZyANDB7aWPu5VQBvLXHfebmHTUQ"/>
                    <img class="w-10 h-10 rounded-full border-2 border-white dark:border-slate-800 object-cover" data-alt="portrait of a young developer looking confident" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDQAcsth7ekeU_AZjrH9eiQ48p4imBEWSrbaPLqv1wt_ybfnFw4k4ppo8222AS7ncs5-b3LkL8Ji_h4R0jXkrv9wtIWdL0RAbH8W47Uw_N4pkRhA-Ao-qqa6DovKHxSczxVv6cJsXb5bQUB31s7lwcwgrBh4iKgYNRn3DeLGVb1ofZLgrC7IVffxGkDygGTz1xUn0M-FMyuW4iNxVCWBnJvVmid0A-FxxJr5jbNsdS8xIC-HZTtF09haiexxzTuibpGl-yv88cYPoE"/>
                    <div class="w-10 h-10 rounded-full border-2 border-white dark:border-slate-800 bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-[10px] font-bold text-slate-700 dark:text-slate-300">+2k</div>
                </div>
                <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Được tin dùng bởi các nhà phát triển toàn cầu</span>
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

<!-- Pricing Section -->
<section class="bg-[#f2f3ff] dark:bg-slate-800/50 py-24 px-6 rounded-3xl relative">
    <div class="max-w-screen-2xl mx-auto">
        <div class="text-center mb-16 space-y-4">
            <h2 class="text-4xl font-extrabold font-manrope text-slate-900 dark:text-white tracking-tight">Khả Năng Mở Rộng Linh Hoạt</h2>
            <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">Lựa chọn động cơ tiếp sức cho tham vọng của bạn. Từ bản mẫu startup đến cụm máy chủ doanh nghiệp, chúng tôi đã sẵn sàng kiến trúc cho bạn.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Plan 1 -->
            <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/5 flex flex-col group border border-transparent hover:border-primary/10">
                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 text-primary group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">cloud</span>
                </div>
                <h3 class="text-2xl font-bold font-manrope text-slate-900 dark:text-white mb-2">VPS Platinum 1</h3>
                <div class="flex items-baseline gap-1 mb-8">
                    <span class="text-4xl font-extrabold text-primary">$46.00</span>
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">/ năm</span>
                </div>
                <div class="space-y-4 mb-10 flex-grow">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">memory</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">1 Lõi vCPU</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">database</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">2GB RAM DDR4</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">storage</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">30GB SSD NVMe</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">speed</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Băng Thông Không Giới Hạn</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">public</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Mỹ / Châu Âu</span>
                    </div>
                </div>
                <button class="w-full py-4 bg-[#f2f3ff] dark:bg-slate-800/50 text-primary font-bold rounded-xl hover:bg-primary hover:text-white transition-all">MUA NGAY</button>
            </div>
            
            <!-- Plan 2 (Featured) -->
            <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] transition-all duration-300 hover:-translate-y-2 shadow-xl shadow-primary/10 flex flex-col group relative ring-2 ring-primary/20">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-primary text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full">Phổ Biến Nhất</div>
                <div class="w-14 h-14 bg-primary rounded-2xl flex items-center justify-center mb-6 text-white group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">cloud</span>
                </div>
                <h3 class="text-2xl font-bold font-manrope text-slate-900 dark:text-white mb-2">VPS Platinum 2</h3>
                <div class="flex items-baseline gap-1 mb-8">
                    <span class="text-4xl font-extrabold text-primary">$89.00</span>
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">/ năm</span>
                </div>
                <div class="space-y-4 mb-10 flex-grow">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">memory</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400 font-semibold text-slate-900 dark:text-white">2 Lõi vCPU</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">database</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400 font-semibold text-slate-900 dark:text-white">4GB RAM DDR4</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">storage</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400 font-semibold text-slate-900 dark:text-white">60GB SSD NVMe</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">speed</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Băng Thông Không Giới Hạn</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">public</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Truy Cập Toàn Cầu</span>
                    </div>
                </div>
                <button class="w-full py-4 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:scale-95 transition-all">MUA NGAY</button>
            </div>
            
            <!-- Plan 3 -->
            <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/5 flex flex-col group border border-transparent hover:border-primary/10">
                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 text-primary group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">cloud</span>
                </div>
                <h3 class="text-2xl font-bold font-manrope text-slate-900 dark:text-white mb-2">VPS Platinum 3</h3>
                <div class="flex items-baseline gap-1 mb-8">
                    <span class="text-4xl font-extrabold text-primary">$156.00</span>
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">/ năm</span>
                </div>
                <div class="space-y-4 mb-10 flex-grow">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">memory</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">4 Lõi vCPU</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">database</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">8GB RAM DDR4</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">storage</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">120GB SSD NVMe</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">speed</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Băng Thông Không Giới Hạn</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">public</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Truy Cập Toàn Cầu</span>
                    </div>
                </div>
                <button class="w-full py-4 bg-[#f2f3ff] dark:bg-slate-800/50 text-primary font-bold rounded-xl hover:bg-primary hover:text-white transition-all">MUA NGAY</button>
            </div>
            
            <!-- Plan 4 -->
            <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/5 flex flex-col group border border-transparent hover:border-primary/10">
                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 text-primary group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">cloud</span>
                </div>
                <h3 class="text-2xl font-bold font-manrope text-slate-900 dark:text-white mb-2">VPS Platinum 4</h3>
                <div class="flex items-baseline gap-1 mb-8">
                    <span class="text-4xl font-extrabold text-primary">$299.00</span>
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">/ năm</span>
                </div>
                <div class="space-y-4 mb-10 flex-grow">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">memory</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">8 Lõi vCPU</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">database</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">16GB RAM DDR4</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">storage</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">250GB SSD NVMe</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">speed</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Băng Thông Không Giới Hạn</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-xl">public</span>
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Truy Cập Toàn Cầu</span>
                    </div>
                </div>
                <button class="w-full py-4 bg-[#f2f3ff] dark:bg-slate-800/50 text-primary font-bold rounded-xl hover:bg-primary hover:text-white transition-all">MUA NGAY</button>
            </div>
        </div>
    </div>
</section>
@endsection