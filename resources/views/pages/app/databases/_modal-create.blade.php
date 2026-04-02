{{-- ═══════════════════════════════════════════ --}}
{{-- MODAL: TẠO DATABASE MỚI --}}
{{-- Dữ liệu: $dbQuota, $currentPlan --}}
{{-- ═══════════════════════════════════════════ --}}
<div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showCreateModal = false"></div>
    <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-100 dark:border-slate-700 p-6"
        @click.away="showCreateModal = false">
        <button @click="showCreateModal = false"
            class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-white">
            <span class="material-symbols-outlined">close</span>
        </button>
        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-1">Tạo Database mới</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
            Gói {{ $planLabel }} —
            còn {{ $dbQuota['max_databases'] - $totalDbs }} slot trống
        </p>

        {{-- Chọn Engine --}}
        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 block">Database Engine</label>
        <div class="grid grid-cols-2 gap-3 mb-5">
            {{-- MySQL (luôn có) --}}
            <button @click="selectedEngine = 'mysql'"
                :class="selectedEngine === 'mysql' ? 'border-primary bg-primary/5 dark:bg-primary/10 ring-2 ring-primary/30' : 'border-slate-200 dark:border-slate-600 hover:border-slate-300'"
                class="flex items-center gap-3 p-4 rounded-xl border-2 transition-all">
                <div class="h-10 w-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                    <x-icons.mysql class="w-8 h-8" />
                </div>
                <div class="text-left">
                    <p class="text-sm font-bold text-slate-900 dark:text-white">MySQL</p>
                    <p class="text-xs text-slate-500">Port 3306</p>
                </div>
            </button>

            {{-- PostgreSQL (Pro/Max only) --}}
            @if(in_array('postgresql', $dbQuota['engines']))
                <button @click="selectedEngine = 'postgresql'"
                    :class="selectedEngine === 'postgresql' ? 'border-primary bg-primary/5 dark:bg-primary/10 ring-2 ring-primary/30' : 'border-slate-200 dark:border-slate-600 hover:border-slate-300'"
                    class="flex items-center gap-3 p-4 rounded-xl border-2 transition-all">
                    <div class="h-10 w-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                        <x-icons.postgresql class="w-8 h-8" />
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-bold text-slate-900 dark:text-white">PostgreSQL</p>
                        <p class="text-xs text-slate-500">Port 5432</p>
                    </div>
                </button>
            @else
                <div class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-100 dark:border-slate-700 opacity-50 relative">
                    <div class="h-10 w-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                        <x-icons.postgresql class="w-8 h-8" />
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-bold text-slate-500 dark:text-slate-400">PostgreSQL</p>
                        <p class="text-xs text-amber-500">Yêu cầu gói Pro+</p>
                    </div>
                    <span class="material-symbols-outlined absolute top-2 right-2 text-slate-300 text-sm">lock</span>
                </div>
            @endif
        </div>

        {{-- Tên database --}}
        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 block">Tên Database</label>
        <div class="flex items-center gap-0 mb-2">
            <span class="bg-slate-100 dark:bg-slate-700 border border-r-0 border-slate-200 dark:border-slate-600 px-3 py-2.5 rounded-l-xl text-sm text-slate-500 font-mono">ndh_</span>
            <input type="text" x-model="dbName" placeholder="ten_database"
                @input="dbName = dbName.replace(/[^a-zA-Z0-9_]/g, '').toLowerCase()"
                maxlength="50"
                class="flex-1 border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2.5 rounded-r-xl text-sm font-mono text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary/30 focus:border-primary">
        </div>
        <p class="text-xs text-slate-400 mb-5">Chỉ chữ cái, số và dấu gạch dưới. VD: my_app_db</p>

        {{-- Thông tin gói --}}
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 mb-6">
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Dung lượng</p>
                    <p class="font-semibold text-slate-900 dark:text-white">{{ $dbQuota['max_db_storage_mb'] }} MB</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Connections</p>
                    <p class="font-semibold text-slate-900 dark:text-white">{{ $dbQuota['max_connections'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Backup</p>
                    <p class="font-semibold text-slate-900 dark:text-white">
                        @if($dbQuota['backup'] === 'daily') Hàng ngày
                        @elseif($dbQuota['backup'] === 'weekly') Hàng tuần
                        @else Không
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Gói</p>
                    <p class="font-semibold text-primary">{{ $planLabel }}</p>
                </div>
            </div>
        </div>

        <button @click="createDatabase()"
            :disabled="!dbName || isCreatingDb"
            class="w-full py-3 rounded-xl bg-primary text-white font-semibold hover:bg-primary/90 transition-colors shadow-sm flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
            <template x-if="isCreatingDb">
                <span class="material-symbols-outlined text-lg animate-spin">progress_activity</span>
            </template>
            <template x-if="!isCreatingDb">
                <span class="material-symbols-outlined text-lg">rocket_launch</span>
            </template>
            <span x-text="isCreatingDb ? 'Đang tạo Database...' : 'Tạo Database'"></span>
        </button>
    </div>
</div>