<div x-show="activeTab === 'history'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Lịch sử giao dịch</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Chi tiết các giao dịch của bạn.</p>
        </div>
        <div class="p-12 flex flex-col items-center text-center">
            <span class="material-symbols-outlined text-[48px] text-slate-300 dark:text-slate-600 mb-3">receipt_long</span>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Chưa có giao dịch nào.</p>
        </div>
    </div>
</div>
