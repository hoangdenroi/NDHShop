{{-- Component Header - Thanh điều hướng chính --}}
<div x-data="{ mobileMenuOpen: false }" class="relative z-[60]">
    <header class="fixed top-0 left-0 w-full z-50 flex items-center justify-between whitespace-nowrap border-b border-solid border-slate-200 bg-white/90 backdrop-blur-md px-3 sm:px-6 py-3 sm:py-4 lg:px-20 dark:border-slate-800 dark:bg-background-dark/90">
        <div class="flex items-center gap-8 w-full max-w-[1400px] mx-auto justify-between">
            <div class="flex items-center gap-2 sm:gap-4 lg:gap-8">
                <a href="/" class="flex items-center gap-2 sm:gap-3 text-slate-900 dark:text-white">
                    <div class="size-8 text-primary">
                        <span class="material-symbols-outlined !text-[32px]">terminal</span>
                    </div>
                    <h2 class="text-slate-900 dark:text-white text-xl font-bold leading-tight tracking-[-0.015em]">NDHShop</h2>
                </a>
                {{-- Desktop Nav --}}
                <div class="hidden lg:flex items-center gap-6 pl-4">
                    <a class="text-slate-900 dark:text-slate-200 text-sm font-medium hover:text-primary transition-colors" href="/">Home</a>
                    <a class="text-slate-900 dark:text-slate-200 text-sm font-medium hover:text-primary transition-colors" href="#">Source Code</a>
                    <a class="text-slate-900 dark:text-slate-200 text-sm font-medium hover:text-primary transition-colors" href="#">Apps</a>
                    <a class="text-slate-900 dark:text-slate-200 text-sm font-medium hover:text-primary transition-colors" href="#">Games</a>
                    <a class="text-slate-900 dark:text-slate-200 text-sm font-medium hover:text-primary transition-colors" href="#">Blog</a>
                    <a class="text-slate-900 dark:text-slate-200 text-sm font-medium hover:text-primary transition-colors" href="#">Contact</a>
                </div>
            </div>
            <div class="flex items-center gap-4 flex-1 justify-end">
                {{-- Thanh tìm kiếm (ẩn trên mobile) --}}
                <label class="hidden md:flex flex-col min-w-40 h-10 max-w-sm flex-1">
                    <div class="flex w-full flex-1 items-stretch rounded-lg h-full bg-slate-100 dark:bg-slate-800 focus-within:ring-2 focus-within:ring-primary/20 transition-all">
                        <div class="text-slate-500 dark:text-slate-400 flex border-none items-center justify-center pl-4 rounded-l-lg">
                            <span class="material-symbols-outlined">search</span>
                        </div>
                        <input class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-slate-900 dark:text-white focus:outline-0 focus:ring-0 border-none bg-transparent h-full placeholder:text-slate-500 dark:placeholder:text-slate-400 px-4 pl-2 text-sm font-normal leading-normal" placeholder="Tìm kiếm sản phẩm..." value=""/>
                    </div>
                </label>
                <div class="flex gap-1 sm:gap-2 items-center">
                    {{-- Nút search với dropdown (chỉ hiện trên mobile/tablet) --}}
                    <div class="relative lg:hidden" x-data="{ searchOpen: false }">
                        <button @click="searchOpen = !searchOpen" class="flex size-10 cursor-pointer items-center justify-center rounded-full text-slate-900 dark:text-white transition-colors" :class="searchOpen ? 'bg-primary text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800'">
                            <span class="material-symbols-outlined">search</span>
                        </button>
                        {{-- Dropdown search --}}
                        <div x-show="searchOpen" @click.outside="searchOpen = false" @keydown.escape.window="searchOpen = false"
                            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
                            style="position: fixed; top: 60px; left: 50%; transform: translateX(-50%); width: min(60vw, 500px); min-width: 280px;"
                            class="bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 p-4 z-[200]" x-cloak>
                            {{-- Mũi tên chỉ lên icon (vị trí tính động theo icon) --}}
                            <div x-ref="arrowOuter"
                                x-effect="
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
                                class="absolute -top-[9px] w-0 h-0 border-l-[9px] border-l-transparent border-r-[9px] border-r-transparent border-b-[9px] border-b-slate-200 dark:border-b-slate-700"></div>
                            <div x-ref="arrowInner"
                                class="absolute -top-[7px] w-0 h-0 border-l-[7px] border-l-transparent border-r-[7px] border-r-transparent border-b-[7px] border-b-white dark:border-b-slate-800"></div>
                            {{-- Ô input --}}
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                                <input x-ref="searchInput" @keydown.enter="window.location.href='/search?q='+$refs.searchInput.value"
                                    x-init="$watch('searchOpen', value => { if(value) $nextTick(() => $refs.searchInput.focus()) })"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                    placeholder="Tìm kiếm sản phẩm..." type="text" />
                            </div>
                        </div>
                    </div>
                    {{-- Icon giỏ hàng & yêu thích --}}
                    <button class="flex size-10 cursor-pointer items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-900 dark:text-white transition-colors">
                        <span class="material-symbols-outlined">shopping_cart</span>
                    </button>
                    <button class="flex size-10 cursor-pointer items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-900 dark:text-white transition-colors">
                        <span class="material-symbols-outlined">favorite</span>
                    </button>

                    {{-- Desktop: Avatar / Đăng nhập --}}
                    @auth
                        <a href="{{ route('app.profile') }}" class="hidden lg:flex items-center gap-2 cursor-pointer">
                            <div class="bg-primary text-white rounded-full size-10 flex items-center justify-center font-bold text-sm hover:ring-2 hover:ring-primary/30 transition-all">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden lg:flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-6 bg-primary hover:bg-primary/90 text-white text-sm font-bold leading-normal tracking-[0.015em] transition-colors shadow-sm shadow-primary/20 ml-2">
                            <span class="truncate">Đăng nhập</span>
                        </a>
                    @endauth

                    {{-- Mobile: Nút hamburger menu (chỉ hiện trên < lg) --}}
                    <button @click="mobileMenuOpen = true" class="lg:hidden flex size-10 cursor-pointer items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-900 dark:text-white transition-colors">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    {{-- Mobile Menu Panel (slide-in full screen, ĐẶT NGOÀI header) --}}
    {{-- Backdrop --}}
    <div x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="mobile-menu-backdrop fixed inset-0 z-[9998] bg-black/50 backdrop-blur-sm" @click="mobileMenuOpen = false" x-cloak>
    </div>

    {{-- Panel --}}
    <div x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
        class="mobile-menu-panel fixed top-0 right-0 z-[9999] h-full w-[70%] md:w-[40%] bg-white dark:bg-slate-900 shadow-2xl flex flex-col" x-cloak>

        {{-- Header panel --}}
        <div class="flex items-center justify-between px-6 py-5">
            <h3 class="text-slate-900 dark:text-white text-base font-bold">Menu</h3>
            <button @click="mobileMenuOpen = false" class="flex size-8 items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        {{-- Navigation links - đơn giản, chỉ text --}}
        <nav class="flex-1 overflow-y-auto px-6">
            <div class="flex flex-col gap-1">
                <a class="py-2.5 text-primary font-semibold text-[15px]" href="/">Trang chủ</a>
                <a class="py-2.5 text-slate-700 dark:text-slate-300 font-medium text-[15px] hover:text-primary transition-colors" href="#">Source Code</a>
                <a class="py-2.5 text-slate-700 dark:text-slate-300 font-medium text-[15px] hover:text-primary transition-colors" href="#">Apps</a>
                <a class="py-2.5 text-slate-700 dark:text-slate-300 font-medium text-[15px] hover:text-primary transition-colors" href="#">Games</a>
                <a class="py-2.5 text-slate-700 dark:text-slate-300 font-medium text-[15px] hover:text-primary transition-colors" href="#">Blog</a>
                <a class="py-2.5 text-slate-700 dark:text-slate-300 font-medium text-[15px] hover:text-primary transition-colors" href="#">Liên hệ</a>
            </div>

            {{-- Thông tin user --}}
            @auth
                <div class="mt-6">
                    <a href="{{ route('app.profile') }}" class="flex items-center gap-3">
                        <div class="bg-primary text-white rounded-full size-9 flex items-center justify-center font-bold text-xs shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-slate-900 dark:text-white text-sm font-bold truncate">{{ Auth::user()->name }}</p>
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
                    <button type="submit" class="w-full flex items-center justify-center h-10 rounded-lg border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-bold hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors">
                        Đăng xuất
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="w-full flex items-center justify-center h-10 rounded-lg bg-primary hover:bg-primary/90 text-white text-sm font-bold transition-colors">
                    Đăng nhập
                </a>
            @endauth
        </div>
    </div>
</div>
