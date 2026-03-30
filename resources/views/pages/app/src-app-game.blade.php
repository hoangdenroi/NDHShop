@extends('layouts.app.app-layout')

@section('content')
    <div class="w-full" x-data="{
                                    selectedGroup: @js($selectedGroup),
                                    mobileFiltersOpen: false,

                                    selectGroup(key) {
                                        this.selectedGroup = (this.selectedGroup === key) ? '' : key;
                                        const params = new URLSearchParams();
                                        if (this.selectedGroup) params.set('group', this.selectedGroup);
                                        window.location.href = `{{ route('app.src-app-game') }}?${params.toString()}`;
                                    },

                                    resetFilters() {
                                        window.location.href = `{{ route('app.src-app-game') }}`;
                                    }
                                }">

        {{-- Breadcrumbs & Title --}}
        <div class="flex flex-col gap-2 pb-6 border-b border-border-color mb-8">
            <div class="flex justify-between items-end mt-4">
                <div>
                    <h1 class="text-text-main text-3xl md:text-4xl font-extrabold tracking-tight leading-tight">
                        Source Code / App / Game
                    </h1>
                    <p class="text-text-secondary mt-2 text-base max-w-2xl">
                        Khám phá bộ sưu tập mã nguồn, ứng dụng và game chất lượng cao.
                        Tìm kiếm sản phẩm phù hợp để sử dụng hoặc tăng tốc dự án của bạn.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Nút toggle bộ lọc trên mobile --}}
            <button @click="mobileFiltersOpen = true"
                class="lg:hidden flex items-center justify-between w-full p-3 bg-slate-50 dark:bg-surface-dark rounded-lg border border-slate-200 dark:border-border-dark font-bold text-slate-900 dark:text-white mb-4">
                <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined">filter_list</span>
                    Bộ lọc
                </span>
                <span class="text-xs text-primary bg-primary/10 px-2 py-1 rounded"
                    x-text="selectedGroup ? selectedGroup : 'Tất cả'"></span>
            </button>

            {{-- Sidebar / Filters --}}
            <aside
                class="fixed inset-0 z-[100] lg:relative lg:inset-auto lg:z-10 lg:w-64 shrink-0 pointer-events-none lg:pointer-events-auto"
                :class="{ 'pointer-events-auto': mobileFiltersOpen }">

                {{-- Overlay Mobile --}}
                <div x-show="mobileFiltersOpen" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="mobileFiltersOpen = false"
                    class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

                {{-- Nội dung bộ lọc --}}
                <div class="absolute inset-y-0 left-0 w-80 max-w-[80vw] bg-white dark:bg-surface-dark lg:bg-transparent lg:dark:bg-transparent p-6 lg:p-0 overflow-y-auto lg:overflow-visible transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:w-full flex flex-col gap-8 shadow-xl lg:shadow-none"
                    :class="mobileFiltersOpen ? '!translate-x-0' : '-translate-x-full'">

                    {{-- Header mobile --}}
                    <div
                        class="flex items-center justify-between lg:hidden pb-4 border-b border-slate-200 dark:border-border-dark mb-2">
                        <h3 class="font-bold text-slate-900 dark:text-white">Bộ lọc</h3>
                        <button @click="mobileFiltersOpen = false"
                            class="material-symbols-outlined text-slate-500">close</button>
                    </div>

                    <div class="flex flex-col gap-6">
                        {{-- Header desktop --}}
                        <div
                            class="hidden lg:flex items-center justify-between pb-2 border-b border-slate-200 dark:border-border-dark">
                            <h3 class="font-bold text-slate-900 dark:text-white">Bộ lọc</h3>
                            <button @click="resetFilters" class="text-xs text-primary font-medium hover:underline">Xóa tất
                                cả</button>
                        </div>

                        {{-- Lọc theo loại: SRC, APP, GAME --}}
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Loại sản phẩm</h4>
                                <button @click="resetFilters"
                                    class="text-[10px] text-primary bg-primary/10 px-2 py-1 rounded font-bold hover:bg-primary/20 lg:hidden">Xóa
                                    tất cả</button>
                            </div>
                            <div class="flex flex-col gap-2">
                                @foreach($sidebarGroups as $group)
                                    <button @click="selectGroup('{{ $group['key'] }}')"
                                        class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg border text-sm font-semibold transition-all duration-200 cursor-pointer"
                                        :class="selectedGroup === '{{ $group['key'] }}'
                                                                    ? 'bg-primary/10 border-primary/40 text-primary dark:text-primary'
                                                                    : 'bg-white dark:bg-slate-800/50 border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 hover:border-primary/30 hover:bg-primary/5'">
                                        <span class="flex items-center gap-2.5">
                                            <span class="material-symbols-outlined text-lg">{{ $group['icon'] }}</span>
                                            {{ $group['label'] }}
                                        </span>
                                        <span class="text-xs bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full font-bold"
                                            :class="selectedGroup === '{{ $group['key'] }}' ? 'bg-primary/20 text-primary' : 'text-slate-500 dark:text-slate-400'">
                                            {{ $group['count'] }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Khu vực nội dung chính --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-col gap-6">
                    {{-- Header kết quả --}}
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-border-dark pb-4">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">
                            @if(!empty($selectedGroup))
                                {{ $selectedGroup }}
                            @else
                                Tất cả
                            @endif
                            <span class="text-base font-normal text-slate-500">({{ $products->total() }} sản phẩm)</span>
                        </h2>
                    </div>

                    @if($products->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                            @foreach($products as $p)
                                <x-app.product-card :product="$p" />
                            @endforeach
                        </div>

                        <div class="mt-8 flex justify-center">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <span
                                class="material-symbols-outlined text-6xl text-slate-300 dark:text-slate-600 mb-4">inventory_2</span>
                            <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300 mb-2">Chưa có sản phẩm nào</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm">
                                Không tìm thấy sản phẩm nào phù hợp với bộ lọc hiện tại. Hãy thử thay đổi bộ lọc hoặc xem tất cả
                                sản phẩm.
                            </p>
                            <button @click="resetFilters"
                                class="mt-6 px-6 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary/90 transition-colors">
                                Xem tất cả
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection