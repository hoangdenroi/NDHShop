<x-admin-layout title="NDHShop - Admin - Quản lý Cron Job">
    <div x-data="cronManager()">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Quản lý Cron Job</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Chạy thủ công hoặc giám sát các tác vụ tự động trong hệ thống.</p>
            </div>
            <button @click="runAllCommands()"
                :disabled="runningAll"
                class="inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed bg-gradient-to-br from-primary to-blue-600 text-white hover:scale-[0.97] shadow-primary/30">
                <template x-if="!runningAll">
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">play_circle</span>
                        Chạy Tất Cả ({{ count($jobs) }})
                    </span>
                </template>
                <template x-if="runningAll">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Đang chạy tất cả...
                    </span>
                </template>
            </button>
        </div>



        {{-- Danh sách Cron Job --}}
        <div class="grid gap-4">
            @foreach($jobs as $job)
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl flex items-center justify-center shadow-sm shrink-0
                            @if($job['color'] === 'blue') bg-blue-50 dark:bg-blue-500/10
                            @elseif($job['color'] === 'amber') bg-amber-50 dark:bg-amber-500/10
                            @else bg-emerald-50 dark:bg-emerald-500/10 @endif">
                            <span class="material-symbols-outlined text-2xl
                                @if($job['color'] === 'blue') text-blue-500
                                @elseif($job['color'] === 'amber') text-amber-500
                                @else text-emerald-500 @endif">{{ $job['icon'] }}</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $job['name'] }}</h3>
                                <code class="text-xs bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 px-2 py-0.5 rounded-md font-mono">{{ $job['command'] }}</code>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $job['description'] }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                <span class="material-symbols-outlined text-xs align-middle">schedule</span>
                                {{ $job['schedule'] }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <button @click="runCommand('{{ $job['command'] }}')"
                            :disabled="runningCommand === '{{ $job['command'] }}'"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed
                                @if($job['color'] === 'blue') bg-blue-500 text-white hover:bg-blue-600
                                @elseif($job['color'] === 'amber') bg-amber-500 text-white hover:bg-amber-600
                                @else bg-emerald-500 text-white hover:bg-emerald-600 @endif">
                            <template x-if="runningCommand !== '{{ $job['command'] }}'">
                                <span class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-base">play_arrow</span>
                                    Chạy ngay
                                </span>
                            </template>
                            <template x-if="runningCommand === '{{ $job['command'] }}'">
                                <span class="flex items-center gap-1.5">
                                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Đang chạy...
                                </span>
                            </template>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
    function cronManager() {
        return {
            runningCommand: null,
            runningAll: false,

            showToast(message, type = 'success', output = '') {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: type,
                        title: type === 'warning' ? 'Cảnh báo' : (type === 'error' ? 'Lỗi' : (type === 'success' ? 'Thành công' : 'Thông báo')),
                        message: message,
                        output: output
                    }
                }));
            },

            async runCommand(command) {
                this.runningCommand = command;

                try {
                    const res = await fetch('{{ route("admin.cron.run") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ command })
                    });
                    const data = await res.json();

                    this.showToast(
                        data.message,
                        data.success ? 'success' : 'error',
                        data.output || ''
                    );
                } catch (e) {
                    console.error(e);
                    this.showToast('Không thể kết nối máy chủ.', 'error');
                } finally {
                    this.runningCommand = null;
                }
            },

            async runAllCommands() {
                this.runningAll = true;

                try {
                    const res = await fetch('{{ route("admin.cron.run-all") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();

                    this.showToast(
                        data.message,
                        data.success ? 'success' : 'warning',
                        data.output || ''
                    );
                } catch (e) {
                    console.error(e);
                    this.showToast('Không thể kết nối máy chủ.', 'error');
                } finally {
                    this.runningAll = false;
                }
            }
        }
    }
    </script>
</x-admin-layout>
