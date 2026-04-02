{{-- Component Bảng Giá Cloud Plan (dùng chung cho Database + Storage) --}}
<div class="text-center mb-8">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Chọn gói Cloud Plan</h1>
    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Một gói duy nhất — mở khóa cả Database & Storage</p>
</div>

<div class="grid sm:grid-cols-3 gap-5">
    {{-- Free --}}
    <div
        class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm p-6 flex flex-col">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Free</h3>
        <p class="text-3xl font-extrabold text-slate-900 dark:text-white mt-3">0đ<span
                class="text-sm font-normal text-slate-500">/tháng</span></p>
        <p class="text-xs text-slate-500 mt-1">Dùng thử miễn phí</p>

        {{-- Database --}}
        <div class="mt-5 mb-2">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Database</p>
            <ul class="space-y-2">
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 1 database
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 5
                    Connections
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 200 MB / DB
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> MySQL only
                </li>
            </ul>
        </div>

        {{-- Storage --}}
        <div class="mb-2">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Storage</p>
            <ul class="space-y-2">
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 2 buckets
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 200 MB tổng
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 5 MB/file
                </li>
            </ul>
        </div>

        {{-- Chung --}}
        <div class="flex-1">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Khác</p>
            <ul class="space-y-2">
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-base">cancel</span>
                    Không backup
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-base">cancel</span>
                    Không CDN
                </li>
            </ul>
        </div>

        <button
            class="mt-6 w-full py-2.5 rounded-xl border-2 border-slate-200 dark:border-slate-600 text-sm font-semibold text-slate-400 cursor-default">
            Gói hiện tại
        </button>
    </div>

    {{-- Pro --}}
    <div
        class="bg-white dark:bg-slate-800 rounded-xl border-2 border-primary shadow-lg shadow-primary/10 p-6 flex flex-col relative">
        <span
            class="absolute -top-3 left-1/2 -translate-x-1/2 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full">Phổ
            biến</span>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pro</h3>
        <p class="text-3xl font-extrabold text-primary mt-3">99.000đ<span
                class="text-sm font-normal text-slate-500">/tháng</span></p>
        <p class="text-xs text-slate-500 mt-1">Phù hợp developer & freelancer</p>

        {{-- Database --}}
        <div class="mt-5 mb-2">
            <p class="text-xs font-bold text-primary/60 uppercase tracking-wider mb-2">Database</p>
            <ul class="space-y-2">
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 5 databases
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 20
                    Connections
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 1 GB / DB
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> MySQL +
                    PostgreSQL
                </li>
            </ul>
        </div>

        {{-- Storage --}}
        <div class="mb-2">
            <p class="text-xs font-bold text-primary/60 uppercase tracking-wider mb-2">Storage</p>
            <ul class="space-y-2">
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 10 buckets
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 5 GB tổng
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 50 MB/file ·
                    CDN
                </li>
            </ul>
        </div>

        {{-- Chung --}}
        <div class="flex-1">
            <p class="text-xs font-bold text-primary/60 uppercase tracking-wider mb-2">Khác</p>
            <ul class="space-y-2">
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> Backup hàng
                    tuần
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 5 API Keys
                </li>
            </ul>
        </div>

        <button
            class="mt-6 w-full py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-colors shadow-sm">
            Nâng cấp Pro
        </button>
    </div>

    {{-- Max --}}
    <div
        class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm p-6 flex flex-col relative">
        <span
            class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">Mạnh
            nhất</span>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Max</h3>
        <p class="text-3xl font-extrabold text-slate-900 dark:text-white mt-3">399.000đ<span
                class="text-sm font-normal text-slate-500">/tháng</span></p>
        <p class="text-xs text-slate-500 mt-1">Dành cho team & startup</p>

        {{-- Database --}}
        <div class="mt-5 mb-2">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Database</p>
            <ul class="space-y-2">
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 15 databases
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 50
                    Connections
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 5 GB / DB
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> MySQL +
                    PostgreSQL
                </li>
            </ul>
        </div>

        {{-- Storage --}}
        <div class="mb-2">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Storage</p>
            <ul class="space-y-2">
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 50 buckets
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 50 GB tổng
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 500 MB/file ·
                    CDN + Custom domain
                </li>
            </ul>
        </div>

        {{-- Chung --}}
        <div class="flex-1">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Khác</p>
            <ul class="space-y-2">
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> Backup hàng
                    ngày
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-emerald-500 text-base">check_circle</span> 20 API Keys ·
                    Hỗ trợ ưu tiên
                </li>
            </ul>
        </div>

        <button
            class="mt-6 w-full py-2.5 rounded-xl border-2 border-slate-200 dark:border-slate-600 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:border-primary hover:text-primary transition-colors">
            Nâng cấp Max
        </button>
    </div>
</div>

{{-- Ghi chú bên dưới --}}
<div class="mt-6 space-y-3">
    <div
        class="bg-amber-50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/20 rounded-xl p-4 flex items-start gap-3">
        <span class="material-symbols-outlined text-amber-500 text-lg shrink-0 mt-0.5">info</span>
        <div class="text-sm text-amber-800 dark:text-amber-300">
            <p class="font-semibold mb-1">Lưu ý khi hết hạn gói</p>
            <p class="text-xs text-amber-700 dark:text-amber-400">Khi gói hết hạn, các resource vượt quota Free sẽ bị
                <strong>tạm dừng</strong>. Bạn có <strong>7 ngày</strong> để gia hạn. Sau 7 ngày, dữ liệu sẽ bị xóa vĩnh
                viễn.
            </p>
        </div>
    </div>

    <div
        class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 flex items-start gap-3">
        <span class="material-symbols-outlined text-primary text-lg shrink-0 mt-0.5">support_agent</span>
        <div class="text-sm text-slate-700 dark:text-slate-300">
            <p class="font-semibold mb-1">Chỉ cần 1 dịch vụ?</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Nếu bạn chỉ muốn nâng cấp riêng
                <strong>Database</strong> hoặc <strong>Storage</strong>, hãy liên hệ Admin để được hỗ trợ gói tùy chỉnh.
            </p>
        </div>
    </div>
</div>