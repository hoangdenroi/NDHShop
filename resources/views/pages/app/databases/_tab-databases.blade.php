{{-- ============================================ --}}
{{-- TAB: DATABASES - Danh sách database instances --}}
{{-- Dữ liệu: $databases (Collection of CloudDatabase) --}}
{{-- ============================================ --}}
<div x-show="activeTab === 'databases'" x-cloak>

    {{-- Tiêu đề + Tìm kiếm --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Databases của bạn</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Quản lý tất cả database instances</p>
        </div>
        @if($databases->count() > 0)
            <div class="relative w-full sm:w-auto">
                <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                <input type="text" placeholder="Tìm kiếm database..."
                    class="w-full sm:w-64 pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors">
            </div>
        @endif
    </div>

    @if($databases->count() > 0)
        {{-- Danh sách Database Cards --}}
        <div class="grid gap-4">
            @foreach($databases as $db)
                @php
                    $isActive = $db->status === \App\Models\CloudDatabase::STATUS_ACTIVE;
                    $isSuspended = $db->status === \App\Models\CloudDatabase::STATUS_SUSPENDED;
                    $isProvisioning = $db->status === \App\Models\CloudDatabase::STATUS_PROVISIONING;
                    $isPg = $db->isPostgresql();
                @endphp
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow p-5
                                    {{ $isProvisioning ? 'opacity-80' : '' }}
                                    {{ $isSuspended ? 'opacity-60 border-red-200 dark:border-red-800' : '' }}">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center shadow-sm shrink-0
                                                {{ $isProvisioning ? 'animate-pulse' : '' }}">
                                @if($isPg)
                                    <x-icons.postgresql class="w-9 h-9" />
                                @else
                                    <x-icons.mysql class="w-9 h-9" />
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $db->db_name }}</h3>
                                    @if($isActive)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Hoạt động
                                        </span>
                                    @elseif($isSuspended)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400">
                                            <span class="material-symbols-outlined text-xs">pause_circle</span>
                                            Tạm dừng
                                        </span>
                                    @elseif($isProvisioning)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                                            <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4" fill="none"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            Đang khởi tạo...
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                                    {{ $isPg ? 'PostgreSQL' : 'MySQL' }} · Tạo {{ $db->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        @if($isActive)
                            <div class="flex items-center gap-2 sm:gap-3">
                                <button @click="openConnection({
                                                        db_name: '{{ $db->db_name }}',
                                                        db_user: '{{ $db->db_user }}',
                                                        host: '{{ $db->host }}',
                                                        port: {{ $db->port }},
                                                        engine: '{{ $db->engine }}',
                                                        raw_password: '{{ $db->getDecryptedPassword() }}',
                                                        connection_string: '{{ $db->isPostgresql() ? "postgresql" : "mysql" }}://{{ $db->db_user }}:{{ $db->getDecryptedPassword() }}@{{ $db->host }}:{{ $db->port }}/{{ $db->db_name }}'
                                                    })"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-primary bg-primary/5 hover:bg-primary/10 dark:bg-primary/10 dark:hover:bg-primary/20 transition-colors">
                                    <span class="material-symbols-outlined text-base">link</span>
                                    Kết nối
                                </button>
                                <button @click="deleteDb({{ $db->id }}, '{{ $db->db_name }}')" :disabled="isDeletingDb"
                                    :class="isDeletingDb ? 'opacity-50 cursor-not-allowed text-slate-400' : 'text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10'"
                                    class="p-2 rounded-lg transition-colors" title="Xóa database">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </div>
                        @elseif($isSuspended)
                            <div class="flex items-center gap-2">
                                <button @click="activeTab = 'pricing'"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-amber-600 bg-amber-50 hover:bg-amber-100 dark:bg-amber-500/10 dark:hover:bg-amber-500/20 transition-colors">
                                    <span class="material-symbols-outlined text-base">upgrade</span>
                                    Nâng cấp để kích hoạt
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Thông tin chi tiết --}}
                    @if($isActive || $isSuspended)
                        <div
                            class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Dung lượng</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">
                                    {{ number_format($db->storage_used_mb, 1) }} MB / {{ $db->max_storage_mb }} MB
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Connections</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $db->active_connections }} / {{ $db->max_connections }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Port</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $db->port }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Ngày tạo</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">
                                    {{ $db->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty state --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm p-12 text-center">
            <div
                class="h-20 w-20 rounded-2xl bg-gradient-to-br from-blue-500/10 to-indigo-500/10 flex items-center justify-center mx-auto mb-5">
                <span class="material-symbols-outlined text-4xl text-primary/60">database</span>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Chưa có database nào</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 max-w-sm mx-auto">
                Tạo database đầu tiên của bạn để bắt đầu lưu trữ dữ liệu.
                @if($currentPlan === 'free')
                    Gói Free cho phép 1 database MySQL miễn phí.
                @else
                    Gói {{ $planLabel }} cho phép tối đa {{ $dbQuota['max_databases'] }} databases.
                @endif
            </p>
            <button @click="showCreateModal = true"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary text-white hover:bg-primary/90 transition-colors font-medium text-sm shadow-sm">
                <span class="material-symbols-outlined text-lg">add_circle</span>
                Tạo Database mới
            </button>
        </div>
    @endif
</div>