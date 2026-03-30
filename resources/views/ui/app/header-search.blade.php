{{-- Thanh tìm kiếm: Desktop (ô input) + Mobile/Tablet (icon + dropdown) --}}

{{-- Desktop search --}}
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

{{-- Mobile search dropdown --}}
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
        {{-- Mũi tên chỉ lên icon --}}
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
