{{-- ═══════════════════════════════════════════ --}}
{{-- SIDEBAR TRÁI - Tổng quan, điều hướng, quota --}}
{{-- Dữ liệu từ controller: $currentPlan, $planLabel, $totalDbs, $activeDbs, $dbQuota, $totalStorageUsed, $databases, $apiKeys --}}
{{-- ═══════════════════════════════════════════ --}}
<aside class="w-full lg:w-72 shrink-0 flex flex-col gap-6">

    {{-- Thẻ tổng quan --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <div
                class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                <span class="material-symbols-outlined text-white text-2xl">database</span>
            </div>
            <div>
                <h2 class="text-slate-900 dark:text-white text-base font-bold">Cloud Database</h2>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="text-xs font-semibold px-1.5 py-0.5 rounded
                        @if($currentPlan === 'max') bg-gradient-to-r from-amber-500 to-orange-500 text-white
                        @elseif($currentPlan === 'pro') bg-primary/10 text-primary
                        @else bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 @endif">
                        {{ $planLabel }}
                    </span>
                    @if($currentPlan !== 'free' && auth()->user()->cloud_plan_expires_at)
                        <span class="text-[10px] text-slate-400">
                            HSD: {{ auth()->user()->cloud_plan_expires_at->format('d/m/Y') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3 text-center">
                <p class="text-2xl font-bold text-primary">{{ $totalDbs }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Databases</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3 text-center">
                <p class="text-2xl font-bold text-emerald-500">{{ $activeDbs }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Hoạt động</p>
            </div>
        </div>
    </div>

    {{-- Menu điều hướng --}}
    <nav
        class="bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="flex flex-col p-2 gap-1">
            <button @click="activeTab = 'databases'"
                :class="activeTab === 'databases' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left w-full group">
                <span class="material-symbols-outlined"
                    :class="activeTab === 'databases' ? 'text-primary' : 'text-slate-400 group-hover:text-primary'">storage</span>
                <span class="font-medium text-sm">Databases</span>
                <span class="ml-auto bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full">{{ $totalDbs }}</span>
            </button>
            <button @click="activeTab = 'api-keys'"
                :class="activeTab === 'api-keys' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left w-full group">
                <span class="material-symbols-outlined"
                    :class="activeTab === 'api-keys' ? 'text-primary' : 'text-slate-400 group-hover:text-primary'">key</span>
                <span class="font-medium text-sm">API Keys</span>
                <span class="ml-auto bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full">{{ $apiKeys->count() }}</span>
            </button>
            <button @click="activeTab = 'pricing'"
                :class="activeTab === 'pricing' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left w-full group">
                <span class="material-symbols-outlined"
                    :class="activeTab === 'pricing' ? 'text-primary' : 'text-slate-400 group-hover:text-primary'">payments</span>
                <span class="font-medium text-sm">Gói dịch vụ</span>
                @if($currentPlan === 'free')
                    <span class="ml-auto bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-[10px] font-bold px-2 py-0.5 rounded-full">Nâng cấp</span>
                @endif
            </button>
        </div>
        <div class="border-t border-slate-100 dark:border-slate-700 p-2 mt-1">
            <button @click="showCreateModal = true"
                class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors font-medium text-sm shadow-sm">
                <span class="material-symbols-outlined text-lg">add_circle</span>
                Tạo Database mới
            </button>
        </div>
    </nav>

    {{-- Quota hiện tại --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-100 dark:border-slate-700 shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Quota hiện tại</h3>
        <div class="space-y-3">
            @php
                $dbPercent = $dbQuota['max_databases'] > 0 ? round($totalDbs / $dbQuota['max_databases'] * 100) : 0;

                // Tổng dung lượng đã dùng / tổng cho phép chung của Plan (Shared Pool)
                $totalMaxStorage = $dbQuota['max_db_storage_mb'];
                $storagePercent = $totalMaxStorage > 0 ? round($totalStorageUsed / $totalMaxStorage * 100) : 0;

                // Tổng connections active / tổng cho phép chung của Plan (Shared Pool)
                $totalActiveConns = $databases->sum('active_connections');
                $totalMaxConns = $dbQuota['max_connections'];
                $connPercent = $totalMaxConns > 0 ? round($totalActiveConns / $totalMaxConns * 100) : 0;
            @endphp

            {{-- Databases --}}
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-slate-500 dark:text-slate-400">Databases</span>
                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $totalDbs }} / {{ $dbQuota['max_databases'] }}</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                    <div class="bg-primary h-1.5 rounded-full transition-all" style="width: {{ min($dbPercent, 100) }}%"></div>
                </div>
            </div>

            {{-- Dung lượng --}}
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-slate-500 dark:text-slate-400">Dung lượng tổng</span>
                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ number_format($totalStorageUsed, 1) }} MB / {{ number_format($totalMaxStorage) }} MB</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full transition-all {{ $storagePercent > 80 ? 'bg-red-500' : 'bg-emerald-500' }}" style="width: {{ min($storagePercent, 100) }}%"></div>
                </div>
            </div>

            {{-- Connections --}}
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-slate-500 dark:text-slate-400">Connections</span>
                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $totalActiveConns }} / {{ $totalMaxConns }}</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full transition-all {{ $connPercent > 80 ? 'bg-red-500' : 'bg-amber-500' }}" style="width: {{ min($connPercent, 100) }}%"></div>
                </div>
            </div>

            {{-- Engines & Gói --}}
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-slate-500 dark:text-slate-400">Engines</span>
                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ implode(', ', array_map('ucfirst', $dbQuota['engines'])) }}</span>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500 dark:text-slate-400">Gói dịch vụ</span>
                    <span class="font-medium text-primary">{{ $planLabel }}</span>
                </div>
            </div>
        </div>
    </div>
</aside>
