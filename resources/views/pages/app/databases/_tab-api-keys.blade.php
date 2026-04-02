{{-- ============================================ --}}
{{-- TAB: API KEYS - Quản lý API keys (dữ liệu thật) --}}
{{-- Dữ liệu: $apiKeys (Collection of ApiKey), $dbQuota --}}
{{-- ============================================ --}}
<div x-show="activeTab === 'api-keys'" x-cloak>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">API Keys</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Quản lý API keys để truy cập qua SDK
                <span class="text-slate-400">({{ $apiKeys->count() }} / {{ $dbQuota['max_api_keys'] }})</span>
            </p>
        </div>
        @if($apiKeys->count() < $dbQuota['max_api_keys'])
        <button x-data="{ creating: false }" @click="creating = true;
            fetch('{{ route('app.cloud-plan.current') }}')
                .then(r => r.json())
                .then(d => { creating = false; showToast('Tính năng đang phát triển', 'error'); })
                .catch(() => { creating = false; showToast('Có lỗi xảy ra', 'error'); })"
            :disabled="creating"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm disabled:opacity-50">
            <span class="material-symbols-outlined text-lg" x-show="!creating">add</span>
            <svg x-show="creating" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Tạo Key mới
        </button>
        @else
        <span class="text-xs text-slate-400 bg-slate-100 dark:bg-slate-700 px-3 py-2 rounded-lg">
            Đã đạt giới hạn — <button @click="activeTab = 'pricing'" class="text-primary hover:underline font-medium">Nâng cấp</button>
        </span>
        @endif
    </div>

    @if($apiKeys->count() > 0)
    <div
        class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-100 dark:border-slate-700">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tên</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Key</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden sm:table-cell">Sử dụng lần cuối</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach($apiKeys as $key)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-5 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ $key->name }}</td>
                    <td class="px-5 py-4">
                        <code class="text-xs bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded font-mono text-slate-600 dark:text-slate-300">
                            {{ $key->getMaskedKey() }}
                        </code>
                    </td>
                    <td class="px-5 py-4 text-sm text-slate-500 dark:text-slate-400 hidden sm:table-cell">
                        {{ $key->last_used_at ? $key->last_used_at->diffForHumans() : 'Chưa dùng' }}
                    </td>
                    <td class="px-5 py-4">
                        @if($key->is_active)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">Kích hoạt</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400">Đã vô hiệu</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <button
                            class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                            title="Vô hiệu hóa">
                            <span class="material-symbols-outlined text-base">block</span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        {{-- Empty state --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm p-12 text-center">
            <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-amber-500/10 to-orange-500/10 flex items-center justify-center mx-auto mb-5">
                <span class="material-symbols-outlined text-4xl text-amber-500/60">key</span>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Chưa có API Key nào</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">
                Tạo API Key để truy cập database qua REST API hoặc SDK.
            </p>
            <p class="text-xs text-slate-400">Gói {{ $planLabel }} cho phép tối đa {{ $dbQuota['max_api_keys'] }} keys.</p>
        </div>
    @endif
</div>
