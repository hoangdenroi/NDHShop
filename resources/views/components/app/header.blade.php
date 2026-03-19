{{-- Component Header - Thanh điều hướng chính --}}
<div x-data="{ mobileMenuOpen: false }" class="relative z-[60]">
    <header
        class="fixed top-0 left-0 w-full z-50 flex items-center justify-between whitespace-nowrap border-b border-solid border-slate-200 bg-white/90 backdrop-blur-md px-3 sm:px-6 py-3 sm:py-4 lg:px-20 dark:border-slate-800 dark:bg-background-dark/90">
        <div class="flex items-center gap-8 w-full max-w-[1400px] mx-auto justify-between">
            <div class="flex items-center gap-2 sm:gap-4 lg:gap-8">
                <a href="/" class="flex items-center gap-2 sm:gap-3 text-slate-900 dark:text-white">
                    <div class="size-8 text-primary">
                        {{-- <span class="material-symbols-outlined !text-[32px]">terminal</span> --}}
                        <img src="{{ asset('NDHShop.jpg') }}" alt="Logo" class="h-full w-full object-cover"
                            style="border-radius: 5px;">
                    </div>
                    <h2 class="text-slate-900 dark:text-white text-xl font-bold leading-tight tracking-[-0.015em]">
                        NDHShop</h2>
                </a>
                {{-- Desktop Nav --}}
                <div class="hidden lg:flex items-center gap-6 pl-4">
                    <a class="text-sm font-medium transition-colors {{ request()->is('/') ? 'text-primary dark:text-primary font-bold underline decoration-2 underline-offset-4' : 'text-slate-900 dark:text-slate-200 hover:text-primary' }}"
                        href="/">Trang chủ</a>

                    @php
                        // Fetch Categories with Cache
                        $menuCategories = \Illuminate\Support\Facades\Cache::remember('categories_menu_null', 3600, function () {
                            return \App\Models\Category::where('is_deleted', false)
                                ->where('is_active', true)
                                ->whereNull('parent_id')
                                ->with([
                                    'children' => function ($q) {
                                        $q->where('is_deleted', false)
                                            ->where('is_active', true)
                                            ->orderBy('id', 'asc');
                                    }
                                ])
                                ->orderBy('id', 'asc')
                                ->get();
                        });

                        // Phân rã theo position
                        // pos1: Dịch vụ chính (mặc định nếu null)
                        $pos1 = $menuCategories->filter(fn($c) => ($c->metadata['position'] ?? '1') == '1');
                        // pos2: Sale & Khuyến mãi
                        $pos2 = $menuCategories->filter(fn($c) => ($c->metadata['position'] ?? '') == '2');
                        // pos3: Dịch vụ khác
                        $pos3 = $menuCategories->filter(fn($c) => ($c->metadata['position'] ?? '') == '3');
                    @endphp

                    {{-- Dropdown Sản phẩm (Mega menu) - Desktop --}}
                    @php
                        $isProductActive = request()->is('games*') || request()->is('source-code*') || request()->is('san-pham*');
                    @endphp
                    <div class="relative group py-4">
                        <a class="text-sm font-medium transition-colors cursor-pointer flex items-center gap-1 {{ $isProductActive ? 'text-primary dark:text-primary font-bold underline decoration-2 underline-offset-4' : 'text-slate-900 dark:text-slate-200 group-hover:text-primary' }}"
                            href="#">
                            Sản phẩm
                        </a>

                        {{-- Mega Menu Content --}}
                        <div
                            class="absolute top-[100%] left-1/2 -translate-x-1/2 w-[750px] opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-300 bg-white dark:bg-[#111c30] rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.3)] border border-slate-200 dark:border-slate-800 p-6 z-[60]">
                            <div class="grid grid-cols-3 gap-8">
                                {{-- POSITION 1: CỘT ĐẦU --}}
                                <div class="relative">
                                    <ul class="space-y-1">
                                        <h4 class="text-primary text-[11px] font-bold uppercase mb-4 tracking-wider">
                                            Sản phẩm chính
                                        </h4>
                                        @foreach($pos1 as $item)
                                            <li x-data="{ openSub: false }" @mouseenter="openSub = true"
                                                @mouseleave="openSub = false" class="relative">
                                                @php
                                                    $isActive = request()->is($item->slug) || request()->is($item->slug . '/*');
                                                @endphp
                                                <a href="/{{ $item->slug }}"
                                                    :class="openSub ? 'bg-slate-50 dark:bg-slate-800 text-primary dark:text-primary' : 'hover:bg-slate-50 dark:hover:bg-slate-800 dark:hover:text-primary {{ $isActive ? 'text-primary dark:text-primary' : 'text-slate-700 dark:text-slate-300' }}'"
                                                    class="flex items-center justify-between px-4 py-2.5 rounded-xl transition-colors {{ $isActive ? 'underline decoration-2 underline-offset-4' : '' }}">
                                                    <span
                                                        class="text-[15px] {{ $isActive ? 'font-bold' : 'font-medium' }}">{{ $item->name }}</span>
                                                    @if($item->children->isNotEmpty())
                                                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                                                    @endif
                                                </a>

                                                {{-- Submenu level 2 --}}
                                                @if($item->children->isNotEmpty())
                                                    <div x-show="openSub" x-cloak
                                                        x-transition:enter="transition ease-out duration-200"
                                                        x-transition:enter-start="opacity-0 -translate-x-2"
                                                        x-transition:enter-end="opacity-100 translate-x-0"
                                                        x-transition:leave="transition ease-in duration-150"
                                                        x-transition:leave-start="opacity-100 translate-x-0"
                                                        x-transition:leave-end="opacity-0 -translate-x-2" style="left: 100%;"
                                                        class="absolute top-0 ml-1 w-[240px] bg-white dark:bg-[#111c30] rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] border border-slate-200 dark:border-slate-800 p-2 z-[61]">
                                                        <ul class="space-y-1">
                                                            @foreach($item->children as $child)
                                                                <li>
                                                                    <a href="/{{ $child->slug }}"
                                                                        class="block text-slate-700 dark:text-slate-300 hover:text-primary dark:hover:bg-slate-800 hover:bg-slate-50 text-[14px] transition-colors font-medium px-4 py-2.5 rounded-lg">
                                                                        {{ $child->name }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                {{-- POSITION 2: CỘT GIỮA --}}
                                <div>
                                    @foreach($pos2 as $item)
                                        <h4
                                            class="text-primary text-[11px] font-bold uppercase mb-4 tracking-wider {{ !$loop->first ? 'mt-6' : '' }}">
                                            {{ $item->name }}
                                        </h4>
                                        @if($item->children->isNotEmpty())
                                            <ul class="space-y-3">
                                                @foreach($item->children as $child)
                                                    <li><a href="/{{ $child->slug }}"
                                                            class="text-slate-700 dark:text-slate-300 hover:text-primary dark:hover:text-white text-sm transition-colors font-medium">{{ $child->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endforeach
                                </div>

                                {{-- POSITION 3: CỘT CUỐI --}}
                                <div>
                                    @foreach($pos3 as $item)
                                        <h4
                                            class="text-primary text-[11px] font-bold uppercase mb-4 tracking-wider {{ !$loop->first ? 'mt-6' : '' }}">
                                            {{ $item->name }}
                                        </h4>
                                        @if($item->children->isNotEmpty())
                                            <ul class="space-y-3">
                                                @foreach($item->children as $child)
                                                    <li><a href="/{{ $child->slug }}"
                                                            class="text-slate-700 dark:text-slate-300 hover:text-primary dark:hover:text-white text-sm transition-colors font-medium">{{ $child->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-center">
                                <a href="{{ route('app.all-product') }}"
                                    class=" text-sm font-medium transition-colors {{ request()->is('apps/all-product*') || request()->is('all-product*') ? 'text-primary dark:text-primary font-bold underline decoration-2 underline-offset-4' : 'text-slate-900 dark:text-slate-200 hover:text-primary' }}">Tất
                                    cả sản phẩm</a>
                            </div>
                        </div>
                    </div>

                    <a class="text-sm font-medium transition-colors {{ request()->is('bai-viet*') || request()->is('blog*') || request()->is('apps/blog*') ? 'text-primary dark:text-primary font-bold underline decoration-2 underline-offset-4' : 'text-slate-900 dark:text-slate-200 hover:text-primary' }}"
                        href="{{ route('app.blog') }}">Bài viết</a>
                    <a class="text-sm font-medium transition-colors {{ request()->is('gioi-thieu*') || request()->is('about*') || request()->is('apps/about*') ? 'text-primary dark:text-primary font-bold underline decoration-2 underline-offset-4' : 'text-slate-900 dark:text-slate-200 hover:text-primary' }}"
                        href="{{ route('app.about') }}">Giới thiệu</a>
                    <a class="text-sm font-medium transition-colors {{ request()->is('lien-he*') || request()->is('contact*') || request()->is('apps/contact*') ? 'text-primary dark:text-primary font-bold underline decoration-2 underline-offset-4' : 'text-slate-900 dark:text-slate-200 hover:text-primary' }}"
                        href="{{ route('app.contact') }}">Liên hệ</a>
                </div>
            </div>
            <div class="flex items-center gap-4 flex-1 justify-end">
                {{-- Thanh tìm kiếm (ẩn trên mobile) --}}
                <label class="hidden lg:flex flex-col min-w-40 h-10 max-w-sm flex-1">
                    <div
                        class="flex w-full flex-1 items-stretch rounded-lg h-full bg-slate-100 dark:bg-slate-800 focus-within:ring-2 focus-within:ring-primary/20 transition-all">
                        <div
                            class="text-slate-500 dark:text-slate-400 flex border-none items-center justify-center pl-4 rounded-l-lg">
                            <span class="material-symbols-outlined">search</span>
                        </div>
                        <input
                            class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-slate-900 dark:text-white focus:outline-0 focus:ring-0 border-none bg-transparent h-full placeholder:text-slate-500 dark:placeholder:text-slate-400 px-4 pl-2 text-sm font-normal leading-normal"
                            placeholder="Tìm kiếm sản phẩm..." value="" />
                    </div>
                </label>
                <div class="flex gap-1 sm:gap-2 items-center">
                    {{-- Nút search với dropdown (chỉ hiện trên mobile/tablet) --}}
                    <div class="relative lg:hidden" x-data="{ searchOpen: false }">
                        <button @click="searchOpen = !searchOpen"
                            class="flex size-10 cursor-pointer items-center justify-center rounded-full text-slate-900 dark:text-white transition-colors"
                            :class="searchOpen ? 'bg-primary text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800'">
                            <span class="material-symbols-outlined">search</span>
                        </button>
                        {{-- Dropdown search --}}
                        <div x-show="searchOpen" @click.outside="searchOpen = false"
                            @keydown.escape.window="searchOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            style="position: fixed; top: 60px; left: 50%; transform: translateX(-50%); width: min(60vw, 500px); min-width: 280px;"
                            class="bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 p-4 z-[200]"
                            x-cloak>
                            {{-- Mũi tên chỉ lên icon (vị trí tính động theo icon) --}}
                            <div x-ref="arrowOuter" x-effect="
                                    if(searchOpen) {
                                        $nextTick(() => {
                                            let btn = $root.querySelector('button');
                                            let btnCenter = btn.getBoundingClientRect().left + btn.offsetWidth / 2;
                                            let dropLeft = $el.parentElement.getBoundingClientRect().left;
                                            $refs.arrowOuter.style.left = (btnCenter - dropLeft - 9) + 'px';
                                            $refs.arrowInner.style.left = (btnCenter - dropLeft - 7) + 'px';
                                        })
                                    }
                                "
                                class="absolute -top-[9px] w-0 h-0 border-l-[9px] border-l-transparent border-r-[9px] border-r-transparent border-b-[9px] border-b-slate-200 dark:border-b-slate-700">
                            </div>
                            <div x-ref="arrowInner"
                                class="absolute -top-[7px] w-0 h-0 border-l-[7px] border-l-transparent border-r-[7px] border-r-transparent border-b-[7px] border-b-white dark:border-b-slate-800">
                            </div>
                            {{-- Ô input --}}
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                                <input x-ref="searchInput"
                                    @keydown.enter="window.location.href='/search?q='+$refs.searchInput.value"
                                    x-init="$watch('searchOpen', value => { if(value) $nextTick(() => $refs.searchInput.focus()) })"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                    placeholder="Tìm kiếm sản phẩm..." type="text" />
                            </div>
                        </div>
                    </div>
                    {{-- Icon thông báo Dropdown --}}
                    <div class="relative" x-data="{ notiOpen: false }">
                        <button @click="notiOpen = !notiOpen"
                            class="flex size-10 cursor-pointer items-center justify-center rounded-full text-slate-900 dark:text-white transition-colors"
                            :class="notiOpen ? 'bg-primary text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800'">
                            <span class="material-symbols-outlined">notifications</span>
                        </button>
                        {{-- Dropdown thông báo --}}
                        <div x-show="notiOpen" @click.outside="notiOpen = false"
                            @keydown.escape.window="notiOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                            class="absolute top-[100%] right-[-88px] sm:right-0 mt-3 w-[calc(100vw-24px)] sm:w-[360px] min-w-[280px] max-w-[360px] bg-white dark:bg-slate-800 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.3)] border border-slate-200 dark:border-slate-700 py-3 z-[200] origin-top-right transform"
                            x-cloak>

                            {{-- Mũi tên --}}
                            <div
                                class="absolute -top-[9px] right-[99px] sm:right-[11px] w-0 h-0 border-l-[9px] border-l-transparent border-r-[9px] border-r-transparent border-b-[9px] border-b-slate-200 dark:border-b-slate-700">
                            </div>
                            <div
                                class="absolute -top-[7px] right-[101px] sm:right-[13px] w-0 h-0 border-l-[7px] border-l-transparent border-r-[7px] border-r-transparent border-b-[7px] border-b-white dark:border-b-slate-800">
                            </div>

                            {{-- Header --}}
                            <div
                                class="px-4 pb-3 border-b border-slate-100 dark:border-slate-700/50 flex items-center justify-between">
                                <h3 class="font-bold text-slate-900 dark:text-white text-[15px]">Thông báo</h3>
                                <a href="#" class="text-xs text-primary font-medium hover:underline">Đã đọc tất cả</a>
                            </div>
                            {{-- Nội dung (Rỗng) --}}
                            <div
                                class="flex flex-col items-center justify-center py-8 px-4 text-slate-500 dark:text-slate-400">
                                <span
                                    class="material-symbols-outlined text-[40px] mb-2 opacity-50">notifications_off</span>
                                <p class="text-sm font-medium">Bạn chưa có thông báo mới nào!</p>
                            </div>
                        </div>
                    </div>

                    {{-- Icon Giỏ hàng Dropdown --}}
                    <div class="relative" x-data="{ cartOpen: false }">
                        <button @click="cartOpen = !cartOpen"
                            class="flex size-10 cursor-pointer items-center justify-center rounded-full text-slate-900 dark:text-white transition-colors"
                            :class="cartOpen ? 'bg-primary text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800'">
                            <span class="material-symbols-outlined">shopping_cart</span>
                        </button>

                        {{-- Dropdown giỏ hàng --}}
                        <div x-show="cartOpen" @click.outside="cartOpen = false"
                            @keydown.escape.window="cartOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                            class="absolute top-[100%] right-[-44px] sm:right-0 mt-3 w-[calc(100vw-24px)] sm:w-[400px] min-w-[280px] max-w-[400px] bg-white dark:bg-slate-800 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.3)] border border-slate-200 dark:border-slate-700 py-3 z-[200] origin-top-right transform"
                            x-cloak>

                            {{-- Mũi tên --}}
                            <div
                                class="absolute -top-[9px] right-[55px] sm:right-[11px] w-0 h-0 border-l-[9px] border-l-transparent border-r-[9px] border-r-transparent border-b-[9px] border-b-slate-200 dark:border-b-slate-700">
                            </div>
                            <div
                                class="absolute -top-[7px] right-[57px] sm:right-[13px] w-0 h-0 border-l-[7px] border-l-transparent border-r-[7px] border-r-transparent border-b-[7px] border-b-white dark:border-b-slate-800">
                            </div>

                            {{-- Header --}}
                            <div
                                class="px-4 pb-3 border-b border-slate-100 dark:border-slate-700/50 flex items-center justify-between">
                                <h3 class="font-bold text-slate-900 dark:text-white text-[15px]">Giỏ hàng của bạn</h3>
                                <span class="bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full">0
                                    mục</span>
                            </div>

                            {{-- Nội dung (Rỗng) --}}
                            <div
                                class="flex flex-col items-center justify-center py-10 px-4 text-slate-500 dark:text-slate-400">
                                <span class="material-symbols-outlined text-[50px] mb-3 opacity-30">shopping_cart</span>
                                <p class="text-[15px] font-medium text-slate-600 dark:text-slate-300 mb-1">Giỏ hàng
                                    trống</p>
                                <p class="text-xs text-center mb-4">Bạn chưa chọn sản phẩm nào để thêm vào giỏ.</p>
                                <button @click="cartOpen = false"
                                    class="bg-primary hover:bg-primary/90 text-white text-sm font-semibold py-2 px-6 rounded-lg transition-colors shadow-sm shadow-primary/20">
                                    Tiếp tục mua sắm
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Desktop: Avatar / Đăng nhập --}}
                    @auth
                        <a href="{{ route('app.profile') }}" class="hidden lg:flex items-center gap-2 cursor-pointer">
                            @if(Auth::user()->avatar_url)
                                <img src="{{ Auth::user()->avatar_url }}" alt="Avatar"
                                    class="rounded-full size-10 object-cover hover:ring-2 hover:ring-primary/30 transition-all">
                            @else
                                <div
                                    class="bg-primary text-white rounded-full size-10 flex items-center justify-center font-bold text-sm hover:ring-2 hover:ring-primary/30 transition-all">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="hidden lg:flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-6 bg-primary hover:bg-primary/90 text-white text-sm font-bold leading-normal tracking-[0.015em] transition-colors shadow-sm shadow-primary/20 ml-2">
                            <span class="truncate">Đăng nhập</span>
                        </a>
                    @endauth

                    {{-- Mobile: Nút hamburger menu (chỉ hiện trên < lg) --}} <button @click="mobileMenuOpen = true"
                        class="lg:hidden flex size-10 cursor-pointer items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-900 dark:text-white transition-colors">
                        <span class="material-symbols-outlined">menu</span>
                        </button>
                </div>
            </div>
        </div>
    </header>

    {{-- Mobile Menu Panel (slide-in full screen, ĐẶT NGOÀI header) --}}
    {{-- Backdrop --}}
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="mobile-menu-backdrop fixed inset-0 z-[9998] bg-black/50 backdrop-blur-sm" @click="mobileMenuOpen = false"
        x-cloak>
    </div>

    {{-- Panel --}}
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="mobile-menu-panel fixed top-0 right-0 z-[9999] h-full w-[70%] md:w-[40%] bg-white dark:bg-slate-900 shadow-2xl flex flex-col"
        x-cloak>

        {{-- Header panel --}}
        <div class="flex items-center justify-between px-6 py-5">
            <h3 class="text-slate-900 dark:text-white text-base font-bold">Menu</h3>
            <button @click="mobileMenuOpen = false"
                class="flex size-8 items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        {{-- Navigation links - đơn giản, chỉ text --}}
        <nav class="flex-1 overflow-y-auto px-6">
            <div class="flex flex-col gap-1">
                <a class="py-2.5 font-semibold text-[15px] transition-colors {{ request()->is('/') ? 'text-primary dark:text-primary underline decoration-2 underline-offset-4' : 'text-slate-900 dark:text-white' }}"
                    href="/">Trang chủ</a>

                {{-- Dropdown Sản phẩm - Mobile --}}
                <div x-data="{ openProductMobile: false }" class="flex flex-col">
                    <button @click="openProductMobile = !openProductMobile"
                        class="flex items-center justify-between py-2.5 font-medium text-[15px] transition-colors w-full text-left"
                        :class="openProductMobile || {{ ($isProductActive ?? false) ? 'true' : 'false' }} ? 'text-primary dark:text-primary' : 'text-slate-700 dark:text-slate-300 hover:text-primary'">
                        Sản phẩm
                        <span class="material-symbols-outlined text-[20px] transition-transform duration-200"
                            :class="openProductMobile ? 'rotate-180' : ''">expand_more</span>
                    </button>

                    <div x-show="openProductMobile" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="flex flex-col pl-4 border-l-2 border-slate-200 dark:border-slate-800 ml-2 mt-1 mb-2">

                        {{-- POSITION 1: CỘT ĐẦU --}}
                        <div class="py-2">
                            @foreach($pos1 as $item)
                                @if($item->children->isNotEmpty())
                                    <div x-data="{ openPos1: false }" class="flex flex-col mb-1">
                                        <button @click="openPos1 = !openPos1"
                                            class="flex items-center justify-between py-2 text-slate-700 dark:text-slate-300 font-medium text-[14px] transition-colors w-full text-left"
                                            :class="openPos1 ? 'text-primary dark:text-primary' : 'hover:text-primary'">
                                            {{ $item->name }}
                                            <span
                                                class="material-symbols-outlined text-[18px] transition-transform duration-200"
                                                :class="openPos1 ? 'rotate-180' : ''">expand_more</span>
                                        </button>
                                        <div x-show="openPos1" x-cloak x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 -translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 -translate-y-2"
                                            class="flex flex-col gap-3 pl-3 py-2 ml-2 mb-2">
                                            @foreach($item->children as $child)
                                                <a href="/{{ $child->slug }}"
                                                    class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white text-sm font-medium transition-colors">{{ $child->name }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <a href="/{{ $item->slug }}"
                                        class="flex items-center py-2 mb-1 text-slate-700 dark:text-slate-300 hover:text-primary dark:hover:text-white text-[14px] font-medium transition-colors">{{ $item->name }}</a>
                                @endif
                            @endforeach
                        </div>

                        {{-- POSITION 2: CỘT GIỮA --}}
                        @foreach($pos2 as $item)
                            <div class="py-2 mt-1 border-t border-slate-200 dark:border-slate-800/60">
                                <h4 class="text-primary text-[11px] font-bold uppercase mb-2 tracking-wider mt-1">
                                    {{ $item->name }}
                                </h4>
                                @if($item->children->isNotEmpty())
                                    <div class="flex flex-col gap-3 pl-2">
                                        @foreach($item->children as $child)
                                            <a href="/{{ $child->slug }}"
                                                class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white text-sm font-medium transition-colors">{{ $child->name }}</a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        {{-- POSITION 3: CỘT CUỐI --}}
                        @foreach($pos3 as $item)
                            <div class="py-2 mt-1 border-t border-slate-200 dark:border-slate-800/60">
                                <h4 class="text-primary text-[11px] font-bold uppercase mb-2 tracking-wider mt-1">
                                    {{ $item->name }}
                                </h4>
                                @if($item->children->isNotEmpty())
                                    <div class="flex flex-col gap-3 pl-2">
                                        @foreach($item->children as $child)
                                            <a href="/{{ $child->slug }}"
                                                class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white text-sm font-medium transition-colors">{{ $child->name }}</a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        {{-- Tất cả dịch vụ --}}
                        <div class="py-3 mt-1 border-t border-slate-200 dark:border-slate-800/60">
                            <a href="#"
                                class="text-slate-900 dark:text-white hover:text-primary text-sm font-semibold transition-colors block text-center">Tất
                                cả sản phẩm</a>
                        </div>
                    </div>
                </div>

                <a class="py-2.5 font-semibold text-[15px] transition-colors {{ request()->is('bai-viet*') || request()->is('blog*') || request()->is('apps/blog*') ? 'text-primary dark:text-primary underline decoration-2 underline-offset-4' : 'text-slate-700 dark:text-slate-300 hover:text-primary' }}"
                    href="{{ route('app.blog') }}">Bài viết</a>
                <a class="py-2.5 font-semibold text-[15px] transition-colors {{ request()->is('gioi-thieu*') || request()->is('about*') || request()->is('apps/about*') ? 'text-primary dark:text-primary underline decoration-2 underline-offset-4' : 'text-slate-700 dark:text-slate-300 hover:text-primary' }}"
                    href="{{ route('app.about') }}">Giới thiệu</a>
                <a class="py-2.5 font-semibold text-[15px] transition-colors {{ request()->is('lien-he*') || request()->is('contact*') || request()->is('apps/contact*') ? 'text-primary dark:text-primary underline decoration-2 underline-offset-4' : 'text-slate-700 dark:text-slate-300 hover:text-primary' }}"
                    href="{{ route('app.contact') }}">Liên hệ</a>
            </div>

            {{-- Thông tin user --}}
            @auth
                <div class="mt-6">
                    <a href="{{ route('app.profile') }}" class="flex items-center gap-3">
                        @if(Auth::user()->avatar_url)
                            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar"
                                class="rounded-full size-9 shrink-0 object-cover">
                        @else
                            <div
                                class="bg-primary text-white rounded-full size-9 flex items-center justify-center font-bold text-xs shrink-0">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-slate-900 dark:text-white text-sm font-bold truncate">{{ Auth::user()->name }}
                            </p>
                        </div>
                    </a>
                </div>
            @endauth
        </nav>

        {{-- Footer panel: Đăng nhập hoặc Đăng xuất (luôn nằm dưới cùng) --}}
        <div class="p-4 border-t border-slate-200 dark:border-slate-700 mt-auto">
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center h-10 rounded-lg border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-bold hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors">
                        Đăng xuất
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="w-full flex items-center justify-center h-10 rounded-lg bg-primary hover:bg-primary/90 text-white text-sm font-bold transition-colors">
                    Đăng nhập
                </a>
            @endauth
        </div>
    </div>
</div>