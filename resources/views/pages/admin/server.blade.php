<x-admin-layout title="NDHShop - Admin - Quản lý Server">
    <div x-data="{
        metrics: @js($metrics),
        loading: false,
        autoRefresh: true,
        intervalId: null,
        maxPoints: 20,

        // Lịch sử dữ liệu cho biểu đồ wave
        cpuHistory: [],
        ramHistory: [],
        diskHistory: [],
        netRxHistory: [],

        init() {
            // Khởi tạo history với giá trị ban đầu
            const initial = this.metrics;
            this.cpuHistory.push(initial.cpu.percent);
            this.ramHistory.push(initial.ram.percent);
            this.diskHistory.push(initial.disk.percent);
            this.netRxHistory.push(initial.network.rx_mbps);
            this.startAutoRefresh();
        },

        async fetchMetrics() {
            this.loading = true;
            try {
                const res = await fetch('{{ route('admin.server.index') }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    this.metrics = await res.json();
                    this.pushHistory();
                }
            } catch (e) {
                console.error('Lỗi lấy dữ liệu server:', e);
            }
            this.loading = false;
        },

        pushHistory() {
            const push = (arr, val) => {
                arr.push(val);
                if (arr.length > this.maxPoints) arr.shift();
            };
            push(this.cpuHistory, this.metrics.cpu.percent);
            push(this.ramHistory, this.metrics.ram.percent);
            push(this.diskHistory, this.metrics.disk.percent);
            push(this.netRxHistory, this.metrics.network.rx_mbps);
        },

        // Sinh SVG path từ mảng history (percent 0-100 -> y 5-38)
        buildPath(history, maxVal = 100) {
            if (history.length < 2) return { area: 'M0,38 V40 H100 V38 Z', line: 'M0,38' };
            const pts = history.map((v, i) => {
                const x = (i / (history.length - 1)) * 100;
                const clamped = Math.min(v, maxVal);
                const y = 38 - (clamped / maxVal) * 33; // y: 5 (max) -> 38 (min)
                return { x: Math.round(x * 10) / 10, y: Math.round(y * 10) / 10 };
            });
            const line = pts.map((p, i) => (i === 0 ? 'M' : 'L') + p.x + ',' + p.y).join(' ');
            const area = line + ' V40 H0 Z';
            return { area, line };
        },

        get cpuPath() { return this.buildPath(this.cpuHistory); },
        get ramPath() { return this.buildPath(this.ramHistory); },
        get diskPath() { return this.buildPath(this.diskHistory); },
        get netPath() {
            const max = Math.max(1, ...this.netRxHistory) * 1.2;
            return this.buildPath(this.netRxHistory, max);
        },

        startAutoRefresh() {
            if (this.intervalId) clearInterval(this.intervalId);
            this.intervalId = setInterval(() => {
                if (this.autoRefresh) this.fetchMetrics();
            }, 5000);
        },

        cpuStatusColor() {
            const p = this.metrics.cpu.percent;
            if (p < 60) return 'text-emerald-500';
            if (p < 85) return 'text-amber-500';
            return 'text-rose-500';
        },

        ramBarColor() {
            const p = this.metrics.ram.percent;
            if (p < 60) return 'bg-purple-500';
            if (p < 85) return 'bg-amber-500';
            return 'bg-rose-500';
        },

        diskBarColor() {
            const p = this.metrics.disk.percent;
            if (p < 70) return 'bg-sky-500';
            if (p < 90) return 'bg-amber-500';
            return 'bg-rose-500';
        }
    }" x-init="init()">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">dns</span>
                    Server Health
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Giám sát tài nguyên hệ thống theo thời gian thực.
                    <span class="text-slate-400" x-text="'• Cập nhật lúc: ' + metrics.timestamp"></span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Toggle auto refresh --}}
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Tự động</span>
                    <div class="relative">
                        <input type="checkbox" x-model="autoRefresh" class="sr-only peer">
                        <div
                            class="w-9 h-5 bg-slate-300 dark:bg-slate-600 rounded-full peer-checked:bg-primary transition-colors">
                        </div>
                        <div
                            class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-4 shadow">
                        </div>
                    </div>
                </label>
                {{-- Refresh thủ công --}}
                <button @click="fetchMetrics()" :disabled="loading"
                    class="flex items-center gap-2 px-4 py-2 bg-surface-dark border border-border-dark text-white text-sm font-semibold rounded-lg hover:border-primary/50 transition-all disabled:opacity-50">
                    <span class="material-symbols-outlined text-[18px]"
                        :class="loading ? 'animate-spin' : ''">refresh</span>
                    Làm mới
                </button>
            </div>
        </div>

        {{-- Grid 4 cột: CPU, RAM, Disk, Network --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">

            {{-- CPU Usage --}}
            <div
                class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-xl p-5 flex flex-col gap-4 relative overflow-hidden group">
                <div class="flex justify-between items-start z-10">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">CPU Usage</p>
                        <h3 class="text-slate-900 dark:text-white text-3xl font-bold mt-1"
                            x-text="metrics.cpu.percent + '%'"></h3>
                    </div>
                    <div class="bg-primary/20 p-2 rounded-lg text-primary">
                        <span class="material-symbols-outlined">memory</span>
                    </div>
                </div>
                <div class="z-10">
                    <p class="text-xs text-slate-500 dark:text-slate-500" x-text="metrics.cpu.cores + ' Cores'"></p>
                    <p class="text-xs font-medium mt-1" :class="cpuStatusColor()" x-text="metrics.cpu.status"></p>
                </div>
                {{-- Biểu đồ CPU động --}}
                <div class="absolute bottom-0 left-0 right-0 h-16 opacity-30 group-hover:opacity-50 transition-opacity">
                    <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 40">
                        <path :d="cpuPath.area" fill="#135bec" class="transition-all duration-500"></path>
                        <path :d="cpuPath.line" fill="none" stroke="#135bec" stroke-width="2"
                            class="transition-all duration-500"></path>
                    </svg>
                </div>
            </div>

            {{-- RAM Usage --}}
            <div
                class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-xl p-5 flex flex-col gap-4 relative overflow-hidden group">
                <div class="flex justify-between items-start z-10">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">RAM Usage</p>
                        <h3 class="text-slate-900 dark:text-white text-3xl font-bold mt-1">
                            <span x-text="metrics.ram.used_gb"></span>
                            <span class="text-lg text-slate-400 dark:text-slate-500 font-medium">GB</span>
                        </h3>
                    </div>
                    <div class="bg-purple-500/20 p-2 rounded-lg text-purple-500">
                        <span class="material-symbols-outlined">sd_card</span>
                    </div>
                </div>
                <div class="z-10">
                    <div class="w-full bg-slate-200 dark:bg-background-dark h-1.5 rounded-full mt-2 mb-1">
                        <div class="h-full rounded-full transition-all duration-700" :class="ramBarColor()"
                            :style="'width: ' + metrics.ram.percent + '%'"></div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-500">
                        Tổng <span x-text="metrics.ram.total_gb"></span>GB
                        • <span x-text="metrics.ram.available_gb"></span>GB Khả dụng
                    </p>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-16 opacity-30 group-hover:opacity-50 transition-opacity">
                    <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 40">
                        <path :d="ramPath.area" fill="#a855f7" class="transition-all duration-500"></path>
                        <path :d="ramPath.line" fill="none" stroke="#a855f7" stroke-width="2"
                            class="transition-all duration-500"></path>
                    </svg>
                </div>
            </div>

            {{-- Disk Usage --}}
            <div
                class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-xl p-5 flex flex-col gap-4 relative overflow-hidden group">
                <div class="flex justify-between items-start z-10">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Disk Usage</p>
                        <h3 class="text-slate-900 dark:text-white text-3xl font-bold mt-1">
                            <span x-text="metrics.disk.used_gb"></span>
                            <span class="text-lg text-slate-400 dark:text-slate-500 font-medium">GB</span>
                        </h3>
                    </div>
                    <div class="bg-sky-500/20 p-2 rounded-lg text-sky-500">
                        <span class="material-symbols-outlined">hard_drive</span>
                    </div>
                </div>
                <div class="z-10">
                    <div class="w-full bg-slate-200 dark:bg-background-dark h-1.5 rounded-full mt-2 mb-1">
                        <div class="h-full rounded-full transition-all duration-700" :class="diskBarColor()"
                            :style="'width: ' + metrics.disk.percent + '%'"></div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-500">
                        Tổng <span x-text="metrics.disk.total_gb"></span>GB
                        • <span x-text="metrics.disk.free_gb"></span>GB Trống
                    </p>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-16 opacity-30 group-hover:opacity-50 transition-opacity">
                    <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 40">
                        <path :d="diskPath.area" fill="#0ea5e9" class="transition-all duration-500"></path>
                        <path :d="diskPath.line" fill="none" stroke="#0ea5e9" stroke-width="2"
                            class="transition-all duration-500"></path>
                    </svg>
                </div>
            </div>

            {{-- Network I/O --}}
            <div
                class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-xl p-5 flex flex-col gap-4 relative overflow-hidden group">
                <div class="flex justify-between items-start z-10">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Network I/O</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="material-symbols-outlined text-emerald-500 text-[18px]">arrow_downward</span>
                            <h3 class="text-slate-900 dark:text-white text-2xl font-bold">
                                <span x-text="metrics.network.rx_mbps"></span>
                                <span class="text-sm text-slate-400 dark:text-slate-500 font-medium">MB/s</span>
                            </h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sky-500 text-[18px]">arrow_upward</span>
                            <h3 class="text-slate-600 dark:text-slate-300 text-lg font-bold">
                                <span x-text="metrics.network.tx_mbps"></span>
                                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">MB/s</span>
                            </h3>
                        </div>
                    </div>
                    <div class="bg-emerald-500/20 p-2 rounded-lg text-emerald-500">
                        <span class="material-symbols-outlined">language</span>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-16 opacity-30 group-hover:opacity-50 transition-opacity">
                    <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 40">
                        <path :d="netPath.area" fill="#10b981" class="transition-all duration-500"></path>
                        <path :d="netPath.line" fill="none" stroke="#10b981" stroke-width="2"
                            class="transition-all duration-500"></path>
                    </svg>
                </div>
            </div>

        </div>

        {{-- Thông tin chi tiết hệ thống --}}
        <div
            class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark rounded-xl overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-border-dark flex items-center justify-between">
                <h3 class="text-slate-900 dark:text-white text-lg font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">info</span>
                    Thông tin hệ thống
                </h3>
                <span class="text-xs text-slate-400 font-mono" x-text="metrics.hostname"></span>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        class="bg-slate-50 dark:bg-background-dark/50 rounded-lg p-4 border border-slate-100 dark:border-border-dark">
                        <p class="text-xs text-slate-400 font-medium mb-1">Hostname</p>
                        <p class="text-sm font-bold text-slate-900 dark:text-white font-mono" x-text="metrics.hostname">
                        </p>
                    </div>
                    <div
                        class="bg-slate-50 dark:bg-background-dark/50 rounded-lg p-4 border border-slate-100 dark:border-border-dark">
                        <p class="text-xs text-slate-400 font-medium mb-1">Uptime</p>
                        <p class="text-sm font-bold text-slate-900 dark:text-white font-mono" x-text="metrics.uptime">
                        </p>
                    </div>
                    <div
                        class="bg-slate-50 dark:bg-background-dark/50 rounded-lg p-4 border border-slate-100 dark:border-border-dark">
                        <p class="text-xs text-slate-400 font-medium mb-1">CPU Cores</p>
                        <p class="text-sm font-bold text-slate-900 dark:text-white font-mono"
                            x-text="metrics.cpu.cores + ' vCPU'"></p>
                    </div>
                    <div
                        class="bg-slate-50 dark:bg-background-dark/50 rounded-lg p-4 border border-slate-100 dark:border-border-dark">
                        <p class="text-xs text-slate-400 font-medium mb-1">Cập nhật lần cuối</p>
                        <p class="text-sm font-bold text-slate-900 dark:text-white font-mono"
                            x-text="metrics.timestamp"></p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>