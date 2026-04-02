<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="w-64 flex-shrink-0 flex flex-col bg-white dark:bg-[#0b0f17] border-r border-slate-200 dark:border-border-dark fixed lg:relative inset-y-0 left-0 z-40 -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
    <div class="p-6 flex items-center gap-3">
        <div class="bg-primary/20 p-2 rounded-lg text-primary">
            {{-- <span class="material-symbols-outlined">grid_view</span> --}}
            <img src="{{ asset('NDHShop.jpg') }}" alt="Logo" class="w-10"
                style="object-fit: contain; border-radius: 10px;">
        </div>
        <div>
            <h1 class="text-slate-900 dark:text-white text-base font-bold leading-tight">NDHShop</h1>
            <p class="text-slate-500 text-xs font-medium">Version: 1.0.0</p>
        </div>
    </div>
    @php
        // Class cho menu item đang active
        $activeClass = 'flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary text-white';
        // Class cho menu item không active
        $defaultClass = 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-surface-dark transition-colors';
    @endphp

    <div class="px-4 py-2 flex flex-col gap-1 flex-1 overflow-y-auto">
        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider px-3 mb-2 mt-2">Main Menu</div>
        <a class="{{ request()->routeIs('admin.dashboard') ? $activeClass : $defaultClass }}"
            href="{{ route('admin.dashboard') }}">
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            <span class="text-sm font-medium">Dashboard</span>
        </a>
        <a class="{{ request()->routeIs('admin.users.*') ? $activeClass : $defaultClass }}"
            href="{{ route('admin.users.index') }}">
            <span class="material-symbols-outlined text-[20px]">person</span>
            <span class="text-sm font-medium">Quản lý người dùng</span>
        </a>
        <a class="{{ request()->routeIs('admin.categories.*') ? $activeClass : $defaultClass }}"
            href="{{ route('admin.categories.index') }}">
            <span class="material-symbols-outlined text-[20px]">category</span>
            <span class="text-sm font-medium">Quản lý danh mục</span>
        </a>
        <a class="{{ request()->routeIs('admin.products.*') ? $activeClass : $defaultClass }}"
            href="{{ route('admin.products.index') }}">
            <span class="material-symbols-outlined text-[20px]">inventory_2</span>
            <span class="text-sm font-medium">Quản lý sản phẩm</span>
        </a>
        <a class="{{ request()->routeIs('admin.coupons.*') ? $activeClass : $defaultClass }}"
            href="{{ route('admin.coupons.index') }}">
            <span class="material-symbols-outlined text-[20px]">local_offer</span>
            <span class="text-sm font-medium">Quản lý mã giảm giá</span>
        </a>
        <a class="{{ request()->routeIs('admin.orders.*') ? $activeClass : $defaultClass }}"
            href="{{ route('admin.orders.index') }}">
            <span class="material-symbols-outlined text-[20px]">receipt_long</span>
            <span class="text-sm font-medium">Quản lý đơn hàng</span>
        </a>
        {{-- Menu quản lý bài viết có sub-menu --}}
        <div
            x-data="{ openPosts: {{ request()->routeIs('admin.blogs-posts.*') || request()->routeIs('admin.post-categories.*') ? 'true' : 'false' }} }">
            <button @click="openPosts = !openPosts"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.blogs-posts.*') || request()->routeIs('admin.post-categories.*') ? 'bg-primary text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-surface-dark' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px]">article</span>
                    <span class="text-sm font-medium">Quản lý nội dung</span>
                </div>
                <span class="material-symbols-outlined text-[20px] transition-transform duration-200"
                    :class="openPosts ? 'rotate-180' : ''">expand_more</span>
            </button>

            <div x-show="openPosts" x-collapse class="mt-1 flex flex-col gap-1 pl-9 pr-3">
                <a class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('admin.blogs-posts.*') ? 'text-primary font-bold bg-primary/10' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-surface-dark' }}"
                    href="{{ route('admin.blogs-posts.index') }}">
                    <span
                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.blogs-posts.*') ? 'bg-primary' : 'bg-slate-400' }}"></span>
                    Bài viết
                </a>
                <a class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('admin.post-categories.*') ? 'text-primary font-bold bg-primary/10' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-surface-dark' }}"
                    href="{{ route('admin.post-categories.index') }}">
                    <span
                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.post-categories.*') ? 'bg-primary' : 'bg-slate-400' }}"></span>
                    Danh mục bài viết
                </a>
            </div>
        </div>

        {{-- Menu quản lý quà tặng có sub-menu gift categories --}}
        <div
            x-data="{ openGifts: {{ request()->routeIs('admin.gift-templates.*') || request()->routeIs('admin.gift-categories.*') || request()->routeIs('admin.gift-assets.*') ? 'true' : 'false' }} }">
            <button @click="openGifts = !openGifts"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.gift-templates.*') || request()->routeIs('admin.gift-categories.*') || request()->routeIs('admin.gift-assets.*') ? 'bg-primary text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-surface-dark' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px]">redeem</span>
                    <span class="text-sm font-medium">Quản lý quà tặng</span>
                </div>
                <span class="material-symbols-outlined text-[20px] transition-transform duration-200"
                    :class="openGifts ? 'rotate-180' : ''">expand_more</span>
            </button>

            <div x-show="openGifts" x-collapse class="mt-1 flex flex-col gap-1 pl-9 pr-3">
                <a class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('admin.gift-templates.*') ? 'text-primary font-bold bg-primary/10' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-surface-dark' }}"
                    href="{{ route('admin.gift-templates.index') }}">
                    <span
                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.gift-templates.*') ? 'bg-primary' : 'bg-slate-400' }}"></span>
                    Gift Templates
                </a>
                <a class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('admin.gift-categories.*') ? 'text-primary font-bold bg-primary/10' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-surface-dark' }}"
                    href="{{ route('admin.gift-categories.index') }}">
                    <span
                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.gift-categories.*') ? 'bg-primary' : 'bg-slate-400' }}"></span>
                    Gift Categories
                </a>
                <a class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('admin.gift-assets.*') ? 'text-primary font-bold bg-primary/10' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-surface-dark' }}"
                    href="{{ route('admin.gift-assets.index') }}">
                    <span
                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.gift-assets.*') ? 'bg-primary' : 'bg-slate-400' }}"></span>
                    Gift Assets
                </a>
            </div>
        </div>

        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider px-3 mb-2 mt-6">System</div>
        <a class="{{ request()->routeIs('admin.audit-logs.*') ? $activeClass : $defaultClass }}" href="{{ route('admin.audit-logs.index') }}">
            <span class="material-symbols-outlined text-[20px]">history</span>
            <span class="text-sm font-medium">Lịch sử thao tác</span>
        </a>
        <a class="{{ request()->routeIs('admin.server-health.*') ? $activeClass : $defaultClass }}" href="#">
            <span class="material-symbols-outlined text-[20px]">dns</span>
            <span class="text-sm font-medium">Quản lý server</span>
        </a>
        <a class="{{ request()->routeIs('admin.cron.*') ? $activeClass : $defaultClass }}" href="{{ route('admin.cron.index') }}">
            <span class="material-symbols-outlined text-[20px]">schedule</span>
            <span class="text-sm font-medium">Cron</span>
        </a>
    </div>
</aside>