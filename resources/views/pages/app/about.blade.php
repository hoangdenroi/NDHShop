@extends('layouts.app.app-layout')

@section('content')
    {{-- Hero Section --}}
    <section class="relative py-20 px-6 bg-white dark:bg-background-dark overflow-hidden rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="mx-auto max-w-7xl">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="flex flex-col gap-6">
                    <span class="inline-block w-fit px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider">Từ năm 2020</span>
                    <h1 class="text-5xl font-extrabold tracking-tight lg:text-6xl text-slate-900 dark:text-slate-100">
                        Đồng hành cùng <span class="text-primary">khách hàng</span>
                    </h1>
                    <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed max-w-xl">
                        NDHShop là điểm đến hàng đầu cho các sản phẩm số chất lượng cao, giúp khách hàng tiếp cận nhanh chóng và thuận tiện với các giải pháp công nghệ sáng tạo.
                    </p>
                </div>
                <div class="relative h-[400px] w-full rounded-2xl overflow-hidden shadow-2xl">
                    <div class="absolute inset-0 bg-gradient-to-tr from-primary/20 to-transparent z-10"></div>
                    <img class="h-full w-full object-cover" alt="Diverse development team collaborating in modern office space" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDyy83w8LhP3TCdlnTSmU-Ok8buvpHnCUjiNAfZf4UFgec4jsy4-jllnP9nSp8WZU84uWhQBiXV3K83AuyJkrM41pbknNJLdZx5vHImyBHNfpxpht8-Iugl0zEhCKEbZCG0bBh2MScsph6urxyLIzkHir8B8s0qYPx_FOFrq4DCSDlY5UgCTU_mp8L19w6_zsy1mxYKX4f7TAhaaxB5u4pd4R5Ad2ZtMT1elylTCOsnZ6gaepDuGaaKoFGp31_tJ0cvn8yb-vFh-kk"/>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="py-12 border border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 rounded-2xl">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <p class="text-4xl font-black text-primary">10k+</p>
                    <p class="text-sm text-slate-500 uppercase font-bold tracking-widest mt-2">Người dùng</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-black text-primary">500+</p>
                    <p class="text-sm text-slate-500 uppercase font-bold tracking-widest mt-2">Sản phẩm số</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-black text-primary">10+</p>
                    <p class="text-sm text-slate-500 uppercase font-bold tracking-widest mt-2">Danh mục</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-black text-primary">24/7</p>
                    <p class="text-sm text-slate-500 uppercase font-bold tracking-widest mt-2">Hỗ trợ</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Our Mission & Story --}}
    <section class="py-24 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="mx-auto max-w-4xl px-6">
            <div class="flex flex-col gap-16">
                <div>
                    <h2 class="text-3xl font-bold mb-6 flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">rocket_launch</span>
                        Sứ mệnh của chúng tôi
                    </h2>
                    <p class="text-xl text-slate-600 dark:text-slate-300 leading-relaxed italic border-l-4 border-primary pl-6">
                        "Cung cấp một nền tảng trực tuyến nơi khách hàng có thể tìm thấy các sản phẩm số chất lượng cao, giúp tiết kiệm thời gian và tập trung vào điều quan trọng nhất: sáng tạo."
                    </p>
                </div>
                <div>
                    <h2 class="text-3xl font-bold mb-6 flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">history_edu</span>
                        Câu chuyện của chúng tôi
                    </h2>
                    <div class="space-y-4 text-slate-600 dark:text-slate-400 leading-relaxed">
                        <p>
                            Thành lập từ năm 2020, NDHShop khởi đầu là một dự án nhỏ nhằm cung cấp các sản phẩm số tiện ích cho cộng đồng. Chúng tôi nhận ra rằng người dùng luôn cần những giải pháp nhanh chóng, chất lượng và đáng tin cậy.
                        </p>
                        <p>
                            Từ một cửa hàng nhỏ, NDHShop đã phát triển thành một nền tảng toàn diện với hàng nghìn sản phẩm số đa dạng. Ngày nay, chúng tôi là điểm đến hàng đầu cho các tài nguyên số chất lượng cao phục vụ nhiều lĩnh vực khác nhau.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Core Values --}}
    <section class="py-24 bg-slate-50 dark:bg-slate-900/30 rounded-2xl">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold">Giá trị cốt lõi</h2>
                <p class="text-slate-500 mt-2">Những nguyên tắc định hướng mọi hoạt động của chúng tôi</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary mb-6">
                        <span class="material-symbols-outlined">verified</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Chất lượng là trên hết</h3>
                    <p class="text-slate-600 dark:text-slate-400">Mỗi sản phẩm trên nền tảng của chúng tôi đều trải qua quy trình kiểm duyệt nghiêm ngặt để đảm bảo chất lượng và an toàn.</p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary mb-6">
                        <span class="material-symbols-outlined">diversity_3</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Lấy khách hàng làm trung tâm</h3>
                    <p class="text-slate-600 dark:text-slate-400">Chúng tôi tin vào sức mạnh của sự hợp tác. Lộ trình phát triển của chúng tôi được định hình bởi phản hồi từ hàng nghìn khách hàng sử dụng NDHShop.</p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary mb-6">
                        <span class="material-symbols-outlined">lightbulb</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Không ngừng đổi mới</h3>
                    <p class="text-slate-600 dark:text-slate-400">Thị trường công nghệ thay đổi nhanh chóng. Chúng tôi liên tục cập nhật danh mục sản phẩm và tính năng để bắt kịp xu hướng mới nhất.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Team Members --}}
    <section class="py-24 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold">Đội ngũ của chúng tôi</h2>
                <p class="text-slate-500 mt-2">Những con người đứng sau thành công</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="group text-center">
                    <div class="relative mb-4 aspect-square rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img class="h-full w-full object-cover transition-transform group-hover:scale-105" alt="Ảnh đại diện Nguyễn Đức Hoàng, Nhà sáng lập" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsufma_56kHu2Ci88dSIrRxHScUNPFKStjImYU3533cdSWWzOJnRU2ewhY6aXq1FzNEHMsqo04RNIrbwUSuw_R9xDfIOZRNNBarN1KwBkHz7IXV0Q3alGN5DMMpQm6vqDZoRuvBcqnYrSYmdk95Bm_Qr8-JKXbSBv_7kilyT4kdb1fqDsNybFOgZP7LH9ifHKI8XWdOJ9fvtuyHa-btoPNouS7tffs-0hXV2qaJmrr_Tw6p92dMF-O-AXmthxFGeg_Af12PwaQVRE"/>
                    </div>
                    <h4 class="text-lg font-bold">Nguyễn Đức Hoàng</h4>
                    <p class="text-primary text-sm font-medium">Nhà sáng lập &amp; CEO</p>
                </div>
                <div class="group text-center">
                    <div class="relative mb-4 aspect-square rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img class="h-full w-full object-cover transition-transform group-hover:scale-105" alt="Ảnh đại diện Trần Minh Anh, Giám đốc Kỹ thuật" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC91SynLdPwkfxfh8aE6IHxbr7C9HZE0PGT2wzhXDlZDaLQSqunyl_j6AZEaP9IrS7uaNvLmsTH3Z5Nvh8c31a7XpWhPNcX6AJRqxT1mE5lr29tEF5Cjno-DlLBps6waGOi-lBRXSqm7UWzJYIJ_Ts6tLC2Hg9QG6_FhPjVCfHMJ8h9BN6OlC21gHvGYyZ22MktGuo_icn2qegB0k8q5MgmAPzOOMif9yoRBX3UHqWgwy4IXGGtKigborWBgpBDG0IXUbsIFCrUDLI"/>
                    </div>
                    <h4 class="text-lg font-bold">Trần Minh Anh</h4>
                    <p class="text-primary text-sm font-medium">Giám đốc Kỹ thuật</p>
                </div>
                <div class="group text-center">
                    <div class="relative mb-4 aspect-square rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img class="h-full w-full object-cover transition-transform group-hover:scale-105" alt="Ảnh đại diện Lê Văn Hùng, Trưởng phòng Phát triển" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDAS_SQxWhF4TL0sgxjZ0CYqe1k-S2OcONeTdnXgGb4pOfnwM2lHV7lMiOcT1wim3XX_4RCuEuqKkkmF3dyuBloi6i2h07DEyYYpCq2MU-Z5sYwzIAQygkztgBtQY9_nuZvRwZD6FHzhuOoe22Wlhpg7mjvZW4UzTqqWJ264OWJb-629q7nwmRyidoM3-5urf7FJhVGbaUDznwFAE0xOY3VC-OyuB1sO1x3uljsHUQ1SuNMxLtRRi-Kvjhx2chkwWu7ZNTcE-CiHo8"/>
                    </div>
                    <h4 class="text-lg font-bold">Lê Văn Hùng</h4>
                    <p class="text-primary text-sm font-medium">Trưởng phòng Phát triển</p>
                </div>
                <div class="group text-center">
                    <div class="relative mb-4 aspect-square rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img class="h-full w-full object-cover transition-transform group-hover:scale-105" alt="Ảnh đại diện Phạm Thu Hà, Thiết kế Sản phẩm" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAXqZzdEngtq0GlM1H1_xCJVcNrvL2EYPAK57Y1yXL0B6iNfwCYQwQ75hbX9l1mu77zkzGean5YKGpTRWxfnRRtk_FeRRusIbqyS9-fNgIxtqQM7x27Ob3fNs-pmCJvD4_QbrDHLpxb01itAnLssCynaW0qanF7MuWfAN2OSgROvyoGE5GDsJI6xKggzhgoRqKIvMv8qgJSde_Bqfe_X0V0c9XuibiVTrvpy8qYNmU0tlKCsZhvwgWKDKYba6BOD7HI1MqtDrklL0U"/>
                    </div>
                    <h4 class="text-lg font-bold">Phạm Thu Hà</h4>
                    <p class="text-primary text-sm font-medium">Thiết kế Sản phẩm</p>
                </div>
            </div>
        </div>
    </section>
@endsection
