@extends('layouts.app.app-layout')

@section('content')
    {{-- Hero Section --}}
    <div
        class="rounded-2xl bg-white dark:bg-slate-800 p-8 md:p-12 shadow-sm border border-slate-100 dark:border-slate-700 relative overflow-hidden group">
        {{-- Decorative Background Blob --}}
        <div
            class="absolute -right-20 -top-20 size-96 bg-primary/10 rounded-full blur-3xl group-hover:bg-primary/20 transition-all duration-700">
        </div>
        <div
            class="absolute -left-20 -bottom-20 size-80 bg-blue-400/10 rounded-full blur-3xl group-hover:bg-blue-400/20 transition-all duration-700">
        </div>
        <div class="relative z-10 flex flex-col md:flex-row gap-10 items-center">
            <div class="flex-1 flex flex-col gap-6 text-left">
                <div
                    class="inline-flex w-fit items-center gap-2 rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
                    <span class="material-symbols-outlined !text-sm">rocket_launch</span>
                    <span>Phiên bản mới 1.0.0</span>
                </div>
                <h1
                    class="text-slate-900 dark:text-white text-4xl md:text-5xl lg:text-6xl font-black leading-tight tracking-[-0.03em]">
                    Tạo thiệp và các mẫu thiết kế<br />
                    <span class="text-primary">nhanh chóng.</span>
                </h1>
                <p class="text-slate-500 dark:text-slate-300 text-lg md:text-xl font-normal leading-relaxed max-w-xl">
                    Truy cập kho lưu trữ các mẫu thiệp và thiết kế đa dạng, được tuyển chọn bởi các chuyên gia.
                </p>
                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="{{ route('app.gifts.templates') }}"
                        class="flex h-12 items-center justify-center rounded-lg bg-primary px-8 text-base font-bold text-white shadow-lg shadow-primary/25 hover:bg-primary/90 hover:scale-105 transition-all">
                        Khám phá ngay
                    </a>
                </div>
            </div>
            {{-- Hero Image --}}
            <div class="w-full md:w-1/2 lg:w-5/12 aspect-[4/3] rounded-xl overflow-hidden shadow-2xl relative">
                <div class="absolute inset-0 bg-gradient-to-tr from-slate-900/10 to-transparent z-10"></div>
                <div class="w-full h-full bg-slate-200 dark:bg-slate-700 bg-cover bg-center"
                    data-alt="Coding on a laptop screen"
                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCnwzG0S_MGo3PjxmxsrU6Mv-yLIuLnBpm7YnQ9iN-EOxWQq80Llzne-FqH2Mh88RvI1j1zi7g8pVAEVU0HVw4YJiZfWAcnGaWMAQ6DlLp_YLGoNsTgvtOK_OPF2k1bFZnJNVowjLIyER8rHM3jZTPZ-c4BnWvS1_VRg0INu-kU0wR9JVsoqaT0w48VRSl2B31CyK4lej5GN14YVB-dGSJyvca6ByUelEgzjYIq0qK7zLytmsC4zbVAC_zi5hPjTe6FdgSISyHnLQ8");'>
                </div>
            </div>
        </div>
    </div>

    {{-- Featured Section --}}
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <h2 class="text-slate-900 dark:text-white text-2xl md:text-3xl font-bold leading-tight tracking-tight">Featured
                Items</h2>
            <a class="text-primary font-bold text-sm hover:underline flex items-center gap-1" href="#">
                View all <span class="material-symbols-outlined !text-sm">arrow_forward</span>
            </a>
        </div>
        {{-- Featured Large Card --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 group cursor-pointer">
                <div
                    class="flex flex-col md:flex-row h-full rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-all overflow-hidden">
                    <div class="w-full md:w-1/2 bg-slate-200 dark:bg-slate-700 aspect-video md:aspect-auto bg-cover bg-center group-hover:scale-105 transition-transform duration-500"
                        data-alt="Modern dashboard analytics interface"
                        style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBuHdi2SH2TOMbQcz1u8TaNMyYgKFBKYn84BWg9l1QbiQDz0MO2G8Aa9cg8H9MoMO5utKKUaSl9RelUyUvD-luZLt3uy4gEEyK0XfDaaFORcibpU2SwL3eBOAt2ztOKRsLvoYO0RblfDXk23kf1sYn7eoohJlLSfTFHN7vcxzBRozreh_u4Bwzsdl3x-YIqIbnn7QCs8EDe3IuY43IhDZFGekxHaDdZsWUr4umqTFml5MzYngdp9UBtVbAeJD3On_gxDFTebI33JYc");'>
                    </div>
                    <div class="flex flex-col p-6 justify-between md:w-1/2 relative bg-white dark:bg-slate-800 z-10">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <span
                                    class="px-2 py-1 rounded bg-blue-50 dark:bg-blue-900/30 text-primary text-xs font-bold uppercase tracking-wide">Best
                                    Seller</span>
                                <div class="flex items-center gap-1 text-amber-500">
                                    <span class="material-symbols-outlined !text-sm fill-current">star</span>
                                    <span class="text-slate-700 dark:text-slate-300 text-sm font-semibold">4.9</span>
                                </div>
                            </div>
                            <h3
                                class="text-slate-900 dark:text-white text-xl font-bold group-hover:text-primary transition-colors">
                                E-Commerce Flutter App Complete Solution</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm line-clamp-3">A full-featured mobile
                                application template for iOS and Android built with Flutter. Includes backend, admin panel,
                                and payment gateway integration.</p>
                        </div>
                        <div class="flex items-end justify-between mt-6">
                            <div class="flex flex-col">
                                <span class="text-slate-400 text-xs line-through">$89.00</span>
                                <span class="text-slate-900 dark:text-white text-2xl font-bold">$49.00</span>
                            </div>
                            <button
                                class="size-10 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white hover:bg-primary hover:text-white flex items-center justify-center transition-colors">
                                <span class="material-symbols-outlined">add_shopping_cart</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Featured Vertical Card --}}
            <div class="group cursor-pointer">
                <div
                    class="flex flex-col h-full rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-all overflow-hidden">
                    <div class="w-full aspect-[16/10] bg-slate-200 dark:bg-slate-700 bg-cover bg-center relative overflow-hidden"
                        data-alt="Code editor showing complex react component"
                        style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDQSGwbAKrqM09HYfyTDSuqReeen9k1ymVLlOz9kmDh3P2_Bl8zkXAn6pvjZ1mprDUGrHcKlnc3SMkX2tYGX23DkoHlVXp5RqYWWkEfzkP5rHkf3BcGTkdzSEwaFX7HeM47PERNRUY7NVTTp9N5IH3WBNX7yxme1NYCOejGgXSPwS8566wU2_beFARFz6yTQLP5eA4eref8RlL5rxJc5A0qQMhcnm7O2qm4ibrBRSD6hfpo2YhJWzTypQLBZU_uDwpU7ef-Bnv005o");'>
                        <div
                            class="absolute top-3 right-3 bg-white/90 dark:bg-black/80 backdrop-blur rounded-full px-2 py-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-amber-500 !text-xs">star</span>
                            <span class="text-xs font-bold">4.7</span>
                        </div>
                    </div>
                    <div class="flex flex-col p-5 grow">
                        <div class="mb-1 text-slate-500 dark:text-slate-400 text-xs font-medium uppercase">Web Scripts</div>
                        <h3
                            class="text-slate-900 dark:text-white text-lg font-bold mb-2 group-hover:text-primary transition-colors">
                            Social Network SaaS Platform</h3>
                        <div
                            class="mt-auto flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-700/50">
                            <span class="text-slate-900 dark:text-white text-lg font-bold">$129.00</span>
                            <div class="flex gap-2">
                                <button class="text-slate-400 hover:text-red-500 transition-colors">
                                    <span class="material-symbols-outlined">favorite</span>
                                </button>
                                <button class="text-primary hover:text-primary/80 transition-colors">
                                    <span class="material-symbols-outlined">shopping_cart</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Regular Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 pt-6">
            {{-- Card 1 --}}
            <div class="flex flex-col gap-3 group cursor-pointer">
                <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden bg-slate-200 dark:bg-slate-700">
                    <div class="w-full h-full bg-cover bg-center hover:scale-105 transition-transform duration-500"
                        data-alt="Abstract code lines"
                        style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBpZ2JUMlxYI_6RuBc14i-0sgyIVeZRYhvcpe3W-JaLVv-mGPM5rJrcxEzhIhQwZsT7kKQTvFfXCidNpYsaMuqhjVVcBJ098Tq0VkQxEQOP8UU68wtmFBhNw8KPENvc7dsr4l4JuYV3BHoHHvHmUhvA1s2p-Ds_LTJklLXfbEM26lxrFRS8uelhEYr6wBxqSFGEiLOhrlkbi7fgfCmpob7G-nqBOdi0us8cWLok83qUgVHhlF2Td5APjrD1bSoGPLYGZHKIYqh2Y3U");'>
                    </div>
                    <div class="absolute top-2 right-2">
                        <button
                            class="bg-white/90 dark:bg-slate-900/90 p-1.5 rounded-full text-slate-400 hover:text-red-500 transition-colors">
                            <span class="material-symbols-outlined !text-lg block">favorite</span>
                        </button>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3
                                class="text-slate-900 dark:text-white text-base font-bold leading-tight group-hover:text-primary transition-colors">
                                RPG Game Engine Unity</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Game Templates</p>
                        </div>
                        <div
                            class="bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-xs font-bold text-slate-600 dark:text-slate-300">
                            Unity</div>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                        <p class="text-slate-900 dark:text-white font-bold">$120.00</p>
                        <div class="flex items-center gap-1 text-slate-400">
                            <span class="material-symbols-outlined !text-sm">download</span>
                            <span class="text-xs">2.4k</span>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Card 2 --}}
            <div class="flex flex-col gap-3 group cursor-pointer">
                <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden bg-slate-200 dark:bg-slate-700">
                    <div class="w-full h-full bg-cover bg-center hover:scale-105 transition-transform duration-500"
                        data-alt="Point of sale tablet system"
                        style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuB3Dc6K3SuthdrMCX_hVclcKl368QmEijuNLOQhUHfr7BmGYO8E2iN7snts6dyo4_U4cbozqK8rFJDSCQge_RUxHY-gOSrUsUDTc_zAk7aAiOXUrcQrV9o596gONOOQb-SeuXktw9RUWWAi2urQ10U66mYsjK5QDFOQUnF55SqvUTtAwYPjqxuI-kwUZSK2XxlfHmjUcacm03l7q1a5nrZRjMAwl6aI896yLIeCELKZVG60q6Q4Om2QPNu3p_NSlf131pJq0B2cifI");'>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3
                                class="text-slate-900 dark:text-white text-base font-bold leading-tight group-hover:text-primary transition-colors">
                                Cloud POS System</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Web Applications</p>
                        </div>
                        <div
                            class="bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-xs font-bold text-slate-600 dark:text-slate-300">
                            PHP</div>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                        <p class="text-slate-900 dark:text-white font-bold">$150.00</p>
                        <div class="flex items-center gap-1 text-slate-400">
                            <span class="material-symbols-outlined !text-sm">download</span>
                            <span class="text-xs">856</span>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Card 3 --}}
            <div class="flex flex-col gap-3 group cursor-pointer">
                <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden bg-slate-200 dark:bg-slate-700">
                    <div class="w-full h-full bg-cover bg-center hover:scale-105 transition-transform duration-500"
                        data-alt="Crypto currency mobile app"
                        style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCTj0rhDQZGCR1ec4uqaYjGnjWkpiUXCbP0NpCjB5nPyAFbyWk_u0k6XHQidyo9L7MKOeoHJzJtKxGdXZfh3eK5kL-0Dx4NDgJ9xgAp2D2Re1CB0TauroErLanyYSfLatMML_QpKUckyJktrJIQqXAgsUTFkfTAz6vclO8jAYHNhX5cmuL6KcY6pb2gXUQNiAQ6bGARje4l4_hjcurtcHyPDBjKmmoH6aa27zl-6HPrTJxwAspAdvqj2yuIxod_GuUAZ0TtQAKQfMk");'>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3
                                class="text-slate-900 dark:text-white text-base font-bold leading-tight group-hover:text-primary transition-colors">
                                Crypto Exchange UI Kit</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">UI Design</p>
                        </div>
                        <div
                            class="bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-xs font-bold text-slate-600 dark:text-slate-300">
                            Figma</div>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                        <p class="text-slate-900 dark:text-white font-bold">$45.00</p>
                        <div class="flex items-center gap-1 text-slate-400">
                            <span class="material-symbols-outlined !text-sm">download</span>
                            <span class="text-xs">4.1k</span>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Card 4 --}}
            <div class="flex flex-col gap-3 group cursor-pointer">
                <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden bg-slate-200 dark:bg-slate-700">
                    <div class="w-full h-full bg-cover bg-center hover:scale-105 transition-transform duration-500"
                        data-alt="Matrix style digital rain code"
                        style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBlaT_XopuJiyBX_Hak_QXKXIIIXAmxIrU4ZwqBcWnkh91XSWd2qSN2uvoOIxya5vHxiic3Rl7qFhOpsbNFjmaeYSl-W-SDEljivKBi-DTS4V2G7Djq3ccWvqt2zT52Lwzndx8UGWQElEMk55F-Sl5ergIvSunUIsLBnxZkedweUoZ4ZAk0hVV7WRvm-Wuuhm56TpqhAjLUWRJa_dyBB5PDXe7C0cM6dYMSdVpjKvF-tRhmFSfZfO27ML6w4O_WBBIQoADeWzLNyZg");'>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3
                                class="text-slate-900 dark:text-white text-base font-bold leading-tight group-hover:text-primary transition-colors">
                                AI Chatbot Python Script</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Scripts</p>
                        </div>
                        <div
                            class="bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-xs font-bold text-slate-600 dark:text-slate-300">
                            Python</div>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                        <p class="text-slate-900 dark:text-white font-bold">$29.00</p>
                        <div class="flex items-center gap-1 text-slate-400">
                            <span class="material-symbols-outlined !text-sm">download</span>
                            <span class="text-xs">1.2k</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Browse by Category --}}
    <div class="w-full py-8">
        <h2 class="text-slate-900 dark:text-white text-2xl font-bold mb-6">Browse by Category</h2>
        <div class="flex flex-wrap gap-4">
            <a class="flex items-center gap-3 px-6 py-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 hover:border-primary dark:hover:border-primary shadow-sm hover:shadow-md transition-all group min-w-[160px]"
                href="#">
                <div
                    class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">smartphone</span>
                </div>
                <span class="font-bold text-slate-700 dark:text-slate-200">Mobile Apps</span>
            </a>
            <a class="flex items-center gap-3 px-6 py-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 hover:border-primary dark:hover:border-primary shadow-sm hover:shadow-md transition-all group min-w-[160px]"
                href="#">
                <div
                    class="size-10 rounded-full bg-purple-50 dark:bg-purple-900/20 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">globe</span>
                </div>
                <span class="font-bold text-slate-700 dark:text-slate-200">Web Scripts</span>
            </a>
            <a class="flex items-center gap-3 px-6 py-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 hover:border-primary dark:hover:border-primary shadow-sm hover:shadow-md transition-all group min-w-[160px]"
                href="#">
                <div
                    class="size-10 rounded-full bg-green-50 dark:bg-green-900/20 text-green-600 flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">sports_esports</span>
                </div>
                <span class="font-bold text-slate-700 dark:text-slate-200">Games</span>
            </a>
            <a class="flex items-center gap-3 px-6 py-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 hover:border-primary dark:hover:border-primary shadow-sm hover:shadow-md transition-all group min-w-[160px]"
                href="#">
                <div
                    class="size-10 rounded-full bg-orange-50 dark:bg-orange-900/20 text-orange-600 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">palette</span>
                </div>
                <span class="font-bold text-slate-700 dark:text-slate-200">UI Kits</span>
            </a>
            <a class="flex items-center gap-3 px-6 py-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 hover:border-primary dark:hover:border-primary shadow-sm hover:shadow-md transition-all group min-w-[160px]"
                href="#">
                <div
                    class="size-10 rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">extension</span>
                </div>
                <span class="font-bold text-slate-700 dark:text-slate-200">Plugins</span>
            </a>
        </div>
    </div>
@endsection