{{-- ═══════════════════════════════════════════ --}}
{{-- MODAL: CHI TIẾT KẾT NỐI (dynamic từ selectedDb) --}}
{{-- ═══════════════════════════════════════════ --}}
<div x-show="showConnectionModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeConnectionModal()"></div>
    <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-100 dark:border-slate-700 p-6"
        @click.away="closeConnectionModal()">
        <button @click="closeConnectionModal()"
            class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-white">
            <span class="material-symbols-outlined">close</span>
        </button>

        {{-- Header --}}
        <template x-if="selectedDb">
            <div>
                <div class="flex items-center gap-3 mb-5">
                    <div class="h-10 w-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                        <template x-if="selectedDb.engine === 'postgresql'">
                            <x-icons.postgresql class="w-8 h-8" />
                        </template>
                        <template x-if="selectedDb.engine !== 'postgresql'">
                            <x-icons.mysql class="w-8 h-8" />
                        </template>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white" x-text="selectedDb.db_name"></h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Thông tin kết nối <span x-text="selectedDb.engine === 'postgresql' ? 'PostgreSQL' : 'MySQL'"></span>
                        </p>
                    </div>
                </div>

                {{-- Các trường kết nối --}}
                <div class="space-y-3">
                    {{-- Host --}}
                    <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-700/50 rounded-xl px-4 py-3">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Host</p>
                            <p class="text-sm font-mono font-medium text-slate-900 dark:text-white" x-text="selectedDb.host"></p>
                        </div>
                        <button @click="copyToClipboard(selectedDb.host, 'host')"
                            class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                            <span class="material-symbols-outlined text-base"
                                :class="copiedField === 'host' ? 'text-emerald-500' : 'text-slate-400'"
                                x-text="copiedField === 'host' ? 'check' : 'content_copy'"></span>
                        </button>
                    </div>
                    
                    {{-- Port --}}
                    <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-700/50 rounded-xl px-4 py-3">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Port</p>
                            <p class="text-sm font-mono font-medium text-slate-900 dark:text-white" x-text="selectedDb.port"></p>
                        </div>
                        <button @click="copyToClipboard(String(selectedDb.port), 'port')"
                            class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                            <span class="material-symbols-outlined text-base"
                                :class="copiedField === 'port' ? 'text-emerald-500' : 'text-slate-400'"
                                x-text="copiedField === 'port' ? 'check' : 'content_copy'"></span>
                        </button>
                    </div>

                    {{-- Database --}}
                    <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-700/50 rounded-xl px-4 py-3">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Database</p>
                            <p class="text-sm font-mono font-medium text-slate-900 dark:text-white" x-text="selectedDb.db_name"></p>
                        </div>
                        <button @click="copyToClipboard(selectedDb.db_name, 'dbname')"
                            class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                            <span class="material-symbols-outlined text-base"
                                :class="copiedField === 'dbname' ? 'text-emerald-500' : 'text-slate-400'"
                                x-text="copiedField === 'dbname' ? 'check' : 'content_copy'"></span>
                        </button>
                    </div>

                    {{-- Username --}}
                    <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-700/50 rounded-xl px-4 py-3">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Username</p>
                            <p class="text-sm font-mono font-medium text-slate-900 dark:text-white" x-text="selectedDb.db_user"></p>
                        </div>
                        <button @click="copyToClipboard(selectedDb.db_user, 'user')"
                            class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                            <span class="material-symbols-outlined text-base"
                                :class="copiedField === 'user' ? 'text-emerald-500' : 'text-slate-400'"
                                x-text="copiedField === 'user' ? 'check' : 'content_copy'"></span>
                        </button>
                    </div>

                    {{-- Password --}}
                    <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-700/50 rounded-xl px-4 py-3" x-data="{ showPass: false }">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Password</p>
                            <p class="text-sm font-mono font-medium text-slate-900 dark:text-white" x-text="showPass ? selectedDb.raw_password : '••••••••••••'"></p>
                        </div>
                        <div class="flex items-center gap-1">
                            <button @click="showPass = !showPass"
                                class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors" title="Hiện mật khẩu">
                                <span class="material-symbols-outlined text-base text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                                    x-text="showPass ? 'visibility_off' : 'visibility'"></span>
                            </button>
                            <button @click="copyToClipboard(selectedDb.raw_password, 'pass')"
                                class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors" title="Sao chép">
                                <span class="material-symbols-outlined text-base"
                                    :class="copiedField === 'pass' ? 'text-emerald-500' : 'text-slate-400'"
                                    x-text="copiedField === 'pass' ? 'check' : 'content_copy'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Connection String --}}
                <div class="mt-4 bg-slate-900 dark:bg-slate-950 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs text-slate-400 font-medium">Connection String</p>
                        <button @click="copyToClipboard(selectedDb.connection_string, 'connstr')"
                            class="text-xs text-primary hover:text-primary/80 font-medium flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm" x-text="copiedField === 'connstr' ? 'check' : 'content_copy'"></span>
                            <span x-text="copiedField === 'connstr' ? 'Đã sao chép!' : 'Sao chép'"></span>
                        </button>
                    </div>
                    <code class="text-sm text-emerald-400 font-mono break-all leading-relaxed" x-text="selectedDb.connection_string"></code>
                </div>
            </div>
        </template>
    </div>
</div>
