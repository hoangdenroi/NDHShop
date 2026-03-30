@extends('layouts.app.app-layout')

@section('content')
    <div class="w-full" x-data="{ 
                        categories: @js($searchCategories),
                        minPrice: @js($minPrice ?: 0),
                        maxPrice: @js($maxPrice ?: $globalMaxPrice),
                        globalMax: @js($globalMaxPrice),
                        timeout: null,
                        mobileFiltersOpen: false,

                        applyFilters() {
                            if (this.timeout) clearTimeout(this.timeout);
                            this.timeout = setTimeout(() => {
                                const params = new URLSearchParams();
                                this.categories.forEach(id => params.append('categories[]', id));
                                params.set('min_price', this.minPrice);
                                params.set('max_price', this.maxPrice);
                                window.location.href = `{{ route('app.all-product') }}?${params.toString()}`;
                            }, 500);
                        },

                        resetFilters() {
                            window.location.href = `{{ route('app.all-product') }}`;
                        }
                    }">

        <!-- Breadcrumbs & Title -->
        <div class="flex flex-col gap-2 pb-6 border-b border-border-color mb-8">
            <div class="flex justify-between items-end mt-4">
                <div>
                    <h1 class="text-text-main text-3xl md:text-4xl font-extrabold tracking-tight leading-tight">Tất cả sản
                        phẩm</h1>
                    <p class="text-text-secondary mt-2 text-base max-w-2xl">Khám phá các sản phẩm chất lượng cao cho dự án
                        tiếp theo của bạn. Từ các ứng dụng di động đến các bảng điều khiển web, tìm mã nguồn chất lượng để
                        tăng
                        tốc phát triển cho bạn.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Mobile toggle --}}
            <button @click="mobileFiltersOpen = true"
                class="lg:hidden flex items-center justify-between w-full p-3 bg-slate-50 dark:bg-surface-dark rounded-lg border border-slate-200 dark:border-border-dark font-bold text-slate-900 dark:text-white mb-4">
                <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined">filter_list</span>
                    Bộ lọc
                </span>
                <span class="text-xs text-primary bg-primary/10 px-2 py-1 rounded">
                    {{ count($searchCategories) > 0 ? count($searchCategories) . ' danh mục' : 'Tất cả' }}
                </span>
            </button>

            {{-- Sidebar / Filters --}}
            <aside 
                class="fixed inset-0 z-[100] lg:relative lg:inset-auto lg:z-10 lg:w-64 shrink-0 pointer-events-none lg:pointer-events-auto"
                :class="{ 'pointer-events-auto': mobileFiltersOpen }">
                
                {{-- Overlay for Mobile --}}
                <div x-show="mobileFiltersOpen" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="mobileFiltersOpen = false"
                     class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

                {{-- Filter Content --}}
                <div class="absolute inset-y-0 left-0 w-80 max-w-[80vw] bg-white dark:bg-surface-dark lg:bg-transparent lg:dark:bg-transparent p-6 lg:p-0 overflow-y-auto lg:overflow-visible transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:w-full flex flex-col gap-8 shadow-xl lg:shadow-none"
                     :class="mobileFiltersOpen ? '!translate-x-0' : '-translate-x-full'">
                    
                    <div class="flex items-center justify-between lg:hidden pb-4 border-b border-slate-200 dark:border-border-dark mb-2">
                        <h3 class="font-bold text-slate-900 dark:text-white">Bộ lọc</h3>
                        <button @click="mobileFiltersOpen = false" class="material-symbols-outlined text-slate-500">close</button>
                    </div>

                    <div class="flex flex-col gap-6">
                        <div class="hidden lg:flex items-center justify-between pb-2 border-b border-slate-200 dark:border-border-dark">
                            <h3 class="font-bold text-slate-900 dark:text-white">Bộ lọc</h3>
                            <button @click="resetFilters" class="text-xs text-primary font-medium hover:underline">Xóa tất cả</button>
                        </div>

                        {{-- Category Filter --}}
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Danh mục</h4>
                                <button @click="resetFilters" class="text-[10px] text-primary bg-primary/10 px-2 py-1 rounded font-bold hover:bg-primary/20 lg:hidden">Xóa tất cả</button>
                            </div>
                            <div class="flex flex-col gap-2 max-h-[60vh] lg:max-h-60 overflow-y-auto scrollbar-hide">
                                @foreach($sidebarCategories as $sidebarCat)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" value="{{ $sidebarCat->id }}" x-model="categories"
                                            @change="applyFilters"
                                            class="form-checkbox rounded border-slate-300 dark:border-border-dark text-primary focus:ring-primary w-4 h-4 bg-transparent outline-none ring-0 focus:ring-offset-0">
                                        <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">
                                            {{ $sidebarCat->name }} ({{ $sidebarCat->products_count }})
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="h-px bg-slate-200 dark:bg-border-dark"></div>

                        {{-- Price Range Filter --}}
                        <div class="flex flex-col gap-4">
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Khoảng giá</h4>

                            <div class="flex flex-col gap-4 px-2">
                                <div class="flex items-center justify-between text-[10px] text-slate-500 font-bold">
                                    <span x-text="new Intl.NumberFormat('vi-VN').format(minPrice) + 'đ'"></span>
                                    <span x-text="new Intl.NumberFormat('vi-VN').format(maxPrice) + 'đ'"></span>
                                </div>

                                <div class="relative h-2 w-full mt-4">
                                    {{-- Range Track background --}}
                                    <div class="absolute inset-0 bg-slate-200 dark:bg-slate-700 rounded-full h-1.5"></div>

                                    {{-- Active range highlight --}}
                                    <div class="absolute h-1.5 bg-primary rounded-full transition-none"
                                        :style="`left: ${(minPrice / globalMax) * 100}%; right: ${100 - (maxPrice / globalMax) * 100}%` ">
                                    </div>

                                    <input type="range" x-model.number="minPrice" :min="0" :max="globalMax" step="10000"
                                        @input="if(minPrice > (maxPrice - 10000)) minPrice = maxPrice - 10000; applyFilters()"
                                        class="absolute inset-0 w-full h-1.5 appearance-none bg-transparent cursor-pointer pointer-events-none z-30 slider-thumb-only">

                                    <input type="range" x-model.number="maxPrice" :min="0" :max="globalMax" step="10000"
                                        @input="if(maxPrice < (minPrice + 10000)) maxPrice = minPrice + 10000; applyFilters()"
                                        class="absolute inset-0 w-full h-1.5 appearance-none bg-transparent cursor-pointer pointer-events-none z-30 slider-thumb-only">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Main Content AREA --}}
            <div class="flex-1 min-w-0">
                @if(isset($products))
                    {{-- Kết quả lọc (Danh sách phẳng) --}}
                    <div class="flex flex-col gap-6">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-border-dark pb-4">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white">
                                Kết quả tìm kiếm ({{ $products->total() }})
                            </h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                            @foreach($products as $p)
                                <x-app.product-card :product="$p" />
                            @endforeach
                        </div>

                        <div class="mt-8 flex justify-center">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    </div>
                @else
                    {{-- Mặc định (Nhóm theo Category) --}}
                    <div class="flex flex-col gap-12">
                        @foreach($categories as $category)
                            @if($category->products->count() > 0)
                                <div class="flex flex-col gap-6">
                                    <div
                                        class="flex items-center justify-between border-b border-slate-200 dark:border-border-dark pb-4">
                                        <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                            <span class="material-symbols-outlined text-primary">category</span>
                                            {{ $category->name }}
                                        </h2>
                                        <button @click="categories = [{{ $category->id }}]; applyFilters()"
                                            class="text-sm font-medium text-primary hover:underline">Xem tất cả</button>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                                        @foreach($category->products as $p)
                                            <x-app.product-card :product="$p" />
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        <div class="mt-8 flex justify-center">
                            {{ $categories->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .slider-thumb-only {
            pointer-events: none;
        }

        .slider-thumb-only::-webkit-slider-thumb {
            pointer-events: auto;
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: white;
            border: 2px solid var(--color-primary, #0d59f2);
            cursor: pointer;
            position: relative;
            z-index: 40;
        }

        .slider-thumb-only::-moz-range-thumb {
            pointer-events: auto;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: white;
            border: 2px solid var(--color-primary, #0d59f2);
            cursor: pointer;
        }
    </style>
@endsection