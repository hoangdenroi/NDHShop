{{-- Desktop Navigation: Mega menu sản phẩm + các link chính --}}
<div class="hidden lg:flex items-center gap-6 pl-4">
    <a class="text-sm font-medium transition-colors {{ request()->is('/') ? 'text-primary dark:text-primary font-bold underline decoration-2 underline-offset-4' : 'text-slate-900 dark:text-slate-200 hover:text-primary' }}"
        href="/">Trang chủ</a>

    {{-- Dropdown Sản phẩm (Mega menu) - Desktop --}}
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
                                                        class="block text-[14px] transition-colors px-4 py-2.5 rounded-lg {{ request()->is($child->slug) ? 'text-primary dark:text-primary font-bold underline decoration-2 underline-offset-4 bg-primary/5 dark:bg-primary/10' : 'text-slate-700 dark:text-slate-300 hover:text-primary dark:hover:bg-slate-800 hover:bg-slate-50 font-medium' }}">
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
                                            class="text-sm transition-colors {{ request()->is($child->slug) ? 'text-primary dark:text-primary font-bold underline decoration-2 underline-offset-4' : 'text-slate-700 dark:text-slate-300 hover:text-primary dark:hover:text-white font-medium' }}">{{ $child->name }}</a>
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
                                            class="text-sm transition-colors {{ request()->is($child->slug) ? 'text-primary dark:text-primary font-bold underline decoration-2 underline-offset-4' : 'text-slate-700 dark:text-slate-300 hover:text-primary dark:hover:text-white font-medium' }}">{{ $child->name }}</a>
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
