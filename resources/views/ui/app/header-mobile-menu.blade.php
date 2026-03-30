{{-- Mobile Menu Panel (slide-in, sử dụng biến mobileMenuOpen từ header gốc) --}}

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

    {{-- Navigation links --}}
    <nav class="flex-1 overflow-y-auto px-6">
        <div class="flex flex-col gap-1">
            <a class="py-2.5 font-semibold text-[15px] transition-colors {{ request()->is('/') ? 'text-primary dark:text-primary underline decoration-2 underline-offset-4' : 'text-slate-900 dark:text-white' }}"
                href="/">Trang chủ</a>

            {{-- Dropdown Sản phẩm - Mobile --}}
            <div x-data="{ openProductMobile: {{ $isProductActive ? 'true' : 'false' }} }" class="flex flex-col">
                <button @click="openProductMobile = !openProductMobile"
                    class="flex items-center justify-between py-2.5 font-medium text-[15px] transition-colors w-full text-left"
                    :class="openProductMobile || {{ ($isProductActive ?? false) ? 'true' : 'false' }} ? 'text-primary dark:text-primary' : 'text-slate-700 dark:text-slate-300 hover:text-primary'">
                    Sản phẩm
                    <span class="material-symbols-outlined text-[20px] transition-transform duration-200"
                        :class="openProductMobile ? 'rotate-180' : ''">expand_more</span>
                </button>

                <div x-show="openProductMobile" x-cloak x-transition:enter="transition-all ease-out duration-300"
                    x-transition:enter-start="opacity-0 max-h-0 overflow-hidden"
                    x-transition:enter-end="opacity-100 max-h-[2000px] overflow-hidden"
                    x-transition:leave="transition-all ease-in duration-200"
                    x-transition:leave-start="opacity-100 max-h-[2000px] overflow-hidden"
                    x-transition:leave-end="opacity-0 max-h-0 overflow-hidden"
                    class="flex flex-col pl-4 border-l-2 border-slate-200 dark:border-slate-800 ml-2 mt-1 mb-2">

                    {{-- POSITION 1: CỘT ĐẦU --}}
                    <div class="py-2">
                        @foreach($pos1 as $item)
                            @if($item->children->isNotEmpty())
                                @php
                                    $isItemActive = request()->is($item->slug);
                                    foreach ($item->children as $child) {
                                        if (request()->is($child->slug)) {
                                            $isItemActive = true;
                                            break;
                                        }
                                    }
                                @endphp
                                <div x-data="{ openPos1: {{ $isItemActive ? 'true' : 'false' }} }"
                                    class="flex flex-col mb-1">
                                    <button @click="openPos1 = !openPos1"
                                        class="flex items-center justify-between py-2 text-[14px] w-full text-left transition-colors"
                                        :class="openPos1 ? 'text-primary dark:text-primary font-bold' : 'text-slate-700 dark:text-slate-300 hover:text-primary font-medium'">
                                        {{ $item->name }}
                                        <span
                                            class="material-symbols-outlined text-[18px] transition-transform duration-200"
                                            :class="openPos1 ? 'rotate-180' : ''">expand_more</span>
                                    </button>
                                    <div x-show="openPos1" x-cloak x-transition:enter="transition-all ease-out duration-300"
                                        x-transition:enter-start="opacity-0 max-h-0 overflow-hidden"
                                        x-transition:enter-end="opacity-100 max-h-[1000px] overflow-hidden"
                                        x-transition:leave="transition-all ease-in duration-200"
                                        x-transition:leave-start="opacity-100 max-h-[1000px] overflow-hidden"
                                        x-transition:leave-end="opacity-0 max-h-0 overflow-hidden"
                                        class="flex flex-col gap-3 pl-3 py-2 ml-2 mb-2">
                                        @foreach($item->children as $child)
                                            <a href="/{{ $child->slug }}"
                                                class="text-sm transition-colors {{ request()->is($child->slug) ? 'text-primary dark:text-primary font-bold underline decoration-2 underline-offset-4' : 'text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white font-medium' }}">{{ $child->name }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="/{{ $item->slug }}"
                                    class="flex items-center py-2 mb-1 text-[14px] transition-colors {{ request()->is($item->slug) ? 'text-primary dark:text-primary font-bold underline decoration-2 underline-offset-4' : 'text-slate-700 dark:text-slate-300 hover:text-primary dark:hover:text-white font-medium' }}">{{ $item->name }}</a>
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
                                            class="text-sm transition-colors {{ request()->is($child->slug) ? 'text-primary dark:text-primary font-bold underline decoration-2 underline-offset-4' : 'text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white font-medium' }}">{{ $child->name }}</a>
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
                                            class="text-sm transition-colors {{ request()->is($child->slug) ? 'text-primary dark:text-primary font-bold underline decoration-2 underline-offset-4' : 'text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white font-medium' }}">{{ $child->name }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach

                    {{-- Tất cả dịch vụ --}}
                    <div class="py-3 mt-1 border-t border-slate-200 dark:border-slate-800/60">
                        <a href="{{ route('app.all-product') }}"
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

    {{-- Footer panel: Đăng nhập hoặc Đăng xuất --}}
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
