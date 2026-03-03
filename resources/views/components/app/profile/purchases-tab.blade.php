<div x-show="activeTab === 'purchases'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Sản phẩm đã mua</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Danh sách các sản phẩm bạn đã mua.</p>
        </div>
        <div class="p-12 flex flex-col items-center text-center">
            <span class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600 mb-3">shopping_bag</span>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Bạn chưa mua sản phẩm nào.</p>
            <a href="/" class="mt-4 text-primary text-sm font-semibold hover:underline">Khám phá ngay →</a>
        </div>
    </div>
</div>
