@extends('layouts.app.app-layout')

@section('content')
    {{-- Featured Post --}}
    <section class="mb-12">
        <div class="group relative overflow-hidden rounded-xl bg-white dark:bg-slate-900 shadow-sm transition-all hover:shadow-md border border-slate-200 dark:border-slate-800">
            <div class="flex flex-col lg:flex-row">
                <div class="h-64 w-full lg:h-auto lg:w-3/5">
                    <div class="h-full w-full bg-slate-200 dark:bg-slate-800 bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAJPAL_47MQuAEkO0sVm9Bj7X3PiSwIIUEGpQDFlnHbqonBj60rWAi2WRw4cvZGTbJ0fC5A_qPwbemWGDVpZxZ2BuJN_O_9AbgA_KAHyKscYxGRF6LbY1Y1i0PVZ4vjr-VHhhTJVQz0551NemOm7t8pbTjieOx2Kar7FNyN8aeErUVsDx639qOkfqIrNbIuRis0AzzUL8HmtOMt92MHOGM_Ob41o6_ZQOfaR609_Sgs17ixcXaaeBao4nxVRRxIHIM3Aihi6pSyzJY')">
                    </div>
                </div>
                <div class="flex flex-1 flex-col justify-center p-8 lg:p-12">
                    <div class="mb-4 inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-primary">
                        Bài viết nổi bật
                    </div>
                    <h1 class="mb-4 text-3xl font-extrabold leading-tight text-slate-900 dark:text-white lg:text-4xl">
                        Làm chủ React Server Components: Tối ưu hiệu suất chuyên sâu
                    </h1>
                    <p class="mb-6 text-lg text-slate-600 dark:text-slate-400">
                        Tìm hiểu cách tối ưu hóa hiệu suất ứng dụng với các mẫu React mới nhất và các phương pháp tốt nhất cho server-side rendering trong năm 2024.
                    </p>
                    <div class="mb-8 flex items-center gap-4 text-sm text-slate-500">
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-base">calendar_today</span> 15/01/2024</span>
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-base">schedule</span> 12 phút đọc</span>
                    </div>
                    <a class="inline-flex w-fit items-center justify-center gap-2 rounded-lg bg-primary px-6 py-3 text-sm font-bold text-white hover:bg-primary/90 transition-all" href="#">
                        Đọc toàn bộ bài viết <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Filter Tabs --}}
    <section class="mb-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Bài viết gần đây</h2>
            <div class="flex flex-wrap gap-2">
                <button class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Tất cả</button>
                <button class="rounded-lg bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold border border-slate-200 dark:border-slate-700 hover:border-primary/50 transition-colors">Hướng dẫn</button>
                <button class="rounded-lg bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold border border-slate-200 dark:border-slate-700 hover:border-primary/50 transition-colors">Tin tức</button>
                <button class="rounded-lg bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold border border-slate-200 dark:border-slate-700 hover:border-primary/50 transition-colors">Mẹo lập trình</button>
            </div>
        </div>
    </section>

    {{-- Blog Post Grid --}}
    <section class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
        {{-- Post Card 1 --}}
        <div class="group flex flex-col rounded-xl bg-white dark:bg-slate-900 overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all">
            <div class="aspect-video w-full overflow-hidden bg-slate-200 dark:bg-slate-800">
                <div class="h-full w-full bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAY5io8DmA7-TOaq8_ESrJ2Z7HfjoSBMkV0Pj9dVZpxghHAuZIbb89wXSjS14nZatkrWNTchvMO2CJ-vU2oPXIOQw-I6EpfxYSMUQ_3takgxyEPEWb2g6Ue11hWsoCrKTTIyYUu53UqO4hIxpnY0EykJBPfW_NgPqZNSbp8Xh2VWZBv_DdXH5tjVA7FELdjh_6qgkADIg2dX6HvovYO2MxQBVyrbNJfY4nlUlzLxgAdbj2WOwsojVLrrijkRjYX_TOoNJt_6MApeZk')"></div>
            </div>
            <div class="flex flex-1 flex-col p-6">
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-widest text-primary">Hướng dẫn</span>
                    <span class="text-xs text-slate-500">12/01/2024</span>
                </div>
                <h3 class="mb-3 text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                    Xây dựng ứng dụng SaaS với Next.js 14 và Tailwind
                </h3>
                <p class="mb-6 line-clamp-2 text-sm text-slate-600 dark:text-slate-400">
                    Hướng dẫn toàn diện để xây dựng ứng dụng phần mềm dịch vụ hiện đại sử dụng bộ công nghệ web mới nhất.
                </p>
                <div class="mt-auto flex items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-4">
                    <span class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">timer</span> 8 phút đọc
                    </span>
                    <a class="text-sm font-bold text-primary hover:underline" href="#">Đọc thêm</a>
                </div>
            </div>
        </div>
        {{-- Post Card 2 --}}
        <div class="group flex flex-col rounded-xl bg-white dark:bg-slate-900 overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all">
            <div class="aspect-video w-full overflow-hidden bg-slate-200 dark:bg-slate-800">
                <div class="h-full w-full bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBCs0lImNbvHJJiOgjbgbatAn8WaNuled5UNN3QHrbmRdjvmzLWj1N8cwhhoulsH5uh73kFfY06ETzDIdEeIuPCC2CoHzmsB-bW7wXpbCHvSiZKXqckgu_dinlxwsB7EuYVIZ7gg_3gb50m1DCK7_T1Hg8cGZzmWVrY-DFKNn6SLRdhNlPUoGM2iiiTwKeHI9chIqUM4LLKy2YOXjGPpMsYH7akJE0KTBGUO3rwRVifmDoQgCz8rubJ2Me7J_xuBYfmiaWpPfj1H8I')"></div>
            </div>
            <div class="flex flex-1 flex-col p-6">
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-widest text-primary">Tin tức</span>
                    <span class="text-xs text-slate-500">10/01/2024</span>
                </div>
                <h3 class="mb-3 text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                    Top 10 Tiện ích VS Code tăng năng suất năm 2024
                </h3>
                <p class="mb-6 line-clamp-2 text-sm text-slate-600 dark:text-slate-400">
                    Tăng tốc quy trình phát triển của bạn với các tiện ích mở rộng thiết yếu cho VS Code.
                </p>
                <div class="mt-auto flex items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-4">
                    <span class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">timer</span> 5 phút đọc
                    </span>
                    <a class="text-sm font-bold text-primary hover:underline" href="#">Đọc thêm</a>
                </div>
            </div>
        </div>
        {{-- Post Card 3 --}}
        <div class="group flex flex-col rounded-xl bg-white dark:bg-slate-900 overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all">
            <div class="aspect-video w-full overflow-hidden bg-slate-200 dark:bg-slate-800">
                <div class="h-full w-full bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDa5TYCC3yRcJVDuUV3vkR1M-_1ZAAP0LnBwhIv6EkiW3pZFW7dgPHgHwVKFwl-JuXMZatyX0OeGNiO3GsVZlkxFCjfF8JK1v93hcF3vzynMByveOTXNWDEP4TMuds4Gi0uiWUj50pqspKNZAWHoOwSKOr-V1Yi1PHRNqiiUWVbvCxCvbDu3NhntQEzi3RSofxBUzcAQ8QaJhK1NB6QlYjUDJH4tWBrI5ZX_Ns4in94S9rSqSFbasZceqcaubbyex5hN0IdfbXdyh0')"></div>
            </div>
            <div class="flex flex-1 flex-col p-6">
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-widest text-primary">Mẹo lập trình</span>
                    <span class="text-xs text-slate-500">08/01/2024</span>
                </div>
                <h3 class="mb-3 text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                    Hiểu về TypeScript Generics một cách dễ dàng
                </h3>
                <p class="mb-6 line-clamp-2 text-sm text-slate-600 dark:text-slate-400">
                    Giải thích rõ ràng và ngắn gọn về một trong những tính năng mạnh mẽ nhất trong TypeScript.
                </p>
                <div class="mt-auto flex items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-4">
                    <span class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">timer</span> 10 phút đọc
                    </span>
                    <a class="text-sm font-bold text-primary hover:underline" href="#">Đọc thêm</a>
                </div>
            </div>
        </div>
        {{-- Post Card 4 --}}
        <div class="group flex flex-col rounded-xl bg-white dark:bg-slate-900 overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all">
            <div class="aspect-video w-full overflow-hidden bg-slate-200 dark:bg-slate-800">
                <div class="h-full w-full bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDBBTnq_G-bzoPiKAaKvIeiXPT7st7sUiajaxIl47X_Dl-FQfvVZzB2F8jBfhafZb6EX6fDcOGyNnuPKfsJ5k7_ODBw53P3eqiZUiL3C8MZZbjxyHRvAf4gR6wCL205PRG3s1sewNalaccxABt9LkgCeX5U-L6TMpzUcWOzeh7y6deMRx0j3Msiux7PiupKAnKFsrdb3nDxm4AK5lLZGaKVjxY8pB5TNpZ2FtYB6hQifyI4qUjLbS9_rxVxgEQYcZP4aCsKsoGWFpY')"></div>
            </div>
            <div class="flex flex-1 flex-col p-6">
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-widest text-primary">Hướng dẫn</span>
                    <span class="text-xs text-slate-500">05/01/2024</span>
                </div>
                <h3 class="mb-3 text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                    Triển khai lên Vercel: Hướng dẫn CI/CD hoàn chỉnh
                </h3>
                <p class="mb-6 line-clamp-2 text-sm text-slate-600 dark:text-slate-400">
                    Làm chủ quy trình triển khai và tự động hóa luồng công việc của bạn với Vercel và GitHub.
                </p>
                <div class="mt-auto flex items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-4">
                    <span class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">timer</span> 6 phút đọc
                    </span>
                    <a class="text-sm font-bold text-primary hover:underline" href="#">Đọc thêm</a>
                </div>
            </div>
        </div>
        {{-- Post Card 5 --}}
        <div class="group flex flex-col rounded-xl bg-white dark:bg-slate-900 overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all">
            <div class="aspect-video w-full overflow-hidden bg-slate-200 dark:bg-slate-800">
                <div class="h-full w-full bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAaomW8yh9miA3byQi-sKU9LexY_iYBX-cznkKIC3pBIF02-MI6x2FNMfdfQKdyKbx9h8lCfRY0dRFoIfrEbdJBcduUXsRDdgZf5EI6Pt3wBYyF_DkfNjUrCaHR4PP8raslDh3UanNKdQLEJazmfPbEb5HUe8gPQ19wM40sGt2DiIslArjQ2RMFn2oBYexidP9G641ahSRCpwG1J5wKmuZHKjgo1mrT9aXAKet3C-xOROxPtAf_P7X4gwpvMx2QXK6Jtat0bwkucpU')"></div>
            </div>
            <div class="flex flex-1 flex-col p-6">
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-widest text-primary">Tin tức</span>
                    <span class="text-xs text-slate-500">03/01/2024</span>
                </div>
                <h3 class="mb-3 text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                    Quản lý State trong ứng dụng React hiện đại
                </h3>
                <p class="mb-6 line-clamp-2 text-sm text-slate-600 dark:text-slate-400">
                    So sánh Redux, Zustand và React Context cho dự án quy mô lớn tiếp theo của bạn.
                </p>
                <div class="mt-auto flex items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-4">
                    <span class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">timer</span> 12 phút đọc
                    </span>
                    <a class="text-sm font-bold text-primary hover:underline" href="#">Đọc thêm</a>
                </div>
            </div>
        </div>
        {{-- Post Card 6 --}}
        <div class="group flex flex-col rounded-xl bg-white dark:bg-slate-900 overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all">
            <div class="aspect-video w-full overflow-hidden bg-slate-200 dark:bg-slate-800">
                <div class="h-full w-full bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBy9WmYSuzj3lfEXaWdMXj0bKO-BuwDco0HtrdbCZidWJCoX3cI7F_dGlngOnWm0a7T75WSbt9wL-Wc-dffm7o6sSeshs3-KMkcqe7PihtMl1sdKKMnrprjLdxrJc3yVc_efUBXuThFyrrKSwxvXk5yaF7wuZUtgefezRkfQhbq3dS3jgPzm8RSxUNA8gm89NbydAgJ46rjvh6ApDBaHy7XCI2OxtkWuklzj_p3B1qBGGY1ZguW9NMs1-2v7Cjh0ydtviSHqfxXYpo')"></div>
            </div>
            <div class="flex flex-1 flex-col p-6">
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-widest text-primary">Mẹo lập trình</span>
                    <span class="text-xs text-slate-500">01/01/2024</span>
                </div>
                <h3 class="mb-3 text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                    CSS Grid vs Flexbox: Khi nào dùng cái nào?
                </h3>
                <p class="mb-6 line-clamp-2 text-sm text-slate-600 dark:text-slate-400">
                    Hết nhầm lẫn và nắm vững nguyên tắc bố cục trong CSS hiện đại.
                </p>
                <div class="mt-auto flex items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-4">
                    <span class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">timer</span> 7 phút đọc
                    </span>
                    <a class="text-sm font-bold text-primary hover:underline" href="#">Đọc thêm</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Newsletter CTA --}}
    {{-- <section class="mt-8 rounded-2xl bg-slate-900 p-8 text-center sm:p-16">
        <h2 class="mb-4 text-3xl font-extrabold text-white">Nhận cập nhật mới nhất</h2>
        <p class="mx-auto mb-8 max-w-xl text-slate-400">Tham gia cùng 15,000+ người đọc nhận bản tin hàng tuần về công nghệ và phát triển web hiện đại.</p>
        <form class="mx-auto flex max-w-md flex-col gap-3 sm:flex-row">
            <input class="h-12 flex-1 rounded-lg border-none bg-white/10 px-4 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-primary/50" placeholder="Nhập email của bạn" type="email"/>
            <button class="h-12 rounded-lg bg-primary px-8 font-bold text-white hover:bg-primary/90 transition-colors" type="submit">Đăng ký</button>
        </form>
    </section> --}}
@endsection
