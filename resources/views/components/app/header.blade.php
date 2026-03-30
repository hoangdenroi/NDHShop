{{-- Component Header - Thanh điều hướng chính --}}
{{-- Các partial được tách ra tại: resources/views/ui/app/header-*.blade.php --}}

@php
    // Fetch Categories with Cache (dùng chung cho desktop nav + mobile menu)
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
    $pos1 = $menuCategories->filter(fn($c) => ($c->metadata['position'] ?? '1') == '1');
    $pos2 = $menuCategories->filter(fn($c) => ($c->metadata['position'] ?? '') == '2');
    $pos3 = $menuCategories->filter(fn($c) => ($c->metadata['position'] ?? '') == '3');

    // Kiểm tra active cho menu Sản phẩm
    $isProductActive = request()->is('games*') || request()->is('source-code*') || request()->is('san-pham*') || request()->is('apps/all-product*') || request()->is('all-product*');
    foreach ($menuCategories as $cat) {
        if (request()->is($cat->slug) || request()->is($cat->slug . '/*')) {
            $isProductActive = true;
            break;
        }
        foreach ($cat->children as $child) {
            if (request()->is($child->slug) || request()->is($child->slug . '/*')) {
                $isProductActive = true;
                break;
            }
        }
    }
@endphp

<div x-data="{ mobileMenuOpen: false }" class="relative z-[60]">
    <header
        class="fixed top-0 left-0 w-full z-50 flex items-center justify-between whitespace-nowrap border-b border-solid border-slate-200 bg-white/90 backdrop-blur-md px-3 sm:px-6 py-3 sm:py-4 lg:px-20 dark:border-slate-800 dark:bg-background-dark/90">
        <div class="flex items-center gap-8 w-full max-w-[1400px] mx-auto justify-between">
            <div class="flex items-center gap-2 sm:gap-4 lg:gap-8">
                {{-- Logo --}}
                <a href="/" class="flex items-center gap-2 sm:gap-3 text-slate-900 dark:text-white">
                    <div class="size-8 text-primary">
                        <img src="{{ asset('NDHShop.jpg') }}" alt="Logo" class="h-full w-full object-cover"
                            style="border-radius: 5px;">
                    </div>
                    <h2 class="text-slate-900 dark:text-white text-xl font-bold leading-tight tracking-[-0.015em]">
                        NDHShop</h2>
                </a>

                {{-- Desktop Navigation + Mega Menu --}}
                @include('ui.app.header-desktop-nav')
            </div>

            <div class="flex items-center gap-4 flex-1 justify-end">
                {{-- Thanh tìm kiếm (Desktop + Mobile) --}}
                @include('ui.app.header-search')

                <div class="flex gap-1 sm:gap-2 items-center">
                    {{-- Dropdown thông báo --}}
                    @include('ui.app.header-notifications')

                    {{-- Dropdown giỏ hàng --}}
                    @include('ui.app.header-cart-dropdown')

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

                    {{-- Mobile: Nút hamburger menu --}}
                    <button @click="mobileMenuOpen = true"
                        class="lg:hidden flex size-10 cursor-pointer items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-900 dark:text-white transition-colors">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    {{-- Mobile Menu Panel --}}
    @include('ui.app.header-mobile-menu')
</div>